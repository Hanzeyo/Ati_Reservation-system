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

$event_title = trim($_POST['event_title'] ?? '');
$pax_count = (int) ($_POST['pax_count'] ?? 0);

if (empty($event_title) || strlen($event_title) < 3) {
    echo json_encode([
        'success' => false,
        'message' => 'Pakilagay ang Activity / Event Title (minimum 3 characters).'
    ]);
    exit;
}

if ($pax_count <= 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Pakitukoy ang wastong bilang ng attendees / guests (hindi bababa sa 1).'
    ]);
    exit;
}

$reservation = new Reservation($conn);
$reservation->reference_no = $_POST['reference_no'];
$reservation->user_id = $_SESSION['user_id'];
$reservation->facility_id = $facility_id;
$reservation->room_id = $room_id;
$reservation->event_title = $event_title;
$reservation->pax_count = $pax_count;

// Convert dates
$start_date = date('Y-m-d', strtotime($_POST['start_date'] ?? date('Y-m-d')));
$end_date = date('Y-m-d', strtotime($_POST['end_date'] ?? $start_date));

// Double Booking / Conflict Prevention Check
if (!$reservation->checkAvailability($facility_id, $start_date, $end_date, $room_id)) {
    $targetName = $room_number ? "{$facility_name} (Room {$room_number})" : $facility_name;
    echo json_encode([
        'success' => false,
        'conflict' => true,
        'message' => "Paumanhin, ang {$targetName} ay may naaprubahan nang reservation para sa napiling petsa ({$start_date} hanggang {$end_date}). Mangyaring pumili ng ibang petsa o ibang pasilidad."
    ]);
    exit;
}

$reservation->start_date = $start_date;
$reservation->end_date = $end_date;
$reservation->time_slot = $_POST['time_slot'] ?? '';
$reservation->special_notes = $_POST['special_notes'] ?? '';
$reservation->status = 'Pending';
// Supporting Document Check (Required)
if (!isset($_FILES['document']) || $_FILES['document']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode([
        'success' => false,
        'message' => 'Kinakailangan mag-upload ng Supporting Document (Special Order, Activity Design, o Request Memo) bago magpatuloy.'
    ]);
    exit;
}

$uploadDir = __DIR__ . '/../uploads/documents/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

$fileInfo = pathinfo($_FILES['document']['name']);
$ext = strtolower($fileInfo['extension']);
$allowedExts = ['pdf', 'doc', 'docx', 'png', 'jpg', 'jpeg'];

if (!in_array($ext, $allowedExts)) {
    echo json_encode([
        'success' => false,
        'message' => 'Hindi suportadong format ng dokumento. PDF, DOCX, DOC, o PNG/JPG lamang ang tinatanggap.'
    ]);
    exit;
}

$fileName = 'doc_' . time() . '_' . uniqid() . '.' . $ext;
$targetPath = $uploadDir . $fileName;

if (move_uploaded_file($_FILES['document']['tmp_name'], $targetPath)) {
    $reservation->document_path = 'uploads/documents/' . $fileName;
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Nagkaroon ng problema sa pag-save ng in-upload na dokumento. Pakisubukan muli.'
    ]);
    exit;
}

if ($reservation->create()) {
    echo json_encode(['success' => true, 'message' => 'Reservation created successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to insert reservation into database']);
}
