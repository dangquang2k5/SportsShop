const express = require('express');
const router = express.Router();
const { body } = require('express-validator');
const categoryController = require('../controllers/category.controller');
const { verifyToken, isAdmin } = require('../middleware/auth');
const validate = require('../middleware/validator');

// Validation rules
const categoryValidation = [
    body('categoryName').trim().notEmpty().withMessage('Tên danh mục không được để trống'),
    body('categoryDescription').optional().trim()
];

const brandValidation = [
    body('brandName').trim().notEmpty().withMessage('Tên thương hiệu không được để trống'),
    body('brandDescription').optional().trim()
];

// Public routes
router.get('/categories', categoryController.getAllCategories);
router.get('/brands', categoryController.getAllBrands);

// Admin routes - Categories
router.post('/admin/categories', verifyToken, isAdmin, categoryValidation, validate, categoryController.createCategory);
router.put('/admin/categories/:id', verifyToken, isAdmin, categoryValidation, validate, categoryController.updateCategory);
router.delete('/admin/categories/:id', verifyToken, isAdmin, categoryController.deleteCategory);

// Admin routes - Brands
router.post('/admin/brands', verifyToken, isAdmin, brandValidation, validate, categoryController.createBrand);
router.put('/admin/brands/:id', verifyToken, isAdmin, brandValidation, validate, categoryController.updateBrand);
router.delete('/admin/brands/:id', verifyToken, isAdmin, categoryController.deleteBrand);

module.exports = router;
