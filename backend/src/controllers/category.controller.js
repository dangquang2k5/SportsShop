const { pool } = require('../config/database');

// @desc    Get all categories
// @route   GET /api/categories
// @access  Public
const getAllCategories = async (req, res, next) => {
    try {
        const [categories] = await pool.query(
            'SELECT * FROM Categories ORDER BY CategoryName'
        );

        res.json({
            success: true,
            data: categories
        });
    } catch (error) {
        next(error);
    }
};

// @desc    Get all brands
// @route   GET /api/brands
// @access  Public
const getAllBrands = async (req, res, next) => {
    try {
        const [brands] = await pool.query(
            'SELECT * FROM Brand ORDER BY BrandName'
        );

        res.json({
            success: true,
            data: brands
        });
    } catch (error) {
        next(error);
    }
};

// @desc    Create category (Admin)
// @route   POST /api/admin/categories
// @access  Private/Admin
const createCategory = async (req, res, next) => {
    try {
        const { categoryName, categoryDescription } = req.body;

        const [result] = await pool.query(
            'INSERT INTO Categories (CategoryName, CategoryDescription) VALUES (?, ?)',
            [categoryName, categoryDescription]
        );

        res.status(201).json({
            success: true,
            message: 'Tạo danh mục thành công',
            data: {
                categoryId: result.insertId
            }
        });
    } catch (error) {
        next(error);
    }
};

// @desc    Update category (Admin)
// @route   PUT /api/admin/categories/:id
// @access  Private/Admin
const updateCategory = async (req, res, next) => {
    try {
        const { id } = req.params;
        const { categoryName, categoryDescription } = req.body;

        await pool.query(
            'UPDATE Categories SET CategoryName = ?, CategoryDescription = ? WHERE CategoryID = ?',
            [categoryName, categoryDescription, id]
        );

        res.json({
            success: true,
            message: 'Cập nhật danh mục thành công'
        });
    } catch (error) {
        next(error);
    }
};

// @desc    Delete category (Admin)
// @route   DELETE /api/admin/categories/:id
// @access  Private/Admin
const deleteCategory = async (req, res, next) => {
    try {
        const { id } = req.params;

        await pool.query('DELETE FROM Categories WHERE CategoryID = ?', [id]);

        res.json({
            success: true,
            message: 'Xóa danh mục thành công'
        });
    } catch (error) {
        next(error);
    }
};

// @desc    Create brand (Admin)
// @route   POST /api/admin/brands
// @access  Private/Admin
const createBrand = async (req, res, next) => {
    try {
        const { brandName, brandDescription } = req.body;

        const [result] = await pool.query(
            'INSERT INTO Brand (BrandName, BrandDescription) VALUES (?, ?)',
            [brandName, brandDescription]
        );

        res.status(201).json({
            success: true,
            message: 'Tạo thương hiệu thành công',
            data: {
                brandId: result.insertId
            }
        });
    } catch (error) {
        next(error);
    }
};

// @desc    Update brand (Admin)
// @route   PUT /api/admin/brands/:id
// @access  Private/Admin
const updateBrand = async (req, res, next) => {
    try {
        const { id } = req.params;
        const { brandName, brandDescription } = req.body;

        await pool.query(
            'UPDATE Brand SET BrandName = ?, BrandDescription = ? WHERE BrandID = ?',
            [brandName, brandDescription, id]
        );

        res.json({
            success: true,
            message: 'Cập nhật thương hiệu thành công'
        });
    } catch (error) {
        next(error);
    }
};

// @desc    Delete brand (Admin)
// @route   DELETE /api/admin/brands/:id
// @access  Private/Admin
const deleteBrand = async (req, res, next) => {
    try {
        const { id } = req.params;

        await pool.query('DELETE FROM Brand WHERE BrandID = ?', [id]);

        res.json({
            success: true,
            message: 'Xóa thương hiệu thành công'
        });
    } catch (error) {
        next(error);
    }
};

module.exports = {
    getAllCategories,
    getAllBrands,
    createCategory,
    updateCategory,
    deleteCategory,
    createBrand,
    updateBrand,
    deleteBrand
};
