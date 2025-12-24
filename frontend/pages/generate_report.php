<?php
require_once '../config.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('login.php');
}

// Use reports directory inside frontend folder (web-accessible)
$reportsDir = __DIR__ . '/../reports';

// Try to create if it doesn't exist, but don't fail if we can't
if (!file_exists($reportsDir)) {
    @mkdir($reportsDir, 0755, true);
}

// If still doesn't exist or not writable, show error
if (!file_exists($reportsDir) || !is_writable($reportsDir)) {
    die('Error: Unable to create reports directory. Please check permissions.');
}

<<<<<<< HEAD
=======
$db = Database::getInstance()->getConnection();

>>>>>>> 3d6d58ed3875cc3c551e3fe1991339ab7637c345
// ===========================
// COLLECT ALL STATISTICS DATA
// ===========================

<<<<<<< HEAD
// 1. Basic Statistics (using API data)
$ordersResponse = makeApiRequest('/orders');
$orders = $ordersResponse['success'] ? $ordersResponse['data']['orders'] ?? [] : [];

$productsResponse = makeApiRequest('/products');
$products = $productsResponse['success'] ? $productsResponse['data']['products'] ?? [] : [];

$totalUsers = 0; // Would need users API endpoint
$totalProducts = count($products);
$totalOrders = count($orders);

// Get categories and brands from API
$categoriesResponse = makeApiRequest('/categories');
$categories = $categoriesResponse['success'] ? $categoriesResponse['data'] ?? [] : [];
$totalCategories = count($categories);

$brandsResponse = makeApiRequest('/brands');
$brands = $brandsResponse['success'] ? $brandsResponse['data'] ?? [] : [];
$totalBrands = count($brands);

// Calculate revenue from orders data
$totalRevenue = 0;
foreach ($orders as $order) {
    if (in_array($order['Status'], ['delivered', 'shipped'])) {
        $totalRevenue += $order['TotalAmount'] ?? 0;
    }
}

// Get reviews from API
$reviewsResponse = makeApiRequest('/reviews');
$reviews = $reviewsResponse['success'] ? $reviewsResponse['data']['reviews'] ?? [] : [];
$totalReviews = count($reviews);

// Calculate average rating from reviews data
$avgRating = 0;
if (!empty($reviews)) {
    $totalRating = 0;
    $ratingCount = 0;
    foreach ($reviews as $review) {
        if (isset($review['Rating'])) {
            $totalRating += $review['Rating'];
            $ratingCount++;
        }
    }
    $avgRating = $ratingCount > 0 ? round($totalRating / $ratingCount, 2) : 0;
}

// 2. Order Statistics by Status (calculated from orders data)
$orderStatusStats = [];
foreach ($orders as $order) {
    $status = $order['Status'] ?? 'unknown';
    $orderStatusStats[$status] = ($orderStatusStats[$status] ?? 0) + 1;
}

// 3. Top 10 Best Selling Products (simplified - would need detailed order items API)
$topProducts = [];

// 4. Revenue by Category (simplified - would need detailed analytics API)
$revenueByCategory = [];

// 5. Revenue by Brand (simplified - would need detailed analytics API)
$revenueByBrand = [];

// 6. Recent User Activity (Last 30 days) - simplified
$newUsers = 0;

// 7. Recent Orders (Last 30 days) - simplified
$recentOrders = 0;
$recentRevenue = 0;

// 8. Low Stock Products (less than 10 items) - simplified
$lowStockProducts = [];

// 9. Top Customers - simplified
$topCustomers = [];

// 10. Monthly Revenue (Last 6 months) - simplified
$monthlyRevenue = [];
=======
// 1. Basic Statistics
$stmt = $db->query("SELECT COUNT(*) as total FROM Users WHERE Status = 1");
$totalUsers = $stmt->fetch()['total'];

$stmt = $db->query("SELECT COUNT(*) as total FROM Product");
$totalProducts = $stmt->fetch()['total'];

