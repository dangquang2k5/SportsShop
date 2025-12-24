const express = require('express');
const router = express.Router();
const { body } = require('express-validator');
const orderController = require('../controllers/order.controller');
const { verifyToken, isAdmin, optionalAuth } = require('../middleware/auth');
const validate = require('../middleware/validator');

// Validation rules
const createOrderValidation = [
    body('items').isArray({ min: 1 }).withMessage('Giỏ hàng không được trống'),
    body('items.*.productDetailId').isInt().withMessage('ProductDetailID không hợp lệ'),
    body('items.*.quantity').isInt({ min: 1 }).withMessage('Số lượng phải lớn hơn 0'),
    body('items.*.price').isFloat({ min: 0 }).withMessage('Giá không hợp lệ'),
    body('shippingAddress').trim().notEmpty().withMessage('Địa chỉ giao hàng không được để trống'),
    body('shippingCity').trim().notEmpty().withMessage('Thành phố không được để trống'),
    body('shippingPhone')
        .matches(/^[0-9]{10,11}$/)
        .withMessage('Số điện thoại phải có 10-11 chữ số'),
    body('guestName').optional().trim(),
    body('guestEmail').optional().isEmail().withMessage('Email không hợp lệ'),
    body('voucherCode').optional().trim(),
    body('notes').optional().trim()
];

// Public/User routes
router.post('/', optionalAuth, createOrderValidation, validate, orderController.createOrder);
router.get('/', verifyToken, orderController.getUserOrders);
router.get('/:id', verifyToken, orderController.getOrderById);

// Admin routes
router.get('/admin/all', verifyToken, isAdmin, orderController.getAllOrders);
router.put('/admin/:id/status', verifyToken, isAdmin, orderController.updateOrderStatus);

module.exports = router;
