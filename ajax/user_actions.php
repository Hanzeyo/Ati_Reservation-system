<?php
require_once __DIR__ . '/../includes/init.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

// Security Check: Only logged in admins can manage users & roles
if (!isset($_SESSION['user_id']) || !isAdmin()) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access. Super Administrator or Governance privileges required.']);
    exit;
}

$currentAdminId = (int)$_SESSION['user_id'];
$currentAdminName = $_SESSION['full_name'] ?? 'Authorized Administrator';
$action = trim($_POST['action'] ?? '');
$ipAddress = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';

$auditLog = new AuditLog($db);
$auditLog->user_id = $currentAdminId;
$auditLog->ip_address = $ipAddress;

try {
    // =========================================================================
    // ACTION: ADD NEW USER
    // =========================================================================
    if ($action === 'add_user') {
        $fullName = trim($_POST['full_name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');
        $contactNumber = trim($_POST['contact_number'] ?? '');
        $officeAgency = trim($_POST['office_agency'] ?? '');
        $category = trim($_POST['category'] ?? 'ATI Personnel');
        $role = trim($_POST['role'] ?? 'user');
        $status = trim($_POST['status'] ?? 'active');

        if (empty($fullName) || empty($email)) {
            echo json_encode(['success' => false, 'message' => 'Full name and email are required.']);
            exit;
        }

        if (empty($password)) {
            $password = 'AtiPass2026!'; // Default secure password if blank
        }

        // Check if email already exists
        $checkStmt = $db->prepare("SELECT id FROM users WHERE email = ?");
        $checkStmt->execute([$email]);
        if ($checkStmt->rowCount() > 0) {
            echo json_encode(['success' => false, 'message' => "The email '{$email}' is already registered."]);
            exit;
        }

        // Valid categories
        $validCategories = ['ATI Personnel', 'Gov Agency', 'Private / Guest'];
        if (!in_array($category, $validCategories)) {
            $category = 'ATI Personnel';
        }

        // Valid roles
        $validRoles = ['user', 'endorsement', 'recommendation', 'clearance', 'super_admin'];
        if (!in_array($role, $validRoles)) {
            $role = 'user';
        }

        $passwordHash = password_hash($password, PASSWORD_BCRYPT);

        $insertStmt = $db->prepare("INSERT INTO users (full_name, email, password_hash, contact_number, office_agency, category, role, status, created_at)
            VALUES (:name, :email, :pass, :phone, :office, :cat, :role, :status, NOW())");
        
        $insertStmt->execute([
            ':name' => $fullName,
            ':email' => $email,
            ':pass' => $passwordHash,
            ':phone' => $contactNumber,
            ':office' => $officeAgency,
            ':cat' => $category,
            ':role' => $role,
            ':status' => $status
        ]);

        $newUserId = (int)$db->lastInsertId();

        $auditLog->action = 'USER_REGISTRATION';
        $auditLog->details = "Registered new user [ID: {$newUserId}] {$fullName} ({$email}) with role: {$role} by {$currentAdminName}.";
        $auditLog->log();

        echo json_encode([
            'success' => true,
            'message' => "Successfully registered {$fullName} as {$role}.",
            'user' => [
                'id' => $newUserId,
                'full_name' => $fullName,
                'email' => $email,
                'office_agency' => $officeAgency,
                'category' => $category,
                'role' => $role,
                'status' => $status
            ]
        ]);
        exit;
    }

    // =========================================================================
    // ACTION: UPDATE ROLE & DIVISION
    // =========================================================================
    if ($action === 'update_role') {
        $userId = (int)($_POST['user_id'] ?? 0);
        $role = trim($_POST['role'] ?? '');
        $officeAgency = trim($_POST['office_agency'] ?? '');

        if ($userId <= 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid user ID.']);
            exit;
        }

        // Valid roles
        $validRoles = ['user', 'endorsement', 'recommendation', 'clearance', 'super_admin'];
        if (!in_array($role, $validRoles)) {
            echo json_encode(['success' => false, 'message' => 'Invalid role specified.']);
            exit;
        }

        // Fetch user before update
        $fetchStmt = $db->prepare("SELECT full_name, email, role, office_agency FROM users WHERE id = ?");
        $fetchStmt->execute([$userId]);
        $targetUser = $fetchStmt->fetch(PDO::FETCH_ASSOC);

        if (!$targetUser) {
            echo json_encode(['success' => false, 'message' => 'User not found.']);
            exit;
        }

        $oldRole = $targetUser['role'];

        $updateStmt = $db->prepare("UPDATE users SET role = :role, office_agency = :office, updated_at = NOW() WHERE id = :id");
        $updateStmt->execute([
            ':role' => $role,
            ':office' => !empty($officeAgency) ? $officeAgency : $targetUser['office_agency'],
            ':id' => $userId
        ]);

        $auditLog->action = 'USER_ROLE_CHANGE';
        $auditLog->details = "Changed role for {$targetUser['full_name']} (ID: {$userId}) from [{$oldRole}] to [{$role}] by {$currentAdminName}.";
        $auditLog->log();

        echo json_encode([
            'success' => true,
            'message' => "Role successfully updated for {$targetUser['full_name']} to {$role}.",
            'user_id' => $userId,
            'role' => $role,
            'office_agency' => $officeAgency ?: $targetUser['office_agency']
        ]);
        exit;
    }

    // =========================================================================
    // ACTION: TOGGLE STATUS (ACTIVE / SUSPENDED)
    // =========================================================================
    if ($action === 'toggle_status') {
        $userId = (int)($_POST['user_id'] ?? 0);
        $newStatus = trim($_POST['status'] ?? '');

        if ($userId <= 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid user ID.']);
            exit;
        }

        // Prevent self-suspension
        if ($userId === $currentAdminId) {
            echo json_encode(['success' => false, 'message' => 'You cannot suspend your own active administrator account.']);
            exit;
        }

        $fetchStmt = $db->prepare("SELECT full_name, status FROM users WHERE id = ?");
        $fetchStmt->execute([$userId]);
        $targetUser = $fetchStmt->fetch(PDO::FETCH_ASSOC);

        if (!$targetUser) {
            echo json_encode(['success' => false, 'message' => 'User not found.']);
            exit;
        }

        // Toggle if not provided
        if (empty($newStatus)) {
            $newStatus = ($targetUser['status'] === 'active') ? 'suspended' : 'active';
        }

        $updateStmt = $db->prepare("UPDATE users SET status = :status, updated_at = NOW() WHERE id = :id");
        $updateStmt->execute([
            ':status' => $newStatus,
            ':id' => $userId
        ]);

        $auditLog->action = 'USER_STATUS_CHANGE';
        $auditLog->details = "Changed account status for {$targetUser['full_name']} (ID: {$userId}) to [{$newStatus}] by {$currentAdminName}.";
        $auditLog->log();

        echo json_encode([
            'success' => true,
            'message' => "Account status for {$targetUser['full_name']} is now {$newStatus}.",
            'user_id' => $userId,
            'status' => $newStatus
        ]);
        exit;
    }

    // =========================================================================
    // ACTION: DELETE USER
    // =========================================================================
    if ($action === 'delete_user') {
        $userId = (int)($_POST['user_id'] ?? 0);

        if ($userId <= 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid user ID.']);
            exit;
        }

        if ($userId === $currentAdminId) {
            echo json_encode(['success' => false, 'message' => 'You cannot delete your own administrator account.']);
            exit;
        }

        $fetchStmt = $db->prepare("SELECT full_name, email FROM users WHERE id = ?");
        $fetchStmt->execute([$userId]);
        $targetUser = $fetchStmt->fetch(PDO::FETCH_ASSOC);

        if (!$targetUser) {
            echo json_encode(['success' => false, 'message' => 'User not found.']);
            exit;
        }

        $delStmt = $db->prepare("DELETE FROM users WHERE id = ?");
        $delStmt->execute([$userId]);

        $auditLog->action = 'USER_DELETION';
        $auditLog->details = "Permanently deleted user {$targetUser['full_name']} (ID: {$userId}, {$targetUser['email']}) by {$currentAdminName}.";
        $auditLog->log();

        echo json_encode([
            'success' => true,
            'message' => "User {$targetUser['full_name']} has been removed from the system.",
            'user_id' => $userId
        ]);
        exit;
    }

    echo json_encode(['success' => false, 'message' => "Unknown action '{$action}'."]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}
