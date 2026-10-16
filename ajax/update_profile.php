<?php
require_once __DIR__ . '/../includes/init.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

$userId = $_SESSION['user_id'] ?? null;
if (!$userId) {
    echo json_encode(['success' => false, 'message' => 'You must be logged in to update your profile.']);
    exit;
}

$db = (new Database())->getConnection();

try {
    $fullName = trim($_POST['fullName'] ?? $_POST['full_name'] ?? '');
    $position = trim($_POST['position'] ?? '');
    $division = trim($_POST['division'] ?? '');
    $dateHired = trim($_POST['dateHired'] ?? $_POST['date_hired'] ?? '');
    $empStatus = trim($_POST['empStatus'] ?? $_POST['employment_status'] ?? '');
    $supervisor = trim($_POST['supervisor'] ?? $_POST['immediate_supervisor'] ?? '');
    $station = trim($_POST['station'] ?? '');

    $email = trim($_POST['email'] ?? '');
    $altEmail = trim($_POST['altEmail'] ?? $_POST['alt_email'] ?? '');
    $phone = trim($_POST['phone'] ?? $_POST['contact_number'] ?? '');
    $emergencyContact = trim($_POST['emergencyContact'] ?? $_POST['emergency_contact'] ?? '');
    $landline = trim($_POST['landline'] ?? '');
    $extension = trim($_POST['extension'] ?? '');
    $building = trim($_POST['building'] ?? '');
    $desk = trim($_POST['desk'] ?? '');

    $showGovService = isset($_POST['showGovService']) ? (int)$_POST['showGovService'] : (isset($_POST['show_gov_service']) ? (int)$_POST['show_gov_service'] : 1);
    $prefSms = isset($_POST['prefSms']) ? (int)$_POST['prefSms'] : 1;
    $prefEmailPass = isset($_POST['prefEmailPass']) ? (int)$_POST['prefEmailPass'] : 1;
    $prefSupervisorCc = isset($_POST['prefSupervisorCc']) ? (int)$_POST['prefSupervisorCc'] : 1;

    // Fetch existing user to avoid overwriting with blank if not provided
    $currStmt = $db->prepare("SELECT * FROM users WHERE id = ?");
    $currStmt->execute([$userId]);
    $currentUser = $currStmt->fetch(PDO::FETCH_ASSOC);

    if (!$currentUser) {
        echo json_encode(['success' => false, 'message' => 'User account not found.']);
        exit;
    }

    if (empty($fullName)) {
        $fullName = $currentUser['full_name'];
    }
    if (empty($email)) {
        $email = $currentUser['email'];
    }

    $updateStmt = $db->prepare("UPDATE users SET 
        full_name = :full_name,
        email = :email,
        contact_number = :contact_number,
        position = :position,
        division = :division,
        date_hired = :date_hired,
        employment_status = :employment_status,
        immediate_supervisor = :immediate_supervisor,
        station = :station,
        alt_email = :alt_email,
        emergency_contact = :emergency_contact,
        landline = :landline,
        extension = :extension,
        building = :building,
        desk = :desk,
        show_gov_service = :show_gov_service,
        pref_sms = :pref_sms,
        pref_email_pass = :pref_email_pass,
        pref_supervisor_cc = :pref_supervisor_cc,
        updated_at = NOW()
        WHERE id = :id");

    $updateStmt->execute([
        ':full_name' => $fullName,
        ':email' => $email,
        ':contact_number' => $phone,
        ':position' => $position,
        ':division' => $division,
        ':date_hired' => $dateHired,
        ':employment_status' => $empStatus,
        ':immediate_supervisor' => $supervisor,
        ':station' => $station,
        ':alt_email' => $altEmail,
        ':emergency_contact' => $emergencyContact,
        ':landline' => $landline,
        ':extension' => $extension,
        ':building' => $building,
        ':desk' => $desk,
        ':show_gov_service' => $showGovService ? 1 : 0,
        ':pref_sms' => $prefSms ? 1 : 0,
        ':pref_email_pass' => $prefEmailPass ? 1 : 0,
        ':pref_supervisor_cc' => $prefSupervisorCc ? 1 : 0,
        ':id' => $userId
    ]);

    $_SESSION['full_name'] = $fullName;

    echo json_encode([
        'success' => true,
        'message' => 'Profile and workstation details saved successfully!',
        'data' => [
            'fullName' => $fullName,
            'email' => $email,
            'phone' => $phone,
            'altEmail' => $altEmail,
            'emergencyContact' => $emergencyContact,
            'landline' => $landline,
            'extension' => $extension,
            'building' => $building,
            'desk' => $desk,
            'showGovService' => $showGovService ? 1 : 0
        ]
    ]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Failed to update profile: ' . $e->getMessage()]);
}
