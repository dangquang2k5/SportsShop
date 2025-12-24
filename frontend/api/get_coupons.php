<?php
header('Content-Type: application/json');
require_once '../config.php';

try {
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
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi khi tải mã giảm giá: ' . $e->getMessage()
    ]);
}
