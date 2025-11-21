<?php
require_once '../config.php';

$pageTitle = "Giỏ hàng";
$isInPages = true;
include '../includes/layout_header.php';
?>

<!-- Page Header -->
<section class="relative py-20 bg-gradient-to-br from-sport-navy to-sport-blue overflow-hidden">
    <div class="absolute inset-0">
        <div class="absolute top-0 right-0 w-96 h-96 bg-sport-neon opacity-10 rounded-full blur-3xl"></div>
        <div class="absolute bottom-0 left-0 w-96 h-96 bg-purple-600 opacity-10 rounded-full blur-3xl"></div>
    </div>
    
    <div class="container mx-auto px-4 lg:px-8 relative z-10">
        <div class="text-center animate-fade-in">
            <h1 class="text-5xl lg:text-6xl font-black text-white mb-4">
                <i class="fas fa-shopping-cart mr-4"></i>Giỏ hàng
            </h1>
            <p class="text-xl text-gray-300">
                Quản lý sản phẩm trong giỏ hàng của bạn
            </p>
        </div>
    </div>
</section>

<!-- Cart Section -->
<section class="py-12 bg-gray-50 dark:bg-sport-navy min-h-screen">
    <div class="container mx-auto px-4 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Cart Items -->
            <div class="lg:col-span-2 space-y-4">
                <!-- Cart Header -->
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                        Sản phẩm (<span id="cart-count">0</span>)
                    </h2>
                    <button onclick="clearCart()" 
                            id="clear-cart-btn"
                            class="px-4 py-2 rounded-xl glass text-red-500 hover:bg-red-500 hover:text-white transition-all duration-300 hidden">
                        <i class="fas fa-trash mr-2"></i>Xóa tất cả
                    </button>
                </div>
                
                <!-- Cart Items Container -->
                <div id="cart-items-container" class="space-y-4">
                    <!-- Items will be inserted here by JavaScript -->
                </div>
                
                <!-- Empty Cart -->
                <div id="empty-cart" class="modern-card bg-white dark:bg-sport-blue rounded-2xl p-16 text-center hidden">
                    <div class="w-32 h-32 bg-gray-100 dark:bg-sport-navy rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-shopping-cart text-6xl text-gray-300 dark:text-gray-600"></i>
                    </div>
                    <h3 class="text-3xl font-bold text-gray-900 dark:text-white mb-3">
                        Giỏ hàng trống
                    </h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-8 text-lg">
                        Bạn chưa có sản phẩm nào trong giỏ hàng
                    </p>
                    <a href="products.php" class="btn-neon inline-block px-8 py-4 text-lg relative z-10">
                        <i class="fas fa-shopping-bag mr-2"></i>Khám phá sản phẩm
                    </a>
                </div>
            </div>
            
            <!-- Order Summary -->
            <div class="lg:col-span-1">
                <div class="sticky top-24">
                    <div class="modern-card bg-white dark:bg-sport-blue rounded-2xl p-6 shadow-lg">
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-6 flex items-center">
                            <i class="fas fa-receipt mr-3 text-sport-neon"></i>
                            Tóm tắt đơn hàng
                        </h3>
                        
                        <div class="space-y-4 mb-6">
                            <!-- Subtotal -->
                            <div class="flex items-center justify-between py-3 border-b border-gray-200 dark:border-gray-700">
                                <span class="text-gray-600 dark:text-gray-400">Tạm tính:</span>
                                <span class="text-xl font-bold text-gray-900 dark:text-white" id="subtotal">0₫</span>
                            </div>
                            
                            
                            <!-- Shipping -->
                            <div class="flex items-center justify-between py-3 border-b border-gray-200 dark:border-gray-700">
                                <span class="text-gray-600 dark:text-gray-400">Phí vận chuyển:</span>
                                <span class="text-lg font-semibold text-green-500" id="shipping">Miễn phí</span>
                            </div>
                            
                            <!-- Total -->
                            <div class="flex items-center justify-between py-4 bg-gradient-to-r from-sport-neon/10 to-blue-600/10 rounded-xl px-4">
                                <span class="text-xl font-bold text-gray-900 dark:text-white">Tổng cộng:</span>
                                <span class="text-3xl font-black text-sport-neon" id="total">0₫</span>
                            </div>
                        </div>
                        
                        <!-- Note about coupon -->
                        <div class="mb-6 p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 rounded-xl">
                            <p class="text-sm text-blue-700 dark:text-blue-400">
                                <i class="fas fa-info-circle mr-2"></i>
                                Bạn có thể sử dụng mã giảm giá ở bước thanh toán
                            </p>
                        </div>
                        
                        <!-- Checkout Button -->
                        <button onclick="proceedToCheckout()" 
                                id="checkout-btn"
                                class="w-full py-4 bg-gradient-to-r from-green-500 to-emerald-600 text-white font-bold rounded-xl hover:shadow-lg hover:shadow-green-500/50 transform hover:scale-105 transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed">
                            <i class="fas fa-lock mr-2"></i>Thanh toán ngay
                        </button>
                        
                        <a href="products.php" 
                           class="block w-full mt-3 py-3 text-center rounded-xl glass text-gray-700 dark:text-gray-300 font-semibold hover:bg-gray-200 dark:hover:bg-gray-700 transition-all duration-300">
                            <i class="fas fa-arrow-left mr-2"></i>Tiếp tục mua sắm
                        </a>
                    </div>
                    
                    <!-- Trust Badges -->
                    <div class="mt-6 space-y-3">
                        <div class="flex items-center gap-3 p-3 rounded-xl glass">
                            <i class="fas fa-shield-alt text-2xl text-green-500"></i>
                            <div class="flex-1">
                                <p class="font-semibold text-gray-900 dark:text-white text-sm">Thanh toán an toàn</p>
                                <p class="text-xs text-gray-600 dark:text-gray-400">Bảo mật SSL 256-bit</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 p-3 rounded-xl glass">
                            <i class="fas fa-shipping-fast text-2xl text-blue-500"></i>
                            <div class="flex-1">
                                <p class="font-semibold text-gray-900 dark:text-white text-sm">Giao hàng nhanh</p>
                                <p class="text-xs text-gray-600 dark:text-gray-400">Miễn phí từ 500.000₫</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 p-3 rounded-xl glass">
                            <i class="fas fa-undo text-2xl text-purple-500"></i>
                            <div class="flex-1">
                                <p class="font-semibold text-gray-900 dark:text-white text-sm">Đổi trả dễ dàng</p>
                                <p class="text-xs text-gray-600 dark:text-gray-400">Trong vòng 30 ngày</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


