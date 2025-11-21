<?php
require_once '../config.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('login.php');
}

$db = Database::getInstance()->getConnection();

// Handle coupon actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    // SỬA 1: Dùng tên cột PascalCase
    if ($action === 'add') {
        $voucherCode = strtoupper(trim($_POST['VoucherCode']));
        $discountValue = (float)$_POST['DiscountValue'];
        $minOrderValue = (float)$_POST['MinOrderValue'];
        $startDate = $_POST['StartDate'];
        $endDate = $_POST['EndDate'];
        $quantity = (int)$_POST['Quantity'];
        
        // SỬA 2: Sửa tên Bảng và Cột.
        $stmt = $db->prepare("INSERT INTO Voucher (VoucherCode, DiscountValue, StartDate, EndDate, Quantity, MinOrderValue) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$voucherCode, $discountValue, $startDate, $endDate, $quantity, $minOrderValue]);
        $message = 'Đã thêm mã giảm giá mới';

    } elseif ($action === 'edit') {
        $voucherId = (int)$_POST['VoucherID'];
        $voucherCode = strtoupper(trim($_POST['VoucherCode']));
        $discountValue = (float)$_POST['DiscountValue'];
        $minOrderValue = (float)$_POST['MinOrderValue'];
        $startDate = $_POST['StartDate'];
        $endDate = $_POST['EndDate'];
        $quantity = (int)$_POST['Quantity'];
        
        // SỬA 3: Sửa tên Bảng và Cột.
        $stmt = $db->prepare("UPDATE Voucher SET VoucherCode = ?, DiscountValue = ?, StartDate = ?, EndDate = ?, Quantity = ?, MinOrderValue = ? WHERE VoucherID = ?");
        $stmt->execute([$voucherCode, $discountValue, $startDate, $endDate, $quantity, $minOrderValue, $voucherId]);
        $message = 'Đã cập nhật mã giảm giá';

    } elseif ($action === 'delete') {
        $voucherId = (int)$_POST['VoucherID'];
        // SỬA 4: Sửa tên Bảng và Cột.
        $stmt = $db->prepare("DELETE FROM Voucher WHERE VoucherID = ?");
        $stmt->execute([$voucherId]);
        $message = 'Đã xóa mã giảm giá';
    }
    
    header('Location: admin_coupons.php?message=' . urlencode($message));
    exit;
}

// SỬA 5: Xóa logic lọc 'status'
$sql = "SELECT * FROM Voucher ORDER BY created_at DESC";
$stmt = $db->prepare($sql);
$stmt->execute();
$coupons = $stmt->fetchAll();

// SỬA 6: Sửa thống kê
$stmt = $db->query("SELECT COUNT(*) as total FROM Voucher");
$totalCoupons = $stmt->fetch()['total'];

$stmt = $db->query("SELECT COUNT(*) as total FROM Voucher WHERE EndDate >= CURDATE()");
$activeCoupons = $stmt->fetch()['total'];

