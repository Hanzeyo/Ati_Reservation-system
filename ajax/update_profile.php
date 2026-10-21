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
$action = trim($_POST['action'] ?? 'update_profile');

try {
    // Fetch current user from database
    $currStmt = $db->prepare("SELECT id, full_name, email, password_hash, contact_number, office_agency, category, role FROM users WHERE id = ? AND deleted_at IS NULL");
    $currStmt->execute([$userId]);
    $currentUser = $currStmt->fetch(PDO::FETCH_ASSOC);

    if (!$currentUser) {
        echo json_encode(['success' => false, 'message' => 'User account not found.']);
        exit;
    }

    // =========================================================================
    // ACTION: CHANGE PASSWORD
    // =========================================================================
    if ($action === 'change_password') {
        $currentPwd = $_POST['current_password'] ?? '';
        $newPwd = $_POST['new_password'] ?? '';
        $confirmPwd = $_POST['confirm_password'] ?? '';

        if (empty($currentPwd) || empty($newPwd) || empty($confirmPwd)) {
            echo json_encode(['success' => false, 'message' => 'Please fill out all password fields.']);
            exit;
        }

        if (strlen($newPwd) < 8) {
            echo json_encode(['success' => false, 'message' => 'New password must be at least 8 characters long.']);
            exit;
        }

        if ($newPwd !== $confirmPwd) {
            echo json_encode(['success' => false, 'message' => 'New password and confirm password do not match.']);
            exit;
        }

        // Verify current password
        if (!password_verify($currentPwd, $currentUser['password_hash'])) {
            echo json_encode(['success' => false, 'message' => 'Incorrect current password.']);
            exit;
        }

        // Hash and update new password
        $newHash = password_hash($newPwd, PASSWORD_BCRYPT);
        $pwdStmt = $db->prepare("UPDATE users SET password_hash = :hash, updated_at = NOW() WHERE id = :id");
        $pwdStmt->execute([
            ':hash' => $newHash,
            ':id' => $userId
        ]);

        if (class_exists('AuditLog')) {
            AuditLog::log($userId, 'USER_PASSWORD_CHANGE', "User {$currentUser['full_name']} updated their account password.", $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1');
        }

        echo json_encode(['success' => true, 'message' => 'Your password has been updated successfully!']);
        exit;
    }

    // =========================================================================
    // ACTION: UPDATE PROFILE INFORMATION
    // =========================================================================
    $fullName = trim($_POST['full_name'] ?? $_POST['fullName'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $contactNumber = trim($_POST['contact_number'] ?? $_POST['phone'] ?? '');
    $officeAgency = trim($_POST['office_agency'] ?? $_POST['division'] ?? '');

    if (empty($fullName)) {
        echo json_encode(['success' => false, 'message' => 'Full name cannot be blank.']);
        exit;
    }

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Please provide a valid email address.']);
        exit;
    }

    // Check email uniqueness if email was changed
    if (strtolower($email) !== strtolower($currentUser['email'])) {
        $emailCheck = $db->prepare("SELECT id FROM users WHERE email = ? AND id != ? AND deleted_at IS NULL LIMIT 1");
        $emailCheck->execute([$email, $userId]);
        if ($emailCheck->fetch()) {
            echo json_encode(['success' => false, 'message' => "The email '{$email}' is already taken by another user."]);
            exit;
        }
    }

    // Update real user columns in MySQL
    $updateStmt = $db->prepare("UPDATE users SET 
        full_name = :full_name,
        email = :email,
        contact_number = :contact_number,
        office_agency = :office_agency,
        updated_at = NOW()
        WHERE id = :id");

    $updateStmt->execute([
        ':full_name' => $fullName,
        ':email' => $email,
        ':contact_number' => $contactNumber,
        ':office_agency' => $officeAgency,
        ':id' => $userId
    ]);

    // Keep session in sync
    $_SESSION['full_name'] = $fullName;
    $_SESSION['email'] = $email;

    if (class_exists('AuditLog')) {
        AuditLog::log($userId, 'USER_PROFILE_UPDATE', "User [ID: {$userId}] updated profile information: {$fullName} ({$email}, {$officeAgency}).", $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1');
    }

    echo json_encode([
        'success' => true,
        'message' => 'Profile information updated successfully!',
        'data' => [
            'fullName' => $fullName,
            'email' => $email,
            'phone' => $contactNumber,
            'officeAgency' => $officeAgency
        ]
    ]);

} catch (Throwable $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
