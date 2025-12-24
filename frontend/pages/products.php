<?php
require_once '../config.php';

<<<<<<< HEAD
// Get filters
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$limit = 12;
=======
$db = Database::getInstance()->getConnection();

// Get filters
>>>>>>> 3d6d58ed3875cc3c551e3fe1991339ab7637c345
$categoryId = isset($_GET['category']) ? (int)$_GET['category'] : null;
$brandId = isset($_GET['brand']) ? (int)$_GET['brand'] : null;
$search = isset($_GET['search']) ? sanitizeInput($_GET['search']) : '';
$sortBy = isset($_GET['sort']) ? $_GET['sort'] : 'newest';

<<<<<<< HEAD
// Build API query parameters
$apiParams = [
    'page' => $page,
    'limit' => $limit,
    'search' => $search
];

if ($categoryId) $apiParams['categoryId'] = $categoryId;
if ($brandId) $apiParams['brandId'] = $brandId;

switch ($sortBy) {
    case 'price_asc':
        $apiParams['sortBy'] = 'Price';
        $apiParams['sortOrder'] = 'ASC';
        break;
    case 'price_desc':
        $apiParams['sortBy'] = 'Price';
        $apiParams['sortOrder'] = 'DESC';
        break;
    case 'rating':
        $apiParams['sortBy'] = 'RatingAvg';
        $apiParams['sortOrder'] = 'DESC';
        break;
    default:
        $apiParams['sortBy'] = 'created_at';
        $apiParams['sortOrder'] = 'DESC';
}

// Make API request
$queryString = http_build_query($apiParams);
$response = makeApiRequest('/products?' . $queryString);

$products = [];
$totalRecords = 0;
$totalPages = 1;

if ($response['success'] && isset($response['data'])) {
    $products = $response['data']['products'] ?? [];
    $totalRecords = $response['data']['pagination']['total'] ?? 0;
    $totalPages = $response['data']['pagination']['totalPages'] ?? 1;
}

// Get categories and brands for filters
$categories = getAllCategories();
$brands = getAllBrands();

// --- 5. HÀM HELPER ĐỂ GIỮ BỘ LỌC KHI CHUYỂN TRANG ---
function getUrl($newPage) {
    $params = $_GET;
    $params['page'] = $newPage;
    return '?' . http_build_query($params);
}
=======
// Build SQL query
$sql = "SELECT p.*, b.BrandName, c.CategoryName 
        FROM Product p
        LEFT JOIN Brand b ON p.BrandID = b.BrandID
        LEFT JOIN Categories c ON p.CategoryID = c.CategoryID
        WHERE p.Status = 'active'";

$params = [];

if ($categoryId) {
    $sql .= " AND p.CategoryID = ?";
    $params[] = $categoryId;
}

if ($brandId) {
    $sql .= " AND p.BrandID = ?";
    $params[] = $brandId;
}

