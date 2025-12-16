<!DOCTYPE html>
<html lang="vi" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'SportShop'; ?> - Cửa hàng thể thao hiện đại</title>
    
    <!-- DNS Prefetch & Preconnect for faster CDN loading -->
    <link rel="dns-prefetch" href="https://cdn.tailwindcss.com">
    <link rel="dns-prefetch" href="https://cdnjs.cloudflare.com">
    <link rel="dns-prefetch" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://cdn.tailwindcss.com">
    <link rel="preconnect" href="https://cdnjs.cloudflare.com">
    
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
                        'sport-neon-pink': '#ff006e',
                        'sport-gold': '#ffd60a',
                    },
                    fontFamily: {
                        'sport': ['Inter', 'SF Pro Display', 'system-ui', 'sans-serif'],
                    },
                    animation: {
                        'fade-in': 'fadeIn 0.6s ease-out',
                        'slide-up': 'slideUp 0.6s ease-out',
                        'slide-down': 'slideDown 0.3s ease-out',
                        'scale-in': 'scaleIn 0.4s ease-out',
                        'glow': 'glow 2s ease-in-out infinite',
                    },
                    keyframes: {
                        fadeIn: {
                            '0%': { opacity: '0' },
                            '100%': { opacity: '1' },
                        },
                        slideUp: {
                            '0%': { transform: 'translateY(30px)', opacity: '0' },
                            '100%': { transform: 'translateY(0)', opacity: '1' },
                        },
                        slideDown: {
                            '0%': { transform: 'translateY(-10px)', opacity: '0' },
                            '100%': { transform: 'translateY(0)', opacity: '1' },
                        },
                        scaleIn: {
                            '0%': { transform: 'scale(0.9)', opacity: '0' },
                            '100%': { transform: 'scale(1)', opacity: '1' },
                        },
                        glow: {
                            '0%, 100%': { boxShadow: '0 0 20px rgba(0, 240, 255, 0.5)' },
                            '50%': { boxShadow: '0 0 40px rgba(0, 240, 255, 0.8)' },
                        },
                    },
                }
            }
        }
    </script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- AnimeJS - Deferred for faster initial load -->
    <script defer src="https://cdnjs.cloudflare.com/ajax/libs/animejs/3.2.1/anime.min.js"></script>
    
    <!-- Custom Modern Styles -->
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: #ffffff;
            transition: background 0.3s ease;
        }
        
        body.dark {
            background: #0a0e27;
            color: #e5e7eb;
        }
        
        /* Modern Glassmorphism */
        .glass {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .dark .glass {
            background: rgba(15, 39, 68, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        /* Modern Card Hover */
        .modern-card {
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }
        
        .modern-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(0, 240, 255, 0.1), transparent);
            transition: left 0.5s ease;
        }
        
        .modern-card:hover::before {
            left: 100%;
        }
        
        .modern-card:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 20px 60px rgba(0, 240, 255, 0.3);
        }
        
        /* Neon Button */
        .btn-neon {
            position: relative;
            padding: 12px 32px;
            font-weight: 600;
            border-radius: 12px;
            background: linear-gradient(135deg, #00f0ff, #0066ff);
            color: white;
            border: none;
            overflow: hidden;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .btn-neon::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.3);
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }
        
        .btn-neon:hover::before {
            width: 300px;
            height: 300px;
        }
        
        .btn-neon:hover {
            box-shadow: 0 0 30px rgba(0, 240, 255, 0.6);
            transform: translateY(-2px);
        }
        
        /* Loading Animation */
        .loader {
            width: 50px;
            height: 50px;
            border: 5px solid rgba(0, 240, 255, 0.2);
            border-top-color: #00f0ff;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        
        /* Smooth Scroll */
        html {
            scroll-behavior: smooth;
        }
        
        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 10px;
        }
        
        ::-webkit-scrollbar-track {
            background: #1a1a2e;
        }
        
        ::-webkit-scrollbar-thumb {
            background: linear-gradient(180deg, #00f0ff, #0066ff);
            border-radius: 10px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(180deg, #00d4ff, #0055ff);
        }
        
        /* Mobile Menu Animation */
        .mobile-menu {
            transform: translateX(-100%);
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .mobile-menu.active {
            transform: translateX(0);
        }
        
        /* Admin Sidebar Sticky */
        #admin-sidebar {
            position: fixed;
            top: 80px;
            left: 0;
            width: 256px;
            height: auto;
            max-height: calc(100vh - 80px - 20px);
            overflow-y: auto;
            overflow-x: hidden;
            scroll-behavior: smooth;
            z-index: 25;
        }
        
        /* Prevent sidebar content from overlapping footer */
        #admin-sidebar .p-6 {
            padding-bottom: 80px !important;
        }
        
        /* Smooth scrolling for sidebar */
        #admin-sidebar::-webkit-scrollbar {
            width: 6px;
        }
        
        #admin-sidebar::-webkit-scrollbar-track {
            background: transparent;
        }
        
        #admin-sidebar::-webkit-scrollbar-thumb {
            background: rgba(0, 240, 255, 0.3);
            border-radius: 3px;
        }
        
        #admin-sidebar::-webkit-scrollbar-thumb:hover {
            background: rgba(0, 240, 255, 0.5);
        }
        
        /* Ensure sidebar doesn't overlap footer */
        footer {
            position: relative;
            z-index: 30;
        }
    </style>
