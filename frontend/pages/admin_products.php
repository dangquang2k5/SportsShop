<?php
require_once '../config.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('login.php');
}

$db = Database::getInstance()->getConnection();

// Helper function to get correct image path
function getImagePath($imagePath) {
    if (empty($imagePath)) {
        return 'https://via.placeholder.com/60';
    }
    
    // If it starts with http, return as is
    if (strpos($imagePath, 'http') === 0) {
        return $imagePath;
    }
    
    // If it's a relative path starting with ../, convert it
    if (strpos($imagePath, '../') === 0) {
        return $imagePath;
    }
    
    // If it's just a filename, add the path
    if (strpos($imagePath, '/') === false) {
        return '../assets/uploads/products/' . $imagePath;
    }
    
    return $imagePath;
}

// Handle product edit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'edit_product') {
    $productId = (int)$_POST['ProductID'];
    $productName = trim($_POST['ProductName']);
    $description = trim($_POST['Description']);
    $price = (float)$_POST['Price'];
    $categoryId = (int)$_POST['CategoryID'];
    $brandId = (int)$_POST['BrandID'];
    
    try {
        // Get current product to keep existing image if not updated
        $currentStmt = $db->prepare("SELECT MainImage FROM Product WHERE ProductID = ?");
        $currentStmt->execute([$productId]);
        $currentProduct = $currentStmt->fetch();
        $mainImage = $currentProduct['MainImage'];
        
        // Handle main image upload
        $uploadDir = __DIR__ . '/../assets/uploads/products/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        if (isset($_FILES['MainImage']) && $_FILES['MainImage']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['MainImage']['tmp_name'];
            $fileName = $_FILES['MainImage']['name'];
            $ext = pathinfo($fileName, PATHINFO_EXTENSION);
            $newFileName = 'main_' . time() . '.' . $ext;
            $uploadPath = $uploadDir . $newFileName;
            
            if (move_uploaded_file($file, $uploadPath)) {
                $mainImage = '../assets/uploads/products/' . $newFileName;
            }
        }
        
        // Update product
        $stmt = $db->prepare("UPDATE Product SET ProductName = ?, Description = ?, Price = ?, CategoryID = ?, BrandID = ?, MainImage = ? WHERE ProductID = ?");
        $stmt->execute([$productName, $description, $price, $categoryId, $brandId, $mainImage, $productId]);
        
        // Handle product variants (Size, Color, Quantity, Image)
        if (isset($_POST['variants']) && is_array($_POST['variants'])) {
            foreach ($_POST['variants'] as $variantIndex => $variant) {
                $size = trim($variant['size']);
                $color = trim($variant['color']);
                $quantity = (int)$variant['quantity'];
                $productDetailId = isset($variant['id']) ? (int)$variant['id'] : 0;
                $variantImage = isset($variant['existing_image']) ? trim($variant['existing_image']) : '';
                $shouldDelete = isset($variant['delete']) && $variant['delete'] === 'on';
                
                // Check if variant should be deleted
                if ($shouldDelete && $productDetailId > 0) {
                    $deleteStmt = $db->prepare("DELETE FROM ProductDetail WHERE ProductDetailID = ?");
                    $deleteStmt->execute([$productDetailId]);
                    continue;
                }
                
                // Allow empty size or color (for shoes with only size numbers, or accessories without size)
                if ((!empty($size) || !empty($color)) && $quantity > 0) {
                    // Handle image upload for this variant
                    if (isset($_FILES['variants']) && isset($_FILES['variants']['tmp_name'][$variantIndex]['image'])) {
                        $file = $_FILES['variants']['tmp_name'][$variantIndex]['image'];
                        $fileName = $_FILES['variants']['name'][$variantIndex]['image'];
                        
                        if (!empty($file) && is_uploaded_file($file)) {
                            // Generate unique filename
                            $ext = pathinfo($fileName, PATHINFO_EXTENSION);
                            $uniqueName = !empty($color) ? str_replace(' ', '_', $color) : (!empty($size) ? str_replace(' ', '_', $size) : 'variant');
                            $newFileName = 'product_' . $productId . '_' . $uniqueName . '_' . time() . '.' . $ext;
                            $uploadPath = $uploadDir . $newFileName;
                            
                            if (move_uploaded_file($file, $uploadPath)) {
                                $variantImage = '../assets/uploads/products/' . $newFileName;
                            }
                        }
                    }
                    
                    if ($productDetailId > 0) {
                        // Update existing variant
                        $updateStmt = $db->prepare("UPDATE ProductDetail SET Size = ?, Color = ?, Quantity = ?, Image = ? WHERE ProductDetailID = ?");
                        $updateStmt->execute([$size, $color, $quantity, $variantImage, $productDetailId]);
                    } else {
                        // Insert new variant
                        $insertStmt = $db->prepare("INSERT INTO ProductDetail (ProductID, Size, Color, Quantity, Image) VALUES (?, ?, ?, ?, ?)");
                        $insertStmt->execute([$productId, $size, $color, $quantity, $variantImage]);
                    }
                }
            }
        }
        
        header("Location: admin_products.php?message=Cập nhật sản phẩm thành công");
        exit;
    } catch (PDOException $e) {
        $error = 'Lỗi CSDL: ' . $e->getMessage();
    }
}

