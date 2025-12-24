<?php
require_once '../config.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('login.php');
}

<<<<<<< HEAD
// Get dashboard statistics from API
try {
    // Get orders for statistics
    $ordersResponse = makeApiRequest('/orders');
    $orders = $ordersResponse['success'] ? $ordersResponse['data']['orders'] ?? [] : [];
    
    // Calculate statistics
    $totalOrders = count($orders);
    $totalRevenue = 0;
    $recentOrders = array_slice($orders, 0, 8);
    
    foreach ($orders as $order) {
        if (in_array($order['Status'], ['delivered', 'shipped'])) {
            $totalRevenue += $order['TotalAmount'] ?? 0;
        }
    }
    
    // Get products count
    $productsResponse = makeApiRequest('/products');
    $products = $productsResponse['success'] ? $productsResponse['data']['products'] ?? [] : [];
    $totalProducts = count($products);
    
    // Get users count (simplified)
    $totalUsers = 0; // This would need a users endpoint
    
    // Get low stock products
    $lowStockProducts = [];
    foreach ($products as $product) {
        $totalStock = $product['TotalStock'] ?? 0;
        if ($totalStock < 10 || $totalStock === null) {
            $lowStockProducts[] = $product;
            if (count($lowStockProducts) >= 5) break;
        }
    }
    
} catch (Exception $e) {
    // Set default values if API fails
    $totalUsers = 0;
    $totalProducts = 0;
    $totalOrders = 0;
    $totalRevenue = 0;
    $recentOrders = [];
    $lowStockProducts = [];
}

// Get order status distribution for pie chart
try {
    $statusCounts = [];
    foreach ($orders as $order) {
        $status = $order['Status'] ?? 'unknown';
        $statusCounts[$status] = ($statusCounts[$status] ?? 0) + 1;
    }
} catch (Exception $e) {
    $statusCounts = [];
}
=======
$db = Database::getInstance()->getConnection();

// Get statistics
$stmt = $db->query("SELECT COUNT(*) as total FROM Users WHERE Status = 1");
$totalUsers = $stmt->fetch()['total'];

$stmt = $db->query("SELECT COUNT(*) as total FROM Product");
$totalProducts = $stmt->fetch()['total'];

$stmt = $db->query("SELECT COUNT(*) as total FROM Orders");
$totalOrders = $stmt->fetch()['total'];

$stmt = $db->query("SELECT SUM(TotalAmount) as revenue FROM Orders WHERE Status IN ('delivered', 'shipped')");
$totalRevenue = $stmt->fetch()['revenue'] ?? 0;

