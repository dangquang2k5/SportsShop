<?php
header('Content-Type: application/json');
require_once '../config.php';

// Check if user is admin
if (!isLoggedIn() || !isAdmin()) {
    http_response_code(403);
    echo json_encode([
        'success' => false,
        'message' => 'Không có quyền truy cập'
    ]);
    exit;
}

try {
    $orderId = (int)$_GET['id'];
    
    // Use backend API to get order details
    $response = makeApiRequest('/orders/' . $orderId);
    
    if ($response['success']) {
        echo json_encode([
            'success' => true,
            'order' => $response['data']['order'] ?? null,
            'items' => $response['data']['items'] ?? []
        ]);
    } else {
        throw new Exception($response['message'] ?? 'Không tìm thấy đơn hàng');
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
