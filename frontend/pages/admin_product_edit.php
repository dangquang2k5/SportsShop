<?php
require_once '../config.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('login.php');
}

<<<<<<< HEAD
=======
$db = Database::getInstance()->getConnection();

>>>>>>> 3d6d58ed3875cc3c551e3fe1991339ab7637c345
// Kiểm tra xem đây là mode Thêm hay Sửa
$editID = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$isEditMode = ($editID > 0);
$product = [
    'ProductName' => '',
    'Price' => '',
    'MainImage' => '',
    'Description' => '',
    'CategoryID' => '',
    'BrandID' => ''
];
$pageTitle = 'Thêm sản phẩm mới';

// Lấy danh mục và thương hiệu cho dropdown
<<<<<<< HEAD
$categoriesResponse = makeApiRequest('/categories');
$categories = $categoriesResponse['success'] ? $categoriesResponse['data'] ?? [] : [];

$brandsResponse = makeApiRequest('/brands');
$brands = $brandsResponse['success'] ? $brandsResponse['data'] ?? [] : [];
=======
$categories = $db->query("SELECT * FROM Categories ORDER BY CategoryName")->fetchAll();
$brands = $db->query("SELECT * FROM Brand ORDER BY BrandName")->fetchAll();
>>>>>>> 3d6d58ed3875cc3c551e3fe1991339ab7637c345

