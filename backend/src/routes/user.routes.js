const express = require('express');
const router = express.Router();
const { body } = require('express-validator');
const userController = require('../controllers/user.controller');
const { verifyToken, isAdmin } = require('../middleware/auth');
const validate = require('../middleware/validator');

// Validation rules
const updateProfileValidation = [
    body('firstName').trim().notEmpty().withMessage('Họ không được để trống'),
    body('lastName').trim().notEmpty().withMessage('Tên không được để trống'),
    body('email').isEmail().withMessage('Email không hợp lệ'),
    body('phone')
        .matches(/^[0-9]{10,11}$/)
        .withMessage('Số điện thoại phải có 10-11 chữ số'),
    body('address').trim().notEmpty().withMessage('Địa chỉ không được để trống')
];

const changePasswordValidation = [
    body('currentPassword').notEmpty().withMessage('Mật khẩu hiện tại không được để trống'),
    body('newPassword')
        .isLength({ min: 6 })
        .withMessage('Mật khẩu mới phải có ít nhất 6 ký tự')
];

// User routes
router.get('/profile', verifyToken, userController.getProfile);
router.put('/profile', verifyToken, updateProfileValidation, validate, userController.updateProfile);
router.put('/password', verifyToken, changePasswordValidation, validate, userController.changePassword);

// Admin routes
router.get('/', verifyToken, isAdmin, userController.getAllUsers);
router.put('/:id/status', verifyToken, isAdmin, userController.updateUserStatus);
router.delete('/:id', verifyToken, isAdmin, userController.deleteUser);

module.exports = router;
