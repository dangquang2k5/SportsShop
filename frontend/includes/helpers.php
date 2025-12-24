<?php
/**
 * HELPERS.PHP - Chứa các hàm chung cho tất cả trang PHP
 * Được include ở tất cả các trang để tránh lặp lại code
 */

// ============================================
// DATABASE & INITIALIZATION
// ============================================

/**
 * Lấy kết nối database - Legacy function for compatibility
 * Now uses API calls instead of direct database
 */
function getDatabase() {
    // This function is kept for backward compatibility but returns null
    // All database operations should use API calls
    return null;
}

// ============================================
// PRODUCT FUNCTIONS
// ============================================

/**
 * Lấy danh sách sản phẩm nổi bật
 * @param int $limit Số lượng sản phẩm
 * @return array Danh sách sản phẩm
 */
function getFeaturedProducts($limit = 8) {
    $db = getDatabase();
    $stmt = $db->prepare("
        SELECT p.*, b.BrandName 
        FROM Product p
        LEFT JOIN Brand b ON p.BrandID = b.BrandID
        WHERE p.Status = 'active'
        ORDER BY p.RatingAvg DESC
        LIMIT ?
    ");
    $stmt->bindParam(1, $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}

/**
 * Lấy tất cả danh mục
 * @return array Danh sách danh mục
 */
function getAllCategories() {
    $db = getDatabase();
    return $db->query("SELECT * FROM Categories ORDER BY CategoryName")->fetchAll();
}

/**
 * Lấy tất cả thương hiệu
 * @return array Danh sách thương hiệu
 */
function getAllBrands() {
    $db = getDatabase();
    return $db->query("SELECT * FROM Brand ORDER BY BrandName")->fetchAll();
}

/**
 * Lấy chi tiết sản phẩm
 * @param int $productId ID sản phẩm
 * @return array|false Chi tiết sản phẩm hoặc false nếu không tìm thấy
 */
function getProductById($productId) {
    $db = getDatabase();
    $stmt = $db->prepare("
        SELECT p.*, b.BrandName, c.CategoryName 
        FROM Product p
        LEFT JOIN Brand b ON p.BrandID = b.BrandID
        LEFT JOIN Categories c ON p.CategoryID = c.CategoryID
        WHERE p.ProductID = ?
    ");
    $stmt->execute([$productId]);
    return $stmt->fetch();
}

/**
 * Lấy biến thể sản phẩm (size, màu, số lượng)
 * @param int $productId ID sản phẩm
 * @return array Danh sách biến thể
 */
function getProductVariants($productId) {
    $db = getDatabase();
    $stmt = $db->prepare("
        SELECT * FROM ProductDetail 
        WHERE ProductID = ? AND Quantity > 0
        ORDER BY Size, Color
    ");
    $stmt->execute([$productId]);
    return $stmt->fetchAll();
}

/**
 * Tìm kiếm sản phẩm
 * @param string $keyword Từ khóa tìm kiếm
 * @param int|null $categoryId ID danh mục (tùy chọn)
 * @param int|null $brandId ID thương hiệu (tùy chọn)
 * @param string $sortBy Sắp xếp theo (newest, price_asc, price_desc, rating)
 * @return array Danh sách sản phẩm
 */
function searchProducts($keyword = '', $categoryId = null, $brandId = null, $sortBy = 'newest') {
    $db = getDatabase();
    
    $sql = "SELECT p.*, b.BrandName, c.CategoryName 
            FROM Product p
            LEFT JOIN Brand b ON p.BrandID = b.BrandID
            LEFT JOIN Categories c ON p.CategoryID = c.CategoryID
            WHERE p.Status = 'active'";
    
    $params = [];
    
    if ($categoryId) {
        $sql .= " AND p.CategoryID = ?";
        $params[] = $categoryId;
    }
    
    if ($brandId) {
        $sql .= " AND p.BrandID = ?";
        $params[] = $brandId;
    }
    
    if ($keyword) {
        $sql .= " AND (p.ProductName LIKE ? OR p.Description LIKE ?)";
        $params[] = "%$keyword%";
        $params[] = "%$keyword%";
    }
    
    // Sorting
    switch ($sortBy) {
        case 'price_asc':
            $sql .= " ORDER BY p.Price ASC";
            break;
        case 'price_desc':
            $sql .= " ORDER BY p.Price DESC";
            break;
        case 'rating':
            $sql .= " ORDER BY p.RatingAvg DESC";
            break;
        default:
            $sql .= " ORDER BY p.created_at DESC";
    }
    
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

// ============================================
// REVIEW FUNCTIONS
// ============================================

/**
 * Lấy đánh giá của sản phẩm
 * @param int $productId ID sản phẩm
 * @return array Danh sách đánh giá
 */
function getProductReviews($productId) {
    $db = getDatabase();
    $stmt = $db->prepare("
        SELECT r.*, u.FirstName, u.LastName, u.FullName
        FROM Reviews r
        LEFT JOIN Users u ON r.UserID = u.UserID
        WHERE r.ProductID = ? AND r.Status = 'approved'
        ORDER BY r.created_at DESC
    ");
    $stmt->execute([$productId]);
    return $stmt->fetchAll();
}

/**
 * Kiểm tra người dùng đã đánh giá sản phẩm chưa
 * @param int $userId ID người dùng
 * @param int $productId ID sản phẩm
 * @return bool True nếu đã đánh giá, false nếu chưa
 */
function hasUserReviewedProduct($userId, $productId) {
    $db = getDatabase();
    $stmt = $db->prepare("SELECT ReviewID FROM Reviews WHERE UserID = ? AND ProductID = ?");
    $stmt->execute([$userId, $productId]);
    return (bool)$stmt->fetch();
}

/**
 * Thêm đánh giá sản phẩm
 * @param int $userId ID người dùng
 * @param int $productId ID sản phẩm
 * @param int $rating Số sao (1-5)
 * @param string $comment Bình luận
 * @return bool True nếu thành công, false nếu thất bại
 */
function addProductReview($userId, $productId, $rating, $comment = '') {
    if ($rating < 1 || $rating > 5) {
        return false;
    }
    
    if (hasUserReviewedProduct($userId, $productId)) {
        return false;
    }
    
    $db = getDatabase();
    $commentValue = !empty($comment) ? $comment : NULL;
    
    $stmt = $db->prepare("
        INSERT INTO Reviews (UserID, ProductID, Rating, Content, Status)
        VALUES (?, ?, ?, ?, 'approved')
    ");
    
    return $stmt->execute([$userId, $productId, $rating, $commentValue]);
}

// ============================================
// USER FUNCTIONS
// ============================================

/**
 * Lấy thông tin người dùng
 * @param int $userId ID người dùng
 * @return array|false Thông tin người dùng hoặc false
 */
function getUserById($userId) {
    $db = getDatabase();
    $stmt = $db->prepare("SELECT * FROM Users WHERE UserID = ?");
    $stmt->execute([$userId]);
    return $stmt->fetch();
}

/**
 * Cập nhật thông tin người dùng
 * @param int $userId ID người dùng
 * @param array $data Dữ liệu cần cập nhật (firstName, lastName, phone, address)
 * @return bool True nếu thành công, false nếu thất bại
 */
function updateUserInfo($userId, $data) {
    $db = getDatabase();
    
    $firstName = $data['firstName'] ?? null;
    $lastName = $data['lastName'] ?? null;
    $phone = $data['phone'] ?? null;
    $address = $data['address'] ?? null;
    
    if (!$firstName || !$lastName || !$phone) {
        return false;
    }
    
    $stmt = $db->prepare("
        UPDATE Users 
        SET FirstName = ?, LastName = ?, Phone = ?, Address = ? 
        WHERE UserID = ?
    ");
    
    return $stmt->execute([$firstName, $lastName, $phone, $address, $userId]);
}

/**
 * Đổi mật khẩu người dùng
 * @param int $userId ID người dùng
 * @param string $currentPassword Mật khẩu hiện tại
 * @param string $newPassword Mật khẩu mới
 * @return string|true True nếu thành công, string lỗi nếu thất bại
 */
function changeUserPassword($userId, $currentPassword, $newPassword) {
    if (strlen($newPassword) < 6) {
        return 'Mật khẩu mới phải có ít nhất 6 ký tự';
    }
    
    $db = getDatabase();
    $stmt = $db->prepare("SELECT Password FROM Users WHERE UserID = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch();
    
    if (!$user || !password_verify($currentPassword, $user['Password'])) {
        return 'Mật khẩu hiện tại không đúng';
    }
    
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
    $stmt = $db->prepare("UPDATE Users SET Password = ? WHERE UserID = ?");
    
    if ($stmt->execute([$hashedPassword, $userId])) {
        return true;
    }
    
    return 'Có lỗi xảy ra khi đổi mật khẩu';
}

// ============================================
// ORDER FUNCTIONS
// ============================================

/**
 * Lấy đơn hàng của người dùng
 * @param int $userId ID người dùng
 * @param int $limit Số lượng đơn hàng
 * @return array Danh sách đơn hàng
 */
function getUserOrders($userId, $limit = 10) {
    $db = getDatabase();
    $stmt = $db->prepare("
        SELECT * FROM Orders 
        WHERE UserID = ? 
        ORDER BY created_at DESC 
        LIMIT ?
    ");
    $stmt->bindParam(1, $userId, PDO::PARAM_INT);
    $stmt->bindParam(2, $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}

/**
 * Lấy chi tiết đơn hàng
 * @param int $orderId ID đơn hàng
 * @return array|false Chi tiết đơn hàng hoặc false
 */
function getOrderDetails($orderId) {
    $db = getDatabase();
    $stmt = $db->prepare("
        SELECT od.*, p.ProductName, p.MainImage
        FROM OrderDetails od
        LEFT JOIN Product p ON od.ProductID = p.ProductID
        WHERE od.OrderID = ?
    ");
    $stmt->execute([$orderId]);
    return $stmt->fetchAll();
}

// ============================================
// VALIDATION FUNCTIONS
// ============================================

/**
 * Kiểm tra email đã tồn tại chưa
 * @param string $email Email cần kiểm tra
 * @return bool True nếu tồn tại, false nếu không
 */
function emailExists($email) {
    $db = getDatabase();
    $stmt = $db->prepare("SELECT UserID FROM Users WHERE Email = ?");
    $stmt->execute([$email]);
    return (bool)$stmt->fetch();
}

/**
 * Kiểm tra số điện thoại đã tồn tại chưa
 * @param string $phone Số điện thoại cần kiểm tra
 * @return bool True nếu tồn tại, false nếu không
 */
function phoneExists($phone) {
    $db = getDatabase();
    $stmt = $db->prepare("SELECT UserID FROM Users WHERE Phone = ?");
    $stmt->execute([$phone]);
    return (bool)$stmt->fetch();
}

// ============================================
// UTILITY FUNCTIONS
// ============================================

/**
 * Định dạng giá tiền
 * @param float $price Giá tiền
 * @return string Giá tiền đã định dạng
 */
function formatPrice($price) {
    return number_format($price, 0, ',', '.') . '₫';
}

/**
 * Lấy tên đầy đủ người dùng
 * @param array $user Thông tin người dùng
 * @return string Tên đầy đủ
 */
function getFullName($user) {
    return ($user['FirstName'] ?? '') . ' ' . ($user['LastName'] ?? '');
}

/**
 * Kiểm tra URL có hợp lệ không
 * @param string $url URL cần kiểm tra
 * @return bool True nếu hợp lệ, false nếu không
 */
function isValidUrl($url) {
    return filter_var($url, FILTER_VALIDATE_URL) !== false;
}

/**
 * Cắt ngắn text
 * @param string $text Text cần cắt
 * @param int $length Độ dài tối đa
 * @param string $suffix Hậu tố (mặc định ...)
 * @return string Text đã cắt
 */
function truncateText($text, $length = 100, $suffix = '...') {
    if (strlen($text) <= $length) {
        return $text;
    }
    return substr($text, 0, $length) . $suffix;
}

/**
 * Chuyển đổi trạng thái thành tiếng Việt
 * @param string $status Trạng thái
 * @return string Trạng thái tiếng Việt
 */
function getStatusLabel($status) {
    $statuses = [
        'active' => 'Hoạt động',
        'inactive' => 'Không hoạt động',
        'pending' => 'Chờ xử lý',
        'approved' => 'Đã duyệt',
        'hidden' => 'Ẩn',
        'completed' => 'Hoàn thành',
        'cancelled' => 'Đã hủy',
    ];
    
    return $statuses[$status] ?? ucfirst($status);
}
?>
