<?php
require_once '../config.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('login.php');
}

$db = Database::getInstance()->getConnection();

// Handle review status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    // SỬA 1: Dùng tên cột PascalCase
    $reviewId = (int)$_POST['ReviewID'];
    $action = $_POST['action'];
    
    if ($action === 'approve') {
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
    }
    
    header('Location: admin_reviews.php?message=' . urlencode($message));
    exit;
}

// Get filter parameters
$statusFilter = $_GET['status'] ?? 'all';
$searchQuery = $_GET['search'] ?? '';

// SỬA 2: Viết lại câu JOIN và tên cột
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
    // SỬA 3: Cập nhật logic tìm kiếm
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

// SỬA 4: Sửa các truy vấn thống kê
$stmt = $db->query("SELECT COUNT(*) as total FROM Reviews");
$totalReviews = $stmt->fetch()['total'];

$stmt = $db->query("SELECT COUNT(*) as total FROM Reviews WHERE Status = 'pending'");
$pendingReviews = $stmt->fetch()['total'];

$stmt = $db->query("SELECT COUNT(*) as total FROM Reviews WHERE Status = 'approved'");
$approvedReviews = $stmt->fetch()['total'];

$stmt = $db->query("SELECT COUNT(*) as total FROM Reviews WHERE Status = 'hidden'");
$hiddenReviews = $stmt->fetch()['total'];
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý bình luận - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .sidebar { min-height: 100vh; background: linear-gradient(135deg, #2563eb, #1e40af); color: white; }
        .sidebar .nav-link { color: rgba(255,255,255,0.8); padding: 15px 20px; transition: all 0.3s; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { background: rgba(255,255,255,0.1); color: white; }
        .stat-card { border-radius: 10px; padding: 20px; margin-bottom: 20px; transition: transform 0.3s; }
        .stat-card:hover { transform: translateY(-5px); }
        .review-card { border-radius: 10px; margin-bottom: 15px; transition: all 0.3s; }
        .review-card:hover { box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        .product-thumb { width: 60px; height: 60px; object-fit: cover; border-radius: 8px; }
        .rating-stars { color: #ffc107; }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-2 sidebar p-0">
                <div class="p-4">
                    <h4 class="mb-4"><i class="fas fa-running"></i> SportShop Admin</h4>
                    <p class="small">Xin chào, <?php echo htmlspecialchars($_SESSION['full_name']); ?></p>
                </div>
                <nav class="nav flex-column">
                    <a class="nav-link" href="admin_dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                    <a class="nav-link" href="admin_products.php"><i class="fas fa-box"></i> Quản lý sản phẩm</a>
                    <a class="nav-link" href="admin_orders.php"><i class="fas fa-shopping-cart"></i> Quản lý đơn hàng</a>
                    <a class="nav-link" href="admin_users.php"><i class="fas fa-users"></i> Quản lý người dùng</a>
                    <a class="nav-link" href="admin_categories.php"><i class="fas fa-list"></i> Danh mục</a>
                    <a class="nav-link" href="admin_brands.php"><i class="fas fa-tag"></i> Thương hiệu</a>
                    <a class="nav-link active" href="admin_reviews.php"><i class="fas fa-star"></i> Đánh giá</a>
                    <a class="nav-link" href="admin_coupons.php"><i class="fas fa-ticket-alt"></i> Mã giảm giá</a>
                    <hr class="bg-white">
                    <a class="nav-link" href="../index.php"><i class="fas fa-home"></i> Về trang chủ</a>
                    <a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt"></i> Đăng xuất</a>
                </nav>
            </div>
            
            <div class="col-md-10 p-4">
                <h2 class="mb-4"><i class="fas fa-star"></i> Quản lý bình luận & đánh giá</h2>
                
                <?php if (isset($_GET['message'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($_GET['message']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <div class="row mb-4">
                    <div class="col-md-3"><div class="stat-card bg-primary text-white"><h6 class="text-uppercase mb-2">Tổng bình luận</h6><h3 class="mb-0"><?php echo number_format($totalReviews); ?></h3></div></div>
                    <div class="col-md-3"><div class="stat-card bg-warning text-white"><h6 class="text-uppercase mb-2">Chờ duyệt</h6><h3 class="mb-0"><?php echo number_format($pendingReviews); ?></h3></div></div>
                    <div class="col-md-3"><div class="stat-card bg-success text-white"><h6 class="text-uppercase mb-2">Đã duyệt</h6><h3 class="mb-0"><?php echo number_format($approvedReviews); ?></h3></div></div>
                    <div class="col-md-3"><div class="stat-card bg-secondary text-white"><h6 class="text-uppercase mb-2">Đã ẩn</h6><h3 class="mb-0"><?php echo number_format($hiddenReviews); ?></h3></div></div>
                </div>
                
                <div class="card mb-4">
                    <div class="card-body">
                        <form method="GET" action="" class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Trạng thái</label>
                                <select name="status" class="form-select">
                                    <option value="all" <?php echo $statusFilter === 'all' ? 'selected' : ''; ?>>Tất cả</option>
                                    <option value="pending" <?php echo $statusFilter === 'pending' ? 'selected' : ''; ?>>Chờ duyệt</option>
                                    <option value="approved" <?php echo $statusFilter === 'approved' ? 'selected' : ''; ?>>Đã duyệt</option>
                                    <option value="hidden" <?php echo $statusFilter === 'hidden' ? 'selected' : ''; ?>>Đã ẩn</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Tìm kiếm</label>
                                <input type="text" name="search" class="form-control" 
                                       placeholder="Tìm theo tên người dùng, sản phẩm hoặc nội dung..."
                                       value="<?php echo htmlspecialchars($searchQuery); ?>">
                            </div>
                            <div class="col-md-2"><label class="form-label">&nbsp;</label><button type="submit" class="btn btn-primary w-100"><i class="fas fa-search"></i> Lọc</button></div>
                        </form>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Danh sách bình luận (<?php echo count($reviews); ?>)</h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($reviews)): ?>
                            <div class="text-center text-muted py-5"><i class="fas fa-inbox fa-3x mb-3"></i><p>Không có bình luận nào</p></div>
                        <?php else: ?>
                            <?php foreach ($reviews as $review): ?>
                            <div class="card review-card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-1">
                                            <img src="<?php echo $review['MainImage'] ?: 'https://via.placeholder.com/60'; ?>" 
                                                 class="product-thumb" alt="Product">
                                        </div>
                                        <div class="col-md-8">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <div>
                                                    <h6 class="mb-1">
                                                        <a href="product_detail.php?id=<?php echo $review['ProductID']; ?>" 
                                                           class="text-decoration-none" target="_blank">
                                                            <?php echo htmlspecialchars($review['ProductName']); ?>
                                                        </a>
                                                    </h6>
                                                    <div class="rating-stars mb-1">
                                                        <?php for ($i = 0; $i < 5; $i++): ?>
                                                            <i class="fas fa-star <?php echo $i < $review['Rating'] ? '' : 'text-muted'; ?>"></i>
                                                        <?php endfor; ?>
                                                    </div>
                                                    <small class="text-muted">
                                                        <i class="fas fa-user"></i> <?php echo htmlspecialchars($review['FullName']); ?> 
                                                        (<?php echo htmlspecialchars($review['Email']); ?>)
                                                        <span class="mx-2">|</span>
                                                        <i class="fas fa-clock"></i> <?php echo date('d/m/Y H:i', strtotime($review['created_at'])); ?>
                                                    </small>
                                                </div>
                                                <div>
                                                    <?php
                                                    $statusBadges = [
                                                        'pending' => '<span class="badge bg-warning">Chờ duyệt</span>',
                                                        'approved' => '<span class="badge bg-success">Đã duyệt</span>',
                                                        'hidden' => '<span class="badge bg-secondary">Đã ẩn</span>'
                                                    ];
                                                    echo $statusBadges[$review['Status']];
                                                    ?>
                                                </div>
                                            </div>
                                            <p class="mb-0"><?php echo nl2br(htmlspecialchars($review['Content'])); ?></p>
                                        </div>
                                        <div class="col-md-3 text-end">
                                            <div class="btn-group-vertical" role="group">
                                                <?php if ($review['Status'] !== 'approved'): ?>
                                                <form method="POST" style="display: inline;">
                                                    <input type="hidden" name="ReviewID" value="<?php echo $review['ReviewID']; ?>">
                                                    <input type="hidden" name="action" value="approve">
                                                    <button type="submit" class="btn btn-sm btn-success mb-1" 
                                                            onclick="return confirm('Duyệt bình luận này?')">
                                                        <i class="fas fa-check"></i> Duyệt
                                                    </button>
                                                </form>
                                                <?php endif; ?>
                                                
                                                <?php if ($review['Status'] !== 'hidden'): ?>
                                                <form method="POST" style="display: inline;">
                                                    <input type="hidden" name="ReviewID" value="<?php echo $review['ReviewID']; ?>">
                                                    <input type="hidden" name="action" value="hide">
                                                    <button type="submit" class="btn btn-sm btn-warning mb-1"
                                                            onclick="return confirm('Ẩn bình luận này?')">
                                                        <i class="fas fa-eye-slash"></i> Ẩn
                                                    </button>
                                                </form>
                                                <?php endif; ?>
                                                
                                                <form method="POST" style="display: inline;">
                                                    <input type="hidden" name="ReviewID" value="<?php echo $review['ReviewID']; ?>">
                                                    <input type="hidden" name="action" value="delete">
                                                    <button type="submit" class="btn btn-sm btn-danger"
                                                            onclick="return confirm('Xóa vĩnh viễn bình luận này?')">
                                                        <i class="fas fa-trash"></i> Xóa
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>