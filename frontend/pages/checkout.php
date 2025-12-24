<?php
require_once '../config.php';

// Guest checkout allowed - no login required

$error = '';
$success = '';
$orderID = null;

// Handle checkout submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $shippingAddress = sanitizeInput($_POST['shipping_address']);
    $shippingCity = sanitizeInput($_POST['shipping_city']);
    $shippingPhone = sanitizeInput($_POST['shipping_phone']);
    $paymentMethod = sanitizeInput($_POST['payment_method']);
    $notes = sanitizeInput($_POST['notes'] ?? '');
    $cartData = $_POST['cart_data'] ?? '';
    $couponData = $_POST['coupon_data'] ?? '';
    
    // Get guest info if not logged in
    $guestName = '';
    $guestEmail = '';
    if (!isLoggedIn()) {
        $guestName = sanitizeInput($_POST['guest_name'] ?? '');
        $guestEmail = sanitizeInput($_POST['guest_email'] ?? '');
        
        if (empty($guestName) || empty($guestEmail)) {
            $error = 'Vui lòng điền đầy đủ thông tin liên hệ (họ tên và email)';
        }
    }
    
    if (empty($error) && (empty($shippingAddress) || empty($shippingCity) || empty($shippingPhone))) {
        $error = 'Vui lòng điền đầy đủ thông tin giao hàng';
    } elseif (empty($cartData)) {
        $error = 'Giỏ hàng trống';
    } else {
        try {
<<<<<<< HEAD
=======
            $db = Database::getInstance()->getConnection();
            $db->beginTransaction();
            
>>>>>>> 3d6d58ed3875cc3c551e3fe1991339ab7637c345
            // Parse cart data
            $cart = json_decode($cartData, true);
            $coupon = !empty($couponData) ? json_decode($couponData, true) : null;
            
            // Calculate totals
            $subtotal = 0;
            foreach ($cart as $item) {
                $subtotal += $item['price'] * $item['quantity'];
            }
            
<<<<<<< HEAD
            // Use backend API for order creation
            $orderData = [
                'shippingAddress' => $shippingAddress,
                'shippingCity' => $shippingCity,
                'shippingPhone' => $shippingPhone,
                'paymentMethod' => $paymentMethod,
                'notes' => $notes,
                'cart' => $cart,
                'coupon' => $coupon,
                'guestName' => $guestName,
                'guestEmail' => $guestEmail
            ];
            
            $response = makeApiRequest('/orders', 'POST', $orderData);
            
            if ($response['success']) {
                $orderID = $response['data']['orderId'];
                $success = 'Đặt hàng thành công! Mã đơn hàng: #' . $orderID;
            } else {
                $error = $response['message'] ?? 'Có lỗi xảy ra khi tạo đơn hàng';
            }
=======
            // Calculate discount
            $discount = 0;
            $voucherID = null;
            if ($coupon && $subtotal >= $coupon['MinOrderValue']) {
                $discount = $coupon['DiscountValue'];
                $voucherID = $coupon['VoucherID'];
                
                // Decrease voucher quantity
                $stmt = $db->prepare("UPDATE Voucher SET Quantity = Quantity - 1 WHERE VoucherID = ? AND Quantity > 0");
                $stmt->execute([$voucherID]);
            }
            
            // Calculate shipping
            $shipping = $subtotal >= 500000 ? 0 : 30000;
            $totalAmount = $subtotal - $discount + $shipping;
            
            // Create order
            $fullAddress = $shippingAddress . ', ' . $shippingCity;
            $userID = isLoggedIn() ? $_SESSION['user_id'] : null; // Guest checkout: NULL user ID
            
            // Add guest info to notes if guest checkout
            $guestInfo = '';
            if (!isLoggedIn()) {
                $guestInfo = "THÔNG TIN KHÁCH VÃNG LAI:\nHọ tên: $guestName\nEmail: $guestEmail\n";
            }
            $fullNotes = $guestInfo . $notes;
            
            $stmt = $db->prepare("
                INSERT INTO Orders (UserID, TotalAmount, Address, Status, VoucherID, Note, created_at) 
                VALUES (?, ?, ?, 'pending', ?, ?, NOW())
            ");
            $stmt->execute([
                $userID,
                $totalAmount,
                $fullAddress,
                $voucherID,
                $fullNotes
            ]);
            
            $orderID = $db->lastInsertId();
            
            // Create order details
            $stmt = $db->prepare("
                INSERT INTO OrderDetails (OrderID, ProductDetailID, Quantity, Price) 
                VALUES (?, ?, ?, ?)
            ");
            
            foreach ($cart as $item) {
                $stmt->execute([
                    $orderID,
                    $item['productDetailId'],
                    $item['quantity'],
                    $item['price']
                ]);
            }
            
            $db->commit();
            $success = 'Đơn hàng của bạn đã được đặt thành công!';
            
        } catch (Exception $e) {
            $db->rollBack();
            $error = 'Có lỗi xảy ra: ' . $e->getMessage();
        }
>>>>>>> 3d6d58ed3875cc3c551e3fe1991339ab7637c345
    }
}

// Get user info if logged in
$user = null;
if (isLoggedIn()) {
<<<<<<< HEAD
    $userResponse = makeApiRequest('/auth/me');
    $user = $userResponse['success'] ? $userResponse['data'] : null;
=======
    $db = Database::getInstance()->getConnection();
    $stmt = $db->prepare("SELECT * FROM Users WHERE UserID = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
>>>>>>> 3d6d58ed3875cc3c551e3fe1991339ab7637c345
}

$pageTitle = "Thanh toán";
$isInPages = true;
include '../includes/layout_header.php';
?>

<!-- Page Header -->
<section class="relative py-12 bg-gradient-to-br from-sport-navy to-sport-blue overflow-hidden">
    <div class="absolute inset-0">
        <div class="absolute top-0 right-0 w-96 h-96 bg-sport-neon opacity-10 rounded-full blur-3xl"></div>
    </div>
    
    <div class="container mx-auto px-4 lg:px-8 relative z-10">
        <div class="text-center animate-fade-in">
            <h1 class="text-4xl lg:text-5xl font-black text-white mb-4">
                <i class="fas fa-shopping-cart mr-3"></i>Thanh toán
            </h1>
            <p class="text-xl text-gray-300">
                Hoàn tất đơn hàng của bạn
            </p>
        </div>
    </div>
</section>

<!-- Checkout Steps -->
<section class="py-8 bg-gray-50 dark:bg-sport-navy">
    <div class="container mx-auto px-4 lg:px-8">
        <div class="flex items-center justify-center space-x-4 md:space-x-8">
            <div class="flex items-center">
                <div class="w-10 h-10 rounded-full bg-green-500 flex items-center justify-center text-white font-bold">
                    <i class="fas fa-check"></i>
                </div>
                <span class="ml-2 text-sm font-semibold text-gray-900 dark:text-white hidden md:inline">Giỏ hàng</span>
            </div>
            <div class="w-16 md:w-24 h-1 bg-green-500"></div>
            <div class="flex items-center">
                <div class="w-10 h-10 rounded-full bg-sport-neon flex items-center justify-center text-white font-bold">
                    2
                </div>
                <span class="ml-2 text-sm font-semibold text-sport-neon hidden md:inline">Thanh toán</span>
            </div>
            <div class="w-16 md:w-24 h-1 bg-gray-300 dark:bg-gray-700"></div>
            <div class="flex items-center">
                <div class="w-10 h-10 rounded-full bg-gray-300 dark:bg-gray-700 flex items-center justify-center text-gray-600 font-bold">
                    3
                </div>
                <span class="ml-2 text-sm font-semibold text-gray-600 dark:text-gray-400 hidden md:inline">Hoàn tất</span>
            </div>
        </div>
    </div>
</section>

<!-- Checkout Form -->
<section class="py-12 bg-white dark:bg-sport-blue">
    <div class="container mx-auto px-4 lg:px-8">
        <?php if ($error): ?>
        <div class="mb-6 p-4 bg-red-500/20 border border-red-500 rounded-2xl text-red-700 dark:text-red-400 animate-shake">
            <i class="fas fa-exclamation-circle mr-2"></i><?php echo $error; ?>
        </div>
        <?php endif; ?>
        
        <?php if ($success): ?>
        <div class="mb-6 p-6 bg-green-500/20 border border-green-500 rounded-2xl text-green-700 dark:text-green-400 text-center animate-scale-in">
            <i class="fas fa-check-circle text-4xl mb-3"></i>
            <h3 class="text-2xl font-bold mb-2"><?php echo $success; ?></h3>
            <p class="mb-4">Mã đơn hàng: <span class="font-bold">#<?php echo str_pad($orderID, 6, '0', STR_PAD_LEFT); ?></span></p>
            <div class="space-y-3 mb-4 text-left max-w-md mx-auto">
                <p class="text-sm"><i class="fas fa-info-circle mr-2"></i>Chúng tôi sẽ gọi điện xác nhận đơn hàng trong vòng 24h</p>
                <p class="text-sm"><i class="fas fa-truck mr-2"></i>Thời gian giao hàng dự kiến: 2-3 ngày</p>
            </div>
            <div class="flex justify-center space-x-4">
                <?php if (isLoggedIn()): ?>
                <a href="profile.php" class="btn-neon px-6 py-3 inline-block relative z-10">
                    <i class="fas fa-list mr-2"></i>Xem đơn hàng
                </a>
                <?php endif; ?>
                <a href="generate_invoice.php?order_id=<?php echo $orderID; ?>" target="_blank" class="px-6 py-3 rounded-xl bg-gradient-to-r from-purple-500 to-purple-700 text-white font-semibold hover:shadow-lg hover:shadow-purple-500/50 transition-all duration-300 inline-block">
                    <i class="fas fa-file-pdf mr-2"></i>In hóa đơn
                </a>
                <a href="products.php" class="px-6 py-3 rounded-xl glass text-gray-700 dark:text-gray-300 font-semibold hover:bg-gray-200 dark:hover:bg-gray-700 transition-all duration-300 inline-block">
                    <i class="fas fa-shopping-bag mr-2"></i>Tiếp tục mua sắm
                </a>
            </div>
        </div>
        <script>
        // Clear cart and coupon after successful order
        localStorage.removeItem('cart');
        localStorage.removeItem('selectedCoupon');
        localStorage.removeItem('buy_now_mode');
        </script>
        <?php else: ?>
        
        <form method="POST" id="checkout-form" class="grid grid-cols-1 lg:grid-cols-3 gap-8" onsubmit="return handleSubmit(event)">
            <!-- Hidden inputs for cart and coupon data -->
            <input type="hidden" name="cart_data" id="cart_data">
            <input type="hidden" name="coupon_data" id="coupon_data">
            <!-- Left Column - Forms -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Contact Information -->
                <div class="modern-card bg-white dark:bg-sport-navy rounded-2xl p-6 shadow-lg">
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-6 flex items-center">
                        <i class="fas fa-user mr-3 text-sport-neon"></i>
                        Thông tin liên hệ
                    </h3>
                    
                    <?php if (isLoggedIn()): ?>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Họ và tên</label>
                            <input type="text" 
                                   value="<?php echo htmlspecialchars($user['FirstName'] . ' ' . $user['LastName']); ?>" 
                                   readonly
                                   class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-sport-blue text-gray-900 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Email</label>
                            <input type="email" 
                                   value="<?php echo htmlspecialchars($user['Email']); ?>" 
                                   readonly
                                   class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-sport-blue text-gray-900 dark:text-white">
                        </div>
                    </div>
                    <?php else: ?>
                    <div class="space-y-4">
                        <p class="text-sm text-blue-600 dark:text-blue-400 mb-4">
                            <i class="fas fa-info-circle mr-2"></i>Bạn đang mua hàng với tư cách khách vãng lai. Vui lòng cung cấp thông tin liên hệ.
                        </p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                    Họ và tên <span class="text-red-500">*</span>
                                </label>
                                <input type="text" 
                                       name="guest_name" 
                                       required
                                       class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 dark:border-gray-700 bg-white dark:bg-sport-blue text-gray-900 dark:text-white focus:border-sport-neon focus:ring-2 focus:ring-sport-neon/20 transition-all duration-300"
                                       placeholder="Nguyễn Văn A">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                    Email <span class="text-red-500">*</span>
                                </label>
                                <input type="email" 
                                       name="guest_email" 
                                       required
                                       class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 dark:border-gray-700 bg-white dark:bg-sport-blue text-gray-900 dark:text-white focus:border-sport-neon focus:ring-2 focus:ring-sport-neon/20 transition-all duration-300"
                                       placeholder="email@example.com">
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
                
                <!-- Shipping Information -->
                <div class="modern-card bg-white dark:bg-sport-navy rounded-2xl p-6 shadow-lg">
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-6 flex items-center">
                        <i class="fas fa-shipping-fast mr-3 text-sport-neon"></i>
                        Thông tin giao hàng
                    </h3>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                Số điện thoại <span class="text-red-500">*</span>
                            </label>
                            <input type="tel" 
                                   name="shipping_phone" 
                                   value="<?php echo htmlspecialchars($user['Phone'] ?? ''); ?>"
                                   required
                                   class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 dark:border-gray-700 bg-white dark:bg-sport-blue text-gray-900 dark:text-white focus:border-sport-neon focus:ring-2 focus:ring-sport-neon/20 transition-all duration-300"
                                   placeholder="0912345678">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                Địa chỉ <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   name="shipping_address" 
                                   value="<?php echo htmlspecialchars($user['Address'] ?? ''); ?>"
                                   required
                                   class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 dark:border-gray-700 bg-white dark:bg-sport-blue text-gray-900 dark:text-white focus:border-sport-neon focus:ring-2 focus:ring-sport-neon/20 transition-all duration-300"
                                   placeholder="Số nhà, tên đường">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                Tỉnh/Thành phố <span class="text-red-500">*</span>
                            </label>
                            <select name="shipping_city" 
                                    required
                                    class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 dark:border-gray-700 bg-white dark:bg-sport-blue text-gray-900 dark:text-white focus:border-sport-neon focus:ring-2 focus:ring-sport-neon/20 transition-all duration-300">
                                <option value="">Chọn tỉnh/thành phố</option>
                                <option value="TP. Hồ Chí Minh">TP. Hồ Chí Minh</option>
                                <option value="Hà Nội">Hà Nội</option>
                                <option value="Đà Nẵng">Đà Nẵng</option>
                                <option value="Cần Thơ">Cần Thơ</option>
                                <option value="Khác">Khác</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                Ghi chú đơn hàng (tùy chọn)
                            </label>
                            <textarea name="notes" 
                                      rows="3"
                                      class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 dark:border-gray-700 bg-white dark:bg-sport-blue text-gray-900 dark:text-white focus:border-sport-neon focus:ring-2 focus:ring-sport-neon/20 transition-all duration-300"
                                      placeholder="Ghi chú về đơn hàng, ví dụ: thời gian hay địa điểm giao hàng chi tiết hơn"></textarea>
                        </div>
                    </div>
                </div>
                
                <!-- Coupon Code -->
                <div class="modern-card bg-white dark:bg-sport-navy rounded-2xl p-6 shadow-lg">
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-6 flex items-center">
                        <i class="fas fa-ticket-alt mr-3 text-sport-neon"></i>
                        Mã giảm giá
                    </h3>
                    
                    <div class="flex gap-2 mb-2">
                        <input type="text" 
                               id="coupon-code"
                               placeholder="Nhập hoặc chọn mã giảm giá"
                               readonly
                               class="flex-1 px-4 py-3 rounded-xl border-2 border-gray-200 dark:border-gray-700 bg-white dark:bg-sport-navy text-gray-900 dark:text-white focus:border-sport-neon focus:ring-2 focus:ring-sport-neon/20 transition-all duration-300">
                        <button onclick="openCouponModal()" 
                                type="button"
                                class="px-6 py-3 bg-gradient-to-r from-purple-500 to-pink-600 text-white font-bold rounded-xl hover:shadow-lg hover:shadow-purple-500/50 transition-all duration-300">
                            <i class="fas fa-tags mr-2"></i>Chọn mã
                        </button>
                    </div>
                    <div id="selected-coupon-info" class="hidden mb-2 p-3 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700 rounded-xl">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-bold text-green-700 dark:text-green-400">
                                    <i class="fas fa-check-circle mr-1"></i>Đã áp dụng: <span id="applied-coupon-code"></span>
                                </p>
                                <p class="text-xs text-green-600 dark:text-green-500" id="coupon-discount-text"></p>
                            </div>
                            <button onclick="removeCoupon()" type="button" class="text-red-500 hover:text-red-700">
                                <i class="fas fa-times-circle"></i>
                            </button>
                        </div>
                    </div>
                    <p id="coupon-message" class="mt-2 text-sm"></p>
                </div>
                
                <!-- Payment Method -->
                <div class="modern-card bg-white dark:bg-sport-navy rounded-2xl p-6 shadow-lg">
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-6 flex items-center">
                        <i class="fas fa-credit-card mr-3 text-sport-neon"></i>
                        Phương thức thanh toán
                    </h3>
                    
                    <div class="space-y-3">
                        <label class="flex items-center p-4 rounded-xl border-2 border-gray-200 dark:border-gray-700 cursor-pointer hover:border-sport-neon transition-all duration-300">
                            <input type="radio" name="payment_method" value="cod" checked class="w-5 h-5 text-sport-neon">
                            <div class="ml-4 flex-1">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="font-bold text-gray-900 dark:text-white">Thanh toán khi nhận hàng (COD)</p>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">Thanh toán bằng tiền mặt khi nhận hàng</p>
                                    </div>
                                    <i class="fas fa-money-bill-wave text-2xl text-green-500"></i>
                                </div>
                            </div>
                        </label>
                        
                        <label class="flex items-center p-4 rounded-xl border-2 border-gray-200 dark:border-gray-700 cursor-pointer hover:border-sport-neon transition-all duration-300">
                            <input type="radio" name="payment_method" value="bank" class="w-5 h-5 text-sport-neon">
                            <div class="ml-4 flex-1">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="font-bold text-gray-900 dark:text-white">Chuyển khoản ngân hàng</p>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">Chuyển khoản qua Internet Banking</p>
                                    </div>
                                    <i class="fas fa-university text-2xl text-blue-500"></i>
                                </div>
                            </div>
                        </label>
                        
                        <label class="flex items-center p-4 rounded-xl border-2 border-gray-200 dark:border-gray-700 cursor-pointer hover:border-sport-neon transition-all duration-300 opacity-50">
                            <input type="radio" name="payment_method" value="momo" disabled class="w-5 h-5 text-sport-neon">
                            <div class="ml-4 flex-1">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="font-bold text-gray-900 dark:text-white">Ví MoMo (Sắp có)</p>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">Thanh toán qua ví điện tử MoMo</p>
                                    </div>
                                    <i class="fas fa-wallet text-2xl text-pink-500"></i>
                                </div>
                            </div>
                        </label>
                    </div>
                </div>
            </div>
            
            <!-- Right Column - Order Summary -->
            <div class="lg:col-span-1">
                <div class="modern-card bg-white dark:bg-sport-navy rounded-2xl p-6 shadow-lg sticky top-24">
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-6 flex items-center">
                        <i class="fas fa-receipt mr-3 text-sport-neon"></i>
                        Đơn hàng
                    </h3>
                    
                    <!-- Cart Items Preview -->
                    <div id="checkout-items" class="space-y-4 mb-6 max-h-64 overflow-y-auto">
                        <!-- Items will be loaded by JavaScript -->
                    </div>
                    
                    <!-- Summary -->
                    <div class="space-y-3 py-4 border-t border-gray-200 dark:border-gray-700">
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Tạm tính:</span>
                            <span class="font-bold text-gray-900 dark:text-white" id="checkout-subtotal">0₫</span>
                        </div>
                        
                        <!-- Discount -->
                        <div id="checkout-discount-row" class="hidden flex items-center justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Giảm giá:</span>
                            <span class="font-bold text-red-500" id="checkout-discount">0₫</span>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Phí vận chuyển:</span>
                            <span class="font-semibold text-green-500" id="checkout-shipping">Miễn phí</span>
                        </div>
                        <div class="flex items-center justify-between py-4 bg-gradient-to-r from-sport-neon/10 to-blue-600/10 rounded-xl px-4">
                            <span class="text-xl font-bold text-gray-900 dark:text-white">Tổng cộng:</span>
                            <span class="text-3xl font-black text-sport-neon" id="checkout-total">0₫</span>
                        </div>
                    </div>
                    
                    <!-- Submit Button -->
                    <button type="submit" 
                            class="w-full py-4 bg-gradient-to-r from-green-500 to-emerald-600 text-white font-bold rounded-xl hover:shadow-lg hover:shadow-green-500/50 transform hover:scale-105 transition-all duration-300 mt-6">
                        <i class="fas fa-lock mr-2"></i>Đặt hàng ngay
                    </button>
                    
                    <!-- Trust Info -->
                    <div class="mt-6 space-y-3 text-center text-sm text-gray-600 dark:text-gray-400">
                        <p><i class="fas fa-shield-alt text-green-500 mr-2"></i>Thanh toán an toàn & bảo mật</p>
                        <p><i class="fas fa-truck text-blue-500 mr-2"></i>Giao hàng toàn quốc</p>
                        <p><i class="fas fa-undo text-purple-500 mr-2"></i>Đổi trả trong 30 ngày</p>
                    </div>
                </div>
            </div>
        </form>
        
        <?php endif; ?>
    </div>
</section>

<!-- Coupon Selection Modal -->
<div id="coupon-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white dark:bg-sport-navy rounded-2xl max-w-2xl w-full max-h-[80vh] overflow-hidden shadow-2xl animate-scale-in">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
            <h3 class="text-2xl font-bold text-gray-900 dark:text-white">
                <i class="fas fa-tags text-sport-neon mr-2"></i>Chọn mã giảm giá
            </h3>
            <button onclick="closeCouponModal()" type="button" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                <i class="fas fa-times text-2xl"></i>
            </button>
        </div>
        <div class="p-6 overflow-y-auto max-h-[60vh]">
            <div id="coupons-list" class="space-y-4">
                <!-- Coupons will be loaded here -->
            </div>
            <div id="coupons-loading" class="text-center py-8">
                <i class="fas fa-spinner fa-spin text-4xl text-sport-neon mb-3"></i>
                <p class="text-gray-600 dark:text-gray-400">Đang tải mã giảm giá...</p>
            </div>
            <div id="coupons-empty" class="hidden text-center py-8">
                <i class="fas fa-inbox text-6xl text-gray-300 dark:text-gray-600 mb-4"></i>
                <p class="text-gray-600 dark:text-gray-400 text-lg">Không có mã giảm giá khả dụng</p>
            </div>
        </div>
    </div>
</div>

<script>
// Global variables
let selectedCoupon = null;
let availableCoupons = [];

// Handle form submit
function handleSubmit(event) {
    const cart = JSON.parse(localStorage.getItem('cart') || '[]');
    
    if (cart.length === 0) {
        alert('Giỏ hàng trống! Vui lòng thêm sản phẩm trước khi thanh toán.');
        event.preventDefault();
        return false;
    }
    
    // Populate hidden inputs
    document.getElementById('cart_data').value = JSON.stringify(cart);
    
    if (selectedCoupon) {
        document.getElementById('coupon_data').value = JSON.stringify(selectedCoupon);
    }
    
    return true;
}

// Load cart items
function loadCheckoutItems() {
    const cart = JSON.parse(localStorage.getItem('cart') || '[]');
    const container = document.getElementById('checkout-items');
    
    if (cart.length === 0) {
        container.innerHTML = '<p class="text-center text-gray-600 dark:text-gray-400">Giỏ hàng trống</p>';
        const submitBtn = document.querySelector('button[type="submit"]');
        if (submitBtn) submitBtn.disabled = true;
        return;
    }
    
    container.innerHTML = cart.map(item => `
        <div class="flex items-center space-x-3 p-3 rounded-xl glass">
            <img src="${item.image || 'https://via.placeholder.com/60'}" 
                 alt="${item.name}"
                 class="w-12 h-12 rounded-lg object-cover">
            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">${item.name}</p>
                <p class="text-xs text-gray-600 dark:text-gray-400">
                    ${item.size ? 'Size: ' + item.size : ''} ${item.color ? '• ' + item.color : ''} • SL: ${item.quantity}
                </p>
            </div>
            <span class="text-sm font-bold text-sport-neon">${formatPrice(item.price * item.quantity)}</span>
        </div>
    `).join('');
    
    updateCheckoutSummary();
}

// Update checkout summary
function updateCheckoutSummary() {
    const cart = JSON.parse(localStorage.getItem('cart') || '[]');
    const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    
    // Calculate discount
    let discount = 0;
    if (selectedCoupon && subtotal >= selectedCoupon.MinOrderValue) {
        discount = selectedCoupon.DiscountValue;
        document.getElementById('checkout-discount').textContent = formatPrice(discount);
        document.getElementById('checkout-discount-row').classList.remove('hidden');
    } else {
        document.getElementById('checkout-discount-row').classList.add('hidden');
    }
    
    const shipping = subtotal >= 500000 ? 0 : 30000;
    const total = subtotal - discount + shipping;
    
    document.getElementById('checkout-subtotal').textContent = formatPrice(subtotal);
    document.getElementById('checkout-shipping').textContent = shipping === 0 ? 'Miễn phí' : formatPrice(shipping);
    document.getElementById('checkout-total').textContent = formatPrice(total);
}

// Open coupon modal
function openCouponModal() {
    document.getElementById('coupon-modal').classList.remove('hidden');
    loadCoupons();
}

// Close coupon modal
function closeCouponModal() {
    document.getElementById('coupon-modal').classList.add('hidden');
}

// Load available coupons from API
async function loadCoupons() {
    const loadingEl = document.getElementById('coupons-loading');
    const listEl = document.getElementById('coupons-list');
    const emptyEl = document.getElementById('coupons-empty');
    
    loadingEl.classList.remove('hidden');
    listEl.innerHTML = '';
    emptyEl.classList.add('hidden');
    
    try {
        const response = await fetch('../api/get_coupons.php');
        const data = await response.json();
        
        loadingEl.classList.add('hidden');
        
        if (data.success && data.coupons.length > 0) {
            availableCoupons = data.coupons;
            renderCoupons(data.coupons);
        } else {
            emptyEl.classList.remove('hidden');
        }
    } catch (error) {
        console.error('Error loading coupons:', error);
        loadingEl.classList.add('hidden');
        listEl.innerHTML = '<p class="text-center text-red-500">Lỗi khi tải mã giảm giá</p>';
    }
}

// Render coupons list
function renderCoupons(coupons) {
    const listEl = document.getElementById('coupons-list');
    const cart = JSON.parse(localStorage.getItem('cart') || '[]');
    const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    
    listEl.innerHTML = coupons.map(coupon => {
        const canUse = subtotal >= coupon.MinOrderValue;
        const isSelected = selectedCoupon && selectedCoupon.VoucherID === coupon.VoucherID;
        
        return `
            <div class="p-4 rounded-xl border-2 ${
                isSelected ? 'border-sport-neon bg-sport-neon/10' : 
                canUse ? 'border-gray-200 dark:border-gray-700 hover:border-sport-neon cursor-pointer' :
                'border-gray-200 dark:border-gray-700 opacity-50'
            } transition-all duration-300" 
                 onclick="${canUse ? `selectCoupon(${coupon.VoucherID})` : ''}">
                <div class="flex items-start justify-between mb-3">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="text-2xl font-black text-sport-neon bg-sport-neon/10 px-3 py-1 rounded-lg">
                                ${coupon.VoucherCode}
                            </span>
                            ${isSelected ? '<span class="text-green-500"><i class="fas fa-check-circle"></i></span>' : ''}
                        </div>
                        <p class="text-xl font-bold text-gray-900 dark:text-white mb-1">
                            Giảm ${formatPrice(coupon.DiscountValue)}
                        </p>
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            <i class="fas fa-shopping-cart mr-1"></i>Đơn tối thiểu: ${formatPrice(coupon.MinOrderValue)}
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-500 mt-1">
                            <i class="far fa-calendar mr-1"></i>HSD: ${new Date(coupon.EndDate).toLocaleDateString('vi-VN')}
                        </p>
                    </div>
                    <div class="text-right">
                        <p class="text-xs text-gray-500 dark:text-gray-500">Còn lại</p>
                        <p class="text-lg font-bold text-gray-900 dark:text-white">${coupon.Quantity}</p>
                    </div>
                </div>
                ${!canUse ? `
                    <div class="mt-2 p-2 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg">
                        <p class="text-xs text-yellow-700 dark:text-yellow-400">
                            <i class="fas fa-info-circle mr-1"></i>Cần mua thêm ${formatPrice(coupon.MinOrderValue - subtotal)} để sử dụng
                        </p>
                    </div>
                ` : ''}
            </div>
        `;
    }).join('');
}

// Select a coupon
function selectCoupon(voucherId) {
    const coupon = availableCoupons.find(c => c.VoucherID === voucherId);
    if (coupon) {
        selectedCoupon = coupon;
        displaySelectedCoupon();
        updateCheckoutSummary();
        closeCouponModal();
    }
}

// Display selected coupon
function displaySelectedCoupon() {
    if (selectedCoupon) {
        document.getElementById('coupon-code').value = selectedCoupon.VoucherCode;
        document.getElementById('applied-coupon-code').textContent = selectedCoupon.VoucherCode;
        document.getElementById('coupon-discount-text').textContent = 
            `Giảm ${formatPrice(selectedCoupon.DiscountValue)}`;
        document.getElementById('selected-coupon-info').classList.remove('hidden');
        document.getElementById('coupon-message').textContent = '';
    }
}

// Remove coupon
function removeCoupon() {
    selectedCoupon = null;
    document.getElementById('coupon-code').value = '';
    document.getElementById('selected-coupon-info').classList.add('hidden');
    updateCheckoutSummary();
}

// Format price
function formatPrice(price) {
    return new Intl.NumberFormat('vi-VN').format(price) + ' ₫';
}

// Initialize
document.addEventListener('DOMContentLoaded', () => {
    loadCheckoutItems();
});
</script>

<?php include '../includes/layout_footer.php'; ?>
