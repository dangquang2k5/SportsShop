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
<<<<<<< HEAD
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
    
=======
    $db = Database::getInstance()->getConnection();
    $orderId = (int)$_GET['id'];
    
    // Get order information
    $stmt = $db->prepare("
        SELECT o.*, u.FirstName, u.LastName, u.Email, u.Phone
        FROM Orders o
        LEFT JOIN Users u ON o.UserID = u.UserID
        WHERE o.OrderID = ?
    ");
    $stmt->execute([$orderId]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$order) {
        throw new Exception('Không tìm thấy đơn hàng');
    }
    
    // Get order items with product details
    $stmt = $db->prepare("
        SELECT 
            od.OrderDetailID,
            od.Quantity,
            od.Price,
            p.ProductName,
            pd.Size,
            pd.Color,
            p.MainImage
        FROM OrderDetails od
        JOIN ProductDetail pd ON od.ProductDetailID = pd.ProductDetailID
        JOIN Product p ON pd.ProductID = p.ProductID
        WHERE od.OrderID = ?
        ORDER BY od.OrderDetailID
    ");
    $stmt->execute([$orderId]);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'order' => $order,
        'items' => $items
    ]);
    
>>>>>>> 3d6d58ed3875cc3c551e3fe1991339ab7637c345
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
