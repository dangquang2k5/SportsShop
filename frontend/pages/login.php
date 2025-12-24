<?php
header('Content-Type: text/html; charset=UTF-8');
require_once '../config.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $phone = sanitizeInput($_POST['phone']);
    $password = $_POST['password'];
    
    if (empty($phone) || empty($password)) {
        $error = 'Vui lòng nhập đầy đủ thông tin';
    } else {
        // Use backend API for authentication
        $response = makeApiRequest('/auth/login', 'POST', [
            'phone' => $phone,
            'password' => $password
        ]);
        
        if ($response['success'] && isset($response['data']['token'])) {
            // Store token in session (for now, in production use secure storage)
            $_SESSION['token'] = $response['data']['token'];
            $_SESSION['user_id'] = $response['data']['user']['UserID'];
            $_SESSION['full_name'] = $response['data']['user']['FirstName'] . ' ' . $response['data']['user']['LastName'];
            $_SESSION['role'] = $response['data']['user']['Role'];
            
            if ($response['data']['user']['Role'] === 'admin') {
                redirect('admin_dashboard.php');
            } else {
                redirect('../index.php');
            }
        } else {
            $error = $response['message'] ?? 'Số điện thoại hoặc mật khẩu không đúng';
        }
    }
}

$pageTitle = "Đăng nhập";
$isInPages = true;
?>
<!DOCTYPE html>
<html lang="vi" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - SportShop</title>
    
    <!-- TailwindCSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        'sport-navy': '#0a1628',
                        'sport-blue': '#0f2744',
                        'sport-neon': '#00f0ff',
                    }
                }
            }
        }
    </script>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/animejs/3.2.1/anime.min.js"></script>
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        .glass {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
    </style>
</head>
<body class="bg-gradient-to-br from-sport-navy via-sport-blue to-purple-900 min-h-screen flex items-center justify-center p-4">
    <!-- Animated Background -->
    <div class="absolute inset-0 overflow-hidden">
        <div class="absolute -top-1/2 -left-1/2 w-full h-full bg-sport-neon opacity-10 rounded-full blur-3xl animate-pulse"></div>
        <div class="absolute -bottom-1/2 -right-1/2 w-full h-full bg-purple-600 opacity-10 rounded-full blur-3xl animate-pulse" style="animation-delay: 1s;"></div>
    </div>
    
    <div class="w-full max-w-md relative z-10">
        <!-- Logo Header -->
        <div class="text-center mb-8 animate-fade-in">
            <div class="inline-flex items-center space-x-3 mb-4">
                <div class="w-16 h-16 bg-gradient-to-br from-sport-neon to-blue-600 rounded-2xl flex items-center justify-center">
                    <i class="fas fa-running text-white text-3xl"></i>
                </div>
                <span class="text-4xl font-black text-white">SportShop</span>
            </div>
            <p class="text-gray-300 text-lg">Đăng nhập vào tài khoản của bạn</p>
        </div>
        
        <!-- Login Card -->
        <div class="glass rounded-3xl p-8 shadow-2xl animate-slide-up">
            <?php if ($error): ?>
                <div class="mb-6 p-4 bg-red-500/20 border border-red-500 rounded-2xl text-white animate-shake">
                    <i class="fas fa-exclamation-circle mr-2"></i><?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="" class="space-y-6">
                <!-- Phone Input -->
                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-white">
                        <i class="fas fa-phone mr-2"></i>Số điện thoại
                    </label>
                    <input type="text" 
                           name="phone" 
                           required
                           class="w-full px-4 py-4 rounded-xl glass text-white placeholder-gray-400 focus:ring-2 focus:ring-sport-neon focus:outline-none transition-all duration-300"
                           placeholder="Nhập số điện thoại">
                </div>
                
                <!-- Password Input -->
                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-white">
                        <i class="fas fa-lock mr-2"></i>Mật khẩu
                    </label>
                    <input type="password" 
                           name="password" 
                           required
                           class="w-full px-4 py-4 rounded-xl glass text-white placeholder-gray-400 focus:ring-2 focus:ring-sport-neon focus:outline-none transition-all duration-300"
                           placeholder="Nhập mật khẩu">
                </div>
                
                <!-- Remember Me -->
                <div class="flex items-center justify-between">
                    <label class="flex items-center space-x-2 cursor-pointer">
                        <input type="checkbox" id="remember" class="w-4 h-4 rounded border-gray-400 text-sport-neon focus:ring-sport-neon">
                        <span class="text-sm text-gray-300">Ghi nhớ đăng nhập</span>
                    </label>
                    <a href="#" class="text-sm text-sport-neon hover:underline">Quên mật khẩu?</a>
                </div>
                
                <!-- Submit Button -->
                <button type="submit" 
                        class="w-full py-4 bg-gradient-to-r from-sport-neon to-blue-600 text-white font-bold rounded-xl hover:shadow-lg hover:shadow-sport-neon/50 transform hover:scale-105 transition-all duration-300">
                    <i class="fas fa-sign-in-alt mr-2"></i>Đăng nhập ngay
                </button>
            </form>
            
            <!-- Footer Links -->
            <div class="mt-8 pt-6 border-t border-gray-600 text-center space-y-3">
                <p class="text-gray-300">
                    Chưa có tài khoản? 
                    <a href="register.php" class="text-sport-neon font-semibold hover:underline">Đăng ký ngay</a>
                </p>
                <a href="../index.php" class="inline-block text-gray-400 hover:text-white transition-colors duration-300">
                    <i class="fas fa-arrow-left mr-2"></i>Quay lại trang chủ
                </a>
            </div>
        </div>
        
        <!-- Social Login (Optional) -->
        <div class="mt-6 text-center">
            <p class="text-gray-400 text-sm mb-4">Hoặc đăng nhập với</p>
            <div class="flex justify-center space-x-4">
                <button class="w-12 h-12 rounded-full glass hover:bg-white/20 transition-all duration-300">
                    <i class="fab fa-facebook-f text-white"></i>
                </button>
                <button class="w-12 h-12 rounded-full glass hover:bg-white/20 transition-all duration-300">
                    <i class="fab fa-google text-white"></i>
                </button>
                <button class="w-12 h-12 rounded-full glass hover:bg-white/20 transition-all duration-300">
                    <i class="fab fa-apple text-white"></i>
                </button>
            </div>
        </div>
    </div>
    
    <script>
        // Animate elements on load
        window.addEventListener('load', () => {
            anime({
                targets: '.animate-fade-in',
                opacity: [0, 1],
                duration: 800,
                easing: 'easeOutQuad'
            });
            
            anime({
                targets: '.animate-slide-up',
                translateY: [50, 0],
                opacity: [0, 1],
                duration: 1000,
                delay: 200,
                easing: 'easeOutQuad'
            });
        });
    </script>
</body>
</html>