<?php
header('Content-Type: text/html; charset=UTF-8');
require_once '../config.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = sanitizeInput($_POST['first_name']);
    $lastName = sanitizeInput($_POST['last_name']);
    $email = sanitizeInput($_POST['email']);
    $phone = sanitizeInput($_POST['phone']);
    $address = sanitizeInput($_POST['address']);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];
    
    if (empty($firstName) || empty($lastName) || empty($email) || empty($phone) || empty($password) || empty($address)) {
        $error = 'Vui lòng điền đầy đủ thông tin bắt buộc';
    } elseif ($password !== $confirmPassword) {
        $error = 'Mật khẩu xác nhận không khớp';
    } elseif (strlen($password) < 6) {
        $error = 'Mật khẩu phải có ít nhất 6 ký tự';
    } else {
        $db = Database::getInstance()->getConnection();
        
        $stmt = $db->prepare("SELECT UserID FROM Users WHERE Email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = 'Email đã được sử dụng';
        } else {
            $stmt = $db->prepare("SELECT UserID FROM Users WHERE Phone = ?");
            $stmt->execute([$phone]);
            if ($stmt->fetch()) {
                $error = 'Số điện thoại đã được sử dụng';
            } else {
                $passwordHash = password_hash($password, PASSWORD_DEFAULT);
                
                try {
                    $stmt = $db->prepare("
                        INSERT INTO Users (FirstName, LastName, Email, Phone, Password, Address, Role) 
                        VALUES (?, ?, ?, ?, ?, ?, 'customer')
                    ");
                    
                    if ($stmt->execute([$firstName, $lastName, $email, $phone, $passwordHash, $address])) {
                        $success = 'Đăng ký thành công! Vui lòng đăng nhập.';
                    } else {
                        $errorInfo = $stmt->errorInfo();
                        $error = 'Có lỗi xảy ra: ' . $errorInfo[2];
                    }
                } catch (PDOException $e) {
                    $error = 'Có lỗi xảy ra: ' . $e->getMessage();
                }
            }
        }
    }
}

