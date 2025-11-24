<?php
require_once '../config.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('login.php');
}

$db = Database::getInstance()->getConnection();

// Handle order status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    // SỬA 1: Dùng tên cột PascalCase
    $orderId = (int)$_POST['OrderID'];
    $action = $_POST['action'];
    
    if ($action === 'update_status') {
        $newStatus = $_POST['new_status'];
        // SỬA 2: Sửa tên cột (Trigger hoàn kho sẽ tự chạy nếu $newStatus == 'canceled')
        $stmt = $db->prepare("UPDATE Orders SET Status = ? WHERE OrderID = ?");
        $stmt->execute([$newStatus, $orderId]);
        $message = 'Đã cập nhật trạng thái đơn hàng';
    } elseif ($action === 'delete') {
        $stmt = $db->prepare("DELETE FROM Orders WHERE OrderID = ?");
        $stmt->execute([$orderId]);
        $message = 'Đã xóa đơn hàng';
    }
    
    header('Location: admin_orders.php?message=' . urlencode($message));
    exit;
}

// Get filter parameters
$statusFilter = $_GET['status'] ?? 'all';
$searchQuery = $_GET['search'] ?? '';

// SỬA 3: Viết lại câu JOIN và WHERE
$sql = "
    SELECT o.*, u.FirstName, u.LastName, u.Email, u.Phone
    FROM Orders o
    LEFT JOIN Users u ON o.UserID = u.UserID
    WHERE 1=1
";
$params = [];

if ($statusFilter !== 'all') {
    $sql .= " AND o.Status = ?"; // Sửa tên cột
    $params[] = $statusFilter;
}

if (!empty($searchQuery)) {
    // SỬA 4: Sửa logic tìm kiếm (xóa guest_name, guest_email)
    $sql .= " AND (o.OrderID LIKE ? OR CONCAT(u.FirstName, ' ', u.LastName) LIKE ? OR u.Email LIKE ? OR u.Phone LIKE ? OR o.Address LIKE ?)";
    $searchParam = "%$searchQuery%";
    $params[] = $searchParam;
    $params[] = $searchParam;
    $params[] = $searchParam;
    $params[] = $searchParam;
    $params[] = $searchParam;
}

$sql .= " ORDER BY o.created_at DESC"; // Sửa tên cột

$stmt = $db->prepare($sql);
$stmt->execute($params);
$orders = $stmt->fetchAll();

// SỬA 5: Sửa các truy vấn thống kê
$stmt = $db->query("SELECT COUNT(*) as total FROM Orders");
$totalOrders = $stmt->fetch()['total'];

$stmt = $db->query("SELECT COUNT(*) as total FROM Orders WHERE Status = 'pending'");
$pendingOrders = $stmt->fetch()['total'];

$stmt = $db->query("SELECT COUNT(*) as total FROM Orders WHERE Status = 'processing'");
$processingOrders = $stmt->fetch()['total'];

$stmt = $db->query("SELECT COUNT(*) as total FROM Orders WHERE Status = 'delivered'");
$deliveredOrders = $stmt->fetch()['total'];

$stmt = $db->query("SELECT SUM(TotalAmount) as revenue FROM Orders WHERE Status IN ('delivered', 'shipped')");
$totalRevenue = $stmt->fetch()['revenue'] ?? 0;

