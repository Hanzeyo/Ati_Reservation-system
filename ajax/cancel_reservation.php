<?php
require_once __DIR__ . '/../includes/init.php';

header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized. Please log in.']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit();
}

$ref = isset($_POST['reference_no']) ? trim($_POST['reference_no']) : '';
$reason = isset($_POST['reason']) ? trim($_POST['reason']) : 'Cancelled by requestor';

if (empty($ref)) {
    echo json_encode(['success' => false, 'message' => 'Reference number is required.']);
    exit();
}

try {
    // Verify that this reservation belongs to the logged-in user (or admin)
    $stmt = $db->prepare("SELECT id, status, reference_no, event_title FROM reservations WHERE reference_no = :ref AND user_id = :user_id LIMIT 1");
    $stmt->execute([
        ':ref' => $ref,
        ':user_id' => $_SESSION['user_id']
    ]);
    $res = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$res) {
        echo json_encode(['success' => false, 'message' => 'Reservation not found or access denied.']);
        exit();
    }

    if ($res['status'] === 'Cancelled') {
        echo json_encode(['success' => true, 'message' => 'Reservation is already cancelled.']);
        exit();
    }

    if ($res['status'] === 'Completed') {
        echo json_encode(['success' => false, 'message' => 'Completed reservations cannot be cancelled.']);
        exit();
    }

    $updateStmt = $db->prepare("UPDATE reservations SET status = 'Cancelled', admin_remarks = CONCAT(COALESCE(admin_remarks, ''), '\n[Cancelled by Requestor: ', :reason, ']') WHERE id = :id");
    $updated = $updateStmt->execute([
        ':reason' => $reason,
        ':id' => $res['id']
    ]);

    if ($updated) {
        // Record Audit Log if AuditLog class is available
        if (isset($auditLog)) {
            $auditLog->log($_SESSION['user_id'], 'CANCEL_RESERVATION', "User cancelled reservation {$ref} ({$res['event_title']}). Reason: {$reason}");
        }

        echo json_encode([
            'success' => true,
            'message' => 'Reservation cancelled successfully.',
            'reference_no' => $ref
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to cancel reservation. Please try again.']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
