<?php
header('Content-Type: text/html; charset=UTF-8');
require_once 'config.php';

// Lấy sản phẩm nổi bật từ API
$featuredProducts = getFeaturedProducts(8);

// Lấy danh mục từ API
$categories = getAllCategories();

$pageTitle = "Trang chủ";
$isInPages = false;
include 'includes/layout_header.php';
?>

<!-- Hero Section - Modern 2026 Design -->
<section class="relative min-h-screen flex items-center justify-center overflow-hidden bg-gradient-to-br from-sport-navy via-sport-blue to-purple-900">
    <!-- Animated Background -->
    <div class="absolute inset-0 overflow-hidden">
        <div class="absolute -top-1/2 -left-1/2 w-full h-full bg-sport-neon opacity-10 rounded-full blur-3xl animate-pulse"></div>
        <div class="absolute -bottom-1/2 -right-1/2 w-full h-full bg-blue-600 opacity-10 rounded-full blur-3xl animate-pulse" style="animation-delay: 1s;"></div>
    </div>
    
    <div class="container mx-auto px-4 lg:px-8 relative z-10">
        <div class="text-center space-y-8 animate-fade-in">
            <!-- Main Heading -->
            <h1 class="text-6xl lg:text-8xl font-black text-white leading-tight animate-slide-up">
                Chào mừng đến <br>
                <span  class="text-sport-neon">
                    SportShop
                </span>
            </h1>
            
            <!-- Subheading -->
            <p class="text-xl lg:text-2xl text-gray-300 max-w-3xl mx-auto animate-slide-up" style="animation-delay: 0.2s;">
                Cửa hàng thể thao trực tuyến hàng đầu Việt Nam
            </p>
            
            <p class="text-lg text-gray-400 max-w-2xl mx-auto animate-slide-up" style="animation-delay: 0.3s;">
                Đồ án công nghệ thiết kế web nâng cao - Nhóm 6 
            </p>
            
            <!-- CTA Buttons -->
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4 pt-8 animate-slide-up" style="animation-delay: 0.4s;">
                <a href="pages/products.php" class="btn-neon text-lg px-8 py-4 relative z-10">
                    <i class="fas fa-shopping-bag mr-2"></i>Khám phá ngay
                </a>
                <a href="#featured-products" class="px-8 py-4 rounded-xl glass text-white font-semibold hover:bg-white hover:text-sport-navy transition-all duration-300">
                    <i class="fas fa-arrow-down mr-2"></i>Xem sản phẩm
                </a>
            </div>
            
            <!-- Stats -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 pt-16 max-w-4xl mx-auto animate-scale-in" style="animation-delay: 0.5s;">
                <div class="glass p-6 rounded-2xl">
                    <div class="text-4xl font-black text-sport-neon mb-2">80+</div>
                    <div class="text-gray-300 text-sm">Sản phẩm</div>
                </div>
                <div class="glass p-6 rounded-2xl">
                    <div class="text-4xl font-black text-sport-neon mb-2">20+</div>
                    <div class="text-gray-300 text-sm">Thương hiệu</div>
                </div>
                <div class="glass p-6 rounded-2xl">
                    <div class="text-4xl font-black text-sport-neon mb-2">100+</div>
                    <div class="text-gray-300 text-sm">Khách hàng</div>
                </div>
                <div class="glass p-6 rounded-2xl">
                    <div class="text-4xl font-black text-sport-neon mb-2">4.9★</div>
                    <div class="text-gray-300 text-sm">Đánh giá</div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Scroll Indicator -->
    <div class="absolute bottom-10 left-1/2 transform -translate-x-1/2 animate-bounce">
        <i class="fas fa-chevron-down text-white text-3xl opacity-50"></i>
    </div>
</section>