<script>
// Cart Management Functions
let cart = [];

// Load cart from localStorage
function loadCart() {
    const savedCart = localStorage.getItem('cart');
    if (savedCart) {
        cart = JSON.parse(savedCart);
    }
    
    renderCart();
    updateSummary();
}

// Save cart to localStorage
function saveCart() {
    localStorage.setItem('cart', JSON.stringify(cart));
}

// Render cart items
function renderCart() {
    const container = document.getElementById('cart-items-container');
    const emptyCart = document.getElementById('empty-cart');
    const clearBtn = document.getElementById('clear-cart-btn');
    const checkoutBtn = document.getElementById('checkout-btn');
    const cartCount = document.getElementById('cart-count');
    
    if (cart.length === 0) {
        container.innerHTML = '';
        emptyCart.classList.remove('hidden');
        clearBtn.classList.add('hidden');
        checkoutBtn.disabled = true;
        cartCount.textContent = '0';
        return;
    }
    
    emptyCart.classList.add('hidden');
    clearBtn.classList.remove('hidden');
    checkoutBtn.disabled = false;
    cartCount.textContent = cart.length;
    
    container.innerHTML = cart.map((item, index) => `
        <div class="modern-card bg-white dark:bg-sport-navy rounded-2xl p-6 shadow-lg animate-slide-up" style="animation-delay: ${index * 0.1}s;">
            <div class="flex flex-col md:flex-row gap-6">
                <!-- Image -->
                <div class="flex-shrink-0">
                    <img src="${item.image || 'https://via.placeholder.com/150x150/667eea/ffffff?text=Product'}" 
                         alt="${item.name}"
                         class="w-32 h-32 object-cover rounded-xl">
                </div>
                
                <!-- Details -->
                <div class="flex-1 space-y-3">
                    <div class="flex items-start justify-between">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-1">
                                ${item.name}
                            </h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                <i class="fas fa-tag mr-1"></i>${item.brand || 'SportShop'}
                            </p>
                            ${item.size ? `<p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                <i class="fas fa-ruler mr-1"></i>Size: ${item.size}
                            </p>` : ''}
                            ${item.color ? `<p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                <i class="fas fa-palette mr-1"></i>Màu: ${item.color}
                            </p>` : ''}
                        </div>
                        <button onclick="removeFromCart(${index})" 
                                class="p-2 rounded-lg text-red-500 hover:bg-red-500 hover:text-white transition-all duration-300">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                    
                    <!-- Price & Quantity -->
                    <div class="flex items-center justify-between flex-wrap gap-4">
                        <div class="flex items-center space-x-3">
                            <button onclick="updateQuantity(${index}, -1)" 
                                    class="w-10 h-10 rounded-lg bg-gray-100 dark:bg-sport-blue hover:bg-sport-neon hover:text-white transition-all duration-300 font-bold">
                                <i class="fas fa-minus"></i>
                            </button>
                            <input type="number" 
                                   value="${item.quantity}" 
                                   min="1" 
                                   onchange="setQuantity(${index}, this.value)"
                                   class="w-16 text-center py-2 rounded-lg border-2 border-gray-200 dark:border-gray-700 bg-white dark:bg-sport-navy text-gray-900 dark:text-white font-bold">
                            <button onclick="updateQuantity(${index}, 1)" 
                                    class="w-10 h-10 rounded-lg bg-gray-100 dark:bg-sport-blue hover:bg-sport-neon hover:text-white transition-all duration-300 font-bold">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                        
                        <div class="text-right">
                            <p class="text-sm text-gray-600 dark:text-gray-400 line-through">
                                ${formatPrice(item.price * item.quantity)}
                            </p>
                            <p class="text-2xl font-black text-sport-neon">
                                ${formatPrice(item.price * item.quantity)}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `).join('');
}

