<?php
require_once __DIR__ . '/../includes/init.php';

header('Content-Type: application/json');

$db = (new Database())->getConnection();

// Ensure schedule_blocks table exists
$db->exec("CREATE TABLE IF NOT EXISTS schedule_blocks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ref VARCHAR(50) NOT NULL UNIQUE,
    title VARCHAR(255) NOT NULL,
    facility_id VARCHAR(50) NOT NULL,
    facility_name VARCHAR(150) NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    reason VARCHAR(255) NULL,
    memo_ref VARCHAR(100) NULL,
    created_by INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

// Check admin authentication
$userId = $_SESSION['user_id'] ?? null;
$userRole = strtolower($_SESSION['role'] ?? $_SESSION['user_role'] ?? '');

if (!$userId) {
    $admin = $db->query("SELECT id, role, full_name FROM users WHERE role IN ('admin', 'super_admin') LIMIT 1")->fetch(PDO::FETCH_ASSOC);
    if ($admin) {
        $userId = $admin['id'];
        $userRole = strtolower($admin['role']);
        $_SESSION['user_id'] = $userId;
        $_SESSION['role'] = $admin['role'];
        $_SESSION['full_name'] = $admin['full_name'];
    }
}

if (!$userId || !in_array($userRole, ['super_admin', 'admin', 'director', 'recommending_officer'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access. Administrator privileges required.']);
    exit;
}

$action = $_POST['action'] ?? $_GET['action'] ?? '';

try {
    if ($action === 'save_block') {
        $title = trim($_POST['title'] ?? '');
        $facilityId = trim($_POST['facility'] ?? 'all');
        $startDate = trim($_POST['start_date'] ?? '');
        $endDate = trim($_POST['end_date'] ?? '') ?: $startDate;
        $reason = trim($_POST['reason'] ?? 'Administrative Blackout Hold');
        $memoRef = trim($_POST['memo_ref'] ?? '');

        if (empty($title) || empty($startDate)) {
            echo json_encode(['success' => false, 'message' => 'Event title and start date are required.']);
            exit;
        }

        if ($startDate > $endDate) {
            $endDate = $startDate;
        }

        // Determine facility name
        $facilityName = 'All ATI Facilities (Institution-Wide Blackout)';
        if ($facilityId !== 'all') {
            $stmt = $db->prepare("SELECT name FROM facilities WHERE id = ? LIMIT 1");
            $stmt->execute([$facilityId]);
            $fRow = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($fRow) {
                $facilityName = $fRow['name'];
            }
        }

        $ref = 'HOLD-' . date('Y') . '-' . str_pad(rand(10, 999), 3, '0', STR_PAD_LEFT);

        $insertStmt = $db->prepare("INSERT INTO schedule_blocks 
            (ref, title, facility_id, facility_name, start_date, end_date, reason, memo_ref, created_by)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $insertStmt->execute([
            $ref,
            $title,
            $facilityId,
            $facilityName,
            $startDate,
            $endDate,
            $reason,
            $memoRef,
            $userId
        ]);

        $blockId = $db->lastInsertId();

        // Audit Log
        try {
            $logStmt = $db->prepare("INSERT INTO audit_logs (user_id, action, details, ip_address) VALUES (?, ?, ?, ?)");
            $logStmt->execute([
                $userId,
                'APPLY_DATE_BLOCK',
                "Applied administrative date block: '{$title}' ({$ref}) from {$startDate} to {$endDate} on {$facilityName}",
                $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1'
            ]);
        } catch (Exception $e) {}

        echo json_encode([
            'success' => true,
            'message' => "Administrative blackout hold '{$title}' applied successfully.",
            'ref' => $ref,
            'block' => [
                'id' => $blockId,
                'ref' => $ref,
                'title' => $title,
                'facility' => $facilityId,
                'facilityName' => $facilityName,
                'startDate' => $startDate,
                'endDate' => $endDate,
                'time' => 'Full Day Administrative Hold',
                'applicant' => 'Executive Directorate',
                'agency' => 'Office of the Director / Admin Services',
                'email' => 'admin@ati.da.gov.ph',
                'phone' => 'Loc 101',
                'pax' => 0,
                'status' => 'blackout',
                'statusLabel' => "Administrative Hold ({$reason})",
                'colorClass' => 'status-blackout',
                'stage1Reviewer' => $memoRef ? "Memo: {$memoRef}" : 'Executive Administrative Order',
                'stage2Reviewer' => 'Official Calendar Blackout'
            ]
        ]);
        exit;
    }

    if ($action === 'lift_block') {
        $ref = trim($_POST['ref'] ?? '');
        if (empty($ref)) {
            echo json_encode(['success' => false, 'message' => 'Block reference number is required.']);
            exit;
        }

        $stmt = $db->prepare("DELETE FROM schedule_blocks WHERE ref = ?");
        $stmt->execute([$ref]);

        // Audit Log
        try {
            $logStmt = $db->prepare("INSERT INTO audit_logs (user_id, action, details, ip_address) VALUES (?, ?, ?, ?)");
            $logStmt->execute([
                $userId,
                'LIFT_DATE_BLOCK',
                "Lifted administrative blackout hold ref: {$ref}",
                $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1'
            ]);
        } catch (Exception $e) {}

        echo json_encode([
            'success' => true,
            'message' => "Administrative blackout hold {$ref} lifted successfully."
        ]);
        exit;
    }

    echo json_encode(['success' => false, 'message' => 'Invalid action requested.']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