// Handle add stock
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_stock') {
    $productDetailId = (int)$_POST['ProductDetailID'];
    $quantity = (int)$_POST['Quantity'];
    
    $stmt = $db->prepare("UPDATE ProductDetail SET Quantity = Quantity + ? WHERE ProductDetailID = ?");
    $stmt->execute([$quantity, $productDetailId]);
    header("Location: admin_products.php?message=Thêm số lượng thành công");
    exit;
}

// Handle delete variant
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_variant') {
    $productDetailId = (int)$_POST['ProductDetailID'];
    $stmt = $db->prepare("DELETE FROM ProductDetail WHERE ProductDetailID = ?");
    $stmt->execute([$productDetailId]);
    header("Location: admin_products.php?message=Xóa biến thể sản phẩm thành công");
    exit;
}

// Handle add new product
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_product') {
    $productName = trim($_POST['ProductName']);
    $description = trim($_POST['Description']);
    $price = (float)$_POST['Price'];
    $categoryId = (int)$_POST['CategoryID'];
    $brandId = (int)$_POST['BrandID'];
    $mainImage = '';
    
    try {
        // Handle main image upload
        $uploadDir = __DIR__ . '/../assets/uploads/products/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        if (isset($_FILES['MainImage']) && $_FILES['MainImage']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['MainImage']['tmp_name'];
            $fileName = $_FILES['MainImage']['name'];
            $ext = pathinfo($fileName, PATHINFO_EXTENSION);
            $newFileName = 'main_' . time() . '.' . $ext;
            $uploadPath = $uploadDir . $newFileName;
            
            if (move_uploaded_file($file, $uploadPath)) {
                $mainImage = '../assets/uploads/products/' . $newFileName;
            }
        }
        
        // Insert product
        // SỬA: Thêm cột Status vào câu lệnh INSERT
        $stmt = $db->prepare("INSERT INTO Product (ProductName, Description, Price, CategoryID, BrandID, MainImage, Status) VALUES (?, ?, ?, ?, ?, ?, 'active')");
        $stmt->execute([$productName, $description, $price, $categoryId, $brandId, $mainImage]);
        $productID = $db->lastInsertId();
        
        // Handle product variants (Size, Color, Quantity, Image)
        if (isset($_POST['variants']) && is_array($_POST['variants'])) {
            $uploadDir = __DIR__ . '/../assets/uploads/products/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            foreach ($_POST['variants'] as $variantIndex => $variant) {
                $size = trim($variant['size']);
                $color = trim($variant['color']);
                $quantity = (int)$variant['quantity'];
                $variantImage = '';
                
                // Allow empty size or color (for shoes with only size numbers, or accessories without size)
                if ((!empty($size) || !empty($color)) && $quantity > 0) {
                    // Handle image upload for this variant
                    if (isset($_FILES['variants']) && isset($_FILES['variants']['tmp_name'][$variantIndex]['image'])) {
                        $file = $_FILES['variants']['tmp_name'][$variantIndex]['image'];
                        $fileName = $_FILES['variants']['name'][$variantIndex]['image'];
                        
                        if (!empty($file) && is_uploaded_file($file)) {
                            // Generate unique filename
                            $ext = pathinfo($fileName, PATHINFO_EXTENSION);
                            $uniqueName = !empty($color) ? str_replace(' ', '_', $color) : (!empty($size) ? str_replace(' ', '_', $size) : 'variant');
                            $newFileName = 'product_' . $productID . '_' . $uniqueName . '_' . time() . '.' . $ext;
                            $uploadPath = $uploadDir . $newFileName;
                            
                            if (move_uploaded_file($file, $uploadPath)) {
                                $variantImage = '../assets/uploads/products/' . $newFileName;
                            }
                        }
                    }
                    
                    $insertStmt = $db->prepare("INSERT INTO ProductDetail (ProductID, Size, Color, Quantity, Image) VALUES (?, ?, ?, ?, ?)");
                    $insertStmt->execute([$productID, $size, $color, $quantity, $variantImage]);
                }
            }
        }
        
        header("Location: admin_products.php?message=Thêm sản phẩm mới thành công");
        exit;
    } catch (PDOException $e) {
        // SỬA: Chuyển hướng với thông báo lỗi để gỡ lỗi
        $errorMessage = urlencode('Lỗi CSDL: ' . $e->getMessage());
        header("Location: admin_products.php?error=" . $errorMessage);
        exit;
    }
}

