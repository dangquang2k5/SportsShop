<?php
require_once '../config.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

// Get order ID from URL
$orderID = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;

if ($orderID <= 0) {
    die('Mã đơn hàng không hợp lệ');
}

<<<<<<< HEAD
// Get order details from API
$orderResponse = makeApiRequest('/orders/' . $orderID);

if (!$orderResponse['success']) {
    die('Không tìm thấy đơn hàng');
}

$order = $orderResponse['data']['order'] ?? [];
$orderItems = $orderResponse['data']['items'] ?? [];
=======
$db = Database::getInstance()->getConnection();

// Get order details
$stmt = $db->prepare("
    SELECT o.*, 
           CONCAT(u.FirstName, ' ', u.LastName) as CustomerName,
           u.Email as CustomerEmail,
           u.Phone as CustomerPhone,
           v.VoucherCode,
           v.DiscountValue
    FROM Orders o
    LEFT JOIN Users u ON o.UserID = u.UserID
    LEFT JOIN Voucher v ON o.VoucherID = v.VoucherID
    WHERE o.OrderID = ? AND o.UserID = ?
");
$stmt->execute([$orderID, $_SESSION['user_id']]);
$order = $stmt->fetch();
>>>>>>> 3d6d58ed3875cc3c551e3fe1991339ab7637c345

if (!$order) {
    die('Không tìm thấy đơn hàng hoặc bạn không có quyền xem đơn hàng này');
}

<<<<<<< HEAD
=======
// Get order items
$stmt = $db->prepare("
    SELECT od.*, 
           p.ProductName,
           p.MainImage,
           b.BrandName,
           pd.Size,
           pd.Color
    FROM OrderDetails od
    JOIN ProductDetail pd ON od.ProductDetailID = pd.ProductDetailID
    JOIN Product p ON pd.ProductID = p.ProductID
    LEFT JOIN Brand b ON p.BrandID = b.BrandID
    WHERE od.OrderID = ?
");
$stmt->execute([$orderID]);
$orderItems = $stmt->fetchAll();

>>>>>>> 3d6d58ed3875cc3c551e3fe1991339ab7637c345
// Calculate totals
$subtotal = 0;
foreach ($orderItems as $item) {
    $subtotal += $item['Price'] * $item['Quantity'];
}

$discount = $order['DiscountValue'] ?? 0;
$shipping = $subtotal >= 500000 ? 0 : 30000;
$total = $order['TotalAmount'];

// Use reports directory inside frontend folder (web-accessible)
$reportsDir = __DIR__ . '/../reports';

// Try to create if it doesn't exist
if (!file_exists($reportsDir)) {
    @mkdir($reportsDir, 0755, true);
}

// If still doesn't exist or not writable, show error
if (!file_exists($reportsDir) || !is_writable($reportsDir)) {
    die('Lỗi: Không thể tạo thư mục reports. Vui lòng kiểm tra quyền truy cập.');
}

// Generate HTML invoice that will be converted to PDF
$invoiceDate = date('d/m/Y H:i:s');
$invoiceFilename = 'HoaDon_' . str_pad($orderID, 6, '0', STR_PAD_LEFT) . '_' . date('Y-m-d_His') . '.html';
$invoicePath = $reportsDir . '/' . $invoiceFilename;

// Status labels
$statusLabels = [
    'pending' => 'Chờ xử lý',
    'processing' => 'Đang xử lý',
    'shipped' => 'Đang giao',
    'delivered' => 'Đã giao',
    'canceled' => 'Đã hủy'
];

// Create HTML content
ob_start();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hóa đơn #<?php echo str_pad($orderID, 6, '0', STR_PAD_LEFT); ?> - SportShop</title>
    <style>
        @media print {
            @page {
                size: A4;
                margin: 15mm;
            }
            body {
                margin: 0;
                padding: 0;
            }
            .no-print {
                display: none;
            }
            .page-break {
                page-break-after: always;
            }
        }
        
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 210mm;
            margin: 0 auto;
            padding: 20px;
            background: #fff;
        }
        
        .invoice-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            border-bottom: 3px solid #00D9FF;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        
        .company-info h1 {
            color: #00D9FF;
            font-size: 32px;
            margin: 0 0 5px 0;
        }
        
        .company-info p {
            color: #666;
            margin: 3px 0;
            font-size: 14px;
        }
        
        .invoice-meta {
            text-align: right;
        }
        
        .invoice-number {
            font-size: 24px;
            font-weight: bold;
            color: #1a1a2e;
            margin-bottom: 10px;
        }
        
        .invoice-date {
            color: #666;
            font-size: 14px;
        }
        
        .info-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 30px;
        }
        
        .info-box {
            background: #f8f9fa;
            border-left: 4px solid #00D9FF;
            padding: 15px;
            border-radius: 5px;
        }
        
        .info-box h3 {
            color: #1a1a2e;
            font-size: 16px;
            margin: 0 0 10px 0;
        }
        
        .info-box p {
            margin: 5px 0;
            font-size: 14px;
        }
        
        .info-label {
            color: #666;
            font-weight: 600;
        }
        
        .section-title {
            background: linear-gradient(135deg, #00D9FF 0%, #3B82F6 100%);
            color: white;
            padding: 12px 20px;
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 15px;
            border-radius: 5px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 14px;
        }
        
        table th {
            background: #1a1a2e;
            color: white;
            padding: 12px;
            text-align: left;
            font-weight: bold;
        }
        
        table td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
        }
        
        table tr:nth-child(even) {
            background: #f8f9fa;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-center {
            text-align: center;
        }
        
        .total-section {
            margin-top: 20px;
            float: right;
            width: 350px;
        }
        
        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #ddd;
        }
        
        .total-row.final {
            background: linear-gradient(135deg, #00D9FF 0%, #3B82F6 100%);
            color: white;
            padding: 15px;
            border-radius: 5px;
            font-size: 18px;
            font-weight: bold;
            border: none;
            margin-top: 10px;
        }
        
        .status-badge {
            display: inline-block;
            padding: 6px 15px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: bold;
        }
        
        .status-pending {
            background: #FEF3C7;
            color: #92400E;
        }
        
        .status-processing {
            background: #DBEAFE;
            color: #1E40AF;
        }
        
        .status-shipped {
            background: #E0E7FF;
            color: #3730A3;
        }
        
        .status-delivered {
            background: #D1FAE5;
            color: #065F46;
        }
        
        .status-canceled {
            background: #FEE2E2;
            color: #991B1B;
        }
        
        .footer {
            text-align: center;
            margin-top: 50px;
            padding-top: 20px;
            border-top: 2px solid #ddd;
            color: #666;
            font-size: 13px;
            clear: both;
        }
        
        .print-btn {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #00D9FF;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            font-weight: bold;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            z-index: 1000;
        }
        
        .print-btn:hover {
            background: #00b8d4;
        }
        
        .product-image {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 5px;
        }
        
        .note-section {
            background: #FEF3C7;
            border-left: 4px solid #F59E0B;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
        }
        
        .note-section p {
            margin: 5px 0;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <button class="print-btn no-print" onclick="window.print()">
        🖨️ In hóa đơn
    </button>
    
    <!-- INVOICE HEADER -->
    <div class="invoice-header">
        <div class="company-info">
            <h1>🏃 SPORTSHOP</h1>
            <p><strong>Cửa hàng thể thao trực tuyến</strong></p>
            <p>📍 Địa chỉ: 123 Đường Thể Thao, Quận 1, TP.HCM</p>
            <p>📞 Hotline: 1900-xxxx</p>
            <p>📧 Email: support@sportshop.vn</p>
        </div>
        <div class="invoice-meta">
            <div class="invoice-number">HÓA ĐƠN #<?php echo str_pad($orderID, 6, '0', STR_PAD_LEFT); ?></div>
            <div class="invoice-date">
                <p><strong>Ngày đặt:</strong> <?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></p>
                <p><strong>Ngày in:</strong> <?php echo $invoiceDate; ?></p>
            </div>
            <div style="margin-top: 10px;">
                <span class="status-badge status-<?php echo $order['Status']; ?>">
                    <?php echo $statusLabels[$order['Status']] ?? $order['Status']; ?>
                </span>
            </div>
        </div>
    </div>
    
    <!-- CUSTOMER & SHIPPING INFO -->
    <div class="info-section">
        <div class="info-box">
            <h3>👤 Thông tin khách hàng</h3>
            <p><span class="info-label">Họ và tên:</span> <?php echo htmlspecialchars($order['CustomerName']); ?></p>
            <p><span class="info-label">Email:</span> <?php echo htmlspecialchars($order['CustomerEmail']); ?></p>
            <p><span class="info-label">Số điện thoại:</span> <?php echo htmlspecialchars($order['CustomerPhone']); ?></p>
        </div>
        
        <div class="info-box">
            <h3>🚚 Thông tin giao hàng</h3>
            <p><span class="info-label">Địa chỉ giao hàng:</span></p>
            <p><?php echo htmlspecialchars($order['Address']); ?></p>
            <?php if (!empty($order['Note'])): ?>
            <p><span class="info-label">Ghi chú:</span> <?php echo htmlspecialchars($order['Note']); ?></p>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- ORDER ITEMS -->
    <div class="section-title">📦 CHI TIẾT ĐƠN HÀNG</div>
    <table>
        <thead>
            <tr>
                <th style="width: 60px;">STT</th>
                <th>Sản phẩm</th>
                <th>Phân loại</th>
                <th class="text-right">Đơn giá</th>
                <th class="text-center">Số lượng</th>
                <th class="text-right">Thành tiền</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($orderItems as $index => $item): 
                $itemTotal = $item['Price'] * $item['Quantity'];
            ?>
            <tr>
                <td class="text-center"><?php echo $index + 1; ?></td>
                <td>
                    <strong><?php echo htmlspecialchars($item['ProductName']); ?></strong>
                    <?php if ($item['BrandName']): ?>
                    <br><small style="color: #666;"><?php echo htmlspecialchars($item['BrandName']); ?></small>
                    <?php endif; ?>
                </td>
                <td>
                    <?php 
                    $variants = [];
                    if ($item['Size']) $variants[] = 'Size: ' . $item['Size'];
                    if ($item['Color']) $variants[] = 'Màu: ' . $item['Color'];
                    echo implode(' • ', $variants);
                    ?>
                </td>
                <td class="text-right"><?php echo number_format($item['Price'], 0, ',', '.'); ?>đ</td>
                <td class="text-center"><?php echo $item['Quantity']; ?></td>
                <td class="text-right"><strong><?php echo number_format($itemTotal, 0, ',', '.'); ?>đ</strong></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    
    <!-- TOTALS -->
    <div class="total-section">
        <div class="total-row">
            <span>Tạm tính:</span>
            <span><?php echo number_format($subtotal, 0, ',', '.'); ?>đ</span>
        </div>
        
        <?php if ($discount > 0): ?>
        <div class="total-row">
            <span>Giảm giá (<?php echo $order['VoucherCode']; ?>):</span>
            <span style="color: #EF4444;">-<?php echo number_format($discount, 0, ',', '.'); ?>đ</span>
        </div>
        <?php endif; ?>
        
        <div class="total-row">
            <span>Phí vận chuyển:</span>
            <span><?php echo $shipping === 0 ? 'Miễn phí' : number_format($shipping, 0, ',', '.') . 'đ'; ?></span>
        </div>
        
        <div class="total-row final">
            <span>TỔNG CỘNG:</span>
            <span><?php echo number_format($total, 0, ',', '.'); ?>đ</span>
        </div>
    </div>
    
    <div style="clear: both;"></div>
    
    <!-- NOTE SECTION -->
    <div class="note-section">
        <p><strong>📌 Lưu ý quan trọng:</strong></p>
        <p>• Vui lòng kiểm tra kỹ sản phẩm khi nhận hàng</p>
        <p>• Liên hệ hotline 1900-xxxx nếu có bất kỳ thắc mắc nào</p>
        <p>• Đổi trả trong vòng 30 ngày nếu sản phẩm còn nguyên tem, mác</p>
        <p>• Thời gian giao hàng dự kiến: 2-3 ngày làm việc</p>
    </div>
    
    <!-- FOOTER -->
    <div class="footer">
        <p><strong>Cảm ơn bạn đã mua hàng tại SportShop! 🙏</strong></p>
        <p>Đây là hóa đơn điện tử được tạo tự động từ hệ thống</p>
        <p>© <?php echo date('Y'); ?> SportShop. All rights reserved.</p>
    </div>
</body>
</html>
<?php
$htmlContent = ob_get_clean();

// Save HTML invoice
file_put_contents($invoicePath, $htmlContent);

// Redirect to the HTML invoice which can be printed to PDF
header('Location: ../reports/' . $invoiceFilename);
exit();
?>