if ($search) {
    $sql .= " AND (p.ProductName LIKE ? OR p.Description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

// Sorting
switch ($sortBy) {
    case 'price_asc':
        $sql .= " ORDER BY p.Price ASC";
        break;
    case 'price_desc':
        $sql .= " ORDER BY p.Price DESC";
        break;
    case 'rating':
        $sql .= " ORDER BY p.RatingAvg DESC";
        break;
    default:
        $sql .= " ORDER BY p.created_at DESC";
}

$stmt = $db->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();

// Get categories and brands for filters
$categories = $db->query("SELECT * FROM Categories ORDER BY CategoryName")->fetchAll();
$brands = $db->query("SELECT * FROM Brand ORDER BY BrandName")->fetchAll();
>>>>>>> 3d6d58ed3875cc3c551e3fe1991339ab7637c345

$pageTitle = "Sản phẩm";
$isInPages = true;
include '../includes/layout_header.php';
?>

<<<<<<< HEAD
=======
<!-- Page Header -->
>>>>>>> 3d6d58ed3875cc3c551e3fe1991339ab7637c345
<section class="relative py-20 bg-gradient-to-br from-sport-navy to-sport-blue overflow-hidden">
    <div class="absolute inset-0">
        <div class="absolute top-0 right-0 w-96 h-96 bg-sport-neon opacity-10 rounded-full blur-3xl"></div>
        <div class="absolute bottom-0 left-0 w-96 h-96 bg-purple-600 opacity-10 rounded-full blur-3xl"></div>
    </div>
    
    <div class="container mx-auto px-4 lg:px-8 relative z-10">
        <div class="text-center animate-fade-in">
            <h1 class="text-5xl lg:text-6xl font-black text-white mb-4">
                Khám phá sản phẩm
            </h1>
            <p class="text-xl text-gray-300">
<<<<<<< HEAD
                Hiển thị <?php echo count($products); ?> trên tổng số <?php echo $totalRecords; ?> sản phẩm
=======
                <?php echo count($products); ?> sản phẩm chất lượng đang chờ bạn
>>>>>>> 3d6d58ed3875cc3c551e3fe1991339ab7637c345
            </p>
        </div>
    </div>
</section>

<<<<<<< HEAD
<section class="py-12 bg-gray-50 dark:bg-sport-navy min-h-screen">
    <div class="container mx-auto px-4 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
=======
<!-- Products Section -->
<section class="py-12 bg-gray-50 dark:bg-sport-navy min-h-screen">
    <div class="container mx-auto px-4 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            <!-- Sidebar Filters -->
>>>>>>> 3d6d58ed3875cc3c551e3fe1991339ab7637c345
            <div class="lg:col-span-1">
                <div class="sticky top-24">
                    <div class="modern-card bg-white dark:bg-sport-blue rounded-2xl p-6 shadow-lg animate-slide-up">
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-6 flex items-center">
                            <i class="fas fa-sliders-h mr-3 text-sport-neon"></i>
                            Bộ lọc
                        </h3>
                        
                        <form method="GET" action="" class="space-y-6" id="filterForm">
<<<<<<< HEAD
                            <input type="hidden" name="page" value="1">

=======
                            <!-- Search -->
>>>>>>> 3d6d58ed3875cc3c551e3fe1991339ab7637c345
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">
                                    <i class="fas fa-search mr-2"></i>Tìm kiếm
                                </label>
                                <input type="text" 
                                       name="search" 
                                       value="<?php echo htmlspecialchars($search); ?>" 
                                       placeholder="Nhập tên sản phẩm..."
                                       class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 dark:border-gray-700 bg-white dark:bg-sport-navy text-gray-900 dark:text-white focus:border-sport-neon focus:ring-2 focus:ring-sport-neon/20 transition-all duration-300">
                            </div>
                            
<<<<<<< HEAD
=======
                            <!-- Category -->
>>>>>>> 3d6d58ed3875cc3c551e3fe1991339ab7637c345
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">
                                    <i class="fas fa-th-large mr-2"></i>Danh mục
                                </label>
                                <select name="category" 
                                        class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 dark:border-gray-700 bg-white dark:bg-sport-navy text-gray-900 dark:text-white focus:border-sport-neon focus:ring-2 focus:ring-sport-neon/20 transition-all duration-300">
                                    <option value="">Tất cả danh mục</option>
                                    <?php foreach ($categories as $cat): ?>
                                        <option value="<?php echo $cat['CategoryID']; ?>" 
                                                <?php echo $categoryId == $cat['CategoryID'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($cat['CategoryName']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
<<<<<<< HEAD
=======
                            <!-- Brand -->
>>>>>>> 3d6d58ed3875cc3c551e3fe1991339ab7637c345
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">
                                    <i class="fas fa-tag mr-2"></i>Thương hiệu
                                </label>
                                <select name="brand" 
                                        class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 dark:border-gray-700 bg-white dark:bg-sport-navy text-gray-900 dark:text-white focus:border-sport-neon focus:ring-2 focus:ring-sport-neon/20 transition-all duration-300">
                                    <option value="">Tất cả thương hiệu</option>
                                    <?php foreach ($brands as $brand): ?>
                                        <option value="<?php echo $brand['BrandID']; ?>" 
                                                <?php echo $brandId == $brand['BrandID'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($brand['BrandName']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
<<<<<<< HEAD
=======
                            <!-- Sort -->
>>>>>>> 3d6d58ed3875cc3c551e3fe1991339ab7637c345
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">
                                    <i class="fas fa-sort mr-2"></i>Sắp xếp
                                </label>
                                <select name="sort" 
                                        class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 dark:border-gray-700 bg-white dark:bg-sport-navy text-gray-900 dark:text-white focus:border-sport-neon focus:ring-2 focus:ring-sport-neon/20 transition-all duration-300">
                                    <option value="newest" <?php echo $sortBy == 'newest' ? 'selected' : ''; ?>>Mới nhất</option>
                                    <option value="price_asc" <?php echo $sortBy == 'price_asc' ? 'selected' : ''; ?>>Giá tăng dần</option>
                                    <option value="price_desc" <?php echo $sortBy == 'price_desc' ? 'selected' : ''; ?>>Giá giảm dần</option>
                                    <option value="rating" <?php echo $sortBy == 'rating' ? 'selected' : ''; ?>>Đánh giá cao</option>
                                </select>
                            </div>
                            
<<<<<<< HEAD
=======
                            <!-- Actions -->
>>>>>>> 3d6d58ed3875cc3c551e3fe1991339ab7637c345
                            <div class="space-y-3 pt-4">
                                <button type="submit" class="btn-neon w-full py-3 text-center relative z-10">
                                    <i class="fas fa-search mr-2"></i>Áp dụng
                                </button>
                                <a href="products.php" class="block w-full py-3 text-center rounded-xl glass text-gray-700 dark:text-gray-300 font-semibold hover:bg-gray-200 dark:hover:bg-gray-700 transition-all duration-300">
                                    <i class="fas fa-redo mr-2"></i>Đặt lại
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
<<<<<<< HEAD
=======
            <!-- Products Grid -->
>>>>>>> 3d6d58ed3875cc3c551e3fe1991339ab7637c345
            <div class="lg:col-span-3">
                <?php if (empty($products)): ?>
                    <div class="modern-card bg-white dark:bg-sport-blue rounded-2xl p-12 text-center shadow-lg animate-fade-in">
                        <i class="fas fa-inbox text-6xl text-gray-300 dark:text-gray-600 mb-4"></i>
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">
                            Không tìm thấy sản phẩm
                        </h3>
                        <p class="text-gray-600 dark:text-gray-400 mb-6">
                            Thử thay đổi bộ lọc hoặc tìm kiếm với từ khóa khác
                        </p>
                        <a href="products.php" class="btn-neon inline-block px-8 py-3 relative z-10">
                            <i class="fas fa-arrow-left mr-2"></i>Quay lại
                        </a>
                    </div>
                <?php else: ?>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <?php foreach ($products as $index => $product): ?>
                        <div class="modern-card bg-white dark:bg-sport-navy rounded-2xl shadow-lg overflow-hidden group animate-on-scroll"
                             style="animation-delay: <?php echo ($index % 12) * 0.05; ?>s;">
<<<<<<< HEAD
=======
                            <!-- Image -->
>>>>>>> 3d6d58ed3875cc3c551e3fe1991339ab7637c345
                            <div class="relative overflow-hidden aspect-square">
                                <img src="<?php echo $product['MainImage'] ?: 'https://via.placeholder.com/400x400/667eea/ffffff?text=SportShop'; ?>" 
                                     alt="<?php echo htmlspecialchars($product['ProductName']); ?>"
                                     class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                                
<<<<<<< HEAD
                                <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                                
=======
                                <!-- Overlay -->
                                <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                                
                                <!-- Brand Badge -->
>>>>>>> 3d6d58ed3875cc3c551e3fe1991339ab7637c345
                                <div class="absolute top-4 left-4">
                                    <span class="px-3 py-1 rounded-full glass text-white text-xs font-semibold">
                                        <?php echo htmlspecialchars($product['BrandName']); ?>
                                    </span>
                                </div>
                                
<<<<<<< HEAD
=======
                                <!-- Quick View -->
>>>>>>> 3d6d58ed3875cc3c551e3fe1991339ab7637c345
                                <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                    <a href="product_detail.php?id=<?php echo $product['ProductID']; ?>" 
                                       class="px-6 py-3 bg-white text-sport-navy rounded-xl font-bold hover:bg-sport-neon hover:text-white transition-colors duration-300">
                                        Xem chi tiết
                                    </a>
                                </div>
                            </div>
                            
<<<<<<< HEAD
=======
                            <!-- Content -->
>>>>>>> 3d6d58ed3875cc3c551e3fe1991339ab7637c345
                            <div class="p-5 space-y-3">
                                <h3 class="text-base font-bold text-gray-900 dark:text-white line-clamp-2 min-h-[3rem] group-hover:text-sport-neon transition-colors duration-300">
                                    <?php echo htmlspecialchars($product['ProductName']); ?>
                                </h3>
                                
<<<<<<< HEAD
=======
                                <!-- Rating -->
>>>>>>> 3d6d58ed3875cc3c551e3fe1991339ab7637c345
                                <div class="flex items-center space-x-1">
                                    <?php for ($i = 0; $i < 5; $i++): ?>
                                        <i class="fas fa-star <?php echo $i < $product['RatingAvg'] ? 'text-yellow-400' : 'text-gray-300'; ?> text-sm"></i>
                                    <?php endfor; ?>
                                    <span class="text-xs text-gray-600 dark:text-gray-400 ml-2">(<?php echo $product['RatingAvg']; ?>)</span>
                                </div>
                                
<<<<<<< HEAD
=======
                                <!-- Price & Action -->
>>>>>>> 3d6d58ed3875cc3c551e3fe1991339ab7637c345
                                <div class="flex items-center justify-between pt-2">
                                    <span class="text-xl font-black text-sport-neon">
                                        <?php echo formatPrice($product['Price']); ?>
                                    </span>
                                    <a href="product_detail.php?id=<?php echo $product['ProductID']; ?>" 
                                       class="w-10 h-10 rounded-full bg-gradient-to-br from-sport-neon to-blue-600 flex items-center justify-center hover:scale-110 transition-transform duration-300">
                                        <i class="fas fa-arrow-right text-white"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
<<<<<<< HEAD

                    <?php if ($totalPages > 1): ?>
                    <div class="mt-12 flex justify-center">
                        <nav class="flex items-center space-x-2 bg-white dark:bg-sport-blue p-2 rounded-xl shadow-lg">
                            
                            <?php if ($page > 1): ?>
                                <a href="<?php echo getUrl($page - 1); ?>" 
                                   class="w-10 h-10 flex items-center justify-center rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-600 dark:text-gray-300 transition-colors">
                                    <i class="fas fa-chevron-left"></i>
                                </a>
                            <?php endif; ?>

                            <?php 
                            // Logic hiển thị thông minh: Hiện trang đầu, trang cuối và các trang xung quanh trang hiện tại
                            $range = 2;
                            for ($i = 1; $i <= $totalPages; $i++): 
                                if ($i == 1 || $i == $totalPages || ($i >= $page - $range && $i <= $page + $range)):
                            ?>
                                <a href="<?php echo getUrl($i); ?>" 
                                   class="w-10 h-10 flex items-center justify-center rounded-lg font-bold transition-all duration-300 
                                   <?php echo $i == $page 
                                       ? 'bg-sport-neon text-white shadow-lg scale-110' 
                                       : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'; ?>">
                                    <?php echo $i; ?>
                                </a>
                            <?php elseif ($i == $page - $range - 1 || $i == $page + $range + 1): ?>
                                <span class="w-10 h-10 flex items-center justify-center text-gray-400">...</span>
                            <?php endif; endfor; ?> 

                            <?php if ($page < $totalPages): ?>
                                <a href="<?php echo getUrl($page + 1); ?>" 
                                   class="w-10 h-10 flex items-center justify-center rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-600 dark:text-gray-300 transition-colors">
                                    <i class="fas fa-chevron-right"></i>
                                </a>
                            <?php endif; ?>
                        </nav>
                    </div>
                    <?php endif; ?>
                    <?php endif; ?>
=======
                <?php endif; ?>
>>>>>>> 3d6d58ed3875cc3c551e3fe1991339ab7637c345
            </div>
        </div>
    </div>
</section>

<script>
    // Auto-submit filter on change
    const filterInputs = document.querySelectorAll('#filterForm select');
    filterInputs.forEach(input => {
        input.addEventListener('change', () => {
<<<<<<< HEAD
            // Khi thay đổi bộ lọc, reset về page 1 để tránh lỗi
            const pageInput = document.querySelector('input[name="page"]');
            if(pageInput) pageInput.value = 1;
=======
>>>>>>> 3d6d58ed3875cc3c551e3fe1991339ab7637c345
            document.getElementById('filterForm').submit();
        });
    });
</script>

<?php include '../includes/layout_footer.php'; ?>