// Handle delete product
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $db->prepare("DELETE FROM Product WHERE ProductID = ?");
    $stmt->execute([$id]);
    header("Location: admin_products.php");
    exit;
}

// Get all products
$search = isset($_GET['search']) ? sanitizeInput($_GET['search']) : '';
$sql = "SELECT p.*, b.BrandName, c.CategoryName FROM Product p 
        LEFT JOIN Brand b ON p.BrandID = b.BrandID 
        LEFT JOIN Categories c ON p.CategoryID = c.CategoryID WHERE 1=1";

if ($search) {
    $sql .= " AND p.ProductName LIKE ?";
    $params = ["%$search%"];
} else {
    $params = [];
}

$sql .= " ORDER BY p.created_at DESC";
$stmt = $db->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();

// Get all categories and brands for dropdowns
$categories = $db->query("SELECT * FROM Categories ORDER BY CategoryName")->fetchAll();
$brands = $db->query("SELECT * FROM Brand ORDER BY BrandName")->fetchAll();

// Get variants for each product
$variants = [];
foreach ($products as $product) {
    $stmt = $db->prepare("SELECT * FROM ProductDetail WHERE ProductID = ? ORDER BY Size, Color");
    $stmt->execute([$product['ProductID']]);
    $variants[$product['ProductID']] = $stmt->fetchAll();
}

$pageTitle = "Quản lý sản phẩm";
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
        
        <a href="admin_products.php" class="flex items-center space-x-3 px-4 py-3 rounded-xl bg-gradient-to-r from-sport-neon to-blue-600 text-white font-semibold">
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