$pageTitle = "Đăng ký";
$isInPages = true;
?>
<!DOCTYPE html>
<html lang="vi" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - SportShop</title>
    
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
        body { font-family: 'Inter', sans-serif; }
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
    
    <div class="w-full max-w-4xl relative z-10">
        <!-- Logo Header -->
        <div class="text-center mb-8 animate-fade-in">
            <div class="inline-flex items-center space-x-3 mb-4">
                <div class="w-16 h-16 bg-gradient-to-br from-sport-neon to-blue-600 rounded-2xl flex items-center justify-center">
                    <i class="fas fa-running text-white text-3xl"></i>
                </div>
                <span class="text-4xl font-black text-white">SportShop</span>
            </div>
            <p class="text-gray-300 text-lg">Tạo tài khoản mới để bắt đầu mua sắm</p>
        </div>
        
        <!-- Register Card -->
        <div class="glass rounded-3xl p-8 shadow-2xl animate-slide-up">
            <?php if ($error): ?>
                <div class="mb-6 p-4 bg-red-500/20 border border-red-500 rounded-2xl text-white">
                    <i class="fas fa-exclamation-circle mr-2"></i><?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="mb-6 p-4 bg-green-500/20 border border-green-500 rounded-2xl text-white">
                    <i class="fas fa-check-circle mr-2"></i><?php echo $success; ?>
                </div>
                <div class="text-center space-y-4">
                    <a href="login.php" class="inline-block px-8 py-4 bg-gradient-to-r from-sport-neon to-blue-600 text-white font-bold rounded-xl hover:shadow-lg hover:shadow-sport-neon/50 transform hover:scale-105 transition-all duration-300">
                        <i class="fas fa-sign-in-alt mr-2"></i>Đăng nhập ngay
                    </a>
                </div>
            <?php else: ?>
            
            <form method="POST" action="" class="space-y-6">
                <!-- Name Fields -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-white">
                            <i class="fas fa-user mr-2"></i>Họ <span class="text-red-400">*</span>
                        </label>
                        <input type="text" name="first_name" required
                               class="w-full px-4 py-4 rounded-xl glass text-white placeholder-gray-400 focus:ring-2 focus:ring-sport-neon focus:outline-none transition-all duration-300"
                               placeholder="Nguyễn">
                    </div>
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-white">
                            <i class="fas fa-user mr-2"></i>Tên <span class="text-red-400">*</span>
                        </label>
                        <input type="text" name="last_name" required
                               class="w-full px-4 py-4 rounded-xl glass text-white placeholder-gray-400 focus:ring-2 focus:ring-sport-neon focus:outline-none transition-all duration-300"
                               placeholder="Văn A">
                    </div>
                </div>
                
                <!-- Contact Fields -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-white">
                            <i class="fas fa-envelope mr-2"></i>Email <span class="text-red-400">*</span>
                        </label>
                        <input type="email" name="email" required
                               class="w-full px-4 py-4 rounded-xl glass text-white placeholder-gray-400 focus:ring-2 focus:ring-sport-neon focus:outline-none transition-all duration-300"
                               placeholder="email@example.com">
                    </div>
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-white">
                            <i class="fas fa-phone mr-2"></i>Số điện thoại <span class="text-red-400">*</span>
                        </label>
                        <input type="tel" name="phone" required
                               class="w-full px-4 py-4 rounded-xl glass text-white placeholder-gray-400 focus:ring-2 focus:ring-sport-neon focus:outline-none transition-all duration-300"
                               placeholder="0912345678">
                    </div>
                </div>
                
                <!-- Address Field -->
                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-white">
                        <i class="fas fa-map-marker-alt mr-2"></i>Địa chỉ <span class="text-red-400">*</span>
                    </label>
                    <input type="text" name="address" required
                           class="w-full px-4 py-4 rounded-xl glass text-white placeholder-gray-400 focus:ring-2 focus:ring-sport-neon focus:outline-none transition-all duration-300"
                           placeholder="123 Nguyễn Văn Cừ, P.1, Q.5, TP.HCM">
                </div>
                
                <!-- Password Fields -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-white">
                            <i class="fas fa-lock mr-2"></i>Mật khẩu <span class="text-red-400">*</span>
                        </label>
                        <input type="password" name="password" required minlength="6"
                               class="w-full px-4 py-4 rounded-xl glass text-white placeholder-gray-400 focus:ring-2 focus:ring-sport-neon focus:outline-none transition-all duration-300"
                               placeholder="Tối thiểu 6 ký tự">
                    </div>
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-white">
                            <i class="fas fa-lock mr-2"></i>Xác nhận mật khẩu <span class="text-red-400">*</span>
                        </label>
                        <input type="password" name="confirm_password" required
                               class="w-full px-4 py-4 rounded-xl glass text-white placeholder-gray-400 focus:ring-2 focus:ring-sport-neon focus:outline-none transition-all duration-300"
                               placeholder="Nhập lại mật khẩu">
                    </div>
                </div>
                
                <!-- Terms Checkbox -->
                <div class="flex items-center space-x-3">
                    <input type="checkbox" id="terms" required
                           class="w-5 h-5 rounded border-gray-400 text-sport-neon focus:ring-sport-neon">
                    <label for="terms" class="text-sm text-gray-300">
                        Tôi đồng ý với <a href="#" class="text-sport-neon hover:underline">Điều khoản sử dụng</a> và <a href="#" class="text-sport-neon hover:underline">Chính sách bảo mật</a>
                    </label>
                </div>
                
                <!-- Submit Button -->
                <button type="submit" 
                        class="w-full py-4 bg-gradient-to-r from-sport-neon to-blue-600 text-white font-bold rounded-xl hover:shadow-lg hover:shadow-sport-neon/50 transform hover:scale-105 transition-all duration-300">
                    <i class="fas fa-user-plus mr-2"></i>Đăng ký ngay
                </button>
            </form>
            
            <?php endif; ?>
            
            <!-- Footer Links -->
            <div class="mt-8 pt-6 border-t border-gray-600 text-center space-y-3">
                <p class="text-gray-300">
                    Đã có tài khoản? 
                    <a href="login.php" class="text-sport-neon font-semibold hover:underline">Đăng nhập</a>
                </p>
                <a href="../index.php" class="inline-block text-gray-400 hover:text-white transition-colors duration-300">
                    <i class="fas fa-arrow-left mr-2"></i>Quay lại trang chủ
                </a>
            </div>
        </div>
    </div>
    
    <script>
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
