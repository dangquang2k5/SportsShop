<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập - SportShop</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="../assets/js/api-client.js"></script>
</head>

<body
    class="bg-gradient-to-br from-blue-900 via-blue-800 to-purple-900 min-h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-md">
        <!-- Logo Header -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center space-x-3 mb-4">
                <div
                    class="w-16 h-16 bg-gradient-to-br from-cyan-400 to-blue-600 rounded-2xl flex items-center justify-center">
                    <i class="fas fa-running text-white text-3xl"></i>
                </div>
                <span class="text-4xl font-black text-white">SportShop</span>
            </div>
            <p class="text-gray-300 text-lg">Đăng nhập vào tài khoản của bạn</p>
        </div>

        <!-- Login Card -->
        <div class="bg-white/10 backdrop-blur-lg rounded-3xl p-8 shadow-2xl border border-white/20">
            <!-- Error Message -->
            <div id="error-message" class="hidden mb-6 p-4 bg-red-500/20 border border-red-500 rounded-2xl text-white">
                <i class="fas fa-exclamation-circle mr-2"></i>
                <span id="error-text"></span>
            </div>

            <!-- Success Message -->
            <div id="success-message"
                class="hidden mb-6 p-4 bg-green-500/20 border border-green-500 rounded-2xl text-white">
                <i class="fas fa-check-circle mr-2"></i>
                <span id="success-text"></span>
            </div>

            <form id="login-form" class="space-y-6">
                <!-- Phone Input -->
                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-white">
                        <i class="fas fa-phone mr-2"></i>Số điện thoại
                    </label>
                    <input type="text" id="phone" name="phone" required
                        class="w-full px-4 py-4 rounded-xl bg-white/10 border border-white/20 text-white placeholder-gray-400 focus:ring-2 focus:ring-cyan-400 focus:outline-none transition-all duration-300"
                        placeholder="Nhập số điện thoại">
                </div>

                <!-- Password Input -->
                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-white">
                        <i class="fas fa-lock mr-2"></i>Mật khẩu
                    </label>
                    <input type="password" id="password" name="password" required
                        class="w-full px-4 py-4 rounded-xl bg-white/10 border border-white/20 text-white placeholder-gray-400 focus:ring-2 focus:ring-cyan-400 focus:outline-none transition-all duration-300"
                        placeholder="Nhập mật khẩu">
                </div>

                <!-- Submit Button -->
                <button type="submit" id="submit-btn"
                    class="w-full py-4 bg-gradient-to-r from-cyan-400 to-blue-600 text-white font-bold rounded-xl hover:shadow-lg hover:shadow-cyan-400/50 transform hover:scale-105 transition-all duration-300">
                    <i class="fas fa-sign-in-alt mr-2"></i>Đăng nhập ngay
                </button>
            </form>

            <!-- Footer Links -->
            <div class="mt-8 pt-6 border-t border-gray-600 text-center space-y-3">
                <p class="text-gray-300">
                    Chưa có tài khoản?
                    <a href="register.php" class="text-cyan-400 font-semibold hover:underline">Đăng ký ngay</a>
                </p>
                <a href="../index.php"
                    class="inline-block text-gray-400 hover:text-white transition-colors duration-300">
                    <i class="fas fa-arrow-left mr-2"></i>Quay lại trang chủ
                </a>
            </div>
        </div>
    </div>

    <script>
        // Check if already logged in
        if (api.isLoggedIn()) {
            const user = api.getUser();
            if (user.role === 'admin') {
                window.location.href = 'admin_dashboard.php';
            } else {
                window.location.href = '../index.php';
            }
        }

        // Handle form submission
        document.getElementById('login-form').addEventListener('submit', async (e) => {
            e.preventDefault();

            const phone = document.getElementById('phone').value;
            const password = document.getElementById('password').value;
            const submitBtn = document.getElementById('submit-btn');
            const errorDiv = document.getElementById('error-message');
            const errorText = document.getElementById('error-text');

            // Hide previous errors
            errorDiv.classList.add('hidden');

            // Disable button and show loading
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Đang đăng nhập...';

            try {
                const response = await api.login(phone, password);

                if (response.success) {
                    // Show success message
                    document.getElementById('success-message').classList.remove('hidden');
                    document.getElementById('success-text').textContent = 'Đăng nhập thành công! Đang chuyển hướng...';

                    // Redirect based on role
                    setTimeout(() => {
                        if (response.data.role === 'admin') {
                            window.location.href = 'admin_dashboard.php';
                        } else {
                            window.location.href = '../index.php';
                        }
                    }, 1000);
                }
            } catch (error) {
                // Show error message
                errorDiv.classList.remove('hidden');
                errorText.textContent = error.message || 'Đã xảy ra lỗi. Vui lòng thử lại.';

                // Re-enable button
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-sign-in-alt mr-2"></i>Đăng nhập ngay';
            }
        });
    </script>
</body>

</html>