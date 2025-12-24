<?php
require_once '../config.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('login.php');
}

// Lấy ProductID từ URL
$productID = isset($_GET['ProductID']) ? (int)$_GET['ProductID'] : 0;
if ($productID === 0) {
    redirect('admin_products.php?message=ProductID không hợp lệ');
}

// Lấy thông tin sản phẩm chung
$productResponse = makeApiRequest('/products/' . $productID);
if (!$productResponse['success']) {
    redirect('admin_products.php?message=Không tìm thấy sản phẩm');
}
$product = $productResponse['data'] ?? [];

// Xử lý POST actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    try {
        if ($action === 'add_variant') {
            $size = trim($_POST['Size']);
            $color = trim($_POST['Color']);
            $quantity = (int)$_POST['Quantity'];
            
            // Use backend API to add variant
            $response = makeApiRequest('/products/' . $productID . '/variants', 'POST', [
                'size' => $size,
                'color' => $color,
                'quantity' => $quantity
            ]);
            
            if ($response['success']) {
                $message = 'Đã thêm biến thể thành công!';
            } else {
                throw new Exception($response['message'] ?? 'Không thể thêm biến thể');
            }

        } elseif ($action === 'delete_variant') {
            $productDetailID = (int)$_POST['ProductDetailID'];
            
            // Use backend API to delete variant
            $response = makeApiRequest('/products/' . $productID . '/variants/' . $productDetailID, 'DELETE');
            
            if ($response['success']) {
                $message = 'Đã xóa biến thể';
            } else {
                throw new Exception($response['message'] ?? 'Không thể xóa biến thể');
            }

        } elseif ($action === 'import_stock') {
            // Gọi Stored Procedure để nhập kho
            $productDetailID = (int)$_POST['ProductDetailID'];
            $additionalQuantity = (int)$_POST['additionalQuantity'];
            $reason = 'Admin nhập kho';

            // Use backend API to import stock
            $response = makeApiRequest('/products/' . $productID . '/variants/' . $productDetailID . '/import', 'POST', [
                'additionalQuantity' => $additionalQuantity,
                'reason' => $reason
            ]);
            
            if ($response['success']) {
                $message = 'Đã nhập thêm ' . $additionalQuantity . ' sản phẩm vào kho';
            } else {
                throw new Exception($response['message'] ?? 'Không thể nhập kho');
            }
        }

    } catch (Exception $e) {
        $error = 'Lỗi: ' . $e->getMessage();
    }
    
    // Tải lại trang để thấy thay đổi
    if (isset($message)) {
        header('Location: admin_product_variants.php?ProductID=' . $productID . '&message=' . urlencode($message));
        exit;
    }
    if (isset($error)) {
        header('Location: admin_product_variants.php?ProductID=' . $productID . '&error=' . urlencode($error));
        exit;
    }
}

// Lấy danh sách biến thể của sản phẩm
$variantsResponse = makeApiRequest('/products/' . $productID . '/variants');
$variants = $variantsResponse['success'] ? $variantsResponse['data']['variants'] ?? [] : [];
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Biến thể - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .sidebar { min-height: 100vh; background: linear-gradient(135deg, #2563eb, #1e40af); color: white; }
        .sidebar .nav-link { color: rgba(255,255,255,0.8); padding: 15px 20px; transition: all 0.3s; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { background: rgba(255,255,255,0.1); color: white; }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-2 sidebar p-0">
                <div class="p-4"><h4 class="mb-4"><i class="fas fa-running"></i> SportShop Admin</h4></div>
                <nav class="nav flex-column">
                    <a class="nav-link" href="admin_dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                    <a class="nav-link active" href="admin_products.php"><i class="fas fa-box"></i> Quản lý sản phẩm</a>
                    <hr class="bg-white">
                    <a class="nav-link" href="admin_products.php"><i class="fas fa-arrow-left"></i> Quay lại DS Sản phẩm</a>
                </nav>
            </div>
            
            <div class="col-md-10 p-4">
                <h2><i class="fas fa-boxes"></i> Quản lý Biến thể & Tồn kho</h2>
                <h4 class="text-muted"><?php echo htmlspecialchars($product['ProductName']); ?></h4>
                
                <?php if (isset($_GET['message'])): ?>
                    <div class="alert alert-success"><?php echo htmlspecialchars($_GET['message']); ?></div>
                <?php endif; ?>
                <?php if (isset($_GET['error'])): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($_GET['error']); ?></div>
                <?php endif; ?>

                <div class="card shadow-sm mb-4">
                    <div class="card-header"><h5 class="mb-0">Thêm biến thể mới</h5></div>
                    <div class="card-body">
                        <form method="POST" class="row g-3">
                            <input type="hidden" name="action" value="add_variant">
                            <div class="col-md-3">
                                <label class="form-label">Size *</label>
                                <input type="text" class="form-control" name="Size" required placeholder="VD: L, 42...">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Màu *</label>
                                <input type="text" class="form-control" name="Color" required placeholder="VD: Đen, Trắng...">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Số lượng *</label>
                                <input type="number" class="form-control" name="Quantity" min="0" value="0" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">URL Ảnh (Tùy chọn)</label>
                                <input type="text" class="form-control" name="Image" placeholder="/images/anh-bien-the.jpg">
                            </div>
                            <div class="col-md-1">
                                <label class="form-label">&nbsp;</label>
                                <button type="submit" class="btn btn-primary w-100"><i class="fas fa-plus"></i></button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card shadow-sm">
                    <div class="card-header"><h5 class="mb-0">Các biến thể hiện tại</h5></div>
                    <div class="card-body">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID Biến thể</th>
                                    <th>Size</th>
                                    <th>Màu</th>
                                    <th>Tồn kho</th>
                                    <th>Nhập kho</th>
                                    <th>Xóa</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($variants as $variant): ?>
                                <tr>
                                    <td><?php echo $variant['ProductDetailID']; ?></td>
                                    <td><?php echo htmlspecialchars($variant['Size']); ?></td>
                                    <td><?php echo htmlspecialchars($variant['Color']); ?></td>
                                    <td><strong><?php echo $variant['Quantity']; ?></strong></td>
                                    <td>
                                        <form method="POST" class="d-flex">
                                            <input type="hidden" name="action" value="import_stock">
                                            <input type="hidden" name="ProductDetailID" value="<?php echo $variant['ProductDetailID']; ?>">
                                            <input type="number" class="form-control form-control-sm" name="additionalQuantity" min="1" placeholder="Số lượng" style="width: 100px;" required>
                                            <button type="submit" class="btn btn-sm btn-success ms-2" title="Nhập kho">
                                                <i class="fas fa-plus"></i> Nhập
                                            </button>
                                        </form>
                                    </td>
                                    <td>
                                        <form method="POST" onsubmit="return confirm('Bạn có chắc muốn xóa biến thể này?')">
                                            <input type="hidden" name="action" value="delete_variant">
                                            <input type="hidden" name="ProductDetailID" value="<?php echo $variant['ProductDetailID']; ?>">
                                            <button type="submit" class="btn btn-sm btn-danger" title="Xóa">
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>