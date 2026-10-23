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

$rawFacId = $_POST['facility_id'] ?? 0;
$facility_id = 0;
if (is_numeric($rawFacId) && (int)$rawFacId > 0) {
    $facility_id = (int)$rawFacId;
} elseif (is_string($rawFacId) && preg_match('/(\d+)/', $rawFacId, $m)) {
    $facility_id = (int)$m[1];
}

$fac = null;
if ($facility_id > 0) {
    $stmt = $conn->prepare("SELECT id, name, status, deleted_at FROM facilities WHERE id = ? LIMIT 1");
    $stmt->execute([$facility_id]);
    $fac = $stmt->fetch(PDO::FETCH_ASSOC);
}
if (!$fac && !empty($facility_name)) {
    $stmt = $conn->prepare("SELECT id, name, status, deleted_at FROM facilities WHERE name = ? LIMIT 1");
    $stmt->execute([$facility_name]);
    $fac = $stmt->fetch(PDO::FETCH_ASSOC);
}

if ($fac && (!empty($fac['deleted_at']) || $fac['status'] === 'inactive')) {
    echo json_encode([
        'success' => false,
        'message' => 'You Can\'t book this facility because it\'s archived or inactive right now.'
    ]);
    exit;
}

if ($fac && $fac['status'] === 'maintenance') {
    echo json_encode([
        'success' => false,
        'message' => 'You can\'t book this facility because it\'s under maintenance right now.'
    ]);
    exit;
}

if (!$fac) {
    $stmt = $conn->prepare("INSERT INTO facilities (name, type, rate, capacity) VALUES (?, ?, ?, ?)");
    $stmt->execute([$facility_name, $facility_type, $_POST['rate'] ?? 'N/A', $_POST['capacity'] ?? 'N/A']);
    $facility_id = (int)$conn->lastInsertId();
} else {
    $facility_id = (int)$fac['id'];
}

$room_id = null;
if ($room_number && $room_number !== 'null') {
    $stmt = $conn->prepare("SELECT id FROM rooms WHERE facility_id = ? AND room_number = ? LIMIT 1");
    $stmt->execute([$facility_id, $room_number]);
    $room = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$room) {
        $stmt = $conn->prepare("INSERT INTO rooms (facility_id, room_number) VALUES (?, ?)");
        $stmt->execute([$facility_id, $room_number]);
        $room_id = (int)$conn->lastInsertId();
    } else {
        $room_id = (int)$room['id'];
    }
}

$event_title = trim($_POST['event_title'] ?? '');
$pax_count = (int) ($_POST['pax_count'] ?? 0);

if (empty($event_title) || strlen($event_title) < 3) {
    echo json_encode([
        'success' => false,
        'message' => 'Please enter the Activity / Event Title (minimum 3 characters).'
    ]);
    exit;
}

if ($pax_count <= 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Please enter the number of attendees / guests (minimum 1).'
    ]);
    exit;
}

// Generate / verify truly unique reference_no
$rawRef = trim($_POST['reference_no'] ?? '');
$prefix = (isset($_POST['facility_type']) && in_array(strtolower($_POST['facility_type']), ['dormitories', 'dorms'])) ? 'ATI-BK' : 'ATI-RES';

$checkStmt = $conn->prepare("SELECT id FROM reservations WHERE reference_no = ? LIMIT 1");
$checkStmt->execute([$rawRef]);
if (empty($rawRef) || $checkStmt->fetch()) {
    do {
        $refNum = $prefix . '-' . date('Y') . '-' . mt_rand(1000, 9999);
        $checkStmt->execute([$refNum]);
    } while ($checkStmt->fetch());
} else {
    $refNum = $rawRef;
}

$reservation = new Reservation($conn);
$reservation->reference_no = $refNum;
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
        'message' => "Sorry, {$targetName} already has an approved reservation for the selected date ({$start_date} to {$end_date}). Please choose another date or a different facility."
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
        'message' => 'You need to upload a Supporting Document (Special Order, Activity Design, or Request Memo) before proceeding.'
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
        'message' => 'Unsupported document format. Only PDF, DOCX, DOC, PNG, or JPG files are accepted.'
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
        'message' => 'Error occurred while saving the uploaded document. Please try again.'
    ]);
    exit;
}

try {
    if ($reservation->create()) {
        if (isset($auditLog)) {
            $auditLog->log($_SESSION['user_id'], 'CREATE_RESERVATION', "Submitted reservation [{$refNum}] for {$event_title} ({$facility_name})");
        }
        echo json_encode([
            'success' => true,
            'message' => 'Reservation created successfully',
            'reference_no' => $refNum
        ]);
    } else {
        $err = $conn->errorInfo();
        echo json_encode([
            'success' => false,
            'message' => 'Failed to insert reservation into database: ' . ($err[2] ?? 'Unknown error')
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
