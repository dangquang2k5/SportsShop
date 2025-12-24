const { validationResult } = require('express-validator');

// Middleware to handle validation results
const validate = (req, res, next) => {
    const errors = validationResult(req);

    if (!errors.isEmpty()) {
        const errorMessages = errors.array().map(err => err.msg);
        return res.status(400).json({
            success: false,
            message: 'Dữ liệu không hợp lệ',
            errors: errorMessages
        });
    }

    next();
};

module.exports = validate;