<!-- Categories Section -->
<section class="py-16 bg-gradient-to-br from-white via-gray-50 to-gray-100 dark:from-sport-navy dark:via-sport-blue dark:to-sport-navy">
    <div class="container mx-auto px-4 lg:px-8">
        <!-- Section Header -->
        <div class="text-center mb-12 animate-on-scroll">
            <div class="inline-flex items-center justify-center mb-4">
                <div class="w-16 h-1 bg-gradient-to-r from-transparent to-sport-neon rounded-full"></div>
                <i class="fas fa-th-large mx-4 text-3xl text-sport-neon"></i>
                <div class="w-16 h-1 bg-gradient-to-l from-transparent to-sport-neon rounded-full"></div>
            </div>
            <h2 class="text-5xl font-black text-gray-900 dark:text-white mb-4">
                Danh mục sản phẩm
            </h2>
            <p class="text-xl text-gray-600 dark:text-gray-400 max-w-2xl mx-auto">
                Khám phá bộ sưu tập đa dạng theo từng danh mục
            </p>
        </div>
        
        <!-- Categories Grid - Centered -->
        <div class="flex flex-wrap justify-center gap-6 max-w-7xl mx-auto">
            <?php 
            // Define icons for different categories
            $categoryIcons = [
                'Giày thể thao' => 'fa-running',
                'Giày' => 'fa-shoe-prints',
                'Áo thể thao' => 'fa-tshirt',
                'Áo' => 'fa-tshirt',
                'Quần thể thao' => 'fa-vest',
                'Quần' => 'fa-vest',
                'Phụ kiện' => 'fa-glasses',
                'Túi xách' => 'fa-shopping-bag',
                'Túi' => 'fa-shopping-bag',
                'Đồ bơi' => 'fa-swimmer',
                'Bơi' => 'fa-swimmer',
                'Đồ tập gym' => 'fa-dumbbell',
                'Gym' => 'fa-dumbbell',
                'Giày chạy bộ' => 'fa-running',
                'Chạy' => 'fa-running',
                'Giày bóng đá' => 'fa-futbol',
                'Bóng đá' => 'fa-futbol',
                'Giày tennis' => 'fa-table-tennis',
                'Tennis' => 'fa-table-tennis',
                'Giày bóng rổ' => 'fa-basketball-ball',
                'Bóng rổ' => 'fa-basketball-ball',
                'Đồ yoga' => 'fa-spa',
                'Yoga' => 'fa-spa',
                'Quần áo outdoor' => 'fa-mountain',
                'Outdoor' => 'fa-mountain'
            ];
            
            foreach ($categories as $index => $category): 
                $categoryName = $category['CategoryName'];
                $icon = 'fa-tags'; // default icon
                
                foreach ($categoryIcons as $key => $value) {
                    if (stripos($categoryName, $key) !== false) {
                        $icon = $value;
                        break;
                    }
                }
            ?>
            <a href="pages/products.php?category=<?php echo $category['CategoryID']; ?>" 
               class="modern-card group bg-white dark:bg-sport-blue rounded-2xl p-6 shadow-lg hover:shadow-2xl transition-all duration-300 animate-on-scroll hover:-translate-y-2 w-40"
               style="animation-delay: <?php echo $index * 0.05; ?>s;">
                <div class="flex flex-col items-center text-center space-y-4">
                    <!-- Icon Container -->
                    <div class="relative">
                        <div class="absolute inset-0 bg-gradient-to-br from-sport-neon to-blue-600 rounded-2xl blur-lg opacity-50 group-hover:opacity-75 transition-opacity duration-300"></div>
                        <div class="relative w-16 h-16 rounded-2xl bg-gradient-to-br from-sport-neon to-blue-600 flex items-center justify-center group-hover:scale-110 group-hover:rotate-12 transition-all duration-300 shadow-xl">
                            <i class="fas <?php echo $icon; ?> text-2xl text-white"></i>
                        </div>
                    </div>
                    
                    <!-- Category Info -->
                    <div class="w-full">
                        <h3 class="text-base font-bold text-gray-900 dark:text-white group-hover:text-sport-neon transition-colors duration-300 line-clamp-2 min-h-[3rem] flex items-center justify-center">
                            <?php echo htmlspecialchars($category['CategoryName']); ?>
                        </h3>
                        <div class="mt-2 h-0.5 w-0 group-hover:w-full bg-gradient-to-r from-sport-neon to-blue-600 mx-auto transition-all duration-300 rounded-full"></div>
                        <p class="text-xs text-sport-neon font-semibold mt-2 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                            Khám phá →
                        </p>
                    </div>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
        
        <!-- View All Button -->
        <div class="text-center mt-12 animate-on-scroll">
            <a href="pages/products.php" class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-sport-neon to-blue-600 text-white font-bold rounded-xl hover:shadow-xl hover:scale-105 transition-all duration-300">
                <i class="fas fa-grid mr-2"></i>
                Xem tất cả sản phẩm
                <i class="fas fa-arrow-right ml-2"></i>
            </a>
        </div>
    </div>
