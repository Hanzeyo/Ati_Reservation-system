<?php
class AuditLog extends BaseModel {
    protected $table_name = "audit_logs";

    public $id;
    public $user_id;
    public $action;
    public $details;
    public $ip_address;
    public $created_at;

    public static function log($user_id = null, $action = null, $details = null, $ip_address = null) {
        $ip = $ip_address ?: ($_SERVER['REMOTE_ADDR'] ?? '127.0.0.1');
        $uid = $user_id ?: ($_SESSION['user_id'] ?? null);

        try {
            global $db;
            $conn = $db;
            if (!$conn && class_exists('Database')) {
                $conn = (new Database())->getConnection();
            }
            if ($conn) {
                $query = "INSERT INTO audit_logs (user_id, action, details, ip_address) VALUES (?, ?, ?, ?)";
                $stmt = $conn->prepare($query);
                return $stmt->execute([$uid, $action, $details, $ip]);
            }
        } catch (Throwable $e) {}
        return false;
    }

    public function getLogs() {
        $query = "SELECT a.*, u.full_name, u.role FROM " . $this->table_name . " a
                  LEFT JOIN users u ON a.user_id = u.id
                  ORDER BY a.created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }
}
?>
