<?php
require_once '../config.php';

// Get order ID from URL
$orderId = $_GET['order_id'] ?? null;

if (!$orderId) {
    header('Location: ../index.php');
    exit;
}

<<<<<<< HEAD
// Use backend API to get order details
$response = makeApiRequest('/orders/' . $orderId);

if (!$response['success']) {
    header('Location: ../index.php');
    exit;
}

$order = $response['data']['order'] ?? null;
$orderItems = $response['data']['items'] ?? [];
=======
$db = Database::getInstance()->getConnection();

// SỬA 1: Sửa truy vấn "Đơn hàng"
$stmt = $db->prepare("
    SELECT o.*, COUNT(od.OrderDetailID) as item_count,
           u.FirstName, u.LastName, u.Email, u.Phone
    FROM Orders o
    LEFT JOIN OrderDetails od ON o.OrderID = od.OrderID
    LEFT JOIN Users u ON o.UserID = u.UserID
    WHERE o.OrderID = ?
    GROUP BY o.OrderID
");
$stmt->execute([$orderId]);
$order = $stmt->fetch();
>>>>>>> 3d6d58ed3875cc3c551e3fe1991339ab7637c345

if (!$order) {
    header('Location: ../index.php');
    exit;
}

<<<<<<< HEAD
// Get recipient info from order data
$recipientName = $order['UserID'] ? ($order['FirstName'] . ' ' . $order['LastName']) : ($order['GuestName'] ?? 'Khách hàng');
=======
// SỬA 2: Sửa truy vấn "Chi tiết đơn hàng" (JOIN 4 bảng)
$stmt = $db->prepare("
    SELECT od.*, p.ProductName, p.MainImage, b.BrandName, pd.Size, pd.Color
    FROM OrderDetails od
    JOIN ProductDetail pd ON od.ProductDetailID = pd.ProductDetailID
    JOIN Product p ON pd.ProductID = p.ProductID
    LEFT JOIN Brand b ON p.BrandID = b.BrandID
    WHERE od.OrderID = ?
");
$stmt->execute([$orderId]);
$orderItems = $stmt->fetchAll();

// SỬA 3: Xác định thông tin người nhận
$recipientName = $order['UserID'] ? ($order['FirstName'] . ' ' . $order['LastName']) : $order['GuestName'];
>>>>>>> 3d6d58ed3875cc3c551e3fe1991339ab7637c345
$recipientPhone = $order['UserID'] ? $order['Phone'] : $order['GuestPhone'];
$recipientEmail = $order['UserID'] ? $order['Email'] : $order['GuestEmail'];
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đặt hàng thành công - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .navbar { background: linear-gradient(135deg, #2563eb, #1e40af); box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .success-container { max-width: 800px; margin: 50px auto; }
        .success-icon { width: 120px; height: 120px; border-radius: 50%; background: linear-gradient(135deg, #10b981, #059669); color: white; display: flex; align-items: center; justify-content: center; font-size: 4em; margin: 0 auto 30px; animation: scaleIn 0.5s ease-out; }
        @keyframes scaleIn { from { transform: scale(0); } to { transform: scale(1); } }
        .order-card { border-radius: 15px; box-shadow: 0 2px 15px rgba(0,0,0,0.1); }
        .order-item { border-bottom: 1px solid #e5e7eb; padding: 15px 0; }
        .order-item:last-child { border-bottom: none; }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand fw-bold" href="../index.php">
                <i class="fas fa-running"></i> SportShop
            </a>
            <ul class="navbar-nav ms-auto">
                 <li class="nav-item">
                    <a class="nav-link" href="cart.php">
                        <i class="fas fa-shopping-cart"></i> Giỏ hàng
                    </a>
                </li>
            </ul>
        </div>
    </nav>

    <div class="container success-container">
        <div class="text-center mb-4">
            <div class="success-icon">
                <i class="fas fa-check"></i>
            </div>
            <h2 class="text-success mb-3">Đặt hàng thành công!</h2>
            <p class="lead">Cảm ơn bạn đã mua hàng tại SportShop</p>
            <p class="text-muted">Mã đơn hàng: <strong class="text-primary">#<?php echo str_pad($orderId, 6, '0', STR_PAD_LEFT); ?></strong></p>
        </div>

        <div class="card order-card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-receipt"></i> Thông tin đơn hàng</h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <p class="mb-1"><strong>Người nhận:</strong></p>
                        <p class="text-muted"><?php echo htmlspecialchars($recipientName); ?></p>
                    </div>
                    <div class="col-md-6">
                        <p class="mb-1"><strong>Số điện thoại:</strong></p>
                        <p class="text-muted"><?php echo htmlspecialchars($recipientPhone); ?></p>
                    </div>
                </div>
                
                <?php if ($recipientEmail): ?>
                <div class="row mb-3">
                    <div class="col-12">
                        <p class="mb-1"><strong>Email:</strong></p>
                        <p class="text-muted"><?php echo htmlspecialchars($recipientEmail); ?></p>
                    </div>
                </div>
                <?php endif; ?>
                
                <div class="row mb-3">
                    <div class="col-12">
                        <p class="mb-1"><strong>Địa chỉ giao hàng:</strong></p>
                        <p class="text-muted"><?php echo htmlspecialchars($order['Address']); ?></p>
                    </div>
                </div>
                
                <?php if ($order['Note']): ?>
                <div class="row mb-3">
                    <div class="col-12">
                        <p class="mb-1"><strong>Ghi chú:</strong></p>
                        <p class="text-muted"><?php echo htmlspecialchars($order['Note']); ?></p>
                    </div>
                </div>
                <?php endif; ?>
                
                <hr>
                
                <h6 class="mb-3">Sản phẩm đã đặt (<?php echo $order['item_count']; ?> sản phẩm)</h6>
                
                <?php foreach ($orderItems as $item): ?>
                <div class="order-item">
                    <div class="row align-items-center">
                        <div class="col-md-2">
                            <img src="<?php echo $item['MainImage'] ?: 'https://via.placeholder.com/80'; ?>" 
                                 class="img-fluid rounded" alt="<?php echo htmlspecialchars($item['ProductName']); ?>">
                        </div>
                        <div class="col-md-6">
                            <h6 class="mb-0"><?php echo htmlspecialchars($item['ProductName']); ?></h6>
                            <small class="text-muted">Size: <?php echo $item['Size']; ?>, Màu: <?php echo $item['Color']; ?></small><br>
                            <small class="text-muted">Số lượng: <?php echo $item['Quantity']; ?></small>
                        </div>
                        <div class="col-md-4 text-end">
                            <strong class="text-danger"><?php echo number_format($item['Price'], 0, ',', '.'); ?> ₫</strong>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
                
                <hr>
                
                <div class="row">
                    <div class="col-md-8 text-end">
                        <h5>Tổng tiền:</h5>
                    </div>
                    <div class="col-md-4 text-end">
                        <h5 class="text-danger"><?php echo number_format($order['TotalAmount'], 0, ',', '.'); ?> ₫</h5>
                    </div>
                </div>
                
                <div class="alert alert-info mt-3">
                    <i class="fas fa-info-circle"></i> 
                    <strong>Phương thức thanh toán:</strong> Thanh toán khi nhận hàng (COD)
                </div>
            </div>
        </div>

        <div class="text-center">
             </div>
        
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // SỬA 7: Logic xóa giỏ hàng (giữ nguyên)
        const isBuyNowMode = localStorage.getItem('buy_now_mode') === 'true';
        if (isBuyNowMode) {
            const backupCart = localStorage.getItem('cart_backup');
            if (backupCart) localStorage.setItem('cart', backupCart);
            localStorage.removeItem('cart_backup');
            localStorage.removeItem('buy_now_mode');
        } else {
            localStorage.removeItem('cart');
        }
        localStorage.removeItem('appliedCoupon');
        
        // SỬA 8: (MỚI) Thêm JS để cập nhật số lượng giỏ hàng
    </script>
</body>
</html>