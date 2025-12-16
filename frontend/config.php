<?php
// Database Configuration
define('DB_HOST', getenv('DB_HOST') ?: '172.18.0.2'); 
define('DB_NAME', getenv('DB_NAME') ?: 'SportsStoreDB'); // Đã cập nhật CSDL
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASSWORD') ?: '030705');
define('DB_CHARSET', 'utf8mb4');

// Site Configuration
define('SITE_NAME', 'SportShop - Cửa hàng thể thao trực tuyến');
define('SITE_URL', 'http://localhost');

// Database Connection Class
class Database {
    private static $instance = null;
    private $conn;
    
    private function __construct() {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
            ];
            $this->conn = new PDO($dsn, DB_USER, DB_PASS, $options);
            // Đảm bảo tất cả charset đều là utf8mb4
            $this->conn->exec("SET CHARACTER SET utf8mb4");
            $this->conn->exec("SET character_set_client = utf8mb4");
            $this->conn->exec("SET character_set_results = utf8mb4");
            $this->conn->exec("SET character_set_connection = utf8mb4");
        } catch (PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }
    
    public function getConnection() {
        return $this->conn;
    }
}

// Setup MySQL Session Handler
require_once __DIR__ . '/SessionHandler.php';
$db = Database::getInstance()->getConnection();
$sessionHandler = new MySQLSessionHandler($db);
session_set_save_handler($sessionHandler, true);

// Start session
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.gc_maxlifetime', 3600); // 1 hour
    session_start();
}

// Helper Functions
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function redirect($url) {
    // Lưu session trước khi redirect (quan trọng khi dùng custom session handler)
    session_write_close();
    header("Location: $url");
    exit();
}

function formatPrice($price) {
    return number_format($price, 0, ',', '.');
}

function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// ============================================
// (MỚI) API ENDPOINT CHO BIẾN THỂ (CHO cart.php VÀ checkout.php)
// ============================================
if (isset($_GET['action']) && $_GET['action'] === 'get_variant_details') {
    header('Content-Type: application/json; charset=utf-8');
    if (!isset($_GET['ids'])) {
        echo json_encode([]);
        exit;
    }
    
    $ids = explode(',', $_GET['ids']);
    $ids = array_map('intval', $ids);
    
    if (empty($ids)) {
        echo json_encode([]);
        exit;
    }
    
    $db = Database::getInstance()->getConnection();
    $placeholders = str_repeat('?,', count($ids) - 1) . '?';
    
    // Truy vấn JOIN 3 bảng để lấy đủ thông tin
    $stmt = $db->prepare("
        SELECT 
            pd.ProductDetailID, pd.ProductID, pd.Size, pd.Color, pd.Quantity, pd.Image,
            p.ProductName, p.Price, p.MainImage,
            b.BrandName
        FROM ProductDetail pd
        JOIN Product p ON pd.ProductID = p.ProductID
        LEFT JOIN Brand b ON p.BrandID = b.BrandID
        WHERE pd.ProductDetailID IN ($placeholders)
    ");
    
    $stmt->execute($ids);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC); 
    
    // Đảm bảo encoding UTF-8 đúng
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($products, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

// SỬA: ENDPOINT 'get_coupons' (để dùng bảng Voucher)
if (isset($_GET['action']) && $_GET['action'] === 'get_coupons') {
    header('Content-Type: application/json; charset=utf-8');
    
    try {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("
            SELECT * FROM Voucher 
            WHERE StartDate <= CURDATE() 
            AND EndDate >= CURDATE()
            AND Quantity > 0
            ORDER BY DiscountValue DESC
        ");
        
        $stmt->execute();
        $coupons = $stmt->fetchAll();
        
        echo json_encode($coupons, JSON_UNESCAPED_UNICODE);
    } catch (Exception $e) {
        echo json_encode([], JSON_UNESCAPED_UNICODE);
    }
    exit;
}

// SỬA: ENDPOINT 'validate_coupon' (để dùng bảng Voucher)
if (isset($_GET['action']) && $_GET['action'] === 'validate_coupon') {
    header('Content-Type: application/json; charset=utf-8');
    
    $code = $_GET['code'] ?? '';
    
    if (empty($code)) {
        echo json_encode(['valid' => false, 'message' => 'Vui lòng nhập mã'], JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    try {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("
            SELECT * FROM Voucher 
            WHERE VoucherCode = ? 
            AND StartDate <= CURDATE() 
            AND EndDate >= CURDATE()
        ");
        
        $stmt->execute([$code]);
        $coupon = $stmt->fetch();
        
        if ($coupon) {
            if ($coupon['Quantity'] <= 0) {
                 echo json_encode(['valid' => false, 'message' => 'Mã đã hết lượt sử dụng'], JSON_UNESCAPED_UNICODE);
                 exit;
            }

            echo json_encode([
                'valid' => true,
                'code' => $coupon['VoucherCode'],
                'discountValue' => floatval($coupon['DiscountValue']),
                'minOrder' => floatval($coupon['MinOrderValue'])
            ], JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode(['valid' => false, 'message' => 'Mã không hợp lệ hoặc hết hạn'], JSON_UNESCAPED_UNICODE);
        }
    } catch (Exception $e) {
        echo json_encode(['valid' => false, 'message' => 'Lỗi: ' . $e->getMessage()], JSON_UNESCAPED_UNICODE);
    }
    exit;
}

// XÓA endpoint 'get_products' cũ

// ============================================
// (MỚI) API ENDPOINTS CHO CHAT
// ============================================
if (isset($_REQUEST['action']) && $_REQUEST['action'] === 'chat_api') {
    header('Content-Type: application/json; charset=utf-8');
    $db = Database::getInstance()->getConnection();
    $type = $_GET['type'] ?? '';

    // Lấy dữ liệu POST từ body
    $postData = json_decode(file_get_contents('php://input'), true);

    switch ($type) {
        // ADMIN: Lấy danh sách user đã chat
        case 'get_users_chatted':
            $stmt = $db->prepare("
                SELECT u.UserID as userId, u.FirstName as firstName, u.LastName as lastName, u.Email as email
                FROM Messages m
                JOIN Users u ON m.UserID = u.UserID
                GROUP BY u.UserID
                ORDER BY MAX(m.SentAt) DESC
            ");
            $stmt->execute();
            echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
            break;

        // Lấy lịch sử tin nhắn
        case 'get_history':
            $userId = (int)($_GET['user_id'] ?? 0);
            if ($userId > 0) {
                $stmt = $db->prepare("
                    SELECT Content as content, AdminFlag as adminFlag, SentAt 
                    FROM Messages 
                    WHERE UserID = ? 
                    ORDER BY SentAt ASC
                ");
                $stmt->execute([$userId]);
                echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
            } else {
                echo json_encode([]);
            }
            break;

        // Gửi tin nhắn (cả user và admin)
        case 'send_message':
            $userId = (int)($postData['userId'] ?? 0);
            $content = trim($postData['content'] ?? '');
            $isAdminMessage = (bool)($postData['isAdmin'] ?? false);

            if ($userId > 0 && !empty($content)) {
                $stmt = $db->prepare(
                    "INSERT INTO Messages (UserID, Content, AdminFlag) VALUES (?, ?, ?)"
                );
                $stmt->execute([$userId, $content, $isAdminMessage ? 1 : 0]);
                echo json_encode(['success' => true]);
            } else {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Dữ liệu không hợp lệ']);
            }
            break;
        
        default:
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Hành động không hợp lệ']);
            break;
    }
    exit; // Dừng script sau khi xử lý API
}
?>