<?php
header('Content-Type: text/html; charset=UTF-8');
require_once '../config.php';

$productID = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$productID) {
    redirect('products.php');
}

$db = Database::getInstance()->getConnection();

// Helper function to get correct image path
function getImagePath($imagePath) {
    if (empty($imagePath)) {
        return 'https://via.placeholder.com/600x600/667eea/ffffff?text=SportShop';
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

// Handle review submission
$reviewSuccess = '';
$reviewError = '';

// Check for success message after redirect
if (isset($_GET['review']) && $_GET['review'] === 'success') {
    $reviewSuccess = 'Đánh giá của bạn đã được gửi thành công và hiển thị bên dưới!';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_review'])) {
    if (!isLoggedIn()) {
        $reviewError = 'Bạn cần đăng nhập để đánh giá sản phẩm';
    } else {
        $rating = (int)$_POST['rating'];
        $comment = sanitizeInput($_POST['comment']);
        $userId = $_SESSION['user_id'];
        
        if ($rating < 1 || $rating > 5) {
            $reviewError = 'Vui lòng chọn số sao từ 1 đến 5';
        } else {
            $stmt = $db->prepare("SELECT ReviewID FROM Reviews WHERE UserID = ? AND ProductID = ?");
            $stmt->execute([$userId, $productID]);
            
            if ($stmt->fetch()) {
                $reviewError = 'Bạn đã đánh giá sản phẩm này rồi';
            } else {
                try {
                    // Allow empty comment - set to NULL if empty
                    $commentValue = !empty($comment) ? $comment : NULL;
                    
                    $stmt = $db->prepare("
                        INSERT INTO Reviews (UserID, ProductID, Rating, Content, Status)
                        VALUES (?, ?, ?, ?, 'approved') 
                    ");
                    
                    if ($stmt->execute([$userId, $productID, $rating, $commentValue])) {
                        $reviewSuccess = 'Đánh giá của bạn đã được gửi thành công!';
                        // Refresh page to show new review
                        header('Location: ' . $_SERVER['PHP_SELF'] . '?id=' . $productID . '&review=success');
                        exit;
                    } else {
                        $reviewError = 'Có lỗi xảy ra. Vui lòng thử lại.';
                    }
                } catch (PDOException $e) {
                    $reviewError = 'Có lỗi xảy ra: ' . $e->getMessage();
                }
            }
        }
    }
}

// Get product details
$stmt = $db->prepare("
    SELECT p.*, b.BrandName, c.CategoryName 
    FROM Product p
    LEFT JOIN Brand b ON p.BrandID = b.BrandID
    LEFT JOIN Categories c ON p.CategoryID = c.CategoryID
    WHERE p.ProductID = ?
");
$stmt->execute([$productID]);
$product = $stmt->fetch();

if (!$product) {
    redirect('products.php');
}

// Get variants
$stmt_variants = $db->prepare("SELECT * FROM ProductDetail WHERE ProductID = ? AND Quantity > 0");
$stmt_variants->execute([$productID]);
$variants = $stmt_variants->fetchAll();

$options = [
    'sizes' => [],
    'colors' => [],
    'has_size_only' => false,      // Has variants with only size (no color)
    'has_color_only' => false,     // Has variants with only color (no size)
    'has_both' => false            // Has variants with both size and color
];

foreach ($variants as $variant) {
    $hasSize = !empty($variant['Size']);
    $hasColor = !empty($variant['Color']);
    
    // Track variant types
    if ($hasSize && $hasColor) {
        $options['has_both'] = true;
    } elseif ($hasSize && !$hasColor) {
        $options['has_size_only'] = true;
    } elseif (!$hasSize && $hasColor) {
        $options['has_color_only'] = true;
    }
    
    // Only add size if not empty and not already in array
    if ($hasSize && !in_array($variant['Size'], $options['sizes'])) {
        $options['sizes'][] = $variant['Size'];
    }
    // Only add color if not empty and not already in array
    if ($hasColor && !in_array($variant['Color'], $options['colors'])) {
        $options['colors'][] = $variant['Color'];
    }
}
$variants_json = json_encode($variants);

// Check if current user has already reviewed this product
$userHasReviewed = false;
if (isLoggedIn()) {
    $stmt_check = $db->prepare("SELECT ReviewID FROM Reviews WHERE UserID = ? AND ProductID = ?");
    $stmt_check->execute([$_SESSION['user_id'], $productID]);
    $userHasReviewed = $stmt_check->fetch() !== false;
}

// Get reviews
$stmt_reviews = $db->prepare("
    SELECT r.*, CONCAT(u.FirstName, ' ', u.LastName) as FullName
    FROM Reviews r
    JOIN Users u ON r.UserID = u.UserID
    WHERE r.ProductID = ? AND r.Status = 'approved'
    ORDER BY r.created_at DESC
    LIMIT 10
");
$stmt_reviews->execute([$productID]);
$reviews = $stmt_reviews->fetchAll();

// Get related products
$stmt_related = $db->prepare("
    SELECT p.*, b.BrandName 
    FROM Product p
    LEFT JOIN Brand b ON p.BrandID = b.BrandID
    WHERE p.CategoryID = ? AND p.ProductID != ? AND p.Status = 'active'
    LIMIT 4
");
$stmt_related->execute([$product['CategoryID'], $productID]);
$relatedProducts = $stmt_related->fetchAll();

$pageTitle = $product['ProductName'];
$isInPages = true;
include '../includes/layout_header.php';
?>

<!-- Breadcrumb -->
<section class="py-6 bg-gray-50 dark:bg-sport-navy border-b border-gray-200 dark:border-gray-800">
    <div class="container mx-auto px-4 lg:px-8">
        <nav class="flex items-center space-x-2 text-sm">
            <a href="../index.php" class="text-gray-600 dark:text-gray-400 hover:text-sport-neon transition-colors duration-300">
                <i class="fas fa-home"></i> Trang chủ
            </a>
            <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
            <a href="products.php" class="text-gray-600 dark:text-gray-400 hover:text-sport-neon transition-colors duration-300">
                Sản phẩm
            </a>
            <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
            <a href="products.php?category=<?php echo $product['CategoryID']; ?>" class="text-gray-600 dark:text-gray-400 hover:text-sport-neon transition-colors duration-300">
                <?php echo htmlspecialchars($product['CategoryName']); ?>
            </a>
            <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
            <span class="text-gray-900 dark:text-white font-semibold"><?php echo htmlspecialchars($product['ProductName']); ?></span>
        </nav>
    </div>
</section>

<!-- Product Detail -->
<section class="py-12 bg-white dark:bg-sport-blue">
    <div class="container mx-auto px-4 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
            <!-- Product Images -->
            <div class="space-y-4 animate-slide-up">
                <!-- Main Image -->
                <div class="relative overflow-hidden rounded-3xl bg-gray-100 dark:bg-sport-navy aspect-square group">
                    <img id="main-image" 
                         src="<?php echo getImagePath($product['MainImage']); ?>" 
                         alt="<?php echo htmlspecialchars($product['ProductName']); ?>"
                         class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500 cursor-zoom-in">
                    
                    <!-- Zoom Icon -->
                    <div class="absolute top-4 right-4 w-12 h-12 rounded-full glass flex items-center justify-center">
                        <i class="fas fa-search-plus text-white"></i>
                    </div>
                    
                    <!-- Sale Badge -->
                    <?php if ($product['Price'] < 1000000): ?>
                    <div class="absolute top-4 left-4 px-4 py-2 bg-red-500 text-white rounded-full font-bold text-sm">
                        <i class="fas fa-fire mr-1"></i>HOT
                    </div>
                    <?php endif; ?>
                </div>
                
                <!-- Thumbnail Images -->
                <div class="grid grid-cols-4 gap-4">
                    <!-- Main Product Image -->
                    <button onclick="changeImage('<?php echo getImagePath($product['MainImage']); ?>')" 
                            class="aspect-square rounded-xl overflow-hidden border-2 border-sport-neon hover:border-blue-500 transition-all cursor-pointer group"
                            title="Ảnh chính">
                        <img src="<?php echo getImagePath($product['MainImage']); ?>" 
                             alt="Main Image"
                             class="w-full h-full object-cover group-hover:scale-110 transition-transform">
                    </button>
                    
                    <!-- Variant Images -->
                    <?php 
                    // Get unique variant images
                    $variantImages = [];
                    foreach ($variants as $variant) {
                        if (!empty($variant['Image']) && !in_array($variant['Image'], $variantImages)) {
                            $variantImages[] = $variant['Image'];
                        }
                    }
                    
                    // Display variant images (max 3 more)
                    foreach (array_slice($variantImages, 0, 3) as $variantImage): 
                    ?>
                    <button onclick="changeImage('<?php echo getImagePath($variantImage); ?>')" 
                            class="aspect-square rounded-xl overflow-hidden border-2 border-gray-300 dark:border-gray-600 hover:border-sport-neon transition-all cursor-pointer group"
                            title="<?php echo htmlspecialchars($variantImage); ?>">
                        <img src="<?php echo getImagePath($variantImage); ?>" 
                             alt="Variant Image"
                             class="w-full h-full object-cover group-hover:scale-110 transition-transform">
                    </button>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <!-- Product Info -->
            <div class="space-y-6 animate-slide-up" style="animation-delay: 0.2s;">
                <!-- Brand -->
                <div class="flex items-center space-x-3">
                    <span class="px-4 py-2 rounded-full glass text-sport-neon font-semibold text-sm">
                        <i class="fas fa-tag mr-2"></i><?php echo htmlspecialchars($product['BrandName']); ?>
                    </span>
                    <div class="flex items-center space-x-1">
                        <?php for ($i = 0; $i < 5; $i++): ?>
                            <i class="fas fa-star <?php echo $i < $product['RatingAvg'] ? 'text-yellow-400' : 'text-gray-300'; ?>"></i>
                        <?php endfor; ?>
                        <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">
                            (<?php echo $product['RatingAvg']; ?>/5)
                        </span>
                    </div>
                </div>
                
                <!-- Product Name -->
                <h1 class="text-4xl lg:text-5xl font-black text-gray-900 dark:text-white leading-tight">
                    <?php echo htmlspecialchars($product['ProductName']); ?>
                </h1>
                
                <!-- Price -->
                <div class="flex items-baseline space-x-4 py-4 border-y border-gray-200 dark:border-gray-700">
                    <span class="text-5xl font-black text-sport-neon">
                        <?php echo formatPrice($product['Price']); ?>
                    </span>
                </div>
                
                <!-- Description -->
                <div class="prose dark:prose-invert">
                    <?php 
                    $description = $product['Description'];
                    $lines = explode("\n", $description);
                    $lineCount = count($lines);
                    $isLong = $lineCount > 5;
                    $displayText = $isLong ? implode("\n", array_slice($lines, 0, 5)) : $description;
                    $hiddenText = $isLong ? implode("\n", array_slice($lines, 5)) : '';
                    ?>
                    <p class="text-gray-600 dark:text-gray-400 text-lg leading-relaxed" id="description-text">
                        <?php echo nl2br(htmlspecialchars($displayText)); ?>
                    </p>
                    <?php if ($isLong): ?>
                    <div id="description-hidden" class="hidden">
                        <p class="text-gray-600 dark:text-gray-400 text-lg leading-relaxed">
                            <?php echo nl2br(htmlspecialchars($hiddenText)); ?>
                        </p>
                    </div>
                    <button onclick="toggleDescription()" class="mt-3 px-6 py-2 rounded-xl bg-sport-neon hover:bg-blue-600 text-white font-semibold transition-all duration-300">
                        <i class="fas fa-chevron-down mr-2"></i><span id="toggle-text">Xem thêm</span>
                    </button>
                    <?php endif; ?>
                </div>
                
                <!-- Variant Selection -->
                <div class="space-y-4 p-6 rounded-2xl glass">
                    <?php if (!empty($options['sizes'])): ?>
                    <div>
                        <label class="block text-sm font-bold text-gray-900 dark:text-white mb-3">
                            <i class="fas fa-ruler mr-2"></i>Chọn size:
                        </label>
                        <div class="flex flex-wrap gap-3" id="size-options">
                            <?php foreach ($options['sizes'] as $size): ?>
                            <button type="button" 
                                    onclick="selectSize('<?php echo $size; ?>')" 
                                    data-size="<?php echo $size; ?>"
                                    class="size-btn px-6 py-3 rounded-xl border-2 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 font-semibold hover:border-sport-neon hover:text-sport-neon transition-all duration-300">
                                <?php echo $size; ?>
                            </button>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($options['colors'])): ?>
                    <div>
                        <label class="block text-sm font-bold text-gray-900 dark:text-white mb-3">
                            <i class="fas fa-palette mr-2"></i>Chọn màu:
                        </label>
                        <div class="flex flex-wrap gap-3" id="color-options">
                            <?php foreach ($options['colors'] as $color): ?>
                            <button type="button" 
                                    onclick="selectColor('<?php echo $color; ?>')" 
                                    data-color="<?php echo $color; ?>"
                                    class="color-btn px-6 py-3 rounded-xl border-2 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 font-semibold hover:border-sport-neon hover:text-sport-neon transition-all duration-300">
                                <?php echo $color; ?>
                            </button>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Quantity (Hidden until variant is selected) -->
                    <div id="quantity-section" class="hidden">
                        <label class="block text-sm font-bold text-gray-900 dark:text-white mb-3">
                            <i class="fas fa-shopping-cart mr-2"></i>Số lượng:
                        </label>
                        <div class="flex items-center space-x-4">
                            <button onclick="changeQuantity(-1)" 
                                    class="w-12 h-12 rounded-xl bg-gray-100 dark:bg-sport-navy hover:bg-sport-neon hover:text-white transition-all duration-300 font-bold">
                                <i class="fas fa-minus"></i>
                            </button>
                            <input type="number" 
                                   id="quantity" 
                                   value="1" 
                                   min="1" 
                                   class="w-20 text-center py-3 rounded-xl border-2 border-gray-200 dark:border-gray-700 bg-white dark:bg-sport-navy text-gray-900 dark:text-white font-bold text-xl">
                            <button onclick="changeQuantity(1)" 
                                    class="w-12 h-12 rounded-xl bg-gray-100 dark:bg-sport-navy hover:bg-sport-neon hover:text-white transition-all duration-300 font-bold">
                                <i class="fas fa-plus"></i>
                            </button>
                            <span id="stock-info" class="text-sm text-gray-600 dark:text-gray-400 ml-auto">
                                Còn: <span id="available-stock" class="font-bold text-green-500">0</span> sản phẩm
                            </span>
                        </div>
                    </div>
                </div>
                
                <!-- Action Buttons (Disabled until variant is selected) -->
                <div id="action-buttons" class="flex flex-col sm:flex-row gap-4">
                    <button id="add-to-cart-btn"
                            onclick="addToCart()" 
                            disabled
                            class="flex-1 py-4 bg-gradient-to-r from-sport-neon to-blue-600 text-white font-bold rounded-xl hover:shadow-lg hover:shadow-sport-neon/50 transform hover:scale-105 transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:shadow-none disabled:hover:scale-100">
                        <i class="fas fa-cart-plus mr-2"></i>Thêm vào giỏ hàng
                    </button>
                    <button id="buy-now-btn"
                            onclick="buyNow()" 
                            disabled
                            class="flex-1 py-4 bg-gradient-to-r from-orange-500 to-red-600 text-white font-bold rounded-xl hover:shadow-lg hover:shadow-orange-500/50 transform hover:scale-105 transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:shadow-none disabled:hover:scale-100">
                        <i class="fas fa-bolt mr-2"></i>Mua ngay
                    </button>
                </div>
                
                <!-- Message when no variant selected -->
                <div id="select-variant-message" class="p-4 rounded-xl bg-blue-50 dark:bg-blue-900/20 border-l-4 border-blue-500">
                    <p class="text-sm text-blue-700 dark:text-blue-300">
                        <i class="fas fa-info-circle mr-2"></i>Vui lòng chọn size hoặc màu để tiếp tục
                    </p>
                </div>
                
                <!-- Features -->
                <div class="grid grid-cols-3 gap-4 pt-6">
                    <div class="text-center p-4 rounded-xl glass">
                        <i class="fas fa-shipping-fast text-3xl text-sport-neon mb-2"></i>
                        <p class="text-xs text-gray-600 dark:text-gray-400 font-semibold">Giao hàng nhanh</p>
                    </div>
                    <div class="text-center p-4 rounded-xl glass">
                        <i class="fas fa-shield-alt text-3xl text-green-500 mb-2"></i>
                        <p class="text-xs text-gray-600 dark:text-gray-400 font-semibold">Chính hãng 100%</p>
                    </div>
                    <div class="text-center p-4 rounded-xl glass">
                        <i class="fas fa-undo text-3xl text-purple-500 mb-2"></i>
                        <p class="text-xs text-gray-600 dark:text-gray-400 font-semibold">Đổi trả 30 ngày</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Reviews Section -->
<section class="py-16 bg-gray-50 dark:bg-sport-navy">
    <div class="container mx-auto px-4 lg:px-8">
        <h2 class="text-4xl font-black text-gray-900 dark:text-white mb-8">
            <i class="fas fa-star text-yellow-400 mr-3"></i>Đánh giá sản phẩm
        </h2>
        
        <?php if ($reviewSuccess): ?>
        <div class="mb-6 p-4 bg-green-500/20 border border-green-500 rounded-2xl text-green-700 dark:text-green-400">
            <i class="fas fa-check-circle mr-2"></i><?php echo $reviewSuccess; ?>
        </div>
        <?php endif; ?>
        
        <?php if ($reviewError): ?>
        <div class="mb-6 p-4 bg-red-500/20 border border-red-500 rounded-2xl text-red-700 dark:text-red-400">
            <i class="fas fa-exclamation-circle mr-2"></i><?php echo $reviewError; ?>
        </div>
        <?php endif; ?>
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Reviews List -->
            <div class="lg:col-span-2 space-y-4">
                <?php if (empty($reviews)): ?>
                <div class="modern-card bg-white dark:bg-sport-blue rounded-2xl p-12 text-center">
                    <i class="fas fa-comment-slash text-6xl text-gray-300 dark:text-gray-600 mb-4"></i>
                    <p class="text-gray-600 dark:text-gray-400">Chưa có đánh giá nào</p>
                </div>
                <?php else: ?>
                    <?php foreach ($reviews as $review): ?>
                    <div class="modern-card bg-white dark:bg-sport-blue rounded-2xl p-6 shadow-lg">
                        <div class="flex items-start space-x-4">
                            <div class="w-12 h-12 rounded-full bg-gradient-to-br from-sport-neon to-blue-600 flex items-center justify-center flex-shrink-0">
                                <span class="text-white font-bold"><?php echo strtoupper(substr($review['FullName'], 0, 1)); ?></span>
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center justify-between mb-2">
                                    <h4 class="font-bold text-gray-900 dark:text-white"><?php echo htmlspecialchars($review['FullName']); ?></h4>
                                    <span class="text-sm text-gray-500 dark:text-gray-400">
                                        <?php echo date('d/m/Y', strtotime($review['created_at'])); ?>
                                    </span>
                                </div>
                                <div class="flex items-center mb-3">
                                    <?php for ($i = 0; $i < 5; $i++): ?>
                                        <i class="fas fa-star <?php echo $i < $review['Rating'] ? 'text-yellow-400' : 'text-gray-300'; ?>"></i>
                                    <?php endfor; ?>
                                </div>
                                <?php if (!empty($review['Content'])): ?>
                                <p class="text-gray-600 dark:text-gray-400">
                                    <?php echo nl2br(htmlspecialchars($review['Content'])); ?>
                                </p>
                                <?php else: ?>
                                <p class="text-gray-500 dark:text-gray-500 italic text-sm">
                                    Khách hàng chưa để lại bình luận
                                </p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            
            <!-- Review Form -->
            <div class="lg:col-span-1">
                <div class="modern-card bg-white dark:bg-sport-blue rounded-2xl p-6 shadow-lg sticky top-24">
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Viết đánh giá</h3>
                    
                    <?php if ($reviewSuccess): ?>
                    <div class="mb-4 p-4 bg-green-50 dark:bg-green-900/20 border-l-4 border-green-500 rounded-xl animate-slide-down">
                        <div class="flex items-center">
                            <i class="fas fa-check-circle text-green-500 text-xl mr-3"></i>
                            <p class="text-green-700 dark:text-green-400 font-semibold"><?php echo $reviewSuccess; ?></p>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if ($reviewError): ?>
                    <div class="mb-4 p-4 bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 rounded-xl animate-shake">
                        <div class="flex items-center">
                            <i class="fas fa-exclamation-circle text-red-500 text-xl mr-3"></i>
                            <p class="text-red-700 dark:text-red-400 font-semibold"><?php echo $reviewError; ?></p>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (isLoggedIn()): ?>
                        <?php if ($userHasReviewed): ?>
                        <div class="text-center p-6">
                            <div class="w-16 h-16 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-check-circle text-4xl text-green-500"></i>
                            </div>
                            <p class="text-gray-700 dark:text-gray-300 font-semibold mb-2">Bạn đã đánh giá sản phẩm này</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Cảm ơn bạn đã chia sẻ trải nghiệm!</p>
                        </div>
                        <?php else: ?>
                        <form method="POST" class="space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Đánh giá của bạn:</label>
                            <div class="flex space-x-2 justify-center py-4" id="star-rating">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                <input type="radio" name="rating" value="<?php echo $i; ?>" id="star<?php echo $i; ?>" class="hidden" required>
                                <label for="star<?php echo $i; ?>" class="text-4xl cursor-pointer transition-all duration-200 star-label" data-rating="<?php echo $i; ?>">
                                    <i class="fas fa-star text-gray-300"></i>
                                </label>
                                <?php endfor; ?>
                            </div>
                            <p class="text-center text-sm text-gray-500 dark:text-gray-400 mb-2" id="rating-text">Chọn số sao</p>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Nội dung: <span class="text-gray-500 text-xs">(không bắt buộc)</span></label>
                            <textarea name="comment" 
                                      rows="4" 
                                      class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 dark:border-gray-700 bg-white dark:bg-sport-navy text-gray-900 dark:text-white focus:border-sport-neon focus:ring-2 focus:ring-sport-neon/20 transition-all duration-300"
                                      placeholder="Chia sẻ trải nghiệm của bạn... (không bắt buộc)"></textarea>
                        </div>
                        <button type="submit" 
                                name="submit_review"
                                class="w-full py-3 bg-gradient-to-r from-sport-neon to-blue-600 text-white font-bold rounded-xl hover:shadow-lg hover:shadow-sport-neon/50 transition-all duration-300">
                            <i class="fas fa-paper-plane mr-2"></i>Gửi đánh giá
                        </button>
                    </form>
                        <?php endif; ?>
                    <?php else: ?>
                    <div class="text-center p-6">
                        <i class="fas fa-lock text-4xl text-gray-300 dark:text-gray-600 mb-4"></i>
                        <p class="text-gray-600 dark:text-gray-400 mb-4">Vui lòng đăng nhập để đánh giá</p>
                        <a href="login.php" class="btn-neon inline-block px-6 py-3 relative z-10">
                            Đăng nhập
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Related Products -->
<?php if (!empty($relatedProducts)): ?>
<section class="py-16 bg-white dark:bg-sport-blue">
    <div class="container mx-auto px-4 lg:px-8">
        <h2 class="text-4xl font-black text-gray-900 dark:text-white mb-8">
            <i class="fas fa-box-open mr-3 text-sport-neon"></i>Sản phẩm tương tự
        </h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <?php foreach ($relatedProducts as $relProd): ?>
            <a href="product_detail.php?id=<?php echo $relProd['ProductID']; ?>" 
               class="modern-card bg-white dark:bg-sport-navy rounded-2xl shadow-lg overflow-hidden group">
                <div class="relative overflow-hidden aspect-square">
                    <img src="<?php echo $relProd['MainImage'] ?: 'https://via.placeholder.com/300'; ?>" 
                         alt="<?php echo htmlspecialchars($relProd['ProductName']); ?>"
                         class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                </div>
                <div class="p-4">
                    <h3 class="font-bold text-gray-900 dark:text-white line-clamp-2 mb-2">
                        <?php echo htmlspecialchars($relProd['ProductName']); ?>
                    </h3>
                    <p class="text-xl font-black text-sport-neon">
                        <?php echo formatPrice($relProd['Price']); ?>
                    </p>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<script>
// Variants data
const variants = <?php echo $variants_json; ?>;
let selectedSize = '';
let selectedColor = '';

// Select size (toggle on/off)
function selectSize(size) {
    // If clicking the same size, deselect it
    if (selectedSize === size) {
        selectedSize = '';
        document.querySelectorAll('.size-btn').forEach(btn => {
            btn.classList.remove('border-sport-neon', 'text-sport-neon', 'bg-sport-neon/10');
        });
    } else {
        // Select new size
        selectedSize = size;
        document.querySelectorAll('.size-btn').forEach(btn => {
            btn.classList.remove('border-sport-neon', 'text-sport-neon', 'bg-sport-neon/10');
        });
        document.querySelector(`[data-size="${size}"]`).classList.add('border-sport-neon', 'text-sport-neon', 'bg-sport-neon/10');
    }
    
    // Change image based on selected size and color
    // If color is selected, find variant with both size and color
    // If color is not selected, find variant with just size
    let variant;
    if (selectedSize && selectedColor) {
        variant = variants.find(v => v.Size === selectedSize && v.Color === selectedColor);
    } else if (selectedSize) {
        variant = variants.find(v => v.Size === selectedSize);
    } else if (selectedColor) {
        variant = variants.find(v => v.Color === selectedColor);
    }
    
    if (variant && variant.Image) {
        changeImage(variant.Image);
    } else {
        // No variant selected, show main image
        changeImage(<?php echo json_encode(getImagePath($product['MainImage'])); ?>);
    }
    
    updateStock();
}

// Select color (toggle on/off)
function selectColor(color) {
    // If clicking the same color, deselect it
    if (selectedColor === color) {
        selectedColor = '';
        document.querySelectorAll('.color-btn').forEach(btn => {
            btn.classList.remove('border-sport-neon', 'text-sport-neon', 'bg-sport-neon/10');
        });
    } else {
        // Select new color
        selectedColor = color;
        document.querySelectorAll('.color-btn').forEach(btn => {
            btn.classList.remove('border-sport-neon', 'text-sport-neon', 'bg-sport-neon/10');
        });
        document.querySelector(`[data-color="${color}"]`).classList.add('border-sport-neon', 'text-sport-neon', 'bg-sport-neon/10');
    }
    
    // Change image based on selected size and color
    // If size is selected, find variant with both size and color
    // If size is not selected, find variant with just color
    let variant;
    if (selectedSize && selectedColor) {
        variant = variants.find(v => v.Size === selectedSize && v.Color === selectedColor);
    } else if (selectedSize) {
        variant = variants.find(v => v.Size === selectedSize);
    } else if (selectedColor) {
        variant = variants.find(v => v.Color === selectedColor);
    }
    
    if (variant && variant.Image) {
        changeImage(variant.Image);
    } else {
        // No variant selected, show main image
        changeImage(<?php echo json_encode(getImagePath($product['MainImage'])); ?>);
    }
    
    updateStock();
}

// Update stock
function updateStock() {
    // Find variant based on what's selected
    let variant;
    <?php if (!empty($options['sizes']) && !empty($options['colors'])): ?>
    // Product has both size and color
    variant = variants.find(v => v.Size === selectedSize && v.Color === selectedColor);
    <?php elseif (!empty($options['sizes'])): ?>
    // Product has only size
    variant = variants.find(v => v.Size === selectedSize);
    <?php elseif (!empty($options['colors'])): ?>
    // Product has only color
    variant = variants.find(v => v.Color === selectedColor);
    <?php else: ?>
    // Product has no size or color
    variant = variants[0];
    <?php endif; ?>
    
    const stockEl = document.getElementById('available-stock');
    const quantitySection = document.getElementById('quantity-section');
    const selectMessage = document.getElementById('select-variant-message');
    const addToCartBtn = document.getElementById('add-to-cart-btn');
    const buyNowBtn = document.getElementById('buy-now-btn');
    
    if (variant) {
        // Variant selected - show quantity and enable buttons
        stockEl.textContent = variant.Quantity;
        stockEl.className = 'font-bold ' + (variant.Quantity > 10 ? 'text-green-500' : 'text-red-500');
        
        // Show quantity section
        if (quantitySection) quantitySection.classList.remove('hidden');
        if (selectMessage) selectMessage.classList.add('hidden');
        
        // Enable buttons
        if (addToCartBtn) addToCartBtn.disabled = false;
        if (buyNowBtn) buyNowBtn.disabled = false;
    } else {
        // No variant selected - hide quantity and disable buttons
        stockEl.textContent = '0';
        stockEl.className = 'font-bold text-gray-400';
        
        // Hide quantity section
        if (quantitySection) quantitySection.classList.add('hidden');
        if (selectMessage) selectMessage.classList.remove('hidden');
        
        // Disable buttons
        if (addToCartBtn) addToCartBtn.disabled = true;
        if (buyNowBtn) buyNowBtn.disabled = true;
    }
}

// Change quantity
function changeQuantity(delta) {
    const input = document.getElementById('quantity');
    const newValue = parseInt(input.value) + delta;
    if (newValue >= 1) {
        input.value = newValue;
    }
}

// Add to cart
function addToCart() {
    const productId = <?php echo $productID; ?>;
    const productName = <?php echo json_encode($product['ProductName']); ?>;
    const price = <?php echo $product['Price']; ?>;
    const image = <?php echo json_encode($product['MainImage']); ?>;
    const brand = <?php echo json_encode($product['BrandName']); ?>;
    const quantity = parseInt(document.getElementById('quantity').value);
    
    // Validate selection based on variant types
    <?php if ($options['has_both']): ?>
    // Product has variants with both size and color - require both
    if (!selectedSize) {
        alert('Vui lòng chọn size!');
        return;
    }
    if (!selectedColor) {
        alert('Vui lòng chọn màu!');
        return;
    }
    <?php elseif ($options['has_size_only'] && !$options['has_color_only']): ?>
    // Product has only size variants - require size
    if (!selectedSize) {
        alert('Vui lòng chọn size!');
        return;
    }
    <?php elseif ($options['has_color_only'] && !$options['has_size_only']): ?>
    // Product has only color variants - require color
    if (!selectedColor) {
        alert('Vui lòng chọn màu!');
        return;
    }
    <?php elseif ($options['has_size_only'] && $options['has_color_only']): ?>
    // Product has mixed variants (some with only size, some with only color)
    // Require at least one to be selected
    if (!selectedSize && !selectedColor) {
        alert('Vui lòng chọn size hoặc màu!');
        return;
    }
    <?php endif; ?>
    
    // Find variant and check stock
    // Support products with only size, only color, or both
    let variant;
    <?php if (!empty($options['sizes']) && !empty($options['colors'])): ?>
    // Product has both size and color
    variant = variants.find(v => v.Size === selectedSize && v.Color === selectedColor);
    <?php elseif (!empty($options['sizes'])): ?>
    // Product has only size
    variant = variants.find(v => v.Size === selectedSize);
    <?php elseif (!empty($options['colors'])): ?>
    // Product has only color
    variant = variants.find(v => v.Color === selectedColor);
    <?php else: ?>
    // Product has no size or color (use first variant)
    variant = variants[0];
    <?php endif; ?>
    
    if (!variant) {
        alert('Không tìm thấy sản phẩm!');
        return;
    }
    
    if (variant.Quantity < quantity) {
        alert(`Chỉ còn ${variant.Quantity} sản phẩm trong kho!`);
        return;
    }
    
    // Get cart from localStorage
    let cart = JSON.parse(localStorage.getItem('cart') || '[]');
    
    // Check if item already exists (same productDetailID)
    const existingIndex = cart.findIndex(item => 
        item.productDetailId === variant.ProductDetailID
    );
    
    if (existingIndex >= 0) {
        // Update quantity of existing item
        const newQuantity = cart[existingIndex].quantity + quantity;
        if (newQuantity > variant.Quantity) {
            alert(`Chỉ còn ${variant.Quantity} sản phẩm trong kho!`);
            return;
        }
        cart[existingIndex].quantity = newQuantity;
    } else {
        // Add new item
        const item = {
            id: productId,
            productDetailId: variant.ProductDetailID, // Quan trọng: lưu variant ID
            name: productName,
            price: price,
            image: variant.Image || image, // Ưu tiên ảnh variant
            brand: brand,
            size: selectedSize,
            color: selectedColor,
            quantity: quantity
        };
        cart.push(item);
    }
    
    localStorage.setItem('cart', JSON.stringify(cart));
    
    // Show success message
    alert('✓ Đã thêm vào giỏ hàng!');
}

// Buy now
function buyNow() {
    const productId = <?php echo $productID; ?>;
    const productName = <?php echo json_encode($product['ProductName']); ?>;
    const price = <?php echo $product['Price']; ?>;
    const image = <?php echo json_encode($product['MainImage']); ?>;
    const brand = <?php echo json_encode($product['BrandName']); ?>;
    const quantity = parseInt(document.getElementById('quantity').value);
    
    // Validate selection based on variant types
    <?php if ($options['has_both']): ?>
    // Product has variants with both size and color - require both
    if (!selectedSize) {
        alert('Vui lòng chọn size!');
        return;
    }
    if (!selectedColor) {
        alert('Vui lòng chọn màu!');
        return;
    }
    <?php elseif ($options['has_size_only'] && !$options['has_color_only']): ?>
    // Product has only size variants - require size
    if (!selectedSize) {
        alert('Vui lòng chọn size!');
        return;
    }
    <?php elseif ($options['has_color_only'] && !$options['has_size_only']): ?>
    // Product has only color variants - require color
    if (!selectedColor) {
        alert('Vui lòng chọn màu!');
        return;
    }
    <?php elseif ($options['has_size_only'] && $options['has_color_only']): ?>
    // Product has mixed variants (some with only size, some with only color)
    // Require at least one to be selected
    if (!selectedSize && !selectedColor) {
        alert('Vui lòng chọn size hoặc màu!');
        return;
    }
    <?php endif; ?>
    
    // Find variant and check stock
    // Support products with only size, only color, or both
    let variant;
    <?php if (!empty($options['sizes']) && !empty($options['colors'])): ?>
    // Product has both size and color
    variant = variants.find(v => v.Size === selectedSize && v.Color === selectedColor);
    <?php elseif (!empty($options['sizes'])): ?>
    // Product has only size
    variant = variants.find(v => v.Size === selectedSize);
    <?php elseif (!empty($options['colors'])): ?>
    // Product has only color
    variant = variants.find(v => v.Color === selectedColor);
    <?php else: ?>
    // Product has no size or color (use first variant)
    variant = variants[0];
    <?php endif; ?>
    
    if (!variant) {
        alert('Không tìm thấy sản phẩm!');
        return;
    }
    
    if (variant.Quantity < quantity) {
        alert(`Chỉ còn ${variant.Quantity} sản phẩm trong kho!`);
        return;
    }
    
    // Create single item for buy now (override cart)
    const item = {
        id: productId,
        productDetailId: variant.ProductDetailID,
        name: productName,
        price: price,
        image: variant.Image || image,
        brand: brand,
        size: selectedSize,
        color: selectedColor,
        quantity: quantity
    };
    
    // Set buy now mode (single item checkout)
    localStorage.setItem('cart', JSON.stringify([item]));
    localStorage.setItem('buy_now_mode', 'true');
    
    // Redirect to checkout
    window.location.href = 'checkout.php';
}

// Change main image
function changeImage(src) {
    document.getElementById('main-image').src = src;
}

// Toggle description
function toggleDescription() {
    const hiddenDiv = document.getElementById('description-hidden');
    const toggleText = document.getElementById('toggle-text');
    const toggleBtn = event.target.closest('button');
    
    if (hiddenDiv.classList.contains('hidden')) {
        hiddenDiv.classList.remove('hidden');
        toggleText.textContent = 'Ẩn bớt';
        toggleBtn.innerHTML = '<i class="fas fa-chevron-up mr-2"></i><span id="toggle-text">Ẩn bớt</span>';
    } else {
        hiddenDiv.classList.add('hidden');
        toggleText.textContent = 'Xem thêm';
        toggleBtn.innerHTML = '<i class="fas fa-chevron-down mr-2"></i><span id="toggle-text">Xem thêm</span>';
    }
}

// Star rating
document.querySelectorAll('input[name="rating"]').forEach((input, index) => {
    input.addEventListener('change', () => {
        const labels = document.querySelectorAll('label[for^="star"]');
        labels.forEach((label, i) => {
            if (i >= (5 - parseInt(input.value))) {
                label.classList.remove('text-gray-300');
                label.classList.add('text-yellow-400');
            } else {
                label.classList.add('text-gray-300');
                label.classList.remove('text-yellow-400');
            }
        });
    });
});

// Initialize
document.addEventListener('DOMContentLoaded', () => {
    // Don't auto-select size or color - let user choose
    // Just update stock display
    updateStock();
    
    // Scroll to reviews section after successful review submission
    <?php if (isset($_GET['review']) && $_GET['review'] === 'success'): ?>
    setTimeout(() => {
        const reviewsSection = document.querySelector('.grid.grid-cols-1.lg\\:grid-cols-3');
        if (reviewsSection) {
            reviewsSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    }, 500);
    <?php endif; ?>
    
    // Star Rating Functionality
    const starRating = document.getElementById('star-rating');
    if (starRating) {
        const starLabels = starRating.querySelectorAll('.star-label');
        const ratingText = document.getElementById('rating-text');
        let selectedRating = 0;
        
        const ratingMessages = {
            1: '⭐ Rất tệ',
            2: '⭐⭐ Tệ',
            3: '⭐⭐⭐ Trung bình',
            4: '⭐⭐⭐⭐ Tốt',
            5: '⭐⭐⭐⭐⭐ Xuất sắc'
        };
        
        // Highlight stars from left to right
        function highlightStars(rating) {
            starLabels.forEach((label, index) => {
                const star = label.querySelector('i');
                if (index < rating) {
                    star.classList.remove('text-gray-300');
                    star.classList.add('text-yellow-400');
                } else {
                    star.classList.remove('text-yellow-400');
                    star.classList.add('text-gray-300');
                }
            });
            
            if (rating > 0) {
                ratingText.textContent = ratingMessages[rating];
                ratingText.classList.remove('text-gray-500');
                ratingText.classList.add('text-yellow-500', 'font-semibold');
            }
        }
        
        // Hover effect
        starLabels.forEach((label) => {
            label.addEventListener('mouseenter', function() {
                const rating = parseInt(this.getAttribute('data-rating'));
                highlightStars(rating);
            });
        });
        
        // Reset on mouse leave if no selection
        starRating.addEventListener('mouseleave', function() {
            if (selectedRating === 0) {
                highlightStars(0);
                ratingText.textContent = 'Chọn số sao';
                ratingText.classList.add('text-gray-500');
                ratingText.classList.remove('text-yellow-500', 'font-semibold');
            } else {
                highlightStars(selectedRating);
            }
        });
        
        // Click to select
        starLabels.forEach((label) => {
            label.addEventListener('click', function() {
                selectedRating = parseInt(this.getAttribute('data-rating'));
                highlightStars(selectedRating);
                // Check the corresponding radio button
                document.getElementById('star' + selectedRating).checked = true;
            });
        });
    }
});
</script>

<?php include '../includes/layout_footer.php'; ?>
