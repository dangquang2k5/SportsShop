<?php
require_once '../config.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('login.php');
}

$db = Database::getInstance()->getConnection();
$message = '';
$messageType = 'success';

// Handle category actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    try {
        if ($action === 'add') {
            $categoryName = trim($_POST['CategoryName']);
            $description = trim($_POST['CategoryDescription']);
            
            if (empty($categoryName)) {
                throw new Exception('Tên danh mục không được để trống');
            }
            
            $stmt = $db->prepare("INSERT INTO Categories (CategoryName, CategoryDescription) VALUES (?, ?)");
            $stmt->execute([$categoryName, $description]);
            $message = 'Thêm danh mục thành công!';

        } elseif ($action === 'edit') {
            $categoryId = (int)$_POST['CategoryID'];
            $categoryName = trim($_POST['CategoryName']);
            $description = trim($_POST['CategoryDescription']);
            
            if (empty($categoryName)) {
                throw new Exception('Tên danh mục không được để trống');
            }
            
            $stmt = $db->prepare("UPDATE Categories SET CategoryName = ?, CategoryDescription = ? WHERE CategoryID = ?");
            $stmt->execute([$categoryName, $description, $categoryId]);
            $message = 'Cập nhật danh mục thành công!';

        } elseif ($action === 'delete') {
            $categoryId = (int)$_POST['CategoryID'];
            
            // Check if category has products
            $stmt = $db->prepare("SELECT COUNT(*) as count FROM Product WHERE CategoryID = ?");
            $stmt->execute([$categoryId]);
            $count = $stmt->fetch()['count'];
            
            if ($count > 0) {
                throw new Exception('Không thể xóa danh mục đang có sản phẩm!');
            }
            
            $stmt = $db->prepare("DELETE FROM Categories WHERE CategoryID = ?");
            $stmt->execute([$categoryId]);
            $message = 'Xóa danh mục thành công!';
        }
    } catch (Exception $e) {
        $message = $e->getMessage();
        $messageType = 'error';
    }
}

// Get all categories
$stmt = $db->query("SELECT c.*, COUNT(p.ProductID) as ProductCount 
                    FROM Categories c 
                    LEFT JOIN Product p ON c.CategoryID = p.CategoryID 
                    GROUP BY c.CategoryID 
                    ORDER BY c.CategoryName");
$categories = $stmt->fetchAll();

$pageTitle = "Quản lý Danh mục";
$isInPages = true;
include '../includes/layout_header.php';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý danh mục - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(135deg, #2563eb, #1e40af);
            color: white;
        }
        .sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 15px 20px;
            transition: all 0.3s;
        }
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background: rgba(255,255,255,0.1);
            color: white;
        }
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
                    <a class="nav-link" href="admin_dashboard.php">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                    <a class="nav-link" href="admin_products.php">
                        <i class="fas fa-box"></i> Quản lý sản phẩm
                    </a>
                    <a class="nav-link" href="admin_orders.php">
                        <i class="fas fa-shopping-cart"></i> Quản lý đơn hàng
                    </a>
                    <a class="nav-link" href="admin_users.php">
                        <i class="fas fa-users"></i> Quản lý người dùng
                    </a>
                    <a class="nav-link active" href="admin_categories.php">
                        <i class="fas fa-list"></i> Danh mục
                    </a>
                    <a class="nav-link" href="admin_brands.php">
                        <i class="fas fa-tag"></i> Thương hiệu
                    </a>
                    <a class="nav-link" href="admin_reviews.php">
                        <i class="fas fa-star"></i> Đánh giá
                    </a>
                    <a class="nav-link" href="admin_coupons.php">
                        <i class="fas fa-ticket-alt"></i> Mã giảm giá
                    </a>
                    <hr class="bg-white">
                    <a class="nav-link" href="../index.php">
                        <i class="fas fa-home"></i> Về trang chủ
                    </a>
                    <a class="nav-link" href="logout.php">
                        <i class="fas fa-sign-out-alt"></i> Đăng xuất
                    </a>
                </nav>
            </div>
            
            <div class="col-md-10 p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2><i class="fas fa-list"></i> Quản lý danh mục</h2>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
                        <i class="fas fa-plus"></i> Thêm danh mục
                    </button>
                </div>
                
                <?php if (isset($_GET['message'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($_GET['message']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Tên danh mục</th>
                                        <th>Mô tả</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($categories as $cat): ?>
                                    <tr>
                                        <td><?php echo $cat['CategoryID']; ?></td>
                                        <td><?php echo htmlspecialchars($cat['CategoryName']); ?></td>
                                        <td><?php echo htmlspecialchars(substr($cat['CategoryDescription'] ?? '', 0, 50)); ?></td>
                                        <td>
                                            <button class="btn btn-sm btn-warning" onclick="editCategory(<?php echo htmlspecialchars(json_encode($cat)); ?>)">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <form method="POST" style="display: inline;">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="CategoryID" value="<?php echo $cat['CategoryID']; ?>"> <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Xóa danh mục này?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="modal fade" id="addModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title">Thêm danh mục mới</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="action" value="add">
                        <div class="mb-3">
                            <label class="form-label">Tên danh mục *</label>
                            <input type="text" name="CategoryName" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Mô tả</label>
                            <textarea name="CategoryDescription" class="form-control" rows="3"></textarea>
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
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title">Sửa danh mục</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="action" value="edit">
                        <input type="hidden" name="CategoryID" id="edit_CategoryID">
                        <div class="mb-3">
                            <label class="form-label">Tên danh mục *</label>
                            <input type="text" name="CategoryName" id="edit_CategoryName" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Mô tả</label>
                            <textarea name="CategoryDescription" id="edit_CategoryDescription" class="form-control" rows="3"></textarea>
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
    function editCategory(cat) {
        // SỬA 11: Cập nhật JavaScript để dùng tên cột PascalCase và xóa logic cũ
        document.getElementById('edit_CategoryID').value = cat.CategoryID;
        document.getElementById('edit_CategoryName').value = cat.CategoryName;
        document.getElementById('edit_CategoryDescription').value = cat.CategoryDescription || '';
        // Xóa 'parent_id' và 'status'
        new bootstrap.Modal(document.getElementById('editModal')).show();
    }
    </script>
</body>
</html>