<div class="ml-64">
    <section class="relative py-12 bg-gradient-to-br from-sport-navy to-sport-blue">
        <div class="container mx-auto px-4 lg:px-8">
            <div class="flex items-center justify-between">
                <h1 class="text-4xl font-black text-white"><i class="fas fa-box mr-3"></i>Quản lý sản phẩm</h1>
                <button onclick="openAddProductModal()" class="btn-neon px-6 py-3 relative z-10">
                    <i class="fas fa-plus mr-2"></i>Thêm sản phẩm
                </button>
            </div>
        </div>
    </section>

    <section class="py-8 bg-gray-50 dark:bg-sport-navy">
        <div class="container mx-auto px-4 lg:px-8">
            <!-- Messages -->
            <?php if (isset($_GET['message'])): ?>
                <div class="modern-card bg-green-50 dark:bg-green-900/20 border-l-4 border-green-500 rounded-xl p-4 mb-6 animate-slide-down">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle text-green-500 text-xl mr-3"></i>
                        <p class="text-green-700 dark:text-green-300 font-semibold"><?php echo htmlspecialchars($_GET['message']); ?></p>
                    </div>
                </div>
            <?php endif; ?>
            <?php if (isset($_GET['error'])): ?>
                <div class="modern-card bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 rounded-xl p-4 mb-6 animate-slide-down">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle text-red-500 text-xl mr-3"></i>
                        <p class="text-red-700 dark:text-red-300 font-semibold"><?php echo htmlspecialchars(urldecode($_GET['error'])); ?></p>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Search -->
            <div class="modern-card bg-white dark:bg-sport-blue rounded-2xl p-6 mb-6 shadow-lg">
                <form method="GET" class="flex gap-4">
                    <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" 
                           placeholder="Tìm kiếm sản phẩm..."
                           class="flex-1 px-4 py-3 rounded-xl border-2 border-gray-200 dark:border-gray-700 bg-white dark:bg-sport-navy text-gray-900 dark:text-white focus:border-sport-neon transition-all">
                    <button type="submit" class="btn-neon px-8 py-3 relative z-10">
                        <i class="fas fa-search mr-2"></i>Tìm kiếm
                    </button>
                </form>
            </div>

            <!-- Products Table -->
            <div class="modern-card bg-white dark:bg-sport-blue rounded-2xl shadow-lg overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gradient-to-r from-sport-neon to-blue-600 text-white">
                            <tr>
                                <th class="px-6 py-4 text-left font-bold">Hình ảnh</th>
                                <th class="px-6 py-4 text-left font-bold">Tên sản phẩm</th>
                                <th class="px-6 py-4 text-left font-bold">Danh mục</th>
                                <th class="px-6 py-4 text-left font-bold">Thương hiệu</th>
                                <th class="px-6 py-4 text-left font-bold">Giá</th>
                                <th class="px-6 py-4 text-left font-bold">Trạng thái</th>
                                <th class="px-6 py-4 text-center font-bold">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($products as $index => $product): ?>
                            <tr class="border-b border-gray-100 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-sport-navy transition-colors animate-slide-up" style="animation-delay: <?php echo $index * 0.05; ?>s;">
                                <td class="px-6 py-4">
                                    <img src="<?php echo getImagePath($product['MainImage']); ?>" 
                                         alt="<?php echo htmlspecialchars($product['ProductName']); ?>"
                                         class="w-16 h-16 rounded-xl object-cover">
                                </td>
                                <td class="px-6 py-4">
                                    <p class="font-bold text-gray-900 dark:text-white"><?php echo htmlspecialchars($product['ProductName']); ?></p>
                                </td>
                                <td class="px-6 py-4 text-gray-600 dark:text-gray-400">
                                    <?php echo htmlspecialchars($product['CategoryName']); ?>
                                </td>
                                <td class="px-6 py-4 text-gray-600 dark:text-gray-400">
                                    <?php echo htmlspecialchars($product['BrandName']); ?>
                                </td>
                                <td class="px-6 py-4 font-bold text-sport-neon">
                                    <?php echo formatPrice($product['Price']); ?> ₫
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-3 py-1 rounded-full text-xs font-bold <?php echo $product['Status'] == 'active' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'; ?>">
                                        <?php echo ucfirst($product['Status']); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-center space-x-2">
                                        <!-- Edit Product -->
                                        <button onclick="openEditModal(<?php echo htmlspecialchars(json_encode($product)); ?>, <?php echo htmlspecialchars(json_encode($variants[$product['ProductID']])); ?>)" 
                                                class="p-2 rounded-lg glass hover:bg-blue-500 hover:text-white transition-all duration-300"
                                                title="Sửa sản phẩm">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        
                                        <!-- Add Stock -->
                                        <button onclick="openAddStockModal(<?php echo $product['ProductID']; ?>, <?php echo htmlspecialchars(json_encode($variants[$product['ProductID']])); ?>)" 
                                                class="p-2 rounded-lg glass hover:bg-green-500 hover:text-white transition-all duration-300"
                                                title="Thêm số lượng">
                                            <i class="fas fa-plus-circle"></i>
                                        </button>
                                        
                                        <!-- Delete Product -->
                                        <button onclick="if(confirm('Bạn chắc chắn muốn xóa sản phẩm này? Tất cả biến thể sẽ bị xóa theo.')) { window.location.href = '?delete=<?php echo $product['ProductID']; ?>'; }" 
                                                class="p-2 rounded-lg glass hover:bg-red-700 hover:text-white transition-all duration-300"
                                                title="Xóa sản phẩm">
                                            <i class="fas fa-times-circle"></i>
                                        </button>
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

