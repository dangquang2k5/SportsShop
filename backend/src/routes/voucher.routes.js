const express = require('express');
const router = express.Router();
const { body } = require('express-validator');
const voucherController = require('../controllers/voucher.controller');
const { verifyToken, isAdmin } = require('../middleware/auth');
const validate = require('../middleware/validator');

// Validation rules
const voucherValidation = [
    body('voucherCode').trim().notEmpty().withMessage('Mã voucher không được để trống'),
    body('discountValue').isFloat({ min: 0 }).withMessage('Giá trị giảm giá phải là số dương'),
    body('minOrderValue').isFloat({ min: 0 }).withMessage('Giá trị đơn hàng tối thiểu phải là số dương'),
    body('quantity').isInt({ min: 0 }).withMessage('Số lượng phải là số nguyên không âm'),
    body('startDate').isDate().withMessage('Ngày bắt đầu không hợp lệ'),
    body('endDate').isDate().withMessage('Ngày kết thúc không hợp lệ')
];

const validateVoucherValidation = [
    body('code').trim().notEmpty().withMessage('Mã voucher không được để trống'),
    body('orderAmount').optional().isFloat({ min: 0 }).withMessage('Giá trị đơn hàng không hợp lệ')
];

// Public routes
router.get('/', voucherController.getAvailableVouchers);
router.post('/validate', validateVoucherValidation, validate, voucherController.validateVoucher);

// Admin routes
router.get('/admin/all', verifyToken, isAdmin, voucherController.getAllVouchers);
router.post('/admin', verifyToken, isAdmin, voucherValidation, validate, voucherController.createVoucher);
router.put('/admin/:id', verifyToken, isAdmin, voucherValidation, validate, voucherController.updateVoucher);
router.delete('/admin/:id', verifyToken, isAdmin, voucherController.deleteVoucher);

module.exports = router;
