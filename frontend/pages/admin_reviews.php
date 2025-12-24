<?php
require_once '../config.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('login.php');
}

<<<<<<< HEAD
$message = '';
=======
$db = Database::getInstance()->getConnection();
>>>>>>> 3d6d58ed3875cc3c551e3fe1991339ab7637c345

// Handle review status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $reviewId = (int)$_POST['ReviewID'];
    $action = $_POST['action'];
    
    if ($action === 'approve') {
<<<<<<< HEAD
        // Use backend API to approve review
        $response = makeApiRequest('/reviews/' . $reviewId . '/status', 'PUT', [
            'status' => 'approved'
        ]);
        
        if ($response['success']) {
            $message = 'Đã duyệt bình luận';
        } else {
            $message = 'Lỗi: ' . ($response['message'] ?? 'Không thể duyệt bình luận');
        }
    } elseif ($action === 'hide') {
        // Use backend API to hide review
        $response = makeApiRequest('/reviews/' . $reviewId . '/status', 'PUT', [
            'status' => 'hidden'
        ]);
        
        if ($response['success']) {
            $message = 'Đã ẩn bình luận';
        } else {
            $message = 'Lỗi: ' . ($response['message'] ?? 'Không thể ẩn bình luận');
        }
    } elseif ($action === 'delete') {
        // Use backend API to delete review
        $response = makeApiRequest('/reviews/' . $reviewId, 'DELETE');
        
        if ($response['success']) {
            $message = 'Đã xóa bình luận';
        } else {
            $message = 'Lỗi: ' . ($response['message'] ?? 'Không thể xóa bình luận');
        }
=======
        $stmt = $db->prepare("UPDATE Reviews SET Status = 'approved' WHERE ReviewID = ?");
        $stmt->execute([$reviewId]);
        $message = 'Đã duyệt bình luận';
    } elseif ($action === 'hide') {
        $stmt = $db->prepare("UPDATE Reviews SET Status = 'hidden' WHERE ReviewID = ?");
        $stmt->execute([$reviewId]);
        $message = 'Đã ẩn bình luận';
    } elseif ($action === 'delete') {
        $stmt = $db->prepare("DELETE FROM Reviews WHERE ReviewID = ?");
        $stmt->execute([$reviewId]);
        $message = 'Đã xóa bình luận';
>>>>>>> 3d6d58ed3875cc3c551e3fe1991339ab7637c345
    }
    
    header('Location: admin_reviews.php?message=' . urlencode($message));
    exit;
}

<<<<<<< HEAD
// Get reviews from API
$reviewsResponse = makeApiRequest('/reviews');
$reviews = $reviewsResponse['success'] ? $reviewsResponse['data']['reviews'] ?? [] : [];

=======
>>>>>>> 3d6d58ed3875cc3c551e3fe1991339ab7637c345
// Get filter parameters
$statusFilter = $_GET['status'] ?? 'all';
$searchQuery = $_GET['search'] ?? '';

<<<<<<< HEAD
// Calculate statistics from reviews data
$totalReviews = count($reviews);
$pendingReviews = count(array_filter($reviews, function($review) { return $review['Status'] === 'pending'; }));
$approvedReviews = count(array_filter($reviews, function($review) { return $review['Status'] === 'approved'; }));
$hiddenReviews = count(array_filter($reviews, function($review) { return $review['Status'] === 'hidden'; }));
=======
$sql = "
    SELECT r.*, CONCAT(u.FirstName, ' ', u.LastName) as FullName, u.Email, p.ProductName, p.MainImage
    FROM Reviews r
    JOIN Users u ON r.UserID = u.UserID
    JOIN Product p ON r.ProductID = p.ProductID
    WHERE 1=1
";

$params = [];

if ($statusFilter !== 'all') {
    $sql .= " AND r.Status = ?";
    $params[] = $statusFilter;
}

