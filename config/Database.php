<?php
// Ensure system timezone is set to Philippine Standard Time (PST / Asia/Manila, UTC+8)
date_default_timezone_set('Asia/Manila');

class Database {
    private $host = "localhost";
    private $db_name = "ati_reservation";
    private $username = "root";
    private $password = "";
    public $conn;

    public function getConnection() {
        $this->conn = null;

        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->exec("set names utf8mb4");
            $this->conn->exec("SET time_zone = '+08:00'");
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $exception) {
            // Auto-initialize if database does not exist (SQLSTATE 1049)
            if ($exception->getCode() == 1049) {
                try {
                    $pdo = new PDO("mysql:host=" . $this->host, $this->username, $this->password);
                    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    $schemaFile = __DIR__ . '/../database/schema.sql';
                    if (file_exists($schemaFile)) {
                        $sql = file_get_contents($schemaFile);
                        $pdo->exec($sql);
                        $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
                        $this->conn->exec("set names utf8mb4");
                        $this->conn->exec("SET time_zone = '+08:00'");
                        $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                        return $this->conn;
                    }
                } catch(PDOException $initException) {
                    echo "Database auto-init error: " . $initException->getMessage();
                    return null;
                }
            }
            echo "Connection error: " . $exception->getMessage();
        }

        return $this->conn;
    }
}
?>
