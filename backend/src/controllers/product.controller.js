const { pool } = require('../config/database');

// @desc    Get all products with filters
// @route   GET /api/products
// @access  Public
const getProducts = async (req, res, next) => {
    try {
        const page = parseInt(req.query.page) || 1;
        const limit = parseInt(req.query.limit) || 12;
        const offset = (page - 1) * limit;
        const search = req.query.search || '';
        const categoryId = req.query.categoryId;
        const brandId = req.query.brandId;
        const minPrice = req.query.minPrice;
        const maxPrice = req.query.maxPrice;
        const sortBy = req.query.sortBy || 'created_at';
        const sortOrder = req.query.sortOrder || 'DESC';

        let query = `
      SELECT p.*, c.CategoryName, b.BrandName,
             (SELECT SUM(pd.Quantity) FROM ProductDetail pd WHERE pd.ProductID = p.ProductID) as TotalStock
      FROM Product p
      LEFT JOIN Categories c ON p.CategoryID = c.CategoryID
      LEFT JOIN Brand b ON p.BrandID = b.BrandID
      WHERE 1=1
    `;
        let countQuery = 'SELECT COUNT(*) as total FROM Product p WHERE 1=1';
        const params = [];
        const countParams = [];

        if (search) {
            query += ' AND p.ProductName LIKE ?';
            countQuery += ' AND p.ProductName LIKE ?';
            const searchParam = `%${search}%`;
            params.push(searchParam);
            countParams.push(searchParam);
        }

        if (categoryId) {
            query += ' AND p.CategoryID = ?';
            countQuery += ' AND p.CategoryID = ?';
            params.push(categoryId);
            countParams.push(categoryId);
        }

        if (brandId) {
            query += ' AND p.BrandID = ?';
            countQuery += ' AND p.BrandID = ?';
            params.push(brandId);
            countParams.push(brandId);
        }

        if (minPrice) {
            query += ' AND p.Price >= ?';
            countQuery += ' AND p.Price >= ?';
            params.push(minPrice);
            countParams.push(minPrice);
        }

        if (maxPrice) {
            query += ' AND p.Price <= ?';
            countQuery += ' AND p.Price <= ?';
            params.push(maxPrice);
            countParams.push(maxPrice);
        }

        // Validate sortBy to prevent SQL injection
        const allowedSortFields = ['ProductName', 'Price', 'created_at', 'RatingAvg'];
        const sortField = allowedSortFields.includes(sortBy) ? sortBy : 'created_at';
        const order = sortOrder.toUpperCase() === 'ASC' ? 'ASC' : 'DESC';

        query += ` ORDER BY p.${sortField} ${order} LIMIT ? OFFSET ?`;

        const [products] = await pool.query(query, [...params, limit, offset]);
        const [countResult] = await pool.query(countQuery, countParams);

        res.json({
            success: true,
            data: {
                products,
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

// @desc    Get product by ID
// @route   GET /api/products/:id
// @access  Public
const getProductById = async (req, res, next) => {
    try {
        const { id } = req.params;

        const [products] = await pool.query(
            `SELECT p.*, c.CategoryName, b.BrandName
       FROM Product p
       LEFT JOIN Categories c ON p.CategoryID = c.CategoryID
       LEFT JOIN Brand b ON p.BrandID = b.BrandID
       WHERE p.ProductID = ?`,
            [id]
        );

        if (products.length === 0) {
            return res.status(404).json({
                success: false,
                message: 'Không tìm thấy sản phẩm'
            });
        }

        res.json({
            success: true,
            data: products[0]
        });
    } catch (error) {
        next(error);
    }
};

// @desc    Get product variants
// @route   GET /api/products/:id/variants
// @access  Public
const getProductVariants = async (req, res, next) => {
    try {
        const { id } = req.params;

        const [variants] = await pool.query(
            'SELECT * FROM ProductDetail WHERE ProductID = ? ORDER BY Size, Color',
            [id]
        );

        res.json({
            success: true,
            data: variants
        });
    } catch (error) {
        next(error);
    }
};

// @desc    Create product (Admin)
// @route   POST /api/products
// @access  Private/Admin
const createProduct = async (req, res, next) => {
    try {
        const { productName, price, description, mainImage, categoryId, brandId, status } = req.body;

        const [result] = await pool.query(
            `INSERT INTO Product (ProductName, Price, Description, MainImage, CategoryID, BrandID, Status)
       VALUES (?, ?, ?, ?, ?, ?, ?)`,
            [productName, price, description, mainImage, categoryId, brandId, status || 'active']
        );

        res.status(201).json({
            success: true,
            message: 'Tạo sản phẩm thành công',
            data: {
                productId: result.insertId
            }
        });
    } catch (error) {
        next(error);
    }
};

// @desc    Update product (Admin)
// @route   PUT /api/products/:id
// @access  Private/Admin
const updateProduct = async (req, res, next) => {
    try {
        const { id } = req.params;
        const { productName, price, description, mainImage, categoryId, brandId, status } = req.body;

        await pool.query(
            `UPDATE Product 
       SET ProductName = ?, Price = ?, Description = ?, MainImage = ?, 
           CategoryID = ?, BrandID = ?, Status = ?
       WHERE ProductID = ?`,
            [productName, price, description, mainImage, categoryId, brandId, status, id]
        );

        res.json({
            success: true,
            message: 'Cập nhật sản phẩm thành công'
        });
    } catch (error) {
        next(error);
    }
};

// @desc    Delete product (Admin)
// @route   DELETE /api/products/:id
// @access  Private/Admin
const deleteProduct = async (req, res, next) => {
    try {
        const { id } = req.params;

        await pool.query('DELETE FROM Product WHERE ProductID = ?', [id]);

        res.json({
            success: true,
            message: 'Xóa sản phẩm thành công'
        });
    } catch (error) {
        next(error);
    }
};

// @desc    Create/Update product variant (Admin)
// @route   POST /api/products/:id/variants
// @access  Private/Admin
const createOrUpdateVariant = async (req, res, next) => {
    try {
        const { id } = req.params;
        const { size, color, quantity, image } = req.body;

        // Check if variant exists
        const [existing] = await pool.query(
            'SELECT * FROM ProductDetail WHERE ProductID = ? AND Size = ? AND Color = ?',
            [id, size, color]
        );

        if (existing.length > 0) {
            // Update existing variant
            await pool.query(
                'UPDATE ProductDetail SET Quantity = ?, Image = ? WHERE ProductDetailID = ?',
                [quantity, image, existing[0].ProductDetailID]
            );
        } else {
            // Create new variant
            await pool.query(
                'INSERT INTO ProductDetail (ProductID, Size, Color, Quantity, Image) VALUES (?, ?, ?, ?, ?)',
                [id, size, color, quantity, image]
            );
        }

        res.json({
            success: true,
            message: 'Cập nhật variant thành công'
        });
    } catch (error) {
        next(error);
    }
};

// @desc    Delete product variant (Admin)
// @route   DELETE /api/products/variants/:variantId
// @access  Private/Admin
const deleteVariant = async (req, res, next) => {
    try {
        const { variantId } = req.params;

        await pool.query('DELETE FROM ProductDetail WHERE ProductDetailID = ?', [variantId]);

        res.json({
            success: true,
            message: 'Xóa variant thành công'
        });
    } catch (error) {
        next(error);
    }
};

module.exports = {
    getProducts,
    getProductById,
    getProductVariants,
    createProduct,
    updateProduct,
    deleteProduct,
    createOrUpdateVariant,
    deleteVariant
};
