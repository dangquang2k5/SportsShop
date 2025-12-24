<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thanh toán - SportShop</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="../assets/js/api-client.js"></script>
</head>

<body class="bg-gray-50">

    <!-- Header -->
    <header class="bg-gradient-to-r from-blue-900 to-purple-900 text-white py-6">
        <div class="container mx-auto px-4">
            <h1 class="text-3xl font-bold">
                <i class="fas fa-shopping-cart mr-2"></i>Thanh toán
            </h1>
        </div>
    </header>

    <!-- Checkout Steps -->
    <section class="bg-white shadow-sm py-4">
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-center space-x-4">
                <div class="flex items-center">
                    <div class="w-10 h-10 rounded-full bg-green-500 flex items-center justify-center text-white">
                        <i class="fas fa-check"></i>
                    </div>
                    <span class="ml-2 font-semibold">Giỏ hàng</span>
                </div>
                <div class="w-16 h-1 bg-green-500"></div>
                <div class="flex items-center">
                    <div class="w-10 h-10 rounded-full bg-blue-500 flex items-center justify-center text-white">2</div>
                    <span class="ml-2 font-semibold text-blue-500">Thanh toán</span>
                </div>
                <div class="w-16 h-1 bg-gray-300"></div>
                <div class="flex items-center">
                    <div class="w-10 h-10 rounded-full bg-gray-300 flex items-center justify-center text-gray-600">3
                    </div>
                    <span class="ml-2 text-gray-600">Hoàn tất</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Main Content -->
    <section class="container mx-auto px-4 py-8">
        <!-- Success Message -->
        <div id="success-section" class="hidden">
            <div class="bg-green-100 border border-green-400 text-green-700 px-6 py-4 rounded-lg mb-6 text-center">
                <i class="fas fa-check-circle text-4xl mb-3"></i>
                <h2 class="text-2xl font-bold mb-2">Đặt hàng thành công!</h2>
                <p class="mb-4">Mã đơn hàng: <span id="order-id" class="font-bold"></span></p>
                <div class="space-x-4">
                    <a href="products_new.php"
                        class="inline-block px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        Tiếp tục mua sắm
                    </a>
                </div>
            </div>
        </div>

        <!-- Checkout Form -->
        <div id="checkout-form" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Column -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Contact Info -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-xl font-bold mb-4">
                        <i class="fas fa-user mr-2 text-blue-600"></i>Thông tin liên hệ
                    </h3>
                    <div id="guest-info" class="space-y-4">
                        <div>
                            <label class="block text-sm font-semibold mb-2">Họ và tên *</label>
                            <input type="text" id="guest-name" required
                                class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold mb-2">Email *</label>
                            <input type="email" id="guest-email" required
                                class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                </div>

                <!-- Shipping Info -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-xl font-bold mb-4">
                        <i class="fas fa-shipping-fast mr-2 text-blue-600"></i>Thông tin giao hàng
                    </h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-semibold mb-2">Số điện thoại *</label>
                            <input type="tel" id="shipping-phone" required
                                class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold mb-2">Địa chỉ *</label>
                            <input type="text" id="shipping-address" required
                                class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold mb-2">Thành phố *</label>
                            <select id="shipping-city" required
                                class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                                <option value="">Chọn thành phố</option>
                                <option value="TP. Hồ Chí Minh">TP. Hồ Chí Minh</option>
                                <option value="Hà Nội">Hà Nội</option>
                                <option value="Đà Nẵng">Đà Nẵng</option>
                                <option value="Cần Thơ">Cần Thơ</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold mb-2">Ghi chú</label>
                            <textarea id="notes" rows="3"
                                class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"></textarea>
                        </div>
                    </div>
                </div>

                <!-- Voucher -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-xl font-bold mb-4">
                        <i class="fas fa-ticket-alt mr-2 text-blue-600"></i>Mã giảm giá
                    </h3>
                    <div class="flex gap-2">
                        <input type="text" id="voucher-code" placeholder="Nhập mã giảm giá"
                            class="flex-1 px-4 py-2 border rounded-lg">
                        <button onclick="applyVoucher()"
                            class="px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">
                            Áp dụng
                        </button>
                    </div>
                    <div id="voucher-message" class="mt-2 text-sm"></div>
                </div>
            </div>

            <!-- Right Column - Order Summary -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-md p-6 sticky top-24">
                    <h3 class="text-xl font-bold mb-4">Đơn hàng</h3>

                    <!-- Cart Items -->
                    <div id="cart-items" class="space-y-3 mb-4 max-h-64 overflow-y-auto">
                        <!-- Items loaded by JS -->
                    </div>

                    <!-- Summary -->
                    <div class="border-t pt-4 space-y-2">
                        <div class="flex justify-between">
                            <span>Tạm tính:</span>
                            <span id="subtotal" class="font-semibold">0₫</span>
                        </div>
                        <div id="discount-row" class="hidden flex justify-between text-red-600">
                            <span>Giảm giá:</span>
                            <span id="discount">0₫</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Phí vận chuyển:</span>
                            <span id="shipping">30,000₫</span>
                        </div>
                        <div class="flex justify-between text-xl font-bold border-t pt-2">
                            <span>Tổng cộng:</span>
                            <span id="total" class="text-blue-600">0₫</span>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button onclick="submitOrder()" id="submit-btn"
                        class="w-full mt-6 py-3 bg-green-600 text-white font-bold rounded-lg hover:bg-green-700">
                        <i class="fas fa-lock mr-2"></i>Đặt hàng ngay
                    </button>
                </div>
            </div>
        </div>
    </section>

    <script>
        let cart = [];
        let selectedVoucher = null;
        let subtotal = 0;
        let discount = 0;
        let shipping = 30000;

        // Load cart from localStorage
        function loadCart() {
            cart = JSON.parse(localStorage.getItem('cart') || '[]');

            if (cart.length === 0) {
                alert('Giỏ hàng trống!');
                window.location.href = 'products_new.php';
                return;
            }

            displayCart();
            calculateTotal();
        }

        // Display cart items
        function displayCart() {
            const container = document.getElementById('cart-items');
            container.innerHTML = cart.map(item => `
                <div class="flex items-center gap-3 p-2 border rounded">
                    <img src="${item.image || 'https://via.placeholder.com/60'}" 
                         class="w-12 h-12 object-cover rounded">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold truncate">${item.name}</p>
                        <p class="text-xs text-gray-600">SL: ${item.quantity}</p>
                    </div>
                    <span class="text-sm font-bold">${formatPrice(item.price * item.quantity)}₫</span>
                </div>
            `).join('');
        }

        // Calculate total
        function calculateTotal() {
            subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
            shipping = subtotal >= 500000 ? 0 : 30000;
            const total = subtotal - discount + shipping;

            document.getElementById('subtotal').textContent = formatPrice(subtotal) + '₫';
            document.getElementById('shipping').textContent = shipping === 0 ? 'Miễn phí' : formatPrice(shipping) + '₫';
            document.getElementById('total').textContent = formatPrice(total) + '₫';
        }

        // Apply voucher
        async function applyVoucher() {
            const code = document.getElementById('voucher-code').value.trim();
            const messageEl = document.getElementById('voucher-message');

            if (!code) {
                messageEl.textContent = 'Vui lòng nhập mã giảm giá';
                messageEl.className = 'mt-2 text-sm text-red-600';
                return;
            }

            try {
                const response = await api.validateVoucher(code, subtotal);

                if (response.success) {
                    selectedVoucher = response.data;
                    discount = response.data.discountValue;

                    document.getElementById('discount').textContent = formatPrice(discount) + '₫';
                    document.getElementById('discount-row').classList.remove('hidden');

                    messageEl.textContent = `✓ Đã áp dụng mã giảm ${formatPrice(discount)}₫`;
                    messageEl.className = 'mt-2 text-sm text-green-600';

                    calculateTotal();
                }
            } catch (error) {
                messageEl.textContent = error.message;
                messageEl.className = 'mt-2 text-sm text-red-600';
            }
        }

        // Submit order
        async function submitOrder() {
            const guestName = document.getElementById('guest-name').value.trim();
            const guestEmail = document.getElementById('guest-email').value.trim();
            const shippingPhone = document.getElementById('shipping-phone').value.trim();
            const shippingAddress = document.getElementById('shipping-address').value.trim();
            const shippingCity = document.getElementById('shipping-city').value;
            const notes = document.getElementById('notes').value.trim();

            // Validation
            if (!guestName || !guestEmail || !shippingPhone || !shippingAddress || !shippingCity) {
                alert('Vui lòng điền đầy đủ thông tin!');
                return;
            }

            const submitBtn = document.getElementById('submit-btn');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Đang xử lý...';

            try {
                const orderData = {
                    items: cart.map(item => ({
                        productDetailId: item.productDetailId,
                        quantity: item.quantity,
                        price: item.price
                    })),
                    shippingAddress,
                    shippingCity,
                    shippingPhone,
                    guestName,
                    guestEmail,
                    notes,
                    voucherCode: selectedVoucher?.voucherCode
                };

                const response = await api.createOrder(orderData);

                if (response.success) {
                    // Clear cart
                    localStorage.removeItem('cart');

                    // Show success
                    document.getElementById('checkout-form').classList.add('hidden');
                    document.getElementById('success-section').classList.remove('hidden');
                    document.getElementById('order-id').textContent = '#' + String(response.data.orderId).padStart(6, '0');

                    window.scrollTo({ top: 0, behavior: 'smooth' });
                }
            } catch (error) {
                alert('Lỗi: ' + error.message);
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-lock mr-2"></i>Đặt hàng ngay';
            }
        }

        // Format price
        function formatPrice(price) {
            return new Intl.NumberFormat('vi-VN').format(price);
        }

        // Initialize
        document.addEventListener('DOMContentLoaded', () => {
            loadCart();
        });
    </script>
</body>

</html>