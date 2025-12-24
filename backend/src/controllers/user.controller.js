const bcrypt = require('bcryptjs');
const { pool } = require('../config/database');

// @desc    Get user profile
// @route   GET /api/users/profile
// @access  Private
const getProfile = async (req, res, next) => {
    try {
        const [users] = await pool.query(
            'SELECT UserID, FirstName, LastName, Email, Phone, Address, Role, created_at FROM Users WHERE UserID = ?',
            [req.user.UserID]
        );

        if (users.length === 0) {
            return res.status(404).json({
                success: false,
                message: 'Không tìm thấy user'
            });
        }

        res.json({
            success: true,
            data: users[0]
        });
    } catch (error) {
        next(error);
    }
};

// @desc    Update user profile
// @route   PUT /api/users/profile
// @access  Private
const updateProfile = async (req, res, next) => {
    try {
        const { firstName, lastName, email, phone, address } = req.body;

        await pool.query(
            'UPDATE Users SET FirstName = ?, LastName = ?, Email = ?, Phone = ?, Address = ? WHERE UserID = ?',
            [firstName, lastName, email, phone, address, req.user.UserID]
        );

        res.json({
            success: true,
            message: 'Cập nhật thông tin thành công'
        });
    } catch (error) {
        next(error);
    }
};

// @desc    Change password
// @route   PUT /api/users/password
// @access  Private
const changePassword = async (req, res, next) => {
    try {
        const { currentPassword, newPassword } = req.body;

        // Get current password
        const [users] = await pool.query(
            'SELECT Password FROM Users WHERE UserID = ?',
            [req.user.UserID]
        );

        if (users.length === 0) {
            return res.status(404).json({
                success: false,
                message: 'Không tìm thấy user'
            });
        }

        // Verify current password
        const isPasswordValid = await bcrypt.compare(currentPassword, users[0].Password);

        if (!isPasswordValid) {
            return res.status(401).json({
                success: false,
                message: 'Mật khẩu hiện tại không đúng'
            });
        }

        // Hash new password
        const hashedPassword = await bcrypt.hash(newPassword, 10);

        // Update password
        await pool.query(
            'UPDATE Users SET Password = ? WHERE UserID = ?',
            [hashedPassword, req.user.UserID]
        );

        res.json({
            success: true,
            message: 'Đổi mật khẩu thành công'
        });
    } catch (error) {
        next(error);
    }
};

// @desc    Get all users (Admin)
// @route   GET /api/users
// @access  Private/Admin
const getAllUsers = async (req, res, next) => {
    try {
        const page = parseInt(req.query.page) || 1;
        const limit = parseInt(req.query.limit) || 20;
        const offset = (page - 1) * limit;
        const search = req.query.search || '';

        let query = 'SELECT UserID, FirstName, LastName, Email, Phone, Address, Role, Status, created_at FROM Users';
        let countQuery = 'SELECT COUNT(*) as total FROM Users';
        const params = [];

        if (search) {
            query += ' WHERE FirstName LIKE ? OR LastName LIKE ? OR Email LIKE ? OR Phone LIKE ?';
            countQuery += ' WHERE FirstName LIKE ? OR LastName LIKE ? OR Email LIKE ? OR Phone LIKE ?';
            const searchParam = `%${search}%`;
            params.push(searchParam, searchParam, searchParam, searchParam);
        }

        query += ' ORDER BY created_at DESC LIMIT ? OFFSET ?';

        const [users] = await pool.query(query, [...params, limit, offset]);
        const [countResult] = await pool.query(countQuery, params);

        res.json({
            success: true,
            data: {
                users,
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

// @desc    Update user status (Admin)
// @route   PUT /api/users/:id/status
// @access  Private/Admin
const updateUserStatus = async (req, res, next) => {
    try {
        const { id } = req.params;
        const { status } = req.body;

        // Prevent admin from disabling themselves
        if (parseInt(id) === req.user.UserID) {
            return res.status(400).json({
                success: false,
                message: 'Không thể thay đổi trạng thái của chính mình'
            });
        }

        await pool.query(
            'UPDATE Users SET Status = ? WHERE UserID = ?',
            [status ? 1 : 0, id]
        );

        res.json({
            success: true,
            message: status ? 'Đã mở khóa tài khoản' : 'Đã khóa tài khoản'
        });
    } catch (error) {
        next(error);
    }
};

// @desc    Delete user (Admin)
// @route   DELETE /api/users/:id
// @access  Private/Admin
const deleteUser = async (req, res, next) => {
    try {
        const { id } = req.params;

        // Prevent admin from deleting themselves
        if (parseInt(id) === req.user.UserID) {
            return res.status(400).json({
                success: false,
                message: 'Không thể xóa tài khoản của chính mình'
            });
        }

        await pool.query('DELETE FROM Users WHERE UserID = ?', [id]);

        res.json({
            success: true,
            message: 'Đã xóa user thành công'
        });
    } catch (error) {
        next(error);
    }
};

module.exports = {
    getProfile,
    updateProfile,
    changePassword,
    getAllUsers,
    updateUserStatus,
    deleteUser
};