$pageTitle = "Quản lý đơn hàng";
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
        
        <a href="admin_dashboard.php" class="flex items-center space-x-3 px-4 py-3 rounded-xl glass hover:bg-gray-100 dark:hover:bg-sport-blue text-gray-700 dark:text-gray-300 transition-all duration-300">
            <i class="fas fa-chart-line w-5"></i>
            <span>Dashboard</span>
        </a>
        
        <a href="admin_products.php" class="flex items-center space-x-3 px-4 py-3 rounded-xl glass hover:bg-gray-100 dark:hover:bg-sport-blue text-gray-700 dark:text-gray-300 transition-all duration-300">
            <i class="fas fa-box w-5"></i>
            <span>Sản phẩm</span>
        </a>
        
        <a href="admin_orders.php" class="flex items-center space-x-3 px-4 py-3 rounded-xl bg-gradient-to-r from-sport-neon to-blue-600 text-white font-semibold">
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
                        <i class="fas fa-shopping-cart mr-3"></i>Quản lý đơn hàng
                    </h1>
                    <p class="text-gray-300">
                        Quản lý và theo dõi tất cả đơn hàng
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Statistics Cards -->
    <section class="py-8 bg-gray-50 dark:bg-sport-navy">
        <div class="container mx-auto px-4 lg:px-8">
            <?php if (isset($_GET['message'])): ?>
                <div class="modern-card bg-green-50 dark:bg-green-900/20 border-l-4 border-green-500 rounded-xl p-4 mb-6 animate-slide-down">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle text-green-500 text-xl mr-3"></i>
                        <p class="text-green-700 dark:text-green-300 font-semibold"><?php echo htmlspecialchars($_GET['message']); ?></p>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Total Orders -->
                <div class="modern-card bg-gradient-to-br from-blue-500 to-blue-700 rounded-2xl p-6 text-white shadow-lg animate-slide-up">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <p class="text-sm text-blue-100 mb-1">Tổng đơn hàng</p>
                            <h3 class="text-3xl font-black"><?php echo number_format($totalOrders); ?></h3>
                        </div>
                        <div class="w-16 h-16 rounded-full bg-white/20 flex items-center justify-center">
                            <i class="fas fa-shopping-cart text-3xl"></i>
                        </div>
                    </div>
                </div>
                
                <!-- Pending Orders -->
                <div class="modern-card bg-gradient-to-br from-yellow-500 to-orange-600 rounded-2xl p-6 text-white shadow-lg animate-slide-up" style="animation-delay: 0.1s;">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <p class="text-sm text-yellow-100 mb-1">Chờ xử lý</p>
                            <h3 class="text-3xl font-black"><?php echo number_format($pendingOrders); ?></h3>
                        </div>
                        <div class="w-16 h-16 rounded-full bg-white/20 flex items-center justify-center">
                            <i class="fas fa-clock text-3xl"></i>
                        </div>
                    </div>
                </div>
                
                <!-- Processing Orders -->
                <div class="modern-card bg-gradient-to-br from-purple-500 to-purple-700 rounded-2xl p-6 text-white shadow-lg animate-slide-up" style="animation-delay: 0.2s;">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <p class="text-sm text-purple-100 mb-1">Đang xử lý</p>
                            <h3 class="text-3xl font-black"><?php echo number_format($processingOrders); ?></h3>
                        </div>
                        <div class="w-16 h-16 rounded-full bg-white/20 flex items-center justify-center">
                            <i class="fas fa-spinner text-3xl"></i>
                        </div>
                    </div>
                </div>
                
                <!-- Total Revenue -->
                <div class="modern-card bg-gradient-to-br from-green-500 to-emerald-700 rounded-2xl p-6 text-white shadow-lg animate-slide-up" style="animation-delay: 0.3s;">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <p class="text-sm text-green-100 mb-1">Doanh thu</p>
                            <h3 class="text-2xl font-black"><?php echo formatPrice($totalRevenue); ?> ₫</h3>
                        </div>
                        <div class="w-16 h-16 rounded-full bg-white/20 flex items-center justify-center">
                            <i class="fas fa-dollar-sign text-3xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filter Section -->
            <div class="modern-card bg-white dark:bg-sport-blue rounded-2xl p-6 mb-6 shadow-lg">
                <form method="GET" action="" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Trạng thái</label>
                        <select name="status" class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 dark:border-gray-700 bg-white dark:bg-sport-navy text-gray-900 dark:text-white focus:border-sport-neon transition-all">
                            <option value="all" <?php echo $statusFilter === 'all' ? 'selected' : ''; ?>>Tất cả</option>
                            <option value="pending" <?php echo $statusFilter === 'pending' ? 'selected' : ''; ?>>Chờ xử lý</option>
                            <option value="processing" <?php echo $statusFilter === 'processing' ? 'selected' : ''; ?>>Đang xử lý</option>
                            <option value="shipped" <?php echo $statusFilter === 'shipped' ? 'selected' : ''; ?>>Đang giao</option>
                            <option value="delivered" <?php echo $statusFilter === 'delivered' ? 'selected' : ''; ?>>Đã giao</option>
                            <option value="canceled" <?php echo $statusFilter === 'canceled' ? 'selected' : ''; ?>>Đã hủy</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Tìm kiếm</label>
                        <input type="text" name="search" 
                               placeholder="Mã đơn, tên, email, SĐT, địa chỉ..."
                               value="<?php echo htmlspecialchars($searchQuery); ?>"
                               class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 dark:border-gray-700 bg-white dark:bg-sport-navy text-gray-900 dark:text-white focus:border-sport-neon transition-all">
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="btn-neon w-full px-6 py-3 relative z-10">
                            <i class="fas fa-search mr-2"></i>Lọc
                        </button>
                    </div>
                </form>
            </div>

            <!-- Orders Table -->
            <div class="modern-card bg-white dark:bg-sport-blue rounded-2xl shadow-lg overflow-hidden">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">
                        Danh sách đơn hàng (<?php echo count($orders); ?>)
                    </h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gradient-to-r from-sport-neon to-blue-600 text-white">
                            <tr>
                                <th class="px-6 py-4 text-left font-bold">Mã đơn</th>
                                <th class="px-6 py-4 text-left font-bold">Khách hàng</th>
                                <th class="px-6 py-4 text-left font-bold">Liên hệ / Địa chỉ</th>
                                <th class="px-6 py-4 text-left font-bold">Ngày đặt</th>
                                <th class="px-6 py-4 text-left font-bold">Tổng tiền</th>
                                <th class="px-6 py-4 text-left font-bold">Trạng thái</th>
                                <th class="px-6 py-4 text-center font-bold">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($orders)): ?>
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center">
                                        <i class="fas fa-inbox text-6xl text-gray-300 dark:text-gray-600 mb-4"></i>
                                        <p class="text-gray-500 dark:text-gray-400">Không có đơn hàng nào</p>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($orders as $index => $order): ?>
                                <tr class="border-b border-gray-100 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-sport-navy transition-colors animate-slide-up" style="animation-delay: <?php echo $index * 0.05; ?>s;">
                                    <td class="px-6 py-4">
                                        <span class="font-bold text-sport-neon">#<?php echo $order['OrderID']; ?></span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <?php 
                                        if ($order['UserID']) {
                                            echo '<p class="font-semibold text-gray-900 dark:text-white">' . htmlspecialchars($order['FirstName'] . ' ' . $order['LastName']) . '</p>';
                                        } else {
                                            echo '<span class="text-gray-500 dark:text-gray-400 italic">Khách vãng lai</span>';
                                        }
                                        ?>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                                        <?php 
                                        if ($order['UserID']) {
                                            echo '<div>' . htmlspecialchars($order['Email']) . '</div>';
                                            echo '<div class="mt-1">' . htmlspecialchars($order['Phone']) . '</div>';
                                        } else {
                                            echo '<div class="line-clamp-2">' . htmlspecialchars($order['Address']) . '</div>';
                                        }
                                        ?>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                                        <?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="font-bold text-sport-neon"><?php echo formatPrice($order['TotalAmount']); ?> ₫</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="OrderID" value="<?php echo $order['OrderID']; ?>">
                                            <input type="hidden" name="action" value="update_status">
                                            <select name="new_status" 
                                                    class="px-3 py-2 text-xs rounded-lg border-2 border-gray-200 dark:border-gray-700 bg-white dark:bg-sport-navy text-gray-900 dark:text-white focus:border-sport-neon transition-all cursor-pointer" 
                                                    onchange="if(confirm('Cập nhật trạng thái đơn hàng?')) this.form.submit();">
                                                <option value="pending" <?php echo $order['Status'] === 'pending' ? 'selected' : ''; ?>>Chờ xử lý</option>
                                                <option value="processing" <?php echo $order['Status'] === 'processing' ? 'selected' : ''; ?>>Đang xử lý</option>
                                                <option value="shipped" <?php echo $order['Status'] === 'shipped' ? 'selected' : ''; ?>>Đang giao</option>
                                                <option value="delivered" <?php echo $order['Status'] === 'delivered' ? 'selected' : ''; ?>>Đã giao</option>
                                                <option value="canceled" <?php echo $order['Status'] === 'canceled' ? 'selected' : ''; ?>>Đã hủy</option>
                                            </select>
                                        </form>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center justify-center space-x-2">
                                            <button onclick="viewOrderDetails(<?php echo $order['OrderID']; ?>)" 
                                               class="p-2 rounded-lg glass hover:bg-blue-500 hover:text-white transition-all duration-300"
                                               title="Chi tiết">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <form method="POST" style="display: inline;">
                                                <input type="hidden" name="OrderID" value="<?php echo $order['OrderID']; ?>">
                                                <input type="hidden" name="action" value="delete">
                                                <button type="submit" 
                                                        class="p-2 rounded-lg glass hover:bg-red-500 hover:text-white transition-all duration-300"
                                                        onclick="return confirm('Xóa đơn hàng này?')" 
                                                        title="Xóa">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Order Details Modal -->
