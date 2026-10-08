<?php
class AuditLog extends BaseModel {
    protected $table_name = "audit_logs";

    public $id;
    public $user_id;
    public $action;
    public $details;
    public $ip_address;
    public $created_at;

    public function log() {
        $query = "INSERT INTO " . $this->table_name . " (user_id, action, details, ip_address) VALUES (:user_id, :action, :details, :ip_address)";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->bindParam(':action', $this->action);
        $stmt->bindParam(':details', $this->details);
        $stmt->bindParam(':ip_address', $this->ip_address);

        if($stmt->execute()) {
            return true;
        }
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