// Get recent orders
$stmt = $db->prepare("
    SELECT o.*, 
           COALESCE(CONCAT(u.FirstName, ' ', u.LastName), o.GuestName, 'Khách vãng lai') as FullName,
           COALESCE(u.Email, o.GuestEmail) as Email
    FROM Orders o
    LEFT JOIN Users u ON o.UserID = u.UserID
    ORDER BY o.created_at DESC
    LIMIT 8
");
$stmt->execute();
$recentOrders = $stmt->fetchAll();

// Get low stock products
$stmt = $db->prepare("
    SELECT p.ProductID, p.ProductName, p.MainImage, SUM(pd.Quantity) as TotalStock
    FROM Product p
    LEFT JOIN ProductDetail pd ON p.ProductID = pd.ProductID
    GROUP BY p.ProductID
    HAVING TotalStock < 10 OR TotalStock IS NULL
    ORDER BY TotalStock ASC
    LIMIT 5
");
$stmt->execute();
$lowStockProducts = $stmt->fetchAll();

// Get order status distribution for pie chart
$stmt = $db->query("
    SELECT Status, COUNT(*) as count 
    FROM Orders 
    GROUP BY Status
");
$orderStatusData = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get top selling products for pie chart
$stmt = $db->prepare("
    SELECT p.ProductName, SUM(od.Quantity) as total_sold
    FROM OrderDetails od
    JOIN ProductDetail pd ON od.ProductDetailID = pd.ProductDetailID
    JOIN Product p ON pd.ProductID = p.ProductID
    JOIN Orders o ON od.OrderID = o.OrderID
    WHERE o.Status IN ('delivered', 'shipped')
    GROUP BY p.ProductID
    ORDER BY total_sold DESC
    LIMIT 5
");
$stmt->execute();
$topProductsData = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get revenue by category for pie chart
$stmt = $db->query("
    SELECT c.CategoryName, SUM(od.Price * od.Quantity) as revenue
    FROM OrderDetails od
    JOIN ProductDetail pd ON od.ProductDetailID = pd.ProductDetailID
    JOIN Product p ON pd.ProductID = p.ProductID
    JOIN Categories c ON p.CategoryID = c.CategoryID
    JOIN Orders o ON od.OrderID = o.OrderID
    WHERE o.Status IN ('delivered', 'shipped')
    GROUP BY c.CategoryID
    ORDER BY revenue DESC
    LIMIT 6
");
$categoryRevenueData = $stmt->fetchAll(PDO::FETCH_ASSOC);
>>>>>>> 3d6d58ed3875cc3c551e3fe1991339ab7637c345

$pageTitle = "Admin Dashboard";
$isInPages = true;
include '../includes/layout_header.php';
?>

<!-- Admin Sidebar -->
<div class="fixed top-20 left-0 h-screen w-64 bg-white dark:bg-sport-navy border-r border-gray-200 dark:border-gray-800 z-30 transition-transform duration-300" id="admin-sidebar">
    <div class="p-6 space-y-2">
        <h3 class="text-lg font-black text-gray-900 dark:text-white mb-6 flex items-center">
            <i class="fas fa-shield-alt mr-3 text-sport-neon"></i>
            Quản trị viên
        </h3>
        
        <a href="admin_dashboard.php" class="flex items-center space-x-3 px-4 py-3 rounded-xl bg-gradient-to-r from-sport-neon to-blue-600 text-white font-semibold">
            <i class="fas fa-chart-line w-5"></i>
            <span>Dashboard</span>
        </a>
        
        <a href="admin_products.php" class="flex items-center space-x-3 px-4 py-3 rounded-xl glass hover:bg-gray-100 dark:hover:bg-sport-blue text-gray-700 dark:text-gray-300 transition-all duration-300">
            <i class="fas fa-box w-5"></i>
            <span>Sản phẩm</span>
        </a>
        
        <a href="admin_orders.php" class="flex items-center space-x-3 px-4 py-3 rounded-xl glass hover:bg-gray-100 dark:hover:bg-sport-blue text-gray-700 dark:text-gray-300 transition-all duration-300">
            <i class="fas fa-shopping-cart w-5"></i>
            <span>Đơn hàng</span>
        </a>
        
        <a href="admin_users.php" class="flex items-center space-x-3 px-4 py-3 rounded-xl glass hover:bg-gray-100 dark:hover:bg-sport-blue text-gray-700 dark:text-gray-300 transition-all duration-300">
            <i class="fas fa-users w-5"></i>
            <span>Người dùng</span>
        </a>
        
        <a href="admin_categories.php" class="flex items-center space-x-3 px-4 py-3 rounded-xl glass hover:bg-gray-100 dark:hover:bg-sport-blue text-gray-700 dark:text-gray-300 transition-all duration-300">
            <i class="fas fa-th-large w-5"></i>
            <span>Danh mục</span>
        </a>
        
        <a href="admin_brands.php" class="flex items-center space-x-3 px-4 py-3 rounded-xl glass hover:bg-gray-100 dark:hover:bg-sport-blue text-gray-700 dark:text-gray-300 transition-all duration-300">
            <i class="fas fa-tag w-5"></i>
            <span>Thương hiệu</span>
        </a>
        
        <a href="admin_coupons.php" class="flex items-center space-x-3 px-4 py-3 rounded-xl glass hover:bg-gray-100 dark:hover:bg-sport-blue text-gray-700 dark:text-gray-300 transition-all duration-300">
            <i class="fas fa-ticket-alt w-5"></i>
            <span>Mã giảm giá</span>
        </a>
        
        <a href="admin_reviews.php" class="flex items-center space-x-3 px-4 py-3 rounded-xl glass hover:bg-gray-100 dark:hover:bg-sport-blue text-gray-700 dark:text-gray-300 transition-all duration-300">
            <i class="fas fa-star w-5"></i>
            <span>Đánh giá</span>
        </a>
        
        <div class="pt-4 mt-4 border-t border-gray-200 dark:border-gray-700">
            <a href="../index.php" class="flex items-center space-x-3 px-4 py-3 rounded-xl glass hover:bg-gray-100 dark:hover:bg-sport-blue text-gray-700 dark:text-gray-300 transition-all duration-300">
                <i class="fas fa-home w-5"></i>
                <span>Về trang chủ</span>
            </a>
        </div>
    </div>
</div>

<!-- Main Content -->
<div class="ml-0 lg:ml-64 transition-all duration-300">
    <!-- Page Header -->
    <section class="relative py-12 bg-gradient-to-br from-sport-navy to-sport-blue overflow-hidden">
        <div class="absolute inset-0">
            <div class="absolute top-0 right-0 w-96 h-96 bg-sport-neon opacity-10 rounded-full blur-3xl"></div>
        </div>
        
        <div class="container mx-auto px-4 lg:px-8 relative z-10">
            <div class="flex items-center justify-between">
                <div class="animate-fade-in">
                    <h1 class="text-4xl font-black text-white mb-2">
                        Dashboard
                    </h1>
                    <p class="text-gray-300">
                        Xin chào, <span class="text-sport-neon"><?php echo $_SESSION['full_name'] ?? 'Admin'; ?></span>
                    </p>
                </div>
                <div class="flex items-center gap-4">
                    <a href="generate_report.php" target="_blank" class="btn-neon inline-flex items-center px-6 py-3 relative z-10 hover:scale-105 transition-transform duration-300">
                        <i class="fas fa-file-pdf mr-2"></i>
                        Xuất báo cáo PDF
                    </a>
                    <div class="text-right text-white">
                        <p class="text-sm text-gray-300">Hôm nay</p>
                        <p class="text-xl font-bold" id="current-date"></p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Statistics Cards -->
    <section class="py-8 bg-gray-50 dark:bg-sport-navy">
        <div class="container mx-auto px-4 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Total Revenue -->
                <div class="modern-card bg-gradient-to-br from-blue-500 to-blue-700 rounded-2xl p-6 text-white shadow-lg animate-slide-up">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <p class="text-sm text-blue-100 mb-1">Tổng doanh thu</p>
                            <h3 class="text-3xl font-black"><?php echo formatPrice($totalRevenue); ?></h3>
                        </div>
                        <div class="w-16 h-16 rounded-full bg-white/20 flex items-center justify-center">
                            <i class="fas fa-dollar-sign text-3xl"></i>
                        </div>
                    </div>
                    <p class="text-sm text-blue-100">
                        <i class="fas fa-arrow-up mr-1"></i>+12.5% so với tháng trước
                    </p>
                </div>
                
                <!-- Total Orders -->
                <div class="modern-card bg-gradient-to-br from-green-500 to-emerald-700 rounded-2xl p-6 text-white shadow-lg animate-slide-up" style="animation-delay: 0.1s;">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <p class="text-sm text-green-100 mb-1">Tổng đơn hàng</p>
                            <h3 class="text-3xl font-black"><?php echo $totalOrders; ?></h3>
                        </div>
                        <div class="w-16 h-16 rounded-full bg-white/20 flex items-center justify-center">
                            <i class="fas fa-shopping-cart text-3xl"></i>
                        </div>
                    </div>
                    <p class="text-sm text-green-100">
                        <i class="fas fa-arrow-up mr-1"></i>+8.3% so với tháng trước
                    </p>
                </div>
                
                <!-- Total Products -->
                <div class="modern-card bg-gradient-to-br from-orange-500 to-orange-700 rounded-2xl p-6 text-white shadow-lg animate-slide-up" style="animation-delay: 0.2s;">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <p class="text-sm text-orange-100 mb-1">Tổng sản phẩm</p>
                            <h3 class="text-3xl font-black"><?php echo $totalProducts; ?></h3>
                        </div>
                        <div class="w-16 h-16 rounded-full bg-white/20 flex items-center justify-center">
                            <i class="fas fa-box text-3xl"></i>
                        </div>
                    </div>
                    <p class="text-sm text-orange-100">
                        <i class="fas fa-arrow-up mr-1"></i>+5 sản phẩm mới
                    </p>
                </div>
                
                <!-- Total Users -->
                <div class="modern-card bg-gradient-to-br from-purple-500 to-purple-700 rounded-2xl p-6 text-white shadow-lg animate-slide-up" style="animation-delay: 0.3s;">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <p class="text-sm text-purple-100 mb-1">Người dùng</p>
                            <h3 class="text-3xl font-black"><?php echo $totalUsers; ?></h3>
                        </div>
                        <div class="w-16 h-16 rounded-full bg-white/20 flex items-center justify-center">
                            <i class="fas fa-users text-3xl"></i>
                        </div>
                    </div>
                    <p class="text-sm text-purple-100">
                        <i class="fas fa-arrow-up mr-1"></i>+15 người dùng mới
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Charts Section -->
    <section class="py-8 bg-white dark:bg-sport-blue">
        <div class="container mx-auto px-4 lg:px-8">
            <h2 class="text-3xl font-black text-gray-900 dark:text-white mb-6 flex items-center">
                <i class="fas fa-chart-pie mr-3 text-sport-neon"></i>
                Biểu đồ thống kê
            </h2>
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Order Status Chart -->
                <div class="modern-card bg-gray-50 dark:bg-sport-navy rounded-2xl p-6 shadow-lg">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4 flex items-center">
                        <i class="fas fa-shopping-cart mr-2 text-blue-500"></i>
                        Trạng thái đơn hàng
                    </h3>
                    <div class="relative h-64">
                        <canvas id="orderStatusChart"></canvas>
                    </div>
                </div>
                
                <!-- Top Products Chart -->
                <div class="modern-card bg-gray-50 dark:bg-sport-navy rounded-2xl p-6 shadow-lg">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4 flex items-center">
                        <i class="fas fa-fire mr-2 text-orange-500"></i>
                        Top 5 sản phẩm bán chạy
                    </h3>
                    <div class="relative h-64">
                        <canvas id="topProductsChart"></canvas>
                    </div>
                </div>
                
                <!-- Category Revenue Chart -->
                <div class="modern-card bg-gray-50 dark:bg-sport-navy rounded-2xl p-6 shadow-lg">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4 flex items-center">
                        <i class="fas fa-chart-line mr-2 text-green-500"></i>
                        Doanh thu theo danh mục
                    </h3>
                    <div class="relative h-64">
                        <canvas id="categoryRevenueChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Recent Orders & Low Stock -->
    <section class="py-8 bg-gray-50 dark:bg-sport-navy">
        <div class="container mx-auto px-4 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Recent Orders -->
                <div class="lg:col-span-2">
                    <div class="modern-card bg-white dark:bg-sport-blue rounded-2xl p-6 shadow-lg">
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-6 flex items-center">
                            <i class="fas fa-shopping-cart mr-3 text-sport-neon"></i>
                            Đơn hàng gần đây
                        </h3>
                        
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead>
                                    <tr class="border-b border-gray-200 dark:border-gray-700">
                                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700 dark:text-gray-300">Mã đơn</th>
                                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700 dark:text-gray-300">Khách hàng</th>
                                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700 dark:text-gray-300">Tổng tiền</th>
                                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700 dark:text-gray-300">Trạng thái</th>
                                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700 dark:text-gray-300">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recentOrders as $order): ?>
                                    <tr class="border-b border-gray-100 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-sport-navy transition-colors duration-200">
                                        <td class="py-4 px-4 text-sm font-semibold text-gray-900 dark:text-white">
                                            #<?php echo $order['OrderID']; ?>
                                        </td>
                                        <td class="py-4 px-4 text-sm text-gray-600 dark:text-gray-400">
                                            <?php echo htmlspecialchars($order['FullName'] ?? 'Khách vãng lai'); ?>
                                        </td>
                                        <td class="py-4 px-4 text-sm font-semibold text-sport-neon">
                                            <?php echo formatPrice($order['TotalAmount']); ?>
                                        </td>
                                        <td class="py-4 px-4">
                                            <span class="px-3 py-1 rounded-full text-xs font-semibold <?php 
                                                echo $order['Status'] == 'pending' ? 'bg-yellow-100 text-yellow-700' : 
                                                    ($order['Status'] == 'delivered' ? 'bg-green-100 text-green-700' : 
                                                    'bg-blue-100 text-blue-700'); 
                                            ?>">
                                                <?php echo ucfirst($order['Status']); ?>
                                            </span>
                                        </td>
                                        <td class="py-4 px-4">
                                            <a href="admin_orders.php?id=<?php echo $order['OrderID']; ?>" 
                                               class="text-sport-neon hover:text-blue-600 transition-colors duration-200">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="mt-6 text-center">
                            <a href="admin_orders.php" class="btn-neon inline-block px-6 py-3 relative z-10">
                                Xem tất cả đơn hàng <i class="fas fa-arrow-right ml-2"></i>
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- Low Stock Alert -->
                <div class="lg:col-span-1">
                    <div class="modern-card bg-white dark:bg-sport-blue rounded-2xl p-6 shadow-lg">
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-6 flex items-center">
                            <i class="fas fa-exclamation-triangle mr-3 text-red-500"></i>
                            Sắp hết hàng
                        </h3>
                        
                        <div class="space-y-4">
                            <?php foreach ($lowStockProducts as $product): ?>
                            <div class="flex items-center space-x-4 p-3 rounded-xl glass hover:bg-gray-100 dark:hover:bg-sport-navy transition-all duration-300">
                                <img src="<?php echo $product['MainImage'] ?: 'https://via.placeholder.com/60'; ?>" 
                                     alt="<?php echo htmlspecialchars($product['ProductName']); ?>"
                                     class="w-12 h-12 rounded-lg object-cover">
                                <div class="flex-1">
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white line-clamp-1">
                                        <?php echo htmlspecialchars($product['ProductName']); ?>
                                    </p>
                                    <p class="text-xs text-red-500">
                                        Còn: <?php echo $product['TotalStock'] ?? 0; ?> sản phẩm
                                    </p>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <div class="mt-6">
                            <a href="admin_products.php" class="block w-full py-3 text-center rounded-xl glass text-gray-700 dark:text-gray-300 font-semibold hover:bg-gray-200 dark:hover:bg-gray-700 transition-all duration-300">
                                Quản lý sản phẩm <i class="fas fa-arrow-right ml-2"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Chart.js Library -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<script>
// Display current date
document.getElementById('current-date').textContent = new Date().toLocaleDateString('vi-VN', {
    weekday: 'long',
    year: 'numeric',
    month: 'long',
    day: 'numeric'
});

// Animate stats on load
document.addEventListener('DOMContentLoaded', () => {
    const statCards = document.querySelectorAll('.modern-card');
    anime({
        targets: statCards,
        translateY: [50, 0],
        opacity: [0, 1],
        delay: anime.stagger(100),
        duration: 800,
        easing: 'easeOutQuad'
    });
    
    // Initialize charts
    initCharts();
});

// Chart colors
const chartColors = {
    primary: '#00D9FF',
    blue: '#3B82F6',
    green: '#10B981',
    yellow: '#F59E0B',
    red: '#EF4444',
    purple: '#8B5CF6',
    pink: '#EC4899',
    indigo: '#6366F1',
    teal: '#14B8A6',
    orange: '#F97316'
};

// Initialize all charts
function initCharts() {
    // Order Status Chart
    const orderStatusData = <?php echo json_encode($orderStatusData); ?>;
    createOrderStatusChart(orderStatusData);
    
    // Top Products Chart
    const topProductsData = <?php echo json_encode($topProductsData); ?>;
    createTopProductsChart(topProductsData);
    
    // Category Revenue Chart
    const categoryRevenueData = <?php echo json_encode($categoryRevenueData); ?>;
    createCategoryRevenueChart(categoryRevenueData);
}

// Order Status Pie Chart
function createOrderStatusChart(data) {
    const ctx = document.getElementById('orderStatusChart').getContext('2d');
    
    const statusColors = {
        'pending': chartColors.yellow,
        'processing': chartColors.blue,
        'shipped': chartColors.indigo,
        'delivered': chartColors.green,
        'canceled': chartColors.red
    };
    
    const statusLabels = {
        'pending': 'Chờ xử lý',
        'processing': 'Đang xử lý',
        'shipped': 'Đang giao',
        'delivered': 'Đã giao',
        'canceled': 'Đã hủy'
    };
    
    const labels = data.map(item => statusLabels[item.Status] || item.Status);
    const counts = data.map(item => parseInt(item.count));
    const colors = data.map(item => statusColors[item.Status] || chartColors.primary);
    
    new Chart(ctx, {
        type: 'pie',
        data: {
            labels: labels,
            datasets: [{
                data: counts,
                backgroundColor: colors,
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        color: getComputedStyle(document.documentElement).getPropertyValue('--text-color') || '#333',
                        padding: 15,
                        font: {
                            size: 12,
                            family: "'Inter', sans-serif"
                        }
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.parsed || 0;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((value / total) * 100).toFixed(1);
                            return `${label}: ${value} đơn (${percentage}%)`;
                        }
                    }
                }
            }
        }
    });
}

// Top Products Pie Chart
function createTopProductsChart(data) {
    const ctx = document.getElementById('topProductsChart').getContext('2d');
    
    const labels = data.map(item => item.ProductName);
    const values = data.map(item => parseInt(item.total_sold));
    const colors = [chartColors.orange, chartColors.red, chartColors.pink, chartColors.purple, chartColors.indigo];
    
    new Chart(ctx, {
        type: 'pie',
        data: {
            labels: labels,
            datasets: [{
                data: values,
                backgroundColor: colors,
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        color: getComputedStyle(document.documentElement).getPropertyValue('--text-color') || '#333',
                        padding: 15,
                        font: {
                            size: 11,
                            family: "'Inter', sans-serif"
                        }
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.parsed || 0;
                            return `${label}: ${value} sản phẩm`;
                        }
                    }
                }
            }
        }
    });
}

// Category Revenue Pie Chart
function createCategoryRevenueChart(data) {
    const ctx = document.getElementById('categoryRevenueChart').getContext('2d');
    
    const labels = data.map(item => item.CategoryName);
    const values = data.map(item => parseFloat(item.revenue));
    const colors = [chartColors.green, chartColors.teal, chartColors.blue, chartColors.indigo, chartColors.purple, chartColors.pink];
    
    new Chart(ctx, {
        type: 'pie',
        data: {
            labels: labels,
            datasets: [{
                data: values,
                backgroundColor: colors,
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        color: getComputedStyle(document.documentElement).getPropertyValue('--text-color') || '#333',
                        padding: 15,
                        font: {
                            size: 11,
                            family: "'Inter', sans-serif"
                        }
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.parsed || 0;
                            const formattedValue = new Intl.NumberFormat('vi-VN', {
                                style: 'currency',
                                currency: 'VND'
                            }).format(value);
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((value / total) * 100).toFixed(1);
                            return `${label}: ${formattedValue} (${percentage}%)`;
                        }
                    }
                }
            }
        }
    });
}
</script>

<?php include '../includes/layout_footer.php'; ?>
