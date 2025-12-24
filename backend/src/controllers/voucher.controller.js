const { pool } = require('../config/database');

// @desc    Get available vouchers
// @route   GET /api/vouchers
// @access  Public
const getAvailableVouchers = async (req, res, next) => {
    try {
        const [vouchers] = await pool.query(
            `SELECT VoucherID, VoucherCode, DiscountValue, MinOrderValue, Quantity, StartDate, EndDate
       FROM Voucher
       WHERE StartDate <= CURDATE()
       AND EndDate >= CURDATE()
       AND Quantity > 0
       ORDER BY DiscountValue DESC`
        );

        res.json({
            success: true,
            data: vouchers
        });
    } catch (error) {
        next(error);
    }
};

// @desc    Validate voucher code
// @route   POST /api/vouchers/validate
// @access  Public
const validateVoucher = async (req, res, next) => {
    try {
        const { code, orderAmount } = req.body;

        if (!code) {
            return res.status(400).json({
                success: false,
                message: 'Vui lòng nhập mã voucher'
            });
        }

        const [vouchers] = await pool.query(
            `SELECT * FROM Voucher
       WHERE VoucherCode = ?
       AND StartDate <= CURDATE()
       AND EndDate >= CURDATE()`,
            [code]
        );

        if (vouchers.length === 0) {
            return res.status(404).json({
                success: false,
                message: 'Mã voucher không hợp lệ hoặc đã hết hạn'
            });
        }

        const voucher = vouchers[0];

        if (voucher.Quantity <= 0) {
            return res.status(400).json({
                success: false,
                message: 'Mã voucher đã hết lượt sử dụng'
            });
        }

        if (orderAmount && orderAmount < voucher.MinOrderValue) {
            return res.status(400).json({
                success: false,
                message: `Đơn hàng tối thiểu ${voucher.MinOrderValue.toLocaleString('vi-VN')}₫ để sử dụng mã này`
            });
        }

        res.json({
            success: true,
            message: 'Mã voucher hợp lệ',
            data: {
                voucherCode: voucher.VoucherCode,
                discountValue: voucher.DiscountValue,
                minOrderValue: voucher.MinOrderValue
            }
        });
    } catch (error) {
        next(error);
    }
};

// @desc    Get all vouchers (Admin)
// @route   GET /api/admin/vouchers
// @access  Private/Admin
const getAllVouchers = async (req, res, next) => {
    try {
        const [vouchers] = await pool.query(
            'SELECT * FROM Voucher ORDER BY created_at DESC'
        );

        res.json({
            success: true,
            data: vouchers
        });
    } catch (error) {
        next(error);
    }
};

// @desc    Create voucher (Admin)
// @route   POST /api/admin/vouchers
// @access  Private/Admin
const createVoucher = async (req, res, next) => {
    try {
        const { voucherCode, discountValue, minOrderValue, quantity, startDate, endDate } = req.body;

        const [result] = await pool.query(
            `INSERT INTO Voucher (VoucherCode, DiscountValue, MinOrderValue, Quantity, StartDate, EndDate)
       VALUES (?, ?, ?, ?, ?, ?)`,
            [voucherCode, discountValue, minOrderValue, quantity, startDate, endDate]
        );

        res.status(201).json({
            success: true,
            message: 'Tạo voucher thành công',
            data: {
                voucherId: result.insertId
            }
        });
    } catch (error) {
        next(error);
    }
};

// @desc    Update voucher (Admin)
// @route   PUT /api/admin/vouchers/:id
// @access  Private/Admin
const updateVoucher = async (req, res, next) => {
    try {
        const { id } = req.params;
        const { voucherCode, discountValue, minOrderValue, quantity, startDate, endDate } = req.body;

        await pool.query(
            `UPDATE Voucher
       SET VoucherCode = ?, DiscountValue = ?, MinOrderValue = ?, Quantity = ?, StartDate = ?, EndDate = ?
       WHERE VoucherID = ?`,
            [voucherCode, discountValue, minOrderValue, quantity, startDate, endDate, id]
        );

        res.json({
            success: true,
            message: 'Cập nhật voucher thành công'
        });
    } catch (error) {
        next(error);
    }
};

// @desc    Delete voucher (Admin)
// @route   DELETE /api/admin/vouchers/:id
// @access  Private/Admin
const deleteVoucher = async (req, res, next) => {
    try {
        const { id } = req.params;

        await pool.query('DELETE FROM Voucher WHERE VoucherID = ?', [id]);

        res.json({
            success: true,
            message: 'Xóa voucher thành công'
        });
    } catch (error) {
        next(error);
    }
};

module.exports = {
    getAvailableVouchers,
    validateVoucher,
    getAllVouchers,
    createVoucher,
    updateVoucher,
    deleteVoucher
};
