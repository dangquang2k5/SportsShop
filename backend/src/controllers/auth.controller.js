const bcrypt = require('bcryptjs');
const jwt = require('jsonwebtoken');
const { pool } = require('../config/database');

// Generate JWT token
const generateToken = (userId) => {
    return jwt.sign(
        { userId },
        process.env.JWT_SECRET,
        { expiresIn: process.env.JWT_EXPIRES_IN || '24h' }
    );
};

// @desc    Register new user
// @route   POST /api/auth/register
// @access  Public
const register = async (req, res, next) => {
    try {
        const { firstName, lastName, email, phone, password, address } = req.body;

        // Hash password
        const hashedPassword = await bcrypt.hash(password, 10);

        // Insert user
        const [result] = await pool.query(
            `INSERT INTO Users (FirstName, LastName, Email, Phone, Password, Address, Role, Status) 
       VALUES (?, ?, ?, ?, ?, ?, 'customer', 1)`,
            [firstName, lastName, email, phone, hashedPassword, address]
        );

        const userId = result.insertId;

        // Generate token
        const token = generateToken(userId);

        res.status(201).json({
            success: true,
            message: 'Đăng ký thành công',
            data: {
                userId,
                firstName,
                lastName,
                email,
                phone,
                role: 'customer',
                token
            }
        });
    } catch (error) {
        next(error);
    }
};

// @desc    Login user
// @route   POST /api/auth/login
// @access  Public
const login = async (req, res, next) => {
    try {
        const { phone, password } = req.body;

        // Find user by phone
        const [users] = await pool.query(
            'SELECT * FROM Users WHERE Phone = ?',
            [phone]
        );

        if (users.length === 0) {
            return res.status(401).json({
                success: false,
                message: 'Số điện thoại hoặc mật khẩu không đúng'
            });
        }

        const user = users[0];

        // Check password
        const isPasswordValid = await bcrypt.compare(password, user.Password);

        if (!isPasswordValid) {
            return res.status(401).json({
                success: false,
                message: 'Số điện thoại hoặc mật khẩu không đúng'
            });
        }

        // Check if account is active
        if (user.Status !== 1) {
            return res.status(403).json({
                success: false,
                message: 'Tài khoản của bạn đã bị khóa. Vui lòng liên hệ quản trị viên.'
            });
        }

        // Generate token
        const token = generateToken(user.UserID);

        res.json({
            success: true,
            message: 'Đăng nhập thành công',
            data: {
                userId: user.UserID,
                firstName: user.FirstName,
                lastName: user.LastName,
                email: user.Email,
                phone: user.Phone,
                role: user.Role,
                token
            }
        });
    } catch (error) {
        next(error);
    }
};

// @desc    Get current user
// @route   GET /api/auth/me
// @access  Private
const getMe = async (req, res, next) => {
    try {
        res.json({
            success: true,
            data: {
                userId: req.user.UserID,
                firstName: req.user.FirstName,
                lastName: req.user.LastName,
                email: req.user.Email,
                phone: req.user.Phone,
                role: req.user.Role
            }
        });
    } catch (error) {
        next(error);
    }
};

module.exports = {
    register,
    login,
    getMe
};
