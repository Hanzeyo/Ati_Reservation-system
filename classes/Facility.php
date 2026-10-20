<?php
class Facility extends BaseModel {
    protected $table_name = "facilities";
    private $rooms_table = "rooms";

    public $id;
    public $name;
    public $type;
    public $rate;
    public $capacity;
    public $beds;
    public $location;
    public $description;
    public $features;
    public $image;
    public $notes;
    public $status;
    public $maintenance_reason;
    public $est_resume_date;
    public $deleted_at;

    public function getAll($type = null, $includeDeleted = false) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE 1=1";
        if (!$includeDeleted) {
            $query .= " AND deleted_at IS NULL";
        }
        if ($type) {
            $query .= " AND type = :type";
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
            $this->beds = $row['beds'] ?? 0;
            $this->location = $row['location'];
            $this->description = $row['description'];
            $this->features = $row['features'] ?? '';
            $this->image = $row['image'] ?? 'assets/images/function_hall.jpg';
            $this->notes = $row['notes'] ?? '';
            $this->status = $row['status'];
            $this->maintenance_reason = $row['maintenance_reason'] ?? '';
            $this->est_resume_date = $row['est_resume_date'] ?? '';
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
