<?php
class Facility extends BaseModel {
    protected $table_name = "facilities";
    private $rooms_table = "rooms";

    public $id;
    public $name;
    public $type;
    public $rate;
    public $capacity;
    public $location;
    public $description;
    public $status;

    public function getAll($type = null) {
        $query = "SELECT * FROM " . $this->table_name;
        if ($type) {
            $query .= " WHERE type = :type";
        }
        $query .= " ORDER BY name ASC";
        
        $stmt = $this->conn->prepare($query);
        if ($type) {
            $stmt->bindParam(':type', $type);
        }
        $stmt->execute();
        return $stmt;
    }

    public function getDetails() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if($row) {
            $this->name = $row['name'];
            $this->type = $row['type'];
            $this->rate = $row['rate'];
            $this->capacity = $row['capacity'];
            $this->location = $row['location'];
            $this->description = $row['description'];
            $this->status = $row['status'];
            return true;
        }
        return false;
    }

    public function getRooms($facility_id) {
        $query = "SELECT * FROM " . $this->rooms_table . " WHERE facility_id = ? ORDER BY room_number ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $facility_id);
        $stmt->execute();
        return $stmt;
    }
}
?>
