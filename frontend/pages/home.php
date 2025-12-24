<?php
require_once '../config.php';

// Get featured products from API
$featuredProducts = getFeaturedProducts(8);
?>

<section class="hero-section text-center">
    <div class="container">
        <h1 class="display-4 fw-bold mb-4">Chào mừng đến với Sports Shop</h1>
        <p class="lead mb-4">Khám phá bộ sưu tập đồ thể thao đa dạng với chất lượng tốt nhất</p>
        <a href="products.php" class="btn btn-light btn-lg px-4">
            <i class="fas fa-shopping-bag me-2"></i>Mua sắm ngay
        </a>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <h2 class="text-center mb-5">Sản phẩm nổi bật</h2>
        <div class="row">
            <?php if (!empty($featuredProducts)): ?>
                <?php foreach ($featuredProducts as $product): ?>
                    <div class="col-md-3 mb-4">
                        <div class="card product-card h-100">
                            <img src="<?php echo htmlspecialchars($product['MainImage'] ?? 'https://via.placeholder.com/300x300'); ?>" 
                                 class="card-img-top" alt="<?php echo htmlspecialchars($product['ProductName']); ?>">
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title"><?php echo htmlspecialchars($product['ProductName']); ?></h5>
                                <p class="card-text text-muted"><?php echo htmlspecialchars($product['BrandName'] ?? 'Không rõ'); ?></p>
                                <p class="card-text">
                                    <span class="text-danger fw-bold"><?php echo formatPrice($product['Price']); ?></span>
                                </p>
                                <div class="d-grid gap-2 mt-auto">
                                    <a href="product_detail.php?id=<?php echo $product['ProductID']; ?>" 
                                       class="btn btn-outline-primary">
                                        Xem chi tiết
                                    </a>
                                    </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center">
                    <p class="text-muted">Không có sản phẩm nào.</p>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="text-center mt-4">
            <a href="products.php" class="btn btn-outline-secondary">
                Xem tất cả sản phẩm <i class="fas fa-arrow-right ms-2"></i>
            </a>
        </div>
    </div>
</section>

<section class="py-5 bg-light">
    <div class="container">
        <div class="row text-center">
            <div class="col-md-4 mb-4">
                <div class="feature-box">
                    <i class="fas fa-truck fa-3x text-primary mb-3"></i>
                    <h4>Giao hàng miễn phí</h4>
                    <p class="text-muted">Giao hàng miễn phí cho đơn hàng từ 500.000₫</p>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="feature-box">
                    <i class="fas fa-undo fa-3x text-primary mb-3"></i>
                    <h4>Đổi trả dễ dàng</h4>
                    <p class="text-muted">Đổi trả trong vòng 30 ngày nếu không hài lòng</p>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="feature-box">
                    <i class="fas fa-shield-alt fa-3x text-primary mb-3"></i>
                    <h4>Bảo hành chính hãng</h4>
                    <p class="text-muted">Bảo hành 12 tháng cho tất cả sản phẩm</p>
                </div>
            </div>
        </div>
    </div>
</section>