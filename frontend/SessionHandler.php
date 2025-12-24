<?php
/**
 * Custom Session Handler - Lưu session vào MySQL
 */
class MySQLSessionHandler implements SessionHandlerInterface {
    private $db;
    
    public function __construct($db) {
        $this->db = $db;
    }
    
    public function open($save_path, $session_name): bool {
        return true;
    }
    
    public function close(): bool {
        return true;
    }
    
    public function read($session_id): string|false {
        try {
            $stmt = $this->db->prepare("SELECT session_data FROM Sessions WHERE session_id = ?");
            $stmt->execute([$session_id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result) {
                // Update last activity
                $updateStmt = $this->db->prepare("UPDATE Sessions SET last_activity = NOW() WHERE session_id = ?");
                $updateStmt->execute([$session_id]);
                
                return $result['session_data'];
            }
            
            return '';
        } catch (PDOException $e) {
            error_log("Session read error: " . $e->getMessage());
            return '';
        }
    }
    
    public function write($session_id, $session_data): bool {
        try {
            // Get user_id from session data if exists
            $userId = null;
            if (!empty($session_data)) {
                $data = @unserialize($session_data);
                if (is_array($data) && isset($data['user_id'])) {
                    $userId = $data['user_id'];
                }
            }
            
            $stmt = $this->db->prepare("
                INSERT INTO Sessions (session_id, session_data, user_id, last_activity) 
                VALUES (?, ?, ?, NOW())
                ON DUPLICATE KEY UPDATE 
                    session_data = VALUES(session_data),
                    user_id = VALUES(user_id),
                    last_activity = NOW()
            ");
            
            return $stmt->execute([$session_id, $session_data, $userId]);
        } catch (PDOException $e) {
            error_log("Session write error: " . $e->getMessage());
            return false;
        }
    }
    
    public function destroy($session_id): bool {
        try {
            $stmt = $this->db->prepare("DELETE FROM Sessions WHERE session_id = ?");
            return $stmt->execute([$session_id]);
        } catch (PDOException $e) {
            error_log("Session destroy error: " . $e->getMessage());
            return false;
        }
    }
    
    public function gc($maxlifetime): int|false {
        try {
            $stmt = $this->db->prepare("
                DELETE FROM Sessions 
                WHERE last_activity < DATE_SUB(NOW(), INTERVAL ? SECOND)
            ");
            $stmt->execute([$maxlifetime]);
            return $stmt->rowCount();
        } catch (PDOException $e) {
            error_log("Session GC error: " . $e->getMessage());
            return 0;
        }
    }
}
?>