$stmt = $db->query("SELECT COUNT(*) as total FROM Categories");
$totalCategories = $stmt->fetch()['total'];

$stmt = $db->query("SELECT COUNT(*) as total FROM Brand");
$totalBrands = $stmt->fetch()['total'];

$stmt = $db->query("SELECT COUNT(*) as total FROM Orders");
$totalOrders = $stmt->fetch()['total'];

$stmt = $db->query("SELECT SUM(TotalAmount) as revenue FROM Orders WHERE Status IN ('delivered', 'shipped')");
$totalRevenue = $stmt->fetch()['revenue'] ?? 0;

$stmt = $db->query("SELECT COUNT(*) as total FROM Reviews");
$totalReviews = $stmt->fetch()['total'];

$stmt = $db->query("SELECT AVG(Rating) as avg FROM Reviews");
$avgRating = round($stmt->fetch()['avg'] ?? 0, 2);

// 2. Order Statistics by Status
$stmt = $db->query("
    SELECT Status, COUNT(*) as count 
    FROM Orders 
    GROUP BY Status
");
$ordersByStatus = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 3. Top 10 Best Selling Products
$stmt = $db->prepare("
    SELECT p.ProductName, b.BrandName, SUM(od.Quantity) as total_sold, SUM(od.Price * od.Quantity) as revenue
    FROM OrderDetails od
    JOIN ProductDetail pd ON od.ProductDetailID = pd.ProductDetailID
    JOIN Product p ON pd.ProductID = p.ProductID
    LEFT JOIN Brand b ON p.BrandID = b.BrandID
    JOIN Orders o ON od.OrderID = o.OrderID
    WHERE o.Status IN ('delivered', 'shipped')
    GROUP BY p.ProductID
    ORDER BY total_sold DESC
    LIMIT 10
");
$stmt->execute();
$topProducts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 4. Revenue by Category
$stmt = $db->query("
    SELECT c.CategoryName, SUM(od.Price * od.Quantity) as revenue, COUNT(DISTINCT od.OrderID) as orders
    FROM OrderDetails od
    JOIN ProductDetail pd ON od.ProductDetailID = pd.ProductDetailID
    JOIN Product p ON pd.ProductID = p.ProductID
    JOIN Categories c ON p.CategoryID = c.CategoryID
    JOIN Orders o ON od.OrderID = o.OrderID
    WHERE o.Status IN ('delivered', 'shipped')
    GROUP BY c.CategoryID
    ORDER BY revenue DESC
");
$categoryRevenue = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 5. Revenue by Brand
$stmt = $db->query("
    SELECT b.BrandName, SUM(od.Price * od.Quantity) as revenue, COUNT(DISTINCT od.OrderID) as orders
    FROM OrderDetails od
    JOIN ProductDetail pd ON od.ProductDetailID = pd.ProductDetailID
    JOIN Product p ON pd.ProductID = p.ProductID
    LEFT JOIN Brand b ON p.BrandID = b.BrandID
    JOIN Orders o ON od.OrderID = o.OrderID
    WHERE o.Status IN ('delivered', 'shipped')
    GROUP BY b.BrandID
    ORDER BY revenue DESC
");
$brandRevenue = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 6. Recent User Activity (Last 30 days)
$stmt = $db->query("
    SELECT COUNT(*) as new_users
    FROM Users
    WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
");
$newUsers30Days = $stmt->fetch()['new_users'];

// 7. Recent Orders (Last 30 days)
$stmt = $db->query("
    SELECT COUNT(*) as recent_orders, SUM(TotalAmount) as recent_revenue
    FROM Orders
    WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
");
$recentOrdersData = $stmt->fetch();
$recentOrders30Days = $recentOrdersData['recent_orders'];
$recentRevenue30Days = $recentOrdersData['recent_revenue'] ?? 0;

// 8. Low Stock Products (less than 10 items)
$stmt = $db->prepare("
    SELECT p.ProductName, b.BrandName, SUM(pd.Quantity) as TotalStock
    FROM Product p
    LEFT JOIN ProductDetail pd ON p.ProductID = pd.ProductID
    LEFT JOIN Brand b ON p.BrandID = b.BrandID
    GROUP BY p.ProductID
    HAVING TotalStock < 10 OR TotalStock IS NULL
    ORDER BY TotalStock ASC
    LIMIT 20
");
$stmt->execute();
$lowStockProducts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 9. Top Customers
$stmt = $db->query("
    SELECT 
        CONCAT(u.FirstName, ' ', u.LastName) as FullName,
        u.Email,
        COUNT(o.OrderID) as total_orders,
        SUM(o.TotalAmount) as total_spent
    FROM Users u
    JOIN Orders o ON u.UserID = o.UserID
    WHERE o.Status IN ('delivered', 'shipped')
    GROUP BY u.UserID
    ORDER BY total_spent DESC
    LIMIT 10
");
$topCustomers = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 10. Monthly Revenue (Last 6 months)
$stmt = $db->query("
    SELECT 
        DATE_FORMAT(created_at, '%Y-%m') as month,
        SUM(TotalAmount) as revenue,
        COUNT(*) as orders
    FROM Orders
    WHERE Status IN ('delivered', 'shipped')
        AND created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
    GROUP BY DATE_FORMAT(created_at, '%Y-%m')
    ORDER BY month DESC
");
$monthlyRevenue = $stmt->fetchAll(PDO::FETCH_ASSOC);
>>>>>>> 3d6d58ed3875cc3c551e3fe1991339ab7637c345

// ===========================
// GENERATE PDF USING FPDF-like approach (pure PHP)
// ===========================

class PDF {
    private $content = '';
    private $pageWidth = 210; // A4 width in mm
    private $pageHeight = 297; // A4 height in mm
    
    public function __construct() {
        $this->content = "%PDF-1.4\n";
    }
    
    public function addText($text) {
        $this->content .= $text . "\n";
    }
    
    public function output($filename) {
        // Simple text-based report for now
        // We'll create a more sophisticated HTML to PDF conversion
        return file_put_contents($filename, $this->content);
    }
}

// Generate HTML report that will be converted to PDF
$reportDate = date('d/m/Y H:i:s');
$reportFilename = 'BaoCao_' . date('Y-m-d_His') . '.html';
$reportPath = $reportsDir . '/' . $reportFilename;

// Create HTML content
ob_start();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Báo Cáo Quản Trị - SportShop</title>
    <style>
        @media print {
            @page {
                size: A4;
                margin: 15mm;
            }
            body {
                margin: 0;
                padding: 0;
            }
            .no-print {
                display: none;
            }
            .page-break {
                page-break-after: always;
            }
        }
        
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 210mm;
            margin: 0 auto;
            padding: 20px;
            background: #fff;
        }
        
        .header {
            text-align: center;
            border-bottom: 3px solid #00D9FF;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        
        .header h1 {
            color: #1a1a2e;
            font-size: 32px;
            margin: 0 0 10px 0;
        }
        
        .header p {
            color: #666;
            margin: 5px 0;
        }
        
        .section {
            margin-bottom: 30px;
        }
        
        .section-title {
            background: linear-gradient(135deg, #00D9FF 0%, #3B82F6 100%);
            color: white;
            padding: 12px 20px;
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 15px;
            border-radius: 5px;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .stat-card {
            background: #f8f9fa;
            border-left: 4px solid #00D9FF;
            padding: 15px;
            border-radius: 5px;
        }
        
        .stat-label {
            font-size: 12px;
            color: #666;
            margin-bottom: 5px;
        }
        
        .stat-value {
            font-size: 24px;
            font-weight: bold;
            color: #1a1a2e;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 11px;
        }
        
        table th {
            background: #1a1a2e;
            color: white;
            padding: 10px;
            text-align: left;
            font-weight: bold;
        }
        
        table td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }
        
        table tr:nth-child(even) {
            background: #f8f9fa;
        }
        
        table tr:hover {
            background: #e9ecef;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-center {
            text-align: center;
        }
        
        .footer {
            text-align: center;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #ddd;
            color: #666;
            font-size: 12px;
        }
        
        .print-btn {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #00D9FF;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            font-weight: bold;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        
        .print-btn:hover {
            background: #00b8d4;
        }
        
        .highlight {
            color: #00D9FF;
            font-weight: bold;
        }
        
        .warning {
            color: #f59e0b;
            font-weight: bold;
        }
        
        .success {
            color: #10b981;
            font-weight: bold;
        }
        
        .danger {
            color: #ef4444;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <button class="print-btn no-print" onclick="window.print()">
        🖨️ In báo cáo
    </button>
    
    <!-- HEADER -->
    <div class="header">
        <h1>📊 BÁO CÁO QUẢN TRỊ HỆ THỐNG</h1>
        <h2 style="color: #00D9FF; margin: 10px 0;">SPORTSHOP - CỬA HÀNG THỂ THAO</h2>
        <p><strong>Ngày xuất báo cáo:</strong> <?php echo $reportDate; ?></p>
        <p><strong>Người xuất:</strong> <?php echo $_SESSION['full_name'] ?? 'Admin'; ?></p>
    </div>
    
    <!-- TỔNG QUAN -->
    <div class="section">
        <div class="section-title">📈 TỔNG QUAN HỆ THỐNG</div>
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-label">Tổng doanh thu</div>
                <div class="stat-value success"><?php echo number_format($totalRevenue, 0, ',', '.'); ?>đ</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Tổng đơn hàng</div>
                <div class="stat-value highlight"><?php echo number_format($totalOrders); ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Người dùng</div>
                <div class="stat-value"><?php echo number_format($totalUsers); ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Sản phẩm</div>
                <div class="stat-value"><?php echo number_format($totalProducts); ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Danh mục</div>
                <div class="stat-value"><?php echo number_format($totalCategories); ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Thương hiệu</div>
                <div class="stat-value"><?php echo number_format($totalBrands); ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Đánh giá</div>
                <div class="stat-value"><?php echo number_format($totalReviews); ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Điểm TB</div>
                <div class="stat-value warning"><?php echo $avgRating; ?>/5 ⭐</div>
            </div>
        </div>
    </div>
    
    <!-- HOẠT ĐỘNG 30 NGÀY GẦN ĐÂY -->
    <div class="section">
        <div class="section-title">📅 HOẠT ĐỘNG 30 NGÀY GẦN ĐÂY</div>
        <div class="stats-grid" style="grid-template-columns: repeat(3, 1fr);">
            <div class="stat-card">
                <div class="stat-label">Người dùng mới</div>
                <div class="stat-value highlight"><?php echo number_format($newUsers30Days); ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Đơn hàng mới</div>
                <div class="stat-value"><?php echo number_format($recentOrders30Days); ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Doanh thu</div>
                <div class="stat-value success"><?php echo number_format($recentRevenue30Days, 0, ',', '.'); ?>đ</div>
            </div>
        </div>
    </div>
    
    <!-- TRẠNG THÁI ĐỌN HÀNG -->
    <div class="section">
        <div class="section-title">📦 TRẠNG THÁI ĐƠN HÀNG</div>
        <table>
            <thead>
                <tr>
                    <th>Trạng thái</th>
                    <th class="text-right">Số lượng</th>
                    <th class="text-right">Tỷ lệ (%)</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $statusLabels = [
                    'pending' => 'Chờ xử lý',
                    'processing' => 'Đang xử lý',
                    'shipped' => 'Đang giao',
                    'delivered' => 'Đã giao',
                    'canceled' => 'Đã hủy'
                ];
                foreach ($ordersByStatus as $status): 
                    $percentage = ($status['count'] / $totalOrders) * 100;
                ?>
                <tr>
                    <td><?php echo $statusLabels[$status['Status']] ?? $status['Status']; ?></td>
                    <td class="text-right"><strong><?php echo number_format($status['count']); ?></strong></td>
                    <td class="text-right"><?php echo number_format($percentage, 1); ?>%</td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    
    <!-- TOP 10 SẢN PHẨM BÁN CHẠY -->
    <div class="section page-break">
        <div class="section-title">🔥 TOP 10 SẢN PHẨM BÁN CHẠY NHẤT</div>
        <table>
            <thead>
                <tr>
                    <th>Hạng</th>
                    <th>Tên sản phẩm</th>
                    <th>Thương hiệu</th>
                    <th class="text-right">Đã bán</th>
                    <th class="text-right">Doanh thu</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($topProducts as $index => $product): ?>
                <tr>
                    <td class="text-center"><strong><?php echo $index + 1; ?></strong></td>
                    <td><?php echo htmlspecialchars($product['ProductName']); ?></td>
                    <td><?php echo htmlspecialchars($product['BrandName'] ?? 'N/A'); ?></td>
                    <td class="text-right highlight"><?php echo number_format($product['total_sold']); ?></td>
                    <td class="text-right success"><?php echo number_format($product['revenue'], 0, ',', '.'); ?>đ</td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    
    <!-- DOANH THU THEO DANH MỤC -->
    <div class="section">
        <div class="section-title">📂 DOANH THU THEO DANH MỤC</div>
        <table>
            <thead>
                <tr>
                    <th>Danh mục</th>
                    <th class="text-right">Số đơn hàng</th>
                    <th class="text-right">Doanh thu</th>
                    <th class="text-right">Tỷ trọng (%)</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $totalCategoryRevenue = array_sum(array_column($categoryRevenue, 'revenue'));
                foreach ($categoryRevenue as $category): 
                    $percentage = ($category['revenue'] / $totalCategoryRevenue) * 100;
                ?>
                <tr>
                    <td><?php echo htmlspecialchars($category['CategoryName']); ?></td>
                    <td class="text-right"><?php echo number_format($category['orders']); ?></td>
                    <td class="text-right success"><?php echo number_format($category['revenue'], 0, ',', '.'); ?>đ</td>
                    <td class="text-right"><?php echo number_format($percentage, 1); ?>%</td>
                </tr>
                <?php endforeach; ?>
                <tr style="background: #e9ecef; font-weight: bold;">
                    <td>TỔNG CỘNG</td>
                    <td class="text-right"><?php echo number_format(array_sum(array_column($categoryRevenue, 'orders'))); ?></td>
                    <td class="text-right success"><?php echo number_format($totalCategoryRevenue, 0, ',', '.'); ?>đ</td>
                    <td class="text-right">100%</td>
                </tr>
            </tbody>
        </table>
    </div>
    
    <!-- DOANH THU THEO THƯƠNG HIỆU -->
    <div class="section">
        <div class="section-title">🏷️ DOANH THU THEO THƯƠNG HIỆU</div>
        <table>
            <thead>
                <tr>
                    <th>Thương hiệu</th>
                    <th class="text-right">Số đơn hàng</th>
                    <th class="text-right">Doanh thu</th>
                    <th class="text-right">Tỷ trọng (%)</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $totalBrandRevenue = array_sum(array_column($brandRevenue, 'revenue'));
                foreach ($brandRevenue as $brand): 
                    $percentage = ($brand['revenue'] / $totalBrandRevenue) * 100;
                ?>
                <tr>
                    <td><?php echo htmlspecialchars($brand['BrandName'] ?? 'N/A'); ?></td>
                    <td class="text-right"><?php echo number_format($brand['orders']); ?></td>
                    <td class="text-right success"><?php echo number_format($brand['revenue'], 0, ',', '.'); ?>đ</td>
                    <td class="text-right"><?php echo number_format($percentage, 1); ?>%</td>
                </tr>
                <?php endforeach; ?>
                <tr style="background: #e9ecef; font-weight: bold;">
                    <td>TỔNG CỘNG</td>
                    <td class="text-right"><?php echo number_format(array_sum(array_column($brandRevenue, 'orders'))); ?></td>
                    <td class="text-right success"><?php echo number_format($totalBrandRevenue, 0, ',', '.'); ?>đ</td>
                    <td class="text-right">100%</td>
                </tr>
            </tbody>
        </table>
    </div>
    
    <!-- TOP 10 KHÁCH HÀNG -->
    <div class="section page-break">
        <div class="section-title">👥 TOP 10 KHÁCH HÀNG TIỀM NĂNG</div>
        <table>
            <thead>
                <tr>
                    <th>Hạng</th>
                    <th>Họ và tên</th>
                    <th>Email</th>
                    <th class="text-right">Số đơn hàng</th>
                    <th class="text-right">Tổng chi tiêu</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($topCustomers as $index => $customer): ?>
                <tr>
                    <td class="text-center"><strong><?php echo $index + 1; ?></strong></td>
                    <td><?php echo htmlspecialchars($customer['FullName']); ?></td>
                    <td><?php echo htmlspecialchars($customer['Email']); ?></td>
                    <td class="text-right highlight"><?php echo number_format($customer['total_orders']); ?></td>
                    <td class="text-right success"><?php echo number_format($customer['total_spent'], 0, ',', '.'); ?>đ</td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    
    <!-- DOANH THU THEO THÁNG -->
    <div class="section">
        <div class="section-title">📊 DOANH THU 6 THÁNG GẦN ĐÂY</div>
        <table>
            <thead>
                <tr>
                    <th>Tháng</th>
                    <th class="text-right">Số đơn hàng</th>
                    <th class="text-right">Doanh thu</th>
                    <th class="text-right">TB/Đơn</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($monthlyRevenue as $month): 
                    $avgPerOrder = $month['orders'] > 0 ? $month['revenue'] / $month['orders'] : 0;
                ?>
                <tr>
                    <td><?php echo $month['month']; ?></td>
                    <td class="text-right"><?php echo number_format($month['orders']); ?></td>
                    <td class="text-right success"><?php echo number_format($month['revenue'], 0, ',', '.'); ?>đ</td>
                    <td class="text-right"><?php echo number_format($avgPerOrder, 0, ',', '.'); ?>đ</td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    
    <!-- CẢNH BÁO TỒN KHO -->
    <?php if (!empty($lowStockProducts)): ?>
    <div class="section">
        <div class="section-title">⚠️ CẢNH BÁO TỒN KHO THẤP (< 10 SẢN PHẨM)</div>
        <table>
            <thead>
                <tr>
                    <th>Tên sản phẩm</th>
                    <th>Thương hiệu</th>
                    <th class="text-right">Tồn kho</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($lowStockProducts as $product): ?>
                <tr>
                    <td><?php echo htmlspecialchars($product['ProductName']); ?></td>
                    <td><?php echo htmlspecialchars($product['BrandName'] ?? 'N/A'); ?></td>
                    <td class="text-right danger"><?php echo $product['TotalStock'] ?? 0; ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
    
    <!-- FOOTER -->
    <div class="footer">
        <p><strong>SportShop - Cửa hàng thể thao trực tuyến</strong></p>
        <p>Báo cáo được tạo tự động bởi hệ thống quản trị</p>
        <p>© <?php echo date('Y'); ?> SportShop. All rights reserved.</p>
    </div>
</body>
</html>
<?php
$htmlContent = ob_get_clean();

// Save HTML report
file_put_contents($reportPath, $htmlContent);

// Redirect to the HTML report which can be printed to PDF
header('Location: ../reports/' . $reportFilename);
exit();
?>
