<?php
class User extends BaseModel {
    protected $table_name = "users";

    public $id;
    public $full_name;
    public $email;
    public $password;
    public $contact_number;
    public $category;
    public $role;
    public $status;

    public function emailExists() {
        $query = "SELECT id, full_name, password_hash, role, status FROM " . $this->table_name . " WHERE email = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->email);
        $stmt->execute();
        $num = $stmt->rowCount();

        if($num > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->id = $row['id'];
            $this->full_name = $row['full_name'];
            $this->password = $row['password_hash'];
            $this->role = $row['role'];
            $this->status = $row['status'];
            return true;
        }
        return false;
    }

    public function register() {
        $query = "INSERT INTO " . $this->table_name . " (full_name, email, password_hash, contact_number, category, role, status) VALUES (:full_name, :email, :password_hash, :contact_number, :category, :role, :status)";
        
        $stmt = $this->conn->prepare($query);

        $this->full_name = htmlspecialchars(strip_tags($this->full_name));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->contact_number = htmlspecialchars(strip_tags($this->contact_number));
        $this->category = htmlspecialchars(strip_tags($this->category));
        
        $this->role = empty($this->role) ? 'user' : $this->role;
        $this->status = 'active';

        $password_hash = password_hash($this->password, PASSWORD_BCRYPT);

        $stmt->bindParam(':full_name', $this->full_name);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':password_hash', $password_hash);
        $stmt->bindParam(':contact_number', $this->contact_number);
        $stmt->bindParam(':category', $this->category);
        $stmt->bindParam(':role', $this->role);
        $stmt->bindParam(':status', $this->status);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function getAllUsers() {
        $query = "SELECT id, full_name, email, category, role, status, created_at FROM " . $this->table_name . " ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }
}
?>
