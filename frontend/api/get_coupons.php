<?php
header('Content-Type: application/json');
require_once '../config.php';

try {
    $db = Database::getInstance()->getConnection();
    
    // Get all active coupons (not expired and quantity > 0)
    $stmt = $db->prepare("
        SELECT VoucherID, VoucherCode, DiscountValue, MinOrderValue, Quantity, StartDate, EndDate
        FROM Voucher
        WHERE EndDate >= CURDATE() 
        AND Quantity > 0
        AND StartDate <= CURDATE()
        ORDER BY DiscountValue DESC
    ");
    
    $stmt->execute();
    $coupons = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'coupons' => $coupons
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi khi tải mã giảm giá: ' . $e->getMessage()
    ]);
}