$stmt = $db->query("SELECT COUNT(*) as total FROM Voucher WHERE EndDate < CURDATE()");
$expiredCoupons = $stmt->fetch()['total'];
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý mã giảm giá - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .sidebar { min-height: 100vh; background: linear-gradient(135deg, #2563eb, #1e40af); color: white; }
        .sidebar .nav-link { color: rgba(255,255,255,0.8); padding: 15px 20px; transition: all 0.3s; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { background: rgba(255,255,255,0.1); color: white; }
        .stat-card { border-radius: 10px; padding: 20px; margin-bottom: 20px; transition: transform 0.3s; }
        .stat-card:hover { transform: translateY(-5px); }
        .coupon-code { font-family: 'Courier New', monospace; font-weight: bold; background: #f8f9fa; padding: 5px 10px; border-radius: 5px; border: 2px dashed #dee2e6; }
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
                    <a class="nav-link" href="admin_reviews.php"><i class="fas fa-star"></i> Đánh giá</a>
                    <a class="nav-link active" href="admin_coupons.php"><i class="fas fa-ticket-alt"></i> Mã giảm giá</a>
                    <hr class="bg-white">
                    <a class="nav-link" href="../index.php"><i class="fas fa-home"></i> Về trang chủ</a>
                    <a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt"></i> Đăng xuất</a>
                </nav>
            </div>
            
            <div class="col-md-10 p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2><i class="fas fa-ticket-alt"></i> Quản lý mã giảm giá</h2>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
                        <i class="fas fa-plus"></i> Thêm mã giảm giá
                    </button>
                </div>
                
                <?php if (isset($_GET['message'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($_GET['message']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="stat-card bg-primary text-white">
                            <h6 class="text-uppercase mb-2">Tổng mã giảm giá</h6>
                            <h3 class="mb-0"><?php echo number_format($totalCoupons); ?></h3>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-card bg-success text-white">
                            <h6 class="text-uppercase mb-2">Còn hạn</h6>
                            <h3 class="mb-0"><?php echo number_format($activeCoupons); ?></h3>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-card bg-secondary text-white">
                            <h6 class="text-uppercase mb-2">Đã hết hạn</h6>
                            <h3 class="mb-0"><?php echo number_format($expiredCoupons); ?></h3>
                        </div>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Danh sách mã giảm giá (<?php echo count($coupons); ?>)</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Mã</th>
                                        <th>Giá trị giảm (VNĐ)</th>
                                        <th>Đơn tối thiểu</th>
                                        <th>Số lượng</th>
                                        <th>Thời gian</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($coupons)): ?>
                                        <tr>
                                            <td colspan="7" class="text-center text-muted py-4">
                                                <i class="fas fa-inbox fa-2x mb-2"></i>
                                                <p>Không có mã giảm giá nào</p>
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($coupons as $coupon): ?>
                                        <tr>
                                            <td><?php echo $coupon['VoucherID']; ?></td>
                                            <td><span class="coupon-code"><?php echo htmlspecialchars($coupon['VoucherCode']); ?></span></td>
                                            <td><strong><?php echo formatPrice($coupon['DiscountValue']); ?></strong></td>
                                            <td><?php echo formatPrice($coupon['MinOrderValue']); ?></td>
                                            <td><?php echo number_format($coupon['Quantity']); ?></td>
                                            <td>
                                                <small>
                                                    <?php echo date('d/m/Y', strtotime($coupon['StartDate'])); ?><br>
                                                    đến<br>
                                                    <?php echo date('d/m/Y', strtotime($coupon['EndDate'])); ?>
                                                </small>
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-warning" onclick="editCoupon(<?php echo htmlspecialchars(json_encode($coupon)); ?>)">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <form method="POST" style="display: inline;">
                                                    <input type="hidden" name="action" value="delete">
                                                    <input type="hidden" name="VoucherID" value="<?php echo $coupon['VoucherID']; ?>">
                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Xóa mã giảm giá này?')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="modal fade" id="addModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title">Thêm mã giảm giá mới</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="action" value="add">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Mã giảm giá *</label>
                                <input type="text" name="VoucherCode" class="form-control" placeholder="VD: SALE50K" required style="text-transform: uppercase;">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Giá trị giảm (VNĐ) *</label>
                                <input type="number" name="DiscountValue" class="form-control" min="0" step="1000" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Đơn hàng tối thiểu (VNĐ) *</label>
                                <input type="number" name="MinOrderValue" class="form-control" min="0" step="1000" value="0" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Số lượng *</label>
                                <input type="number" name="Quantity" class="form-control" min="0" step="1" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Ngày bắt đầu *</label>
                                <input type="date" name="StartDate" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Ngày kết thúc *</label>
                                <input type="date" name="EndDate" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-primary">Thêm</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="modal fade" id="editModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title">Sửa mã giảm giá</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="action" value="edit">
                        <input type="hidden" name="VoucherID" id="edit_VoucherID">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Mã giảm giá *</label>
                                <input type="text" name="VoucherCode" id="edit_VoucherCode" class="form-control" required style="text-transform: uppercase;">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Giá trị giảm (VNĐ) *</label>
                                <input type="number" name="DiscountValue" id="edit_DiscountValue" class="form-control" min="0" step="1000" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Đơn hàng tối thiểu (VNĐ) *</label>
                                <input type="number" name="MinOrderValue" id="edit_MinOrderValue" class="form-control" min="0" step="1000" required>
                            </div>
                             <div class="col-md-6 mb-3">
                                <label class="form-label">Số lượng *</label>
                                <input type="number" name="Quantity" id="edit_Quantity" class="form-control" min="0" step="1" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Ngày bắt đầu *</label>
                                <input type="date" name="StartDate" id="edit_StartDate" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Ngày kết thúc *</label>
                                <input type="date" name="EndDate" id="edit_EndDate" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-primary">Cập nhật</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    // SỬA 11: Sửa hàm JavaScript
    function editCoupon(coupon) {
        document.getElementById('edit_VoucherID').value = coupon.VoucherID;
        document.getElementById('edit_VoucherCode').value = coupon.VoucherCode;
        document.getElementById('edit_DiscountValue').value = coupon.DiscountValue;
        document.getElementById('edit_MinOrderValue').value = coupon.MinOrderValue;
        document.getElementById('edit_Quantity').value = coupon.Quantity;
        document.getElementById('edit_StartDate').value = coupon.StartDate;
        document.getElementById('edit_EndDate').value = coupon.EndDate;
        new bootstrap.Modal(document.getElementById('editModal')).show();
    }
    </I>
</body>
</html>