<!-- Edit Product Modal -->
<div id="editModal" class="fixed inset-0 bg-black/50 hidden z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-sport-blue rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <div class="sticky top-0 bg-gradient-to-r from-sport-neon to-blue-600 text-white p-6 flex items-center justify-between">
            <h2 class="text-2xl font-bold">Sửa sản phẩm</h2>
            <button onclick="closeEditModal()" class="text-2xl hover:opacity-80">×</button>
        </div>
        
        <form method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
            <input type="hidden" name="action" value="edit_product">
            <input type="hidden" name="ProductID" id="editProductID">
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Tên sản phẩm *</label>
                    <input type="text" name="ProductName" id="editProductName" required
                           class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 dark:border-gray-700 bg-white dark:bg-sport-navy text-gray-900 dark:text-white focus:border-sport-neon transition-all">
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Giá (VNĐ) *</label>
                    <input type="number" name="Price" id="editPrice" step="0.01" required
                           class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 dark:border-gray-700 bg-white dark:bg-sport-navy text-gray-900 dark:text-white focus:border-sport-neon transition-all">
                </div>
            </div>
            
            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Mô tả</label>
                <textarea name="Description" id="editDescription" rows="3"
                          class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 dark:border-gray-700 bg-white dark:bg-sport-navy text-gray-900 dark:text-white focus:border-sport-neon transition-all"></textarea>
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Danh mục *</label>
                    <select name="CategoryID" id="editCategoryID" required
                            class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 dark:border-gray-700 bg-white dark:bg-sport-navy text-gray-900 dark:text-white focus:border-sport-neon transition-all">
                        <option value="">-- Chọn danh mục --</option>
                        <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo $cat['CategoryID']; ?>"><?php echo htmlspecialchars($cat['CategoryName']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Thương hiệu *</label>
                    <select name="BrandID" id="editBrandID" required
                            class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 dark:border-gray-700 bg-white dark:bg-sport-navy text-gray-900 dark:text-white focus:border-sport-neon transition-all">
                        <option value="">-- Chọn thương hiệu --</option>
                        <?php foreach ($brands as $brand): ?>
                        <option value="<?php echo $brand['BrandID']; ?>"><?php echo htmlspecialchars($brand['BrandName']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            
            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Ảnh chính sản phẩm</label>
                <input type="file" name="MainImage" accept="image/*"
                       class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 dark:border-gray-700 bg-white dark:bg-sport-navy text-gray-900 dark:text-white focus:border-sport-neon transition-all">
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Để trống nếu không muốn thay đổi</p>
            </div>
            
            <hr class="border-gray-200 dark:border-gray-700">
            
            <!-- Variants Section -->
            <div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-3">
                    <i class="fas fa-th mr-2"></i>Quản lý biến thể (Size - Màu - Số lượng)
                </h3>
                <div id="editProductVariantsContainer">
                    <!-- Variants will be loaded here -->
                </div>
                
                <button type="button" class="btn-neon px-4 py-2 mt-3" onclick="addEditProductVariantRow()">
                    <i class="fas fa-plus mr-2"></i>Thêm biến thể
                </button>
            </div>
            
            <div class="flex gap-3 pt-4">
                <button type="submit" class="flex-1 btn-neon px-6 py-3">
                    <i class="fas fa-save mr-2"></i>Lưu thay đổi
                </button>
                <button type="button" onclick="closeEditModal()" class="flex-1 px-6 py-3 rounded-xl border-2 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-all">
                    Hủy
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Add Stock Modal -->
<div id="addStockModal" class="fixed inset-0 bg-black/50 hidden z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-sport-blue rounded-2xl shadow-2xl max-w-md w-full">
        <div class="bg-gradient-to-r from-sport-neon to-blue-600 text-white p-6 flex items-center justify-between">
            <h2 class="text-2xl font-bold">Thêm số lượng sản phẩm</h2>
            <button onclick="closeAddStockModal()" class="text-2xl hover:opacity-80">×</button>
        </div>
        
        <form method="POST" class="p-6 space-y-4">
            <input type="hidden" name="action" value="add_stock">
            <input type="hidden" name="ProductDetailID" id="addStockProductDetailID">
            
            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Chọn biến thể (Size - Màu)</label>
                <select id="addStockVariantSelect" onchange="updateAddStockProductDetailID()"
                        class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 dark:border-gray-700 bg-white dark:bg-sport-navy text-gray-900 dark:text-white focus:border-sport-neon transition-all">
                    <option value="">-- Chọn biến thể --</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Số lượng cần thêm</label>
                <input type="number" name="Quantity" id="addStockQuantity" min="1" value="1" required
                       class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 dark:border-gray-700 bg-white dark:bg-sport-navy text-gray-900 dark:text-white focus:border-sport-neon transition-all">
            </div>
            
            <div class="flex gap-3 pt-4">
                <button type="submit" class="flex-1 btn-neon px-6 py-3">
                    <i class="fas fa-plus mr-2"></i>Thêm
                </button>
                <button type="button" onclick="closeAddStockModal()" class="flex-1 px-6 py-3 rounded-xl border-2 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-all">
                    Hủy
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Add Product Modal -->
<div id="addProductModal" class="fixed inset-0 bg-black/50 hidden z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-sport-blue rounded-2xl shadow-2xl max-w-3xl w-full max-h-[90vh] overflow-y-auto">
        <div class="sticky top-0 bg-gradient-to-r from-sport-neon to-blue-600 text-white p-6 flex items-center justify-between">
            <h2 class="text-2xl font-bold">Thêm sản phẩm mới</h2>
            <button onclick="closeAddProductModal()" class="text-2xl hover:opacity-80">×</button>
        </div>
        
        <form method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
            <input type="hidden" name="action" value="add_product">
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Tên sản phẩm *</label>
                    <input type="text" name="ProductName" required
                           class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 dark:border-gray-700 bg-white dark:bg-sport-navy text-gray-900 dark:text-white focus:border-sport-neon transition-all">
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Giá (VNĐ) *</label>
                    <input type="number" name="Price" step="0.01" required
                           class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 dark:border-gray-700 bg-white dark:bg-sport-navy text-gray-900 dark:text-white focus:border-sport-neon transition-all">
                </div>
            </div>
            
            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Mô tả</label>
                <textarea name="Description" rows="3"
                          class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 dark:border-gray-700 bg-white dark:bg-sport-navy text-gray-900 dark:text-white focus:border-sport-neon transition-all"></textarea>
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Danh mục *</label>
                    <select name="CategoryID" required
                            class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 dark:border-gray-700 bg-white dark:bg-sport-navy text-gray-900 dark:text-white focus:border-sport-neon transition-all">
                        <option value="">-- Chọn danh mục --</option>
                        <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo $cat['CategoryID']; ?>"><?php echo htmlspecialchars($cat['CategoryName']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Thương hiệu *</label>
                    <select name="BrandID" required
                            class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 dark:border-gray-700 bg-white dark:bg-sport-navy text-gray-900 dark:text-white focus:border-sport-neon transition-all">
                        <option value="">-- Chọn thương hiệu --</option>
                        <?php foreach ($brands as $brand): ?>
                        <option value="<?php echo $brand['BrandID']; ?>"><?php echo htmlspecialchars($brand['BrandName']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            
            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Ảnh chính sản phẩm</label>
                <input type="file" name="MainImage" accept="image/*"
                       class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 dark:border-gray-700 bg-white dark:bg-sport-navy text-gray-900 dark:text-white focus:border-sport-neon transition-all">
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Chọn ảnh từ máy tính</p>
            </div>
            
            <hr class="border-gray-200 dark:border-gray-700">
            
            <!-- Variants Section -->
            <div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-3">
                    <i class="fas fa-th mr-2"></i>Quản lý biến thể (Size - Màu - Số lượng)
                </h3>
                <div id="addProductVariantsContainer">
                    <!-- Variants will be added here -->
                </div>
                
                <button type="button" class="btn-neon px-4 py-2 mt-3" onclick="addProductVariantRow()">
                    <i class="fas fa-plus mr-2"></i>Thêm biến thể
                </button>
            </div>
            
            <div class="flex gap-3 pt-4">
                <button type="submit" class="flex-1 btn-neon px-6 py-3">
                    <i class="fas fa-save mr-2"></i>Lưu sản phẩm
                </button>
                <button type="button" onclick="closeAddProductModal()" class="flex-1 px-6 py-3 rounded-xl border-2 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-all">
                    Hủy
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Edit Modal Functions
let editProductVariantCount = 0;

function openEditModal(product, variants) {
    editProductVariantCount = 0;
    document.getElementById('editProductID').value = product.ProductID;
    document.getElementById('editProductName').value = product.ProductName;
    document.getElementById('editDescription').value = product.Description || '';
    document.getElementById('editPrice').value = product.Price;
    document.getElementById('editCategoryID').value = product.CategoryID || '';
    document.getElementById('editBrandID').value = product.BrandID || '';
    
    // Load existing variants
    const variantsContainer = document.getElementById('editProductVariantsContainer');
    variantsContainer.innerHTML = '';
    
    if (variants && variants.length > 0) {
        variants.forEach((variant, index) => {
            const newRow = document.createElement('div');
            newRow.className = 'variant-row p-4 mb-4 rounded-xl bg-gray-50 dark:bg-sport-navy border border-gray-200 dark:border-gray-700';
            newRow.innerHTML = `
                <input type="hidden" name="variants[${index}][id]" value="${variant.ProductDetailID}">
                <input type="hidden" name="variants[${index}][existing_image]" value="${variant.Image || ''}">
                <div class="grid grid-cols-3 gap-3 mb-3">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Size <span class="text-gray-500">(tùy chọn)</span></label>
                        <input type="text" class="w-full px-3 py-2 rounded-lg border-2 border-gray-200 dark:border-gray-700 bg-white dark:bg-sport-blue text-gray-900 dark:text-white focus:border-sport-neon transition-all" 
                               name="variants[${index}][size]" value="${variant.Size}" placeholder="M, L, XL hoặc để trống">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Màu <span class="text-gray-500">(tùy chọn)</span></label>
                        <input type="text" class="w-full px-3 py-2 rounded-lg border-2 border-gray-200 dark:border-gray-700 bg-white dark:bg-sport-blue text-gray-900 dark:text-white focus:border-sport-neon transition-all" 
                               name="variants[${index}][color]" value="${variant.Color}" placeholder="Đỏ, Xanh hoặc để trống">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Số lượng</label>
                        <input type="number" class="w-full px-3 py-2 rounded-lg border-2 border-gray-200 dark:border-gray-700 bg-white dark:bg-sport-blue text-gray-900 dark:text-white focus:border-sport-neon transition-all" 
                               name="variants[${index}][quantity]" value="${variant.Quantity}" min="0">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3 mb-3">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Ảnh cho màu này</label>
                        <input type="file" accept="image/*" class="w-full px-3 py-2 rounded-lg border-2 border-gray-200 dark:border-gray-700 bg-white dark:bg-sport-blue text-gray-900 dark:text-white focus:border-sport-neon transition-all text-xs" 
                               name="variants[${index}][image]">
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Để trống nếu không muốn thay đổi</p>
                    </div>
                    <div class="flex items-center">
                        <label class="flex items-center space-x-2 cursor-pointer">
                            <input type="checkbox" name="variants[${index}][delete]" class="w-4 h-4 rounded">
                            <span class="text-xs font-semibold text-red-600 dark:text-red-400">Xóa biến thể này</span>
                        </label>
                    </div>
                </div>
            `;
            variantsContainer.appendChild(newRow);
            editProductVariantCount++;
        });
    }
    
    document.getElementById('editModal').classList.remove('hidden');
}

function closeEditModal() {
    document.getElementById('editModal').classList.add('hidden');
}

function addEditProductVariantRow() {
    const container = document.getElementById('editProductVariantsContainer');
    const newRow = document.createElement('div');
    newRow.className = 'variant-row p-4 mb-4 rounded-xl bg-gray-50 dark:bg-sport-navy border border-gray-200 dark:border-gray-700';
    newRow.innerHTML = `
        <div class="grid grid-cols-3 gap-3 mb-3">
            <div>
                <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Size <span class="text-gray-500">(tùy chọn)</span></label>
                <input type="text" class="w-full px-3 py-2 rounded-lg border-2 border-gray-200 dark:border-gray-700 bg-white dark:bg-sport-blue text-gray-900 dark:text-white focus:border-sport-neon transition-all" 
                       name="variants[${editProductVariantCount}][size]" placeholder="M, L, XL hoặc để trống">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Màu <span class="text-gray-500">(tùy chọn)</span></label>
                <input type="text" class="w-full px-3 py-2 rounded-lg border-2 border-gray-200 dark:border-gray-700 bg-white dark:bg-sport-blue text-gray-900 dark:text-white focus:border-sport-neon transition-all" 
                       name="variants[${editProductVariantCount}][color]" placeholder="Đỏ, Xanh hoặc để trống">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Số lượng</label>
                <input type="number" class="w-full px-3 py-2 rounded-lg border-2 border-gray-200 dark:border-gray-700 bg-white dark:bg-sport-blue text-gray-900 dark:text-white focus:border-sport-neon transition-all" 
                       name="variants[${editProductVariantCount}][quantity]" min="0" value="0">
            </div>
        </div>
        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Ảnh cho màu này</label>
                <input type="file" accept="image/*" class="w-full px-3 py-2 rounded-lg border-2 border-gray-200 dark:border-gray-700 bg-white dark:bg-sport-blue text-gray-900 dark:text-white focus:border-sport-neon transition-all text-xs" 
                       name="variants[${editProductVariantCount}][image]">
            </div>
            <div class="flex items-end">
                <button type="button" class="w-full px-3 py-2 rounded-lg bg-red-500 hover:bg-red-600 text-white font-semibold transition-all text-sm" 
                        onclick="removeEditProductVariant(this)">
                    <i class="fas fa-trash"></i> Xóa
                </button>
            </div>
        </div>
    `;
    container.appendChild(newRow);
    editProductVariantCount++;
}

function removeEditProductVariant(btn) {
    btn.closest('.variant-row').remove();
}

// Add Stock Modal Functions
function openAddStockModal(productId, variants) {
    const select = document.getElementById('addStockVariantSelect');
    select.innerHTML = '<option value="">-- Chọn biến thể --</option>';
    
    variants.forEach(variant => {
        const option = document.createElement('option');
        option.value = variant.ProductDetailID;
        option.textContent = `${variant.Size} - ${variant.Color} (Hiện có: ${variant.Quantity})`;
        select.appendChild(option);
    });
    
    document.getElementById('addStockModal').classList.remove('hidden');
}

function closeAddStockModal() {
    document.getElementById('addStockModal').classList.add('hidden');
}

function updateAddStockProductDetailID() {
    const select = document.getElementById('addStockVariantSelect');
    document.getElementById('addStockProductDetailID').value = select.value;
}

// Add Product Modal Functions
let addProductVariantCount = 0;

function openAddProductModal() {
    addProductVariantCount = 0;
    document.getElementById('addProductVariantsContainer').innerHTML = '';
    document.getElementById('addProductModal').classList.remove('hidden');
}

function closeAddProductModal() {
    document.getElementById('addProductModal').classList.add('hidden');
}

function addProductVariantRow() {
    const container = document.getElementById('addProductVariantsContainer');
    const newRow = document.createElement('div');
    newRow.className = 'variant-row p-4 mb-4 rounded-xl bg-gray-50 dark:bg-sport-navy border border-gray-200 dark:border-gray-700';
    newRow.innerHTML = `
        <div class="grid grid-cols-3 gap-3 mb-3">
            <div>
                <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Size <span class="text-gray-500">(tùy chọn)</span></label>
                <input type="text" class="w-full px-3 py-2 rounded-lg border-2 border-gray-200 dark:border-gray-700 bg-white dark:bg-sport-blue text-gray-900 dark:text-white focus:border-sport-neon transition-all" 
                       name="variants[${addProductVariantCount}][size]" placeholder="M, L, XL hoặc để trống">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Màu <span class="text-gray-500">(tùy chọn)</span></label>
                <input type="text" class="w-full px-3 py-2 rounded-lg border-2 border-gray-200 dark:border-gray-700 bg-white dark:bg-sport-blue text-gray-900 dark:text-white focus:border-sport-neon transition-all" 
                       name="variants[${addProductVariantCount}][color]" placeholder="Đỏ, Xanh hoặc để trống">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Số lượng</label>
                <input type="number" class="w-full px-3 py-2 rounded-lg border-2 border-gray-200 dark:border-gray-700 bg-white dark:bg-sport-blue text-gray-900 dark:text-white focus:border-sport-neon transition-all" 
                       name="variants[${addProductVariantCount}][quantity]" min="0" value="0">
            </div>
        </div>
        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Ảnh cho màu này</label>
                <input type="file" accept="image/*" class="w-full px-3 py-2 rounded-lg border-2 border-gray-200 dark:border-gray-700 bg-white dark:bg-sport-blue text-gray-900 dark:text-white focus:border-sport-neon transition-all text-xs" 
                       name="variants[${addProductVariantCount}][image]">
            </div>
            <div class="flex items-end">
                <button type="button" class="w-full px-3 py-2 rounded-lg bg-red-500 hover:bg-red-600 text-white font-semibold transition-all text-sm" 
                        onclick="removeProductVariant(this)">
                    <i class="fas fa-trash"></i> Xóa
                </button>
            </div>
        </div>
    `;
    container.appendChild(newRow);
    addProductVariantCount++;
}

function removeProductVariant(btn) {
    btn.closest('.variant-row').remove();
}

// Close modals when clicking outside
document.addEventListener('click', function(event) {
    const editModal = document.getElementById('editModal');
    const addStockModal = document.getElementById('addStockModal');
    const addProductModal = document.getElementById('addProductModal');
    
    if (event.target === editModal) closeEditModal();
    if (event.target === addStockModal) closeAddStockModal();
    if (event.target === addProductModal) closeAddProductModal();
});
</script>

<?php include '../includes/layout_footer.php'; ?>