</section>

<!-- Featured Products Section -->
<section id="featured-products" class="py-20 bg-white dark:bg-sport-blue">
    <div class="container mx-auto px-4 lg:px-8">
        <div class="text-center mb-16 animate-on-scroll">
            <h2 class="text-5xl font-black text-gray-900 dark:text-white mb-4">
                Sản phẩm nổi bật
            </h2>
            <p class="text-xl text-gray-600 dark:text-gray-400">
                Top sản phẩm được yêu thích nhất
            </p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            <?php foreach ($featuredProducts as $index => $product): ?>
            <div class="modern-card bg-white dark:bg-sport-navy rounded-2xl shadow-lg overflow-hidden group animate-on-scroll"
                 style="animation-delay: <?php echo $index * 0.1; ?>s;">
                <!-- Image -->
                <div class="relative overflow-hidden aspect-square">
                    <img src="<?php echo $product['MainImage'] ?: 'https://via.placeholder.com/400x400/667eea/ffffff?text=SportShop'; ?>" 
                         alt="<?php echo htmlspecialchars($product['ProductName']); ?>"
                         class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                    
                    <!-- Overlay -->
                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    
                    <!-- Brand Badge -->
                    <div class="absolute top-4 left-4">
                        <span class="px-4 py-2 rounded-full glass text-white text-sm font-semibold">
                            <?php echo htmlspecialchars($product['BrandName']); ?>
                        </span>
                    </div>
                    
                    <!-- Quick View Button -->
                    <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                        <a href="pages/product_detail.php?id=<?php echo $product['ProductID']; ?>" 
                           class="px-6 py-3 bg-white text-sport-navy rounded-xl font-bold hover:bg-sport-neon hover:text-white transition-colors duration-300">
                            Xem chi tiết
                        </a>
                    </div>
                </div>
                
                <!-- Content -->
                <div class="p-6 space-y-4">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white line-clamp-2 group-hover:text-sport-neon transition-colors duration-300">
                        <?php echo htmlspecialchars($product['ProductName']); ?>
                    </h3>
                    
                    <!-- Rating -->
                    <div class="flex items-center space-x-2">
                        <?php for ($i = 0; $i < 5; $i++): ?>
                            <i class="fas fa-star <?php echo $i < $product['RatingAvg'] ? 'text-yellow-400' : 'text-gray-300'; ?>"></i>
                        <?php endfor; ?>
                        <span class="text-sm text-gray-600 dark:text-gray-400">(<?php echo $product['RatingAvg']; ?>)</span>
                    </div>
                    
                    <!-- Price -->
                    <div class="flex items-center justify-between">
                        <span class="text-2xl font-black text-sport-neon">
                            <?php echo formatPrice($product['Price']); ?>
                        </span>
                        <a href="pages/product_detail.php?id=<?php echo $product['ProductID']; ?>" 
                           class="w-12 h-12 rounded-full bg-gradient-to-br from-sport-neon to-blue-600 flex items-center justify-center hover:scale-110 transition-transform duration-300">
                            <i class="fas fa-arrow-right text-white"></i>
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <!-- View All Button -->
        <div class="text-center mt-12 animate-on-scroll">
            <a href="pages/products.php" class="btn-neon text-lg px-10 py-4 inline-block relative z-10">
                Xem tất cả sản phẩm <i class="fas fa-arrow-right ml-2"></i>
            </a>
        </div>
    </div>
</section>

<?php include 'includes/layout_footer.php'; ?>