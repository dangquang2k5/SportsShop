<?php
require_once '../config.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('login.php');
}

$db = Database::getInstance()->getConnection();

// Handle user actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $userId = (int)$_POST['user_id'];
    $action = $_POST['action'];
    
    if ($action === 'update_status') {
        $newStatus = (int)$_POST['new_status']; // Ép kiểu thành 0 hoặc 1
        $stmt = $db->prepare("UPDATE Users SET Status = ? WHERE UserID = ?"); // Sửa tên cột 'Status'
        $stmt->execute([$newStatus, $userId]);
        $message = 'Đã cập nhật trạng thái người dùng';
    } elseif ($action === 'update_role') {
        $newRole = $_POST['new_role'];
        $stmt = $db->prepare("UPDATE Users SET Role = ? WHERE UserID = ?");
        $stmt->execute([$newRole, $userId]);
        $message = 'Đã cập nhật vai trò người dùng';
    } elseif ($action === 'delete') {
        if ($userId != $_SESSION['user_id']) {
            $stmt = $db->prepare("DELETE FROM Users WHERE UserID = ?");
            $stmt->execute([$userId]);
            $message = 'Đã xóa người dùng';
        } else {
            $message = 'Không thể xóa tài khoản của chính bạn';
        }
    }
    
    header('Location: admin_users.php?message=' . urlencode($message));
    exit;
}

// Get filter parameters
$roleFilter = $_GET['role'] ?? 'all';
$statusFilter = $_GET['status'] ?? 'all';
$searchQuery = $_GET['search'] ?? '';

// Build query
$sql = "SELECT * FROM Users WHERE 1=1";
$params = [];

if ($roleFilter !== 'all') {
    $sql .= " AND Role = ?";
    $params[] = $roleFilter;
}

if ($statusFilter !== 'all') {
    $sql .= " AND Status = ?"; // Sửa tên cột 'Status'
    $params[] = (int)$statusFilter; // Lọc theo 1 hoặc 0
}

if (!empty($searchQuery)) {
    $sql .= " AND (CONCAT(FirstName, ' ', LastName) LIKE ? OR Email LIKE ? OR Phone LIKE ?)";
    $searchParam = "%$searchQuery%";
    $params[] = $searchParam;
    $params[] = $searchParam;
    $params[] = $searchParam;
}

$sql .= " ORDER BY UserID DESC";

$stmt = $db->prepare($sql);
$stmt->execute($params);
$users = $stmt->fetchAll();

// Get statistics
$stmt = $db->query("SELECT COUNT(*) as total FROM Users");
$totalUsers = $stmt->fetch()['total'];

$stmt = $db->query("SELECT COUNT(*) as total FROM Users WHERE Role = 'admin'");
$adminUsers = $stmt->fetch()['total'];

$stmt = $db->query("SELECT COUNT(*) as total FROM Users WHERE Role = 'customer'");
$memberUsers = $stmt->fetch()['total'];

$stmt = $db->query("SELECT COUNT(*) as total FROM Users WHERE Status = 1");
$activeUsers = $stmt->fetch()['total'];