if (!empty($searchQuery)) {
    $sql .= " AND (CONCAT(u.FirstName, ' ', u.LastName) LIKE ? OR p.ProductName LIKE ? OR r.Content LIKE ?)";
    $searchParam = "%$searchQuery%";
    $params[] = $searchParam;
    $params[] = $searchParam;
    $params[] = $searchParam;
}

$sql .= " ORDER BY r.created_at DESC";

$stmt = $db->prepare($sql);
$stmt->execute($params);
$reviews = $stmt->fetchAll();

$stmt = $db->query("SELECT COUNT(*) as total FROM Reviews");
$totalReviews = $stmt->fetch()['total'];

$stmt = $db->query("SELECT COUNT(*) as total FROM Reviews WHERE Status = 'pending'");
$pendingReviews = $stmt->fetch()['total'];

$stmt = $db->query("SELECT COUNT(*) as total FROM Reviews WHERE Status = 'approved'");
$approvedReviews = $stmt->fetch()['total'];

$stmt = $db->query("SELECT COUNT(*) as total FROM Reviews WHERE Status = 'hidden'");
$hiddenReviews = $stmt->fetch()['total'];
>>>>>>> 3d6d58ed3875cc3c551e3fe1991339ab7637c345

$pageTitle = "Quản lý đánh giá";
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
            <i class="fas fa-chart-line w-5"></i><span>Dashboard</span>
        </a>
        <a href="admin_products.php" class="flex items-center space-x-3 px-4 py-3 rounded-xl glass hover:bg-gray-100 dark:hover:bg-sport-blue text-gray-700 dark:text-gray-300 transition-all duration-300">
            <i class="fas fa-box w-5"></i><span>Sản phẩm</span>
        </a>
        <a href="admin_orders.php" class="flex items-center space-x-3 px-4 py-3 rounded-xl glass hover:bg-gray-100 dark:hover:bg-sport-blue text-gray-700 dark:text-gray-300 transition-all duration-300">
            <i class="fas fa-shopping-cart w-5"></i><span>Đơn hàng</span>
        </a>
        <a href="admin_users.php" class="flex items-center space-x-3 px-4 py-3 rounded-xl glass hover:bg-gray-100 dark:hover:bg-sport-blue text-gray-700 dark:text-gray-300 transition-all duration-300">
            <i class="fas fa-users w-5"></i><span>Người dùng</span>
        </a>
        <a href="admin_categories.php" class="flex items-center space-x-3 px-4 py-3 rounded-xl glass hover:bg-gray-100 dark:hover:bg-sport-blue text-gray-700 dark:text-gray-300 transition-all duration-300">
            <i class="fas fa-th-large w-5"></i><span>Danh mục</span>
        </a>
        <a href="admin_brands.php" class="flex items-center space-x-3 px-4 py-3 rounded-xl glass hover:bg-gray-100 dark:hover:bg-sport-blue text-gray-700 dark:text-gray-300 transition-all duration-300">
            <i class="fas fa-tag w-5"></i><span>Thương hiệu</span>
        </a>
        <a href="admin_coupons.php" class="flex items-center space-x-3 px-4 py-3 rounded-xl glass hover:bg-gray-100 dark:hover:bg-sport-blue text-gray-700 dark:text-gray-300 transition-all duration-300">
            <i class="fas fa-ticket-alt w-5"></i><span>Mã giảm giá</span>
        </a>
        <a href="admin_reviews.php" class="flex items-center space-x-3 px-4 py-3 rounded-xl bg-gradient-to-r from-sport-neon to-blue-600 text-white font-semibold">
            <i class="fas fa-star w-5"></i><span>Đánh giá</span>
        </a>
        
        <div class="pt-4 mt-4 border-t border-gray-200 dark:border-gray-700">
            <a href="../index.php" class="flex items-center space-x-3 px-4 py-3 rounded-xl glass hover:bg-gray-100 dark:hover:bg-sport-blue text-gray-700 dark:text-gray-300 transition-all duration-300">
                <i class="fas fa-home w-5"></i><span>Về trang chủ</span>
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
                    <i class="fas fa-star mr-3"></i>Quản lý đánh giá
                </h1>
                <p class="text-gray-300">Duyệt và quản lý đánh giá sản phẩm</p>
            </div>
        </div>
    </section>

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
                <div class="modern-card bg-gradient-to-br from-blue-500 to-blue-700 rounded-2xl p-6 text-white shadow-lg animate-slide-up">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <p class="text-sm text-blue-100 mb-1">Tổng bình luận</p>
                            <h3 class="text-3xl font-black"><?php echo number_format($totalReviews); ?></h3>
                        </div>
                        <div class="w-16 h-16 rounded-full bg-white/20 flex items-center justify-center">
                            <i class="fas fa-comments text-3xl"></i>
                        </div>
                    </div>
                </div>
                
                <div class="modern-card bg-gradient-to-br from-yellow-500 to-orange-600 rounded-2xl p-6 text-white shadow-lg animate-slide-up" style="animation-delay: 0.1s;">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <p class="text-sm text-yellow-100 mb-1">Chờ duyệt</p>
                            <h3 class="text-3xl font-black"><?php echo number_format($pendingReviews); ?></h3>
                        </div>
                        <div class="w-16 h-16 rounded-full bg-white/20 flex items-center justify-center">
                            <i class="fas fa-clock text-3xl"></i>
                        </div>
                    </div>
                </div>
                
                <div class="modern-card bg-gradient-to-br from-green-500 to-emerald-700 rounded-2xl p-6 text-white shadow-lg animate-slide-up" style="animation-delay: 0.2s;">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <p class="text-sm text-green-100 mb-1">Đã duyệt</p>
                            <h3 class="text-3xl font-black"><?php echo number_format($approvedReviews); ?></h3>
                        </div>
                        <div class="w-16 h-16 rounded-full bg-white/20 flex items-center justify-center">
                            <i class="fas fa-check-circle text-3xl"></i>
                        </div>
                    </div>
                </div>
                
                <div class="modern-card bg-gradient-to-br from-gray-500 to-gray-700 rounded-2xl p-6 text-white shadow-lg animate-slide-up" style="animation-delay: 0.3s;">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <p class="text-sm text-gray-100 mb-1">Đã ẩn</p>
                            <h3 class="text-3xl font-black"><?php echo number_format($hiddenReviews); ?></h3>
                        </div>
                        <div class="w-16 h-16 rounded-full bg-white/20 flex items-center justify-center">
                            <i class="fas fa-eye-slash text-3xl"></i>
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
                            <option value="pending" <?php echo $statusFilter === 'pending' ? 'selected' : ''; ?>>Chờ duyệt</option>
                            <option value="approved" <?php echo $statusFilter === 'approved' ? 'selected' : ''; ?>>Đã duyệt</option>
                            <option value="hidden" <?php echo $statusFilter === 'hidden' ? 'selected' : ''; ?>>Đã ẩn</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Tìm kiếm</label>
                        <input type="text" name="search" 
                               placeholder="Tìm theo tên, sản phẩm, nội dung..."
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

            <!-- Reviews List -->
            <div class="space-y-4">
                <?php if (empty($reviews)): ?>
                    <div class="modern-card bg-white dark:bg-sport-blue rounded-2xl p-12 text-center">
                        <i class="fas fa-inbox text-6xl text-gray-300 dark:text-gray-600 mb-4"></i>
                        <p class="text-gray-500 dark:text-gray-400">Không có bình luận nào</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($reviews as $index => $review): ?>
                    <div class="modern-card bg-white dark:bg-sport-blue rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all duration-300 animate-slide-up" style="animation-delay: <?php echo $index * 0.05; ?>s;">
                        <div class="flex gap-4">
                            <!-- Product Image -->
                            <div class="flex-shrink-0">
                                <img src="<?php echo $review['MainImage'] ?: 'https://via.placeholder.com/80'; ?>" 
                                     alt="Product"
                                     class="w-20 h-20 rounded-xl object-cover">
                            </div>
                            
                            <!-- Review Content -->
                            <div class="flex-1">
                                <div class="flex items-start justify-between mb-3">
                                    <div>
                                        <h4 class="font-bold text-gray-900 dark:text-white mb-1">
                                            <a href="product_detail.php?id=<?php echo $review['ProductID']; ?>" 
                                               class="hover:text-sport-neon transition-colors"
                                               target="_blank">
                                                <?php echo htmlspecialchars($review['ProductName']); ?>
                                            </a>
                                        </h4>
                                        <div class="flex items-center gap-2 mb-2">
                                            <div class="flex text-yellow-400">
                                                <?php for ($i = 0; $i < 5; $i++): ?>
                                                    <i class="fas fa-star <?php echo $i < $review['Rating'] ? '' : 'text-gray-300 dark:text-gray-600'; ?>"></i>
                                                <?php endfor; ?>
                                            </div>
                                            <span class="text-sm text-gray-600 dark:text-gray-400">
                                                <?php echo $review['Rating']; ?>/5
                                            </span>
                                        </div>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">
                                            <i class="fas fa-user mr-1"></i>
                                            <?php echo htmlspecialchars($review['FullName']); ?>
                                            <span class="mx-2">|</span>
                                            <i class="fas fa-clock mr-1"></i>
                                            <?php echo date('d/m/Y H:i', strtotime($review['created_at'])); ?>
                                        </p>
                                    </div>
                                    <div>
                                        <?php
                                        $statusBadges = [
                                            'pending' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-300',
                                            'approved' => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300',
                                            'hidden' => 'bg-gray-100 text-gray-700 dark:bg-gray-900/30 dark:text-gray-300'
                                        ];
                                        $statusTexts = [
                                            'pending' => 'Chờ duyệt',
                                            'approved' => 'Đã duyệt',
                                            'hidden' => 'Đã ẩn'
                                        ];
                                        ?>
                                        <span class="px-3 py-1 rounded-full text-xs font-semibold <?php echo $statusBadges[$review['Status']]; ?>">
                                            <?php echo $statusTexts[$review['Status']]; ?>
                                        </span>
                                    </div>
                                </div>
                                
                                <p class="text-gray-700 dark:text-gray-300 mb-4">
                                    <?php echo nl2br(htmlspecialchars($review['Content'] ?? 'Không có bình luận')); ?>
                                </p>
                                
                                <!-- Actions -->
                                <div class="flex gap-2">
                                    <?php if ($review['Status'] !== 'approved'): ?>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="ReviewID" value="<?php echo $review['ReviewID']; ?>">
                                        <input type="hidden" name="action" value="approve">
                                        <button type="submit" 
                                                class="px-4 py-2 rounded-lg glass hover:bg-green-500 hover:text-white transition-all duration-300"
                                                onclick="return confirm('Duyệt bình luận này?')">
                                            <i class="fas fa-check mr-2"></i>Duyệt
                                        </button>
                                    </form>
                                    <?php endif; ?>
                                    
                                    <?php if ($review['Status'] !== 'hidden'): ?>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="ReviewID" value="<?php echo $review['ReviewID']; ?>">
                                        <input type="hidden" name="action" value="hide">
                                        <button type="submit" 
                                                class="px-4 py-2 rounded-lg glass hover:bg-yellow-500 hover:text-white transition-all duration-300"
                                                onclick="return confirm('Ẩn bình luận này?')">
                                            <i class="fas fa-eye-slash mr-2"></i>Ẩn
                                        </button>
                                    </form>
                                    <?php endif; ?>
                                    
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="ReviewID" value="<?php echo $review['ReviewID']; ?>">
                                        <input type="hidden" name="action" value="delete">
                                        <button type="submit" 
                                                class="px-4 py-2 rounded-lg glass hover:bg-red-500 hover:text-white transition-all duration-300"
                                                onclick="return confirm('Xóa vĩnh viễn bình luận này?')">
                                            <i class="fas fa-trash mr-2"></i>Xóa
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </section>
</div>

<?php include '../includes/layout_footer.php'; ?>
