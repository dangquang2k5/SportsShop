const express = require('express');
const cors = require('cors');
const helmet = require('helmet');
const morgan = require('morgan');
require('dotenv').config();

const { testConnection } = require('./config/database');
const { errorHandler, notFound } = require('./middleware/errorHandler');

// Import routes
const authRoutes = require('./routes/auth.routes');
const userRoutes = require('./routes/user.routes');
const productRoutes = require('./routes/product.routes');
const orderRoutes = require('./routes/order.routes');
const voucherRoutes = require('./routes/voucher.routes');
const categoryRoutes = require('./routes/category.routes');

// Initialize app
const app = express();
const PORT = process.env.PORT || 3000;

// Middleware
app.use(helmet()); // Security headers
app.use(cors({
    origin: process.env.FRONTEND_URL || 'http://localhost:8081',
    credentials: true
}));
app.use(morgan('dev')); // Logging
app.use(express.json()); // Parse JSON bodies
app.use(express.urlencoded({ extended: true })); // Parse URL-encoded bodies

// Health check endpoint
app.get('/health', (req, res) => {
    res.json({
        success: true,
        message: 'Backend server is running',
        timestamp: new Date().toISOString()
    });
});

// API Routes
app.use('/api/auth', authRoutes);
app.use('/api/users', userRoutes);
app.use('/api/products', productRoutes);
app.use('/api/orders', orderRoutes);
app.use('/api/vouchers', voucherRoutes);
app.use('/api', categoryRoutes); // Categories and Brands

// 404 handler
app.use(notFound);

// Error handler (must be last)
app.use(errorHandler);

// Start server
const startServer = async () => {
    try {
        // Test database connection
        const dbConnected = await testConnection();

        if (!dbConnected) {
            console.error('⚠️  Failed to connect to database. Server will not start.');
            process.exit(1);
        }

        // Start listening
        app.listen(PORT, () => {
            console.log('');
            console.log('╔═══════════════════════════════════════════════════════╗');
            console.log('║                                                       ║');
            console.log('║       🏃 SportShop Backend Server Running 🏃         ║');
            console.log('║                                                       ║');
            console.log('╚═══════════════════════════════════════════════════════╝');
            console.log('');
            console.log(`🚀 Server:      http://localhost:${PORT}`);
            console.log(`🏥 Health:      http://localhost:${PORT}/health`);
            console.log(`📡 API:         http://localhost:${PORT}/api`);
            console.log(`🌍 Environment: ${process.env.NODE_ENV || 'development'}`);
            console.log('');
            console.log('Available API endpoints:');
            console.log('  - POST   /api/auth/register');
            console.log('  - POST   /api/auth/login');
            console.log('  - GET    /api/auth/me');
            console.log('  - GET    /api/products');
            console.log('  - GET    /api/products/:id');
            console.log('  - POST   /api/orders');
            console.log('  - GET    /api/vouchers');
            console.log('  - GET    /api/categories');
            console.log('  - GET    /api/brands');
            console.log('');
            console.log('Press Ctrl+C to stop the server');
            console.log('');
        });
    } catch (error) {
        console.error('❌ Failed to start server:', error);
        process.exit(1);
    }
};

// Handle unhandled promise rejections
process.on('unhandledRejection', (err) => {
    console.error('❌ Unhandled Promise Rejection:', err);
    process.exit(1);
});

// Handle uncaught exceptions
process.on('uncaughtException', (err) => {
    console.error('❌ Uncaught Exception:', err);
    process.exit(1);
});

// Start the server
startServer();

module.exports = app;
