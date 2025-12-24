const express = require('express');
const router = express.Router();
const { body } = require('express-validator');
const productController = require('../controllers/product.controller');
const { verifyToken, isAdmin, optionalAuth } = require('../middleware/auth');
const validate = require('../middleware/validator');

// Validation rules
const productValidation = [
    body('productName').trim().notEmpty().withMessage('Tên sản phẩm không được để trống'),
    body('price').isFloat({ min: 0 }).withMessage('Giá phải là số dương'),
    body('description').optional(),
    body('mainImage').optional(),
    body('categoryId').optional().isInt().withMessage('CategoryID phải là số'),
    body('brandId').optional().isInt().withMessage('BrandID phải là số'),
    body('status').optional().isIn(['active', 'inactive', 'out_of_stock']).withMessage('Status không hợp lệ')
];

const variantValidation = [
    body('size').trim().notEmpty().withMessage('Size không được để trống'),
    body('color').trim().notEmpty().withMessage('Color không được để trống'),
    body('quantity').isInt({ min: 0 }).withMessage('Quantity phải là số nguyên không âm'),
    body('image').optional()
];

// Public routes
router.get('/', productController.getProducts);
router.get('/:id', productController.getProductById);
router.get('/:id/variants', productController.getProductVariants);

// Admin routes
router.post('/', verifyToken, isAdmin, productValidation, validate, productController.createProduct);
router.put('/:id', verifyToken, isAdmin, productValidation, validate, productController.updateProduct);
router.delete('/:id', verifyToken, isAdmin, productController.deleteProduct);
router.post('/:id/variants', verifyToken, isAdmin, variantValidation, validate, productController.createOrUpdateVariant);
router.delete('/variants/:variantId', verifyToken, isAdmin, productController.deleteVariant);

module.exports = router;
