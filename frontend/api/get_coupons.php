<?php
header('Content-Type: application/json');
require_once '../config.php';

try {
<<<<<<< HEAD
    // Use backend API to get vouchers
    $response = makeApiRequest('/vouchers');
    
    if ($response['success']) {
        echo json_encode([
            'success' => true,
            'coupons' => $response['data'] ?? []
        ]);
    } else {
        throw new Exception($response['message'] ?? 'Failed to fetch vouchers');
    }
=======
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
>>>>>>> 3d6d58ed3875cc3c551e3fe1991339ab7637c345
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi khi tải mã giảm giá: ' . $e->getMessage()
    ]);
}
