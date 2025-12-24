<?php
require_once '../config.php';

// Destroy session completely
$_SESSION = array(); // Clear session data

// Delete session cookie
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time()-3600, '/');
}

// Destroy session
session_destroy();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Đăng xuất...</title>
</head>
<body>
    <script>
        // Xóa giỏ hàng trong localStorage khi đăng xuất
        localStorage.removeItem('cart');
        localStorage.removeItem('cart_backup');
        localStorage.removeItem('buy_now_mode');
        localStorage.removeItem('appliedCoupon');
        
        // Redirect về trang chủ
        window.location.href = '../index.php';
    </script>
</body>
</html>
