// Global error handler middleware
const errorHandler = (err, req, res, next) => {
    console.error('Error:', err);

    // Default error
    let statusCode = err.statusCode || 500;
    let message = err.message || 'Đã xảy ra lỗi server';

    // MySQL errors
    if (err.code === 'ER_DUP_ENTRY') {
        statusCode = 400;
        if (err.message.includes('Email')) {
            message = 'Email đã được sử dụng';
        } else if (err.message.includes('Phone')) {
            message = 'Số điện thoại đã được sử dụng';
        } else {
            message = 'Dữ liệu đã tồn tại';
        }
    }

    if (err.code === 'ER_NO_REFERENCED_ROW_2') {
        statusCode = 400;
        message = 'Dữ liệu tham chiếu không tồn tại';
    }

    // Validation errors
    if (err.name === 'ValidationError') {
        statusCode = 400;
        message = err.message;
    }

    res.status(statusCode).json({
        success: false,
        message,
        ...(process.env.NODE_ENV === 'development' && { stack: err.stack })
    });
};

// 404 handler
const notFound = (req, res, next) => {
    res.status(404).json({
        success: false,
        message: 'Route không tồn tại'
    });
};

module.exports = {
    errorHandler,
    notFound
};
