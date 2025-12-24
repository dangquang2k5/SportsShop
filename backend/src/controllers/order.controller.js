const { pool } = require('../config/database');

// @desc    Create new order
// @route   POST /api/orders
// @access  Public (supports guest checkout)
const createOrder = async (req, res, next) => {
    const connection = await pool.getConnection();

    try {
        await connection.beginTransaction();

        const {
            items, // Array of {productDetailId, quantity, price}
            shippingAddress,
            shippingCity,
            shippingPhone,
            notes,
            voucherCode,
            guestName,
            guestEmail
        } = req.body;

        // Validate items
        if (!items || items.length === 0) {
            return res.status(400).json({
                success: false,
                message: 'Giỏ hàng trống'
            });
        }

        // Calculate subtotal
        let subtotal = 0;
        for (const item of items) {
            subtotal += item.price * item.quantity;
        }

        // Handle voucher
        let discount = 0;
        let voucherId = null;

        if (voucherCode) {
            const [vouchers] = await connection.query(
                `SELECT * FROM Voucher 
         WHERE VoucherCode = ? 
         AND StartDate <= CURDATE() 
         AND EndDate >= CURDATE()
         AND Quantity > 0`,
                [voucherCode]
            );

            if (vouchers.length > 0) {
                const voucher = vouchers[0];
                if (subtotal >= voucher.MinOrderValue) {
                    discount = voucher.DiscountValue;
                    voucherId = voucher.VoucherID;

                    // Decrease voucher quantity
                    await connection.query(
                        'UPDATE Voucher SET Quantity = Quantity - 1 WHERE VoucherID = ?',
                        [voucherId]
                    );
                }
            }
        }

        // Calculate shipping
        const shipping = subtotal >= 500000 ? 0 : 30000;
        const totalAmount = subtotal - discount + shipping;

        // Create order
        const fullAddress = `${shippingAddress}, ${shippingCity}`;
        const userId = req.user ? req.user.UserID : null;

        let fullNotes = notes || '';
        if (!userId && guestName && guestEmail) {
            fullNotes = `KHÁCH VÃNG LAI:\nHọ tên: ${guestName}\nEmail: ${guestEmail}\nSĐT: ${shippingPhone}\n\n${notes || ''}`;
        }

        const [orderResult] = await connection.query(
            `INSERT INTO Orders (UserID, GuestName, GuestEmail, GuestPhone, TotalAmount, Address, Status, VoucherID, Note)
       VALUES (?, ?, ?, ?, ?, ?, 'pending', ?, ?)`,
            [userId, guestName || null, guestEmail || null, shippingPhone, totalAmount, fullAddress, voucherId, fullNotes]
        );

        const orderId = orderResult.insertId;

        // Create order details
        for (const item of items) {
            await connection.query(
                'INSERT INTO OrderDetails (OrderID, ProductDetailID, Quantity, Price) VALUES (?, ?, ?, ?)',
                [orderId, item.productDetailId, item.quantity, item.price]
            );
        }

        await connection.commit();

        res.status(201).json({
            success: true,
            message: 'Đặt hàng thành công',
            data: {
                orderId,
                totalAmount,
                discount,
                shipping
            }
        });
    } catch (error) {
        await connection.rollback();
        next(error);
    } finally {
        connection.release();
    }
};

// @desc    Get user orders
// @route   GET /api/orders
// @access  Private
const getUserOrders = async (req, res, next) => {
    try {
        const [orders] = await pool.query(
            `SELECT o.*, 
              (SELECT COUNT(*) FROM OrderDetails WHERE OrderID = o.OrderID) as ItemCount
       FROM Orders o
       WHERE o.UserID = ?
       ORDER BY o.created_at DESC`,
            [req.user.UserID]
        );

        res.json({
            success: true,
            data: orders
        });
    } catch (error) {
        next(error);
    }
};

// @desc    Get order by ID
// @route   GET /api/orders/:id
// @access  Private
const getOrderById = async (req, res, next) => {
    try {
        const { id } = req.params;

        // Get order
        const [orders] = await pool.query(
            'SELECT * FROM Orders WHERE OrderID = ?',
            [id]
        );

        if (orders.length === 0) {
            return res.status(404).json({
                success: false,
                message: 'Không tìm thấy đơn hàng'
            });
        }

        const order = orders[0];

        // Check permission (user can only view their own orders, admin can view all)
        if (req.user.Role !== 'admin' && order.UserID !== req.user.UserID) {
            return res.status(403).json({
                success: false,
                message: 'Bạn không có quyền xem đơn hàng này'
            });
        }

        // Get order details
        const [orderDetails] = await pool.query(
            `SELECT od.*, p.ProductName, p.MainImage, pd.Size, pd.Color
       FROM OrderDetails od
       JOIN ProductDetail pd ON od.ProductDetailID = pd.ProductDetailID
       JOIN Product p ON pd.ProductID = p.ProductID
       WHERE od.OrderID = ?`,
            [id]
        );

        res.json({
            success: true,
            data: {
                ...order,
                items: orderDetails
            }
        });
    } catch (error) {
        next(error);
    }
};

// @desc    Get all orders (Admin)
// @route   GET /api/admin/orders
// @access  Private/Admin
const getAllOrders = async (req, res, next) => {
    try {
        const page = parseInt(req.query.page) || 1;
        const limit = parseInt(req.query.limit) || 20;
        const offset = (page - 1) * limit;
        const status = req.query.status;

        let query = `
      SELECT o.*, 
             u.FirstName, u.LastName, u.Email,
             (SELECT COUNT(*) FROM OrderDetails WHERE OrderID = o.OrderID) as ItemCount
      FROM Orders o
      LEFT JOIN Users u ON o.UserID = u.UserID
      WHERE 1=1
    `;
        let countQuery = 'SELECT COUNT(*) as total FROM Orders WHERE 1=1';
        const params = [];
        const countParams = [];

        if (status) {
            query += ' AND o.Status = ?';
            countQuery += ' AND Status = ?';
            params.push(status);
            countParams.push(status);
        }

        query += ' ORDER BY o.created_at DESC LIMIT ? OFFSET ?';

        const [orders] = await pool.query(query, [...params, limit, offset]);
        const [countResult] = await pool.query(countQuery, countParams);

        res.json({
            success: true,
            data: {
                orders,
                pagination: {
                    page,
                    limit,
                    total: countResult[0].total,
                    totalPages: Math.ceil(countResult[0].total / limit)
                }
            }
        });
    } catch (error) {
        next(error);
    }
};

// @desc    Update order status (Admin)
// @route   PUT /api/admin/orders/:id/status
// @access  Private/Admin
const updateOrderStatus = async (req, res, next) => {
    try {
        const { id } = req.params;
        const { status } = req.body;

        const validStatuses = ['pending', 'processing', 'shipped', 'delivered', 'canceled'];
        if (!validStatuses.includes(status)) {
            return res.status(400).json({
                success: false,
                message: 'Trạng thái không hợp lệ'
            });
        }

        await pool.query(
            'UPDATE Orders SET Status = ? WHERE OrderID = ?',
            [status, id]
        );

        res.json({
            success: true,
            message: 'Cập nhật trạng thái đơn hàng thành công'
        });
    } catch (error) {
        next(error);
    }
};

module.exports = {
    createOrder,
    getUserOrders,
    getOrderById,
    getAllOrders,
    updateOrderStatus
};