// Nếu là mode Sửa, tải dữ liệu sản phẩm
if ($isEditMode) {
    $pageTitle = 'Sửa sản phẩm';
<<<<<<< HEAD
    $productResponse = makeApiRequest('/products/' . $editID);
    
    if ($productResponse['success']) {
        $product = $productResponse['data'] ?? $product;
    } else {
=======
    $stmt = $db->prepare("SELECT * FROM Product WHERE ProductID = ?");
    $stmt->execute([$editID]);
    $product = $stmt->fetch();
    if (!$product) {
>>>>>>> 3d6d58ed3875cc3c551e3fe1991339ab7637c345
        redirect('admin_products.php?message=Không tìm thấy sản phẩm');
    }
}

// Xử lý POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productName = trim($_POST['ProductName']);
    $price = (float)$_POST['Price'];
    $mainImage = trim($_POST['MainImage']);
    $description = trim($_POST['Description']);
    $categoryID = (int)$_POST['CategoryID'];
    $brandID = (int)$_POST['BrandID'];
<<<<<<< HEAD
    
    $productData = [
        'productName' => $productName,
        'price' => $price,
        'mainImage' => $mainImage,
        'description' => $description,
        'categoryID' => $categoryID,
        'brandID' => $brandID
    ];
    
    if ($isEditMode) {
        // Update product
        $response = makeApiRequest('/products/' . $editID, 'PUT', $productData);
        $message = $response['success'] ? 'Cập nhật sản phẩm thành công!' : 'Lỗi: ' . ($response['message'] ?? 'Không thể cập nhật sản phẩm');
    } else {
        // Create new product
        $response = makeApiRequest('/products', 'POST', $productData);
        $message = $response['success'] ? 'Thêm sản phẩm thành công!' : 'Lỗi: ' . ($response['message'] ?? 'Không thể thêm sản phẩm');
    }
    
    if ($response['success']) {
        redirect('admin_products.php?message=' . urlencode($message));
=======

    try {
        if ($isEditMode) {
            // Logic UPDATE
            $stmt = $db->prepare("
                UPDATE Product 
                SET ProductName = ?, Price = ?, MainImage = ?, Description = ?, CategoryID = ?, BrandID = ?
                WHERE ProductID = ?
            ");
            $stmt->execute([$productName, $price, $mainImage, $description, $categoryID, $brandID, $editID]);
            $productID = $editID;
            $message = 'Đã cập nhật sản phẩm!';
        } else {
            // Logic INSERT
            $stmt = $db->prepare("
                INSERT INTO Product (ProductName, Price, MainImage, Description, CategoryID, BrandID)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([$productName, $price, $mainImage, $description, $categoryID, $brandID]);
            $productID = $db->lastInsertId();
            $message = 'Đã thêm sản phẩm mới!';
        }
        
        // Handle product variants (Size, Color, Quantity)
        if (isset($_POST['variants']) && is_array($_POST['variants'])) {
            foreach ($_POST['variants'] as $variant) {
                $size = trim($variant['size']);
                $color = trim($variant['color']);
                $quantity = (int)$variant['quantity'];
                
                if (!empty($size) && !empty($color) && $quantity > 0) {
                    // Check if variant already exists
                    $checkStmt = $db->prepare("
                        SELECT ProductDetailID FROM ProductDetail 
                        WHERE ProductID = ? AND Size = ? AND Color = ?
                    ");
                    $checkStmt->execute([$productID, $size, $color]);
                    $existingVariant = $checkStmt->fetch();
                    
                    if ($existingVariant) {
                        // Update existing variant
                        $updateStmt = $db->prepare("
                            UPDATE ProductDetail 
                            SET Quantity = ? 
                            WHERE ProductDetailID = ?
                        ");
                        $updateStmt->execute([$quantity, $existingVariant['ProductDetailID']]);
                    } else {
                        // Insert new variant
                        $insertStmt = $db->prepare("
                            INSERT INTO ProductDetail (ProductID, Size, Color, Quantity)
                            VALUES (?, ?, ?, ?)
                        ");
                        $insertStmt->execute([$productID, $size, $color, $quantity]);
                    }
                }
            }
        }
        
        header('Location: admin_products.php?message=' . urlencode($message));
        exit;
    } catch (PDOException $e) {
        $error = 'Lỗi CSDL: ' . $e->getMessage();
>>>>>>> 3d6d58ed3875cc3c551e3fe1991339ab7637c345
    }
}

// Get existing variants if in edit mode
$existingVariants = [];
if ($isEditMode) {
<<<<<<< HEAD
    $variantsResponse = makeApiRequest('/products/' . $editID . '/variants');
    if ($variantsResponse['success']) {
        $existingVariants = $variantsResponse['data']['variants'] ?? [];
    }
=======
    $stmt = $db->prepare("SELECT * FROM ProductDetail WHERE ProductID = ? ORDER BY Size, Color");
    $stmt->execute([$editID]);
    $existingVariants = $stmt->fetchAll();
>>>>>>> 3d6d58ed3875cc3c551e3fe1991339ab7637c345
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - <?php echo SITE_NAME; ?></title>
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
                    <a class="nav-link" href="admin_products.php"><i class="fas fa-arrow-left"></i> Quay lại</a>
                </nav>
            </div>
            
            <div class="col-md-10 p-4">
                <h2><i class="fas fa-box"></i> <?php echo $pageTitle; ?></h2>
                
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>

                <div class="card shadow-sm">
                    <div class="card-body">
                        <form method="POST">
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="mb-3">
                                        <label class="form-label">Tên sản phẩm *</label>
                                        <input type="text" class="form-control" name="ProductName" value="<?php echo htmlspecialchars($product['ProductName']); ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Mô tả</label>
                                        <textarea class="form-control" name="Description" rows="5"><?php echo htmlspecialchars($product['Description']); ?></textarea>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label">Giá (VNĐ) *</label>
                                        <input type="number" class="form-control" name="Price" value="<?php echo htmlspecialchars($product['Price']); ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Danh mục *</label>
                                        <select class="form-select" name="CategoryID" required>
                                            <option value="">-- Chọn --</option>
                                            <?php foreach ($categories as $cat): ?>
                                                <option value="<?php echo $cat['CategoryID']; ?>" <?php echo ($product['CategoryID'] == $cat['CategoryID']) ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($cat['CategoryName']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Thương hiệu *</label>
                                        <select class="form-select" name="BrandID" required>
                                            <option value="">-- Chọn --</option>
                                            <?php foreach ($brands as $brand): ?>
                                                <option value="<?php echo $brand['BrandID']; ?>" <?php echo ($product['BrandID'] == $brand['BrandID']) ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($brand['BrandName']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">URL Ảnh chính</label>
                                        <input type="text" class="form-control" name="MainImage" value="<?php echo htmlspecialchars($product['MainImage']); ?>" placeholder="/images/ten-anh.jpg">
                                    </div>
                                </div>
                            </div>
                            
                            <hr>
                            <!-- Variants Section -->
                            <div class="mb-4">
                                <h4 class="mb-3"><i class="fas fa-th"></i> Quản lý biến thể (Size - Màu - Số lượng)</h4>
                                <div id="variants-container">
                                    <?php if (!empty($existingVariants)): ?>
                                        <?php foreach ($existingVariants as $index => $variant): ?>
                                        <div class="variant-row row mb-3 p-3 border rounded" style="background-color: #f8f9fa;">
                                            <div class="col-md-3">
                                                <label class="form-label">Size</label>
                                                <input type="text" class="form-control" name="variants[<?php echo $index; ?>][size]" value="<?php echo htmlspecialchars($variant['Size']); ?>" placeholder="VD: M, L, XL">
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">Màu</label>
                                                <input type="text" class="form-control" name="variants[<?php echo $index; ?>][color]" value="<?php echo htmlspecialchars($variant['Color']); ?>" placeholder="VD: Đỏ, Xanh">
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">Số lượng</label>
                                                <input type="number" class="form-control" name="variants[<?php echo $index; ?>][quantity]" value="<?php echo $variant['Quantity']; ?>" min="0">
                                            </div>
                                            <div class="col-md-3 d-flex align-items-end">
                                                <button type="button" class="btn btn-danger btn-sm w-100" onclick="removeVariant(this)">
                                                    <i class="fas fa-trash"></i> Xóa
                                                </button>
                                            </div>
                                        </div>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                                
                                <button type="button" class="btn btn-success btn-sm mt-3" onclick="addVariantRow()">
                                    <i class="fas fa-plus"></i> Thêm biến thể
                                </button>
                            </div>
                            
                            <hr>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Lưu sản phẩm
                            </button>
                            <a href="admin_products.php" class="btn btn-secondary">Hủy</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let variantCount = <?php echo count($existingVariants); ?>;
        
        function addVariantRow() {
            const container = document.getElementById('variants-container');
            const newRow = document.createElement('div');
            newRow.className = 'variant-row row mb-3 p-3 border rounded';
            newRow.style.backgroundColor = '#f8f9fa';
            newRow.innerHTML = `
                <div class="col-md-3">
                    <label class="form-label">Size</label>
                    <input type="text" class="form-control" name="variants[${variantCount}][size]" placeholder="VD: M, L, XL">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Màu</label>
                    <input type="text" class="form-control" name="variants[${variantCount}][color]" placeholder="VD: Đỏ, Xanh">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Số lượng</label>
                    <input type="number" class="form-control" name="variants[${variantCount}][quantity]" min="0" value="0">
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="button" class="btn btn-danger btn-sm w-100" onclick="removeVariant(this)">
                        <i class="fas fa-trash"></i> Xóa
                    </button>
                </div>
            `;
            container.appendChild(newRow);
            variantCount++;
        }
        
        function removeVariant(btn) {
            btn.closest('.variant-row').remove();
        }
    </script>
</body>
</html>