// Update quantity
function updateQuantity(index, change) {
    if (cart[index]) {
        cart[index].quantity = Math.max(1, cart[index].quantity + change);
        saveCart();
        renderCart();
        updateSummary();
        
        // Animation
        anime({
            targets: `.modern-card:nth-child(${index + 1})`,
            scale: [0.95, 1],
            duration: 300,
            easing: 'easeOutQuad'
        });
    }
}

// Set quantity directly
function setQuantity(index, value) {
    const quantity = parseInt(value);
    if (cart[index] && quantity > 0) {
        cart[index].quantity = quantity;
        saveCart();
        renderCart();
        updateSummary();
    }
}

// Remove item from cart
function removeFromCart(index) {
    if (confirm('Bạn có chắc muốn xóa sản phẩm này khỏi giỏ hàng?')) {
        cart.splice(index, 1);
        saveCart();
        renderCart();
        updateSummary();
    }
}

// Clear entire cart
function clearCart() {
    if (confirm('Bạn có chắc muốn xóa toàn bộ giỏ hàng?')) {
        cart = [];
        saveCart();
        renderCart();
        updateSummary();
    }
}

// Update order summary
function updateSummary() {
    const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    const shipping = subtotal >= 500000 ? 0 : 30000;
    const total = subtotal + shipping;
    
    document.getElementById('subtotal').textContent = formatPrice(subtotal);
    document.getElementById('shipping').textContent = shipping === 0 ? 'Miễn phí' : formatPrice(shipping);
    document.getElementById('total').textContent = formatPrice(total);
}


// Proceed to checkout
function proceedToCheckout() {
    if (cart.length === 0) {
        alert('Giỏ hàng của bạn đang trống!');
        return;
    }
    
    // Save cart and redirect to checkout
    saveCart();
    window.location.href = 'checkout.php';
}

// Format price
function formatPrice(price) {
    return new Intl.NumberFormat('vi-VN').format(price);
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', () => {
    loadCart();
    
    // Animate elements
    anime({
        targets: '.modern-card',
        translateY: [30, 0],
        opacity: [0, 1],
        delay: anime.stagger(100),
        duration: 800,
        easing: 'easeOutQuad'
    });
});
</script>

<?php include '../includes/layout_footer.php'; ?>
