const express = require('express');
const router = express.Router();
const { body } = require('express-validator');
const authController = require('../controllers/auth.controller');
const { verifyToken } = require('../middleware/auth');
const validate = require('../middleware/validator');

// Validation rules
const registerValidation = [
    body('firstName').trim().notEmpty().withMessage('Họ không được để trống'),
    body('lastName').trim().notEmpty().withMessage('Tên không được để trống'),
    body('email').isEmail().withMessage('Email không hợp lệ'),
    body('phone')
        .matches(/^[0-9]{10,11}$/)
        .withMessage('Số điện thoại phải có 10-11 chữ số'),
    body('password')
        .isLength({ min: 6 })
        .withMessage('Mật khẩu phải có ít nhất 6 ký tự'),
    body('address').trim().notEmpty().withMessage('Địa chỉ không được để trống')
];

const loginValidation = [
    body('phone')
        .matches(/^[0-9]{10,11}$/)
        .withMessage('Số điện thoại phải có 10-11 chữ số'),
    body('password').notEmpty().withMessage('Mật khẩu không được để trống')
];

// Routes
router.post('/register', registerValidation, validate, authController.register);
router.post('/login', loginValidation, validate, authController.login);
router.get('/me', verifyToken, authController.getMe);

module.exports = router;
