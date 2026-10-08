<?php
class Reservation extends BaseModel {
    protected $table_name = "reservations";

    public $id;
    public $reference_no;
    public $user_id;
    public $facility_id;
    public $room_id;
    public $event_title;
    public $pax_count;
    public $start_date;
    public $end_date;
    public $time_slot;
    public $special_notes;
    public $status;
    public $admin_remarks;
    public $document_path;

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                (reference_no, user_id, facility_id, room_id, event_title, pax_count, start_date, end_date, time_slot, special_notes, status, document_path) 
                VALUES 
                (:reference_no, :user_id, :facility_id, :room_id, :event_title, :pax_count, :start_date, :end_date, :time_slot, :special_notes, :status, :document_path)";
        
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':reference_no', $this->reference_no);
        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->bindParam(':facility_id', $this->facility_id);
        $stmt->bindParam(':room_id', $this->room_id);
        $stmt->bindParam(':event_title', $this->event_title);
        $stmt->bindParam(':pax_count', $this->pax_count);
        $stmt->bindParam(':start_date', $this->start_date);
        $stmt->bindParam(':end_date', $this->end_date);
        $stmt->bindParam(':time_slot', $this->time_slot);
        $stmt->bindParam(':special_notes', $this->special_notes);
        $stmt->bindParam(':status', $this->status);
        $stmt->bindParam(':document_path', $this->document_path);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function getByUser($user_id) {
        $query = "SELECT r.*, f.name as facility_name FROM " . $this->table_name . " r 
                  LEFT JOIN facilities f ON r.facility_id = f.id 
                  WHERE r.user_id = :user_id ORDER BY r.created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        return $stmt;
    }

    public function getPending() {
        $query = "SELECT r.*, f.name as facility_name, u.full_name as user_name FROM " . $this->table_name . " r 
                  LEFT JOIN facilities f ON r.facility_id = f.id 
                  LEFT JOIN users u ON r.user_id = u.id 
                  WHERE r.status = 'Pending' ORDER BY r.created_at ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function updateStatus() {
        $query = "UPDATE " . $this->table_name . " SET status = :status, admin_remarks = :admin_remarks WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':status', $this->status);
        $stmt->bindParam(':admin_remarks', $this->admin_remarks);
        $stmt->bindParam(':id', $this->id);
        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function checkAvailability($facility_id, $start_date, $end_date) {
        $query = "SELECT id FROM " . $this->table_name . " 
                  WHERE facility_id = ? AND status IN ('Approved', 'Completed') 
                  AND (start_date <= ? AND end_date >= ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $facility_id);
        $stmt->bindParam(2, $end_date);
        $stmt->bindParam(3, $start_date);
        $stmt->execute();
        return $stmt->rowCount() == 0;
    }
}
?>