$pageTitle = "Quản lý người dùng";
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
        
        <a href="admin_orders.php" class="flex items-center space-x-3 px-4 py-3 rounded-xl glass hover:bg-gray-100 dark:hover:bg-sport-blue text-gray-700 dark:text-gray-300 transition-all duration-300">
            <i class="fas fa-shopping-cart w-5"></i>
            <span>Đơn hàng</span>
        </a>
        
        <a href="admin_users.php" class="flex items-center space-x-3 px-4 py-3 rounded-xl bg-gradient-to-r from-sport-neon to-blue-600 text-white font-semibold">
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
            <div class="animate-fade-in">
                <h1 class="text-4xl font-black text-white mb-2">
                    <i class="fas fa-users mr-3"></i>Quản lý người dùng
                </h1>
                <p class="text-gray-300">
                    Quản lý tài khoản và phân quyền người dùng
                </p>
            </div>
        </div>
    </section>

    <!-- Content -->
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
                <!-- Total Users -->
                <div class="modern-card bg-gradient-to-br from-blue-500 to-blue-700 rounded-2xl p-6 text-white shadow-lg animate-slide-up">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <p class="text-sm text-blue-100 mb-1">Tổng người dùng</p>
                            <h3 class="text-3xl font-black"><?php echo number_format($totalUsers); ?></h3>
                        </div>
                        <div class="w-16 h-16 rounded-full bg-white/20 flex items-center justify-center">
                            <i class="fas fa-users text-3xl"></i>
                        </div>
                    </div>
                </div>
                
                <!-- Admin Users -->
                <div class="modern-card bg-gradient-to-br from-red-500 to-red-700 rounded-2xl p-6 text-white shadow-lg animate-slide-up" style="animation-delay: 0.1s;">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <p class="text-sm text-red-100 mb-1">Quản trị viên</p>
                            <h3 class="text-3xl font-black"><?php echo number_format($adminUsers); ?></h3>
                        </div>
                        <div class="w-16 h-16 rounded-full bg-white/20 flex items-center justify-center">
                            <i class="fas fa-user-shield text-3xl"></i>
                        </div>
                    </div>
                </div>
                
                <!-- Member Users -->
                <div class="modern-card bg-gradient-to-br from-purple-500 to-purple-700 rounded-2xl p-6 text-white shadow-lg animate-slide-up" style="animation-delay: 0.2s;">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <p class="text-sm text-purple-100 mb-1">Khách hàng</p>
                            <h3 class="text-3xl font-black"><?php echo number_format($memberUsers); ?></h3>
                        </div>
                        <div class="w-16 h-16 rounded-full bg-white/20 flex items-center justify-center">
                            <i class="fas fa-user text-3xl"></i>
                        </div>
                    </div>
                </div>
                
                <!-- Active Users -->
                <div class="modern-card bg-gradient-to-br from-green-500 to-emerald-700 rounded-2xl p-6 text-white shadow-lg animate-slide-up" style="animation-delay: 0.3s;">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <p class="text-sm text-green-100 mb-1">Đang hoạt động</p>
                            <h3 class="text-3xl font-black"><?php echo number_format($activeUsers); ?></h3>
                        </div>
                        <div class="w-16 h-16 rounded-full bg-white/20 flex items-center justify-center">
                            <i class="fas fa-check-circle text-3xl"></i>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Filters -->
            <div class="modern-card bg-white dark:bg-sport-blue rounded-2xl p-6 mb-6 shadow-lg">
                <form method="GET" action="" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Vai trò</label>
                        <select name="role" class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 dark:border-gray-700 bg-white dark:bg-sport-navy text-gray-900 dark:text-white focus:border-sport-neon transition-all">
                            <option value="all" <?php echo $roleFilter === 'all' ? 'selected' : ''; ?>>Tất cả</option>
                            <option value="admin" <?php echo $roleFilter === 'admin' ? 'selected' : ''; ?>>Quản trị viên</option>
                            <option value="customer" <?php echo $roleFilter === 'customer' ? 'selected' : ''; ?>>Khách hàng</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Trạng thái</label>
                        <select name="status" class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 dark:border-gray-700 bg-white dark:bg-sport-navy text-gray-900 dark:text-white focus:border-sport-neon transition-all">
                            <option value="all" <?php echo $statusFilter === 'all' ? 'selected' : ''; ?>>Tất cả</option>
                            <option value="1" <?php echo $statusFilter === '1' ? 'selected' : ''; ?>>Hoạt động</option>
                            <option value="0" <?php echo $statusFilter === '0' ? 'selected' : ''; ?>>Đã khóa</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Tìm kiếm</label>
                        <input type="text" name="search" 
                               placeholder="Tìm theo tên, email, SĐT..."
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

            <!-- Users Table -->
            <div class="modern-card bg-white dark:bg-sport-blue rounded-2xl shadow-lg overflow-hidden">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">
                        Danh sách người dùng (<?php echo count($users); ?>)
                    </h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gradient-to-r from-sport-neon to-blue-600 text-white">
                            <tr>
                                <th class="px-6 py-4 text-left font-bold">ID</th>
                                <th class="px-6 py-4 text-left font-bold">Họ tên</th>
                                <th class="px-6 py-4 text-left font-bold">Email</th>
                                <th class="px-6 py-4 text-left font-bold">SĐT</th>
                                <th class="px-6 py-4 text-left font-bold">Vai trò</th>
                                <th class="px-6 py-4 text-left font-bold">Trạng thái</th>
                                <th class="px-6 py-4 text-center font-bold">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $index => $user): ?>
                            <tr class="border-b border-gray-100 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-sport-navy transition-colors animate-slide-up" style="animation-delay: <?php echo $index * 0.05; ?>s;">
                                <td class="px-6 py-4">
                                    <span class="font-bold text-sport-neon">#<?php echo $user['UserID']; ?></span>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="font-semibold text-gray-900 dark:text-white">
                                        <?php echo htmlspecialchars($user['FirstName'] . ' ' . $user['LastName']); ?>
                                    </p>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                                    <?php echo htmlspecialchars($user['Email']); ?>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                                    <?php echo htmlspecialchars($user['Phone'] ?? '-'); ?>
                                </td>
                                <td class="px-6 py-4">
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="user_id" value="<?php echo $user['UserID']; ?>">
                                        <input type="hidden" name="action" value="update_role">
                                        <select name="new_role" 
                                                class="px-3 py-2 text-xs rounded-lg border-2 border-gray-200 dark:border-gray-700 bg-white dark:bg-sport-navy text-gray-900 dark:text-white focus:border-sport-neon transition-all cursor-pointer"
                                                onchange="if(confirm('Cập nhật vai trò?')) this.form.submit();"
                                                <?php echo $user['UserID'] == $_SESSION['user_id'] ? 'disabled' : ''; ?>>
                                            <option value="customer" <?php echo $user['Role'] === 'customer' ? 'selected' : ''; ?>>Khách hàng</option>
                                            <option value="admin" <?php echo $user['Role'] === 'admin' ? 'selected' : ''; ?>>Admin</option>
                                        </select>
                                    </form>
                                </td>
                                <td class="px-6 py-4">
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="user_id" value="<?php echo $user['UserID']; ?>">
                                        <input type="hidden" name="action" value="update_status">
                                        <select name="new_status" 
                                                class="px-3 py-2 text-xs rounded-lg border-2 border-gray-200 dark:border-gray-700 bg-white dark:bg-sport-navy text-gray-900 dark:text-white focus:border-sport-neon transition-all cursor-pointer"
                                                onchange="if(confirm('Cập nhật trạng thái?')) this.form.submit();">
                                            <option value="1" <?php echo $user['Status'] == 1 ? 'selected' : ''; ?>>✓ Hoạt động</option>
                                            <option value="0" <?php echo $user['Status'] == 0 ? 'selected' : ''; ?>>✗ Đã khóa</option>
                                        </select>
                                    </form>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-center">
                                        <?php if ($user['UserID'] != $_SESSION['user_id']): ?>
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="user_id" value="<?php echo $user['UserID']; ?>">
                                            <input type="hidden" name="action" value="delete">
                                            <button type="submit" 
                                                    class="p-2 rounded-lg glass hover:bg-red-500 hover:text-white transition-all duration-300"
                                                    onclick="return confirm('Xóa người dùng này?')"
                                                    title="Xóa">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                        <?php else: ?>
                                        <span class="text-gray-400 dark:text-gray-600 text-sm italic">Tài khoản hiện tại</span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>

<?php include '../includes/layout_footer.php'; ?>
