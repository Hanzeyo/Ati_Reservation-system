<?php
require_once __DIR__ . '/../includes/init.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

// Security Check: Only logged in admins can manage reservations
if (!isset($_SESSION['user_id']) || !isAdmin()) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access. Administrative privileges required.']);
    exit;
}

$currentAdminId = (int)$_SESSION['user_id'];
$currentAdminName = $_SESSION['full_name'] ?? 'Authorized Administrator';
$currentAdminRole = $_SESSION['role'] ?? $_SESSION['user_role'] ?? 'admin';
$action = trim($_POST['action'] ?? '');
$ipAddress = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';

$auditLog = new AuditLog($db);
$auditLog->user_id = $currentAdminId;
$auditLog->ip_address = $ipAddress;

try {
    // =========================================================================
    // ACTION: SOFT DELETE RESERVATION (MOVE TO TRASH)
    // =========================================================================
    if ($action === 'delete_reservation' || $action === 'soft_delete_reservation') {
        $ref = trim($_POST['ref'] ?? '');
        $resId = (int)($_POST['reservation_id'] ?? 0);

        if (empty($ref) && $resId <= 0) {
            echo json_encode(['success' => false, 'message' => 'Reservation reference number or ID is required.']);
            exit;
        }

        if (!empty($ref)) {
            $stmt = $db->prepare("SELECT id, reference_no, event_title, user_id FROM reservations WHERE reference_no = ?");
            $stmt->execute([$ref]);
        } else {
            $stmt = $db->prepare("SELECT id, reference_no, event_title, user_id FROM reservations WHERE id = ?");
            $stmt->execute([$resId]);
        }
        $res = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$res) {
            echo json_encode(['success' => false, 'message' => 'Reservation not found.']);
            exit;
        }

        $updateStmt = $db->prepare("UPDATE reservations SET deleted_at = NOW() WHERE id = ?");
        $updateStmt->execute([$res['id']]);

        $auditLog->action = 'RESERVATION_SOFT_DELETE';
        $auditLog->details = "Moved reservation {$res['reference_no']} ('{$res['event_title']}') to Trash by {$currentAdminName}.";
        $auditLog->log();

        echo json_encode([
            'success' => true,
            'message' => "Reservation {$res['reference_no']} was moved to Trash (Archived).",
            'reference_no' => $res['reference_no'],
            'reservation_id' => (int)$res['id']
        ]);
        exit;
    }

    // =========================================================================
    // ACTION: RESTORE RESERVATION FROM TRASH
    // =========================================================================
    if ($action === 'restore_reservation') {
        $ref = trim($_POST['ref'] ?? '');
        $resId = (int)($_POST['reservation_id'] ?? 0);

        if (empty($ref) && $resId <= 0) {
            echo json_encode(['success' => false, 'message' => 'Reservation reference number or ID is required.']);
            exit;
        }

        if (!empty($ref)) {
            $stmt = $db->prepare("SELECT id, reference_no, event_title, user_id FROM reservations WHERE reference_no = ?");
            $stmt->execute([$ref]);
        } else {
            $stmt = $db->prepare("SELECT id, reference_no, event_title, user_id FROM reservations WHERE id = ?");
            $stmt->execute([$resId]);
        }
        $res = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$res) {
            echo json_encode(['success' => false, 'message' => 'Reservation not found.']);
            exit;
        }

        $updateStmt = $db->prepare("UPDATE reservations SET deleted_at = NULL WHERE id = ?");
        $updateStmt->execute([$res['id']]);

        $auditLog->action = 'RESERVATION_RESTORE';
        $auditLog->details = "Restored reservation {$res['reference_no']} ('{$res['event_title']}') from Trash to active queue by {$currentAdminName}.";
        $auditLog->log();

        echo json_encode([
            'success' => true,
            'message' => "Reservation {$res['reference_no']} was restored to the active queue.",
            'reference_no' => $res['reference_no'],
            'reservation_id' => (int)$res['id']
        ]);
        exit;
    }

    // =========================================================================
    // ACTION: PERMANENTLY PURGE RESERVATION (HARD DELETE - SUPER ADMIN ONLY)
    // =========================================================================
    if ($action === 'permanent_delete_reservation' || $action === 'purge_reservation') {
        if ($currentAdminRole !== 'super_admin') {
            echo json_encode(['success' => false, 'message' => 'Only Super Administrators have permission to permanently delete reservations.']);
            exit;
        }

        $ref = trim($_POST['ref'] ?? '');
        $resId = (int)($_POST['reservation_id'] ?? 0);

        if (empty($ref) && $resId <= 0) {
            echo json_encode(['success' => false, 'message' => 'Reservation reference number or ID is required.']);
            exit;
        }

        if (!empty($ref)) {
            $stmt = $db->prepare("SELECT id, reference_no, event_title, user_id, document_path FROM reservations WHERE reference_no = ?");
            $stmt->execute([$ref]);
        } else {
            $stmt = $db->prepare("SELECT id, reference_no, event_title, user_id, document_path FROM reservations WHERE id = ?");
            $stmt->execute([$resId]);
        }
        $res = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$res) {
            echo json_encode(['success' => false, 'message' => 'Reservation not found.']);
            exit;
        }

        // Delete reservation record
        $delStmt = $db->prepare("DELETE FROM reservations WHERE id = ?");
        $delStmt->execute([$res['id']]);

        // Clean up uploaded document if existing
        if (!empty($res['document_path'])) {
            $filePath = __DIR__ . '/../' . ltrim($res['document_path'], '/');
            if (file_exists($filePath) && is_file($filePath)) {
                @unlink($filePath);
            }
        }

        $auditLog->action = 'RESERVATION_PERMANENT_DELETE';
        $auditLog->details = "Permanently deleted reservation {$res['reference_no']} ('{$res['event_title']}') from database by {$currentAdminName}.";
        $auditLog->log();

        echo json_encode([
            'success' => true,
            'message' => "Reservation {$res['reference_no']} has been permanently deleted from the database.",
            'reference_no' => $res['reference_no'],
            'reservation_id' => (int)$res['id']
        ]);
        exit;
    }

    // =========================================================================
    // ACTION: COMPLETE RESERVATION (CONCLUDE ACTIVITY)
    // =========================================================================
    if ($action === 'complete_reservation' || $action === 'conclude_reservation') {
        $ref = trim($_POST['ref'] ?? '');
        $resId = (int)($_POST['reservation_id'] ?? 0);

        if (empty($ref) && $resId <= 0) {
            echo json_encode(['success' => false, 'message' => 'Reservation reference number or ID is required.']);
            exit;
        }

        if (!empty($ref)) {
            $stmt = $db->prepare("SELECT id, reference_no, event_title, user_id, status FROM reservations WHERE reference_no = ?");
            $stmt->execute([$ref]);
        } else {
            $stmt = $db->prepare("SELECT id, reference_no, event_title, user_id, status FROM reservations WHERE id = ?");
            $stmt->execute([$resId]);
        }
        $res = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$res) {
            echo json_encode(['success' => false, 'message' => 'Reservation not found.']);
            exit;
        }

        $updateStmt = $db->prepare("UPDATE reservations SET status = 'Completed', updated_at = NOW() WHERE id = ?");
        $updateStmt->execute([$res['id']]);

        $auditLog->action = 'RESERVATION_COMPLETED';
        $auditLog->details = "Marked reservation {$res['reference_no']} ('{$res['event_title']}') as Completed & Concluded by {$currentAdminName}.";
        $auditLog->log();

        echo json_encode([
            'success' => true,
            'message' => "Reservation {$res['reference_no']} was successfully marked as Completed & Concluded.",
            'reference_no' => $res['reference_no'],
            'reservation_id' => (int)$res['id']
        ]);
        exit;
    }

    echo json_encode(['success' => false, 'message' => "Unknown action '{$action}'."]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}