</head>
<body class="font-sport antialiased">
    <!-- Page Loader -->
    <div id="pageLoader" class="fixed inset-0 bg-sport-navy z-50 flex items-center justify-center transition-opacity duration-200">
        <div class="text-center">
            <div class="loader mx-auto mb-4"></div>
            <p class="text-sport-neon text-lg font-semibold">Đang tải...</p>
        </div>
    </div>

    <!-- Navigation Bar -->
    <nav class="fixed top-0 w-full z-40 glass backdrop-blur-md border-b border-gray-200 dark:border-gray-800 transition-all duration-300" id="mainNav">
        <div class="container mx-auto px-4 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <!-- Logo -->
                <a href="<?php echo isset($isInPages) && $isInPages ? '../index.php' : 'index.php'; ?>" class="flex items-center space-x-3 group">
                    <div class="w-12 h-12 bg-gradient-to-br from-sport-neon to-blue-600 rounded-xl flex items-center justify-center transform group-hover:rotate-12 transition-transform duration-300">
                        <i class="fas fa-running text-white text-xl"></i>
                    </div>
                    <span class="text-2xl font-black bg-gradient-to-r from-sport-neon to-blue-600 bg-clip-text text-transparent">
                        SportShop
                    </span>
                </a>

                <!-- Desktop Menu -->
                <div class="hidden lg:flex items-center space-x-8">
                    <a href="<?php echo isset($isInPages) && $isInPages ? '../index.php' : 'index.php'; ?>" 
                       class="nav-link text-gray-700 dark:text-gray-300 hover:text-sport-neon font-medium transition-colors duration-300">
                        <i class="fas fa-home mr-2"></i>Trang chủ
                    </a>
                    <a href="<?php echo isset($isInPages) && $isInPages ? 'products.php' : 'pages/products.php'; ?>" 
                       class="nav-link text-gray-700 dark:text-gray-300 hover:text-sport-neon font-medium transition-colors duration-300">
                        <i class="fas fa-shopping-bag mr-2"></i>Sản phẩm
                    </a>
                    
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                            <a href="<?php echo isset($isInPages) && $isInPages ? 'admin_dashboard.php' : 'pages/admin_dashboard.php'; ?>" 
                               class="nav-link text-gray-700 dark:text-gray-300 hover:text-sport-neon font-medium transition-colors duration-300">
                                <i class="fas fa-tachometer-alt mr-2"></i>Quản trị
                            </a>
                        <?php endif; ?>
                        <a href="<?php echo isset($isInPages) && $isInPages ? 'profile.php' : 'pages/profile.php'; ?>" 
                           class="nav-link text-gray-700 dark:text-gray-300 hover:text-sport-neon font-medium transition-colors duration-300">
                            <i class="fas fa-user mr-2"></i>Tài khoản
                        </a>
                        <a href="<?php echo isset($isInPages) && $isInPages ? 'logout.php' : 'pages/logout.php'; ?>" 
                           class="nav-link text-gray-700 dark:text-gray-300 hover:text-red-500 font-medium transition-colors duration-300">
                            <i class="fas fa-sign-out-alt mr-2"></i>Đăng xuất
                        </a>
                    <?php else: ?>
                        <a href="<?php echo isset($isInPages) && $isInPages ? 'login.php' : 'pages/login.php'; ?>" 
                           class="nav-link text-gray-700 dark:text-gray-300 hover:text-sport-neon font-medium transition-colors duration-300">
                            <i class="fas fa-sign-in-alt mr-2"></i>Đăng nhập
                        </a>
                        <a href="<?php echo isset($isInPages) && $isInPages ? 'register.php' : 'pages/register.php'; ?>" 
                           class="btn-neon text-sm relative z-10">
                            <i class="fas fa-user-plus mr-2"></i>Đăng ký
                        </a>
                    <?php endif; ?>
                    
                    <a href="<?php echo isset($isInPages) && $isInPages ? 'cart.php' : 'pages/cart.php'; ?>" 
                       class="p-2 text-gray-700 dark:text-gray-300 hover:text-sport-neon transition-colors duration-300">
                        <i class="fas fa-shopping-cart text-xl"></i>
                    </a>
                    
                    <!-- Dark Mode Toggle -->
                    <button onclick="toggleDarkMode()" class="p-2 rounded-lg glass hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors duration-300">
                        <i class="fas fa-moon dark:hidden text-gray-700"></i>
                        <i class="fas fa-sun hidden dark:inline text-yellow-400"></i>
                    </button>
                </div>

                <!-- Mobile Menu Button -->
                <button onclick="toggleMobileMenu()" class="lg:hidden p-2 rounded-lg glass">
                    <i class="fas fa-bars text-2xl text-gray-700 dark:text-gray-300"></i>
                </button>
            </div>
        </div>
    </nav>

    <!-- Mobile Menu -->
    <div id="mobileMenu" class="mobile-menu fixed top-0 left-0 w-80 h-full bg-white dark:bg-sport-blue z-50 shadow-2xl lg:hidden">
        <div class="p-6">
            <div class="flex items-center justify-between mb-8">
                <span class="text-2xl font-black bg-gradient-to-r from-sport-neon to-blue-600 bg-clip-text text-transparent">
                    SportShop
                </span>
                <button onclick="toggleMobileMenu()" class="p-2 rounded-lg glass">
                    <i class="fas fa-times text-2xl text-gray-700 dark:text-gray-300"></i>
                </button>
            </div>
            
            <nav class="space-y-4">
                <a href="<?php echo isset($isInPages) && $isInPages ? '../index.php' : 'index.php'; ?>" 
                   class="block px-4 py-3 rounded-lg glass hover:bg-sport-neon hover:text-white transition-all duration-300">
                    <i class="fas fa-home mr-3"></i>Trang chủ
                </a>
                <a href="<?php echo isset($isInPages) && $isInPages ? 'products.php' : 'pages/products.php'; ?>" 
                   class="block px-4 py-3 rounded-lg glass hover:bg-sport-neon hover:text-white transition-all duration-300">
                    <i class="fas fa-shopping-bag mr-3"></i>Sản phẩm
                </a>
                
                <?php if (isset($_SESSION['user_id'])): ?>
                    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                        <a href="<?php echo isset($isInPages) && $isInPages ? 'admin_dashboard.php' : 'pages/admin_dashboard.php'; ?>" 
                           class="block px-4 py-3 rounded-lg glass hover:bg-sport-neon hover:text-white transition-all duration-300">
                            <i class="fas fa-tachometer-alt mr-3"></i>Quản trị
                        </a>
                    <?php endif; ?>
                    <a href="<?php echo isset($isInPages) && $isInPages ? 'profile.php' : 'pages/profile.php'; ?>" 
                       class="block px-4 py-3 rounded-lg glass hover:bg-sport-neon hover:text-white transition-all duration-300">
                        <i class="fas fa-user mr-3"></i>Tài khoản
                    </a>
                    <a href="<?php echo isset($isInPages) && $isInPages ? 'logout.php' : 'pages/logout.php'; ?>" 
                       class="block px-4 py-3 rounded-lg glass hover:bg-red-500 hover:text-white transition-all duration-300">
                        <i class="fas fa-sign-out-alt mr-3"></i>Đăng xuất
                    </a>
                <?php else: ?>
                    <a href="<?php echo isset($isInPages) && $isInPages ? 'login.php' : 'pages/login.php'; ?>" 
                       class="block px-4 py-3 rounded-lg glass hover:bg-sport-neon hover:text-white transition-all duration-300">
                        <i class="fas fa-sign-in-alt mr-3"></i>Đăng nhập
                    </a>
                    <a href="<?php echo isset($isInPages) && $isInPages ? 'register.php' : 'pages/register.php'; ?>" 
                       class="block px-4 py-3 rounded-lg bg-gradient-to-r from-sport-neon to-blue-600 text-white font-semibold">
                        <i class="fas fa-user-plus mr-3"></i>Đăng ký
                    </a>
                <?php endif; ?>
                
                <a href="<?php echo isset($isInPages) && $isInPages ? 'cart.php' : 'pages/cart.php'; ?>" 
                   class="block px-4 py-3 rounded-lg glass hover:bg-sport-neon hover:text-white transition-all duration-300">
                    <i class="fas fa-shopping-cart mr-3"></i>Giỏ hàng
                </a>
                
                <button onclick="toggleDarkMode()" class="w-full px-4 py-3 rounded-lg glass hover:bg-gray-200 dark:hover:bg-gray-700 transition-all duration-300 text-left">
                    <i class="fas fa-moon dark:hidden mr-3"></i>
                    <i class="fas fa-sun hidden dark:inline mr-3 text-yellow-400"></i>
                    <span class="dark:hidden">Chế độ tối</span>
                    <span class="hidden dark:inline">Chế độ sáng</span>
                </button>
            </nav>
        </div>
    </div>

    <!-- Mobile Menu Overlay -->
    <div id="mobileMenuOverlay" onclick="toggleMobileMenu()" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden lg:hidden"></div>

    <!-- Main Content -->
    <main class="pt-20">
