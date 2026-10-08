<?php
require_once __DIR__ . '/../includes/init.php';
require_once __DIR__ . '/../classes/Reservation.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$db = new Database();
$conn = $db->getConnection();

$facility_name = $_POST['facility_name'] ?? 'Unknown Facility';
$facility_type = $_POST['facility_type'] ?? 'halls';
$room_number = $_POST['room_number'] ?? null;

// Temporary auto-seed facility if it doesn't exist in the DB to prevent foreign key errors
$stmt = $conn->prepare("SELECT id FROM facilities WHERE name = ? LIMIT 1");
$stmt->execute([$facility_name]);
$fac = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$fac) {
    $stmt = $conn->prepare("INSERT INTO facilities (name, type, rate, capacity) VALUES (?, ?, ?, ?)");
    $stmt->execute([$facility_name, $facility_type, $_POST['rate'] ?? 'N/A', $_POST['capacity'] ?? 'N/A']);
    $facility_id = $conn->lastInsertId();
} else {
    $facility_id = $fac['id'];
}

$room_id = null;
if ($room_number && $room_number !== 'null') {
    $stmt = $conn->prepare("SELECT id FROM rooms WHERE facility_id = ? AND room_number = ? LIMIT 1");
    $stmt->execute([$facility_id, $room_number]);
    $room = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$room) {
        $stmt = $conn->prepare("INSERT INTO rooms (facility_id, room_number) VALUES (?, ?)");
        $stmt->execute([$facility_id, $room_number]);
        $room_id = $conn->lastInsertId();
    } else {
        $room_id = $room['id'];
    }
}

$reservation = new Reservation($conn);
$reservation->reference_no = $_POST['reference_no'];
$reservation->user_id = $_SESSION['user_id'];
$reservation->facility_id = $facility_id;
$reservation->room_id = $room_id;
$reservation->event_title = $_POST['event_title'] ?? '';
$reservation->pax_count = (int)($_POST['pax_count'] ?? 0);

// Convert dates
$start_date = date('Y-m-d', strtotime($_POST['start_date'] ?? date('Y-m-d')));
$end_date = date('Y-m-d', strtotime($_POST['end_date'] ?? $start_date));

$reservation->start_date = $start_date;
$reservation->end_date = $end_date;
$reservation->time_slot = $_POST['time_slot'] ?? '';
$reservation->special_notes = $_POST['special_notes'] ?? '';
$reservation->status = 'Pending';
$reservation->document_path = null;

if (isset($_FILES['document']) && $_FILES['document']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = __DIR__ . '/../uploads/documents/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    
    $fileInfo = pathinfo($_FILES['document']['name']);
    $ext = strtolower($fileInfo['extension']);
    $allowedExts = ['pdf', 'doc', 'docx', 'png', 'jpg', 'jpeg'];
    
    if (in_array($ext, $allowedExts)) {
        $fileName = 'doc_' . time() . '_' . uniqid() . '.' . $ext;
        $targetPath = $uploadDir . $fileName;
        
        if (move_uploaded_file($_FILES['document']['tmp_name'], $targetPath)) {
            $reservation->document_path = 'uploads/documents/' . $fileName;
        }
    }
}

if ($reservation->create()) {
    echo json_encode(['success' => true, 'message' => 'Reservation created successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to insert reservation into database']);
}