<div id="orderDetailsModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <!-- Background overlay -->
        <div class="fixed inset-0 transition-opacity bg-gray-900 bg-opacity-75" onclick="closeOrderDetailsModal()"></div>

        <!-- Modal panel -->
        <div class="inline-block align-bottom bg-white dark:bg-sport-blue rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
            <!-- Header -->
            <div class="bg-gradient-to-r from-sport-neon to-blue-600 px-6 py-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-2xl font-black text-white flex items-center">
                        <i class="fas fa-receipt mr-3"></i>
                        <span>Chi tiết đơn hàng #<span id="modalOrderId"></span></span>
                    </h3>
                    <button onclick="closeOrderDetailsModal()" class="text-white hover:text-gray-200 transition-colors">
                        <i class="fas fa-times text-2xl"></i>
                    </button>
                </div>
            </div>

            <!-- Content -->
            <div class="px-6 py-6">
                <!-- Loading State -->
                <div id="orderDetailsLoading" class="text-center py-12">
                    <i class="fas fa-spinner fa-spin text-4xl text-sport-neon mb-4"></i>
                    <p class="text-gray-600 dark:text-gray-400">Đang tải thông tin...</p>
                </div>

                <!-- Order Info -->
                <div id="orderDetailsContent" class="hidden">
                    <!-- Customer Info -->
                    <div class="mb-6 p-4 bg-gray-50 dark:bg-sport-navy rounded-xl">
                        <h4 class="text-lg font-bold text-gray-900 dark:text-white mb-3">
                            <i class="fas fa-user mr-2 text-sport-neon"></i>Thông tin khách hàng
                        </h4>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="text-gray-600 dark:text-gray-400">Tên:</span>
                                <span class="font-semibold text-gray-900 dark:text-white ml-2" id="customerName"></span>
                            </div>
                            <div>
                                <span class="text-gray-600 dark:text-gray-400">Email:</span>
                                <span class="font-semibold text-gray-900 dark:text-white ml-2" id="customerEmail"></span>
                            </div>
                            <div>
                                <span class="text-gray-600 dark:text-gray-400">SĐT:</span>
                                <span class="font-semibold text-gray-900 dark:text-white ml-2" id="customerPhone"></span>
                            </div>
                            <div>
                                <span class="text-gray-600 dark:text-gray-400">Địa chỉ:</span>
                                <span class="font-semibold text-gray-900 dark:text-white ml-2" id="customerAddress"></span>
                            </div>
                        </div>
                        <div class="mt-3" id="orderNoteSection">
                            <span class="text-gray-600 dark:text-gray-400">Ghi chú:</span>
                            <span class="font-semibold text-gray-900 dark:text-white ml-2" id="orderNote"></span>
                        </div>
                    </div>

                    <!-- Products Table -->
                    <div class="mb-6">
                        <h4 class="text-lg font-bold text-gray-900 dark:text-white mb-3">
                            <i class="fas fa-shopping-bag mr-2 text-sport-neon"></i>Danh sách sản phẩm
                        </h4>
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-gray-100 dark:bg-sport-navy">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-sm font-bold text-gray-900 dark:text-white">Sản phẩm</th>
                                        <th class="px-4 py-3 text-center text-sm font-bold text-gray-900 dark:text-white">Size/Màu</th>
                                        <th class="px-4 py-3 text-center text-sm font-bold text-gray-900 dark:text-white">Số lượng</th>
                                        <th class="px-4 py-3 text-right text-sm font-bold text-gray-900 dark:text-white">Đơn giá</th>
                                        <th class="px-4 py-3 text-right text-sm font-bold text-gray-900 dark:text-white">Thành tiền</th>
                                    </tr>
                                </thead>
                                <tbody id="orderItemsTable" class="divide-y divide-gray-200 dark:divide-gray-700">
                                    <!-- Items will be inserted here -->
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Order Summary -->
                    <div class="border-t-2 border-gray-200 dark:border-gray-700 pt-4">
                        <div class="flex justify-end">
                            <div class="w-64 space-y-2">
                                <div class="flex justify-between text-gray-700 dark:text-gray-300">
                                    <span>Tổng tiền:</span>
                                    <span class="font-bold text-2xl text-sport-neon" id="orderTotal"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="bg-gray-50 dark:bg-sport-navy px-6 py-4 flex justify-end">
                <button onclick="closeOrderDetailsModal()" 
                        class="px-6 py-2 bg-gray-500 hover:bg-gray-600 text-white font-bold rounded-xl transition-all duration-300">
                    Đóng
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// View Order Details
async function viewOrderDetails(orderId) {
    const modal = document.getElementById('orderDetailsModal');
    const loading = document.getElementById('orderDetailsLoading');
    const content = document.getElementById('orderDetailsContent');
    
    // Show modal
    modal.classList.remove('hidden');
    loading.classList.remove('hidden');
    content.classList.add('hidden');
    
    document.getElementById('modalOrderId').textContent = orderId;
    
    try {
        const response = await fetch(`../api/get_order_details.php?id=${orderId}`);
        const data = await response.json();
        
        if (data.success) {
            // Display customer info
            const order = data.order;
            const customerName = order.GuestName || `${order.FirstName} ${order.LastName}`;
            const customerEmail = order.GuestEmail || order.Email;
            const customerPhone = order.GuestPhone || order.Phone;
            
            document.getElementById('customerName').textContent = customerName;
            document.getElementById('customerEmail').textContent = customerEmail || 'N/A';
            document.getElementById('customerPhone').textContent = customerPhone || 'N/A';
            document.getElementById('customerAddress').textContent = order.Address;
            
            if (order.Note) {
                document.getElementById('orderNote').textContent = order.Note;
                document.getElementById('orderNoteSection').classList.remove('hidden');
            } else {
                document.getElementById('orderNoteSection').classList.add('hidden');
            }
            
            // Display order items
            const itemsTable = document.getElementById('orderItemsTable');
            itemsTable.innerHTML = '';
            
            let total = 0;
            data.items.forEach(item => {
                const subtotal = item.Quantity * item.Price;
                total += subtotal;
                
                const row = `
                    <tr class="hover:bg-gray-50 dark:hover:bg-sport-navy/50 transition-colors">
                        <td class="px-4 py-3 text-gray-900 dark:text-white">
                            <div class="font-semibold">${item.ProductName}</div>
                        </td>
                        <td class="px-4 py-3 text-center text-gray-700 dark:text-gray-300">
                            <span class="inline-block px-2 py-1 bg-gray-100 dark:bg-gray-700 rounded text-xs">
                                ${item.Size} / ${item.Color}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center text-gray-900 dark:text-white font-semibold">
                            ${item.Quantity}
                        </td>
                        <td class="px-4 py-3 text-right text-gray-900 dark:text-white">
                            ${formatPrice(item.Price)}
                        </td>
                        <td class="px-4 py-3 text-right text-gray-900 dark:text-white font-bold">
                            ${formatPrice(subtotal)}
                        </td>
                    </tr>
                `;
                itemsTable.innerHTML += row;
            });
            
            document.getElementById('orderTotal').textContent = formatPrice(total);
            
            loading.classList.add('hidden');
            content.classList.remove('hidden');
        } else {
            throw new Error(data.message || 'Không thể tải thông tin đơn hàng');
        }
    } catch (error) {
        alert('Lỗi: ' + error.message);
        closeOrderDetailsModal();
    }
}

function closeOrderDetailsModal() {
    document.getElementById('orderDetailsModal').classList.add('hidden');
}

function formatPrice(price) {
    return new Intl.NumberFormat('vi-VN').format(price) + ' ₫';
}
</script>

<?php include '../includes/layout_footer.php'; ?>