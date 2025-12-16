<?php
/**
 * ADMIN_SIDEBAR.PHP - Sidebar chung cho tất cả trang admin
 * Include file này ở tất cả trang admin thay vì lặp lại code
 */

// Xác định trang hiện tại để highlight menu item
$currentPage = basename($_SERVER['PHP_SELF']);
?>

<!-- Admin Sidebar -->
<div class="fixed top-20 left-0 h-screen w-64 bg-white dark:bg-sport-navy border-r border-gray-200 dark:border-gray-800 z-30 transition-transform duration-300" id="admin-sidebar">
    <div class="p-6 space-y-2">
        <h3 class="text-lg font-black text-gray-900 dark:text-white mb-6 flex items-center">
            <i class="fas fa-shield-alt mr-3 text-sport-neon"></i>
            Quản trị viên
        </h3>
        
        <!-- Dashboard -->
        <a href="admin_dashboard.php" class="flex items-center space-x-3 px-4 py-3 rounded-xl <?php echo $currentPage === 'admin_dashboard.php' ? 'bg-gradient-to-r from-sport-neon to-blue-600 text-white font-semibold' : 'glass hover:bg-gray-100 dark:hover:bg-sport-blue text-gray-700 dark:text-gray-300'; ?> transition-all duration-300">
            <i class="fas fa-chart-line w-5"></i>
            <span>Dashboard</span>
        </a>
        
        <!-- Sản phẩm -->
        <a href="admin_products.php" class="flex items-center space-x-3 px-4 py-3 rounded-xl <?php echo $currentPage === 'admin_products.php' ? 'bg-gradient-to-r from-sport-neon to-blue-600 text-white font-semibold' : 'glass hover:bg-gray-100 dark:hover:bg-sport-blue text-gray-700 dark:text-gray-300'; ?> transition-all duration-300">
            <i class="fas fa-box w-5"></i>
            <span>Sản phẩm</span>
        </a>
        
        <!-- Đơn hàng -->
        <a href="admin_orders.php" class="flex items-center space-x-3 px-4 py-3 rounded-xl <?php echo $currentPage === 'admin_orders.php' ? 'bg-gradient-to-r from-sport-neon to-blue-600 text-white font-semibold' : 'glass hover:bg-gray-100 dark:hover:bg-sport-blue text-gray-700 dark:text-gray-300'; ?> transition-all duration-300">
            <i class="fas fa-shopping-cart w-5"></i>
            <span>Đơn hàng</span>
        </a>
        
        <!-- Người dùng -->
        <a href="admin_users.php" class="flex items-center space-x-3 px-4 py-3 rounded-xl <?php echo $currentPage === 'admin_users.php' ? 'bg-gradient-to-r from-sport-neon to-blue-600 text-white font-semibold' : 'glass hover:bg-gray-100 dark:hover:bg-sport-blue text-gray-700 dark:text-gray-300'; ?> transition-all duration-300">
            <i class="fas fa-users w-5"></i>
            <span>Người dùng</span>
        </a>
        
        <!-- Danh mục -->
        <a href="admin_categories.php" class="flex items-center space-x-3 px-4 py-3 rounded-xl <?php echo $currentPage === 'admin_categories.php' ? 'bg-gradient-to-r from-sport-neon to-blue-600 text-white font-semibold' : 'glass hover:bg-gray-100 dark:hover:bg-sport-blue text-gray-700 dark:text-gray-300'; ?> transition-all duration-300">
            <i class="fas fa-th-large w-5"></i>
            <span>Danh mục</span>
        </a>
        
        <!-- Thương hiệu -->
        <a href="admin_brands.php" class="flex items-center space-x-3 px-4 py-3 rounded-xl <?php echo $currentPage === 'admin_brands.php' ? 'bg-gradient-to-r from-sport-neon to-blue-600 text-white font-semibold' : 'glass hover:bg-gray-100 dark:hover:bg-sport-blue text-gray-700 dark:text-gray-300'; ?> transition-all duration-300">
            <i class="fas fa-tag w-5"></i>
            <span>Thương hiệu</span>
        </a>
        
        <!-- Mã giảm giá -->
        <a href="admin_coupons.php" class="flex items-center space-x-3 px-4 py-3 rounded-xl <?php echo $currentPage === 'admin_coupons.php' ? 'bg-gradient-to-r from-sport-neon to-blue-600 text-white font-semibold' : 'glass hover:bg-gray-100 dark:hover:bg-sport-blue text-gray-700 dark:text-gray-300'; ?> transition-all duration-300">
            <i class="fas fa-ticket-alt w-5"></i>
            <span>Mã giảm giá</span>
        </a>
        
        <!-- Đánh giá -->
        <a href="admin_reviews.php" class="flex items-center space-x-3 px-4 py-3 rounded-xl <?php echo $currentPage === 'admin_reviews.php' ? 'bg-gradient-to-r from-sport-neon to-blue-600 text-white font-semibold' : 'glass hover:bg-gray-100 dark:hover:bg-sport-blue text-gray-700 dark:text-gray-300'; ?> transition-all duration-300">
            <i class="fas fa-star w-5"></i>
            <span>Đánh giá</span>
        </a>
        
        <!-- Divider -->
        <div class="pt-4 mt-4 border-t border-gray-200 dark:border-gray-700">
            <!-- Về trang chủ -->
            <a href="../index.php" class="flex items-center space-x-3 px-4 py-3 rounded-xl glass hover:bg-gray-100 dark:hover:bg-sport-blue text-gray-700 dark:text-gray-300 transition-all duration-300">
                <i class="fas fa-home w-5"></i>
                <span>Về trang chủ</span>
            </a>
        </div>
    </div>
</div>
