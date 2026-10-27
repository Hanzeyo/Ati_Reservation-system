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

    // =========================================================================
    // ACTION: LOOKUP RESERVATION BY REFERENCE NUMBER (QUICK CHECK-IN)
    // =========================================================================
    if ($action === 'lookup_reservation') {
        $ref = trim($_POST['ref'] ?? '');

        if (empty($ref)) {
            echo json_encode(['success' => false, 'message' => 'Please enter a Reservation Reference Number (e.g. ATI-2026-0042 or 0042).']);
            exit;
        }

        // Clean query: strip extra spaces
        $cleanRef = trim($ref);
        $likeRef = '%' . $cleanRef . '%';

        $query = "SELECT r.*, 
                         f.name AS facility_name, 
                         f.type AS facility_type, 
                         f.rate AS facility_rate,
                         rm.room_number,
                         rm.floor AS room_floor,
                         u.full_name AS applicant_name, 
                         u.email AS applicant_email, 
                         u.contact_number AS applicant_phone, 
                         u.office_agency AS applicant_agency,
                         u_chk.full_name AS checked_in_by_name
                  FROM reservations r
                  LEFT JOIN facilities f ON r.facility_id = f.id
                  LEFT JOIN rooms rm ON r.room_id = rm.id
                  LEFT JOIN users u ON r.user_id = u.id
                  LEFT JOIN users u_chk ON r.check_in_by = u_chk.id
                  WHERE (r.reference_no = :exactRef OR r.reference_no LIKE :likeRef)
                    AND r.deleted_at IS NULL
                  ORDER BY (r.reference_no = :exactRefOrder) DESC, r.id DESC
                  LIMIT 5";

        $stmt = $db->prepare($query);
        $stmt->execute([
            ':exactRef' => $cleanRef,
            ':likeRef' => $likeRef,
            ':exactRefOrder' => $cleanRef
        ]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($rows)) {
            echo json_encode([
                'success' => false, 
                'message' => "No active reservation found matching '{$cleanRef}'. Please verify the reference code."
            ]);
            exit;
        }

        $formattedResults = [];
        foreach ($rows as $row) {
            $startDateTs = !empty($row['start_date']) ? strtotime($row['start_date']) : time();
            $startDateStr = date('M d, Y', $startDateTs);
            if (!empty($row['end_date']) && $row['end_date'] !== $row['start_date']) {
                $datesStr = date('M d', $startDateTs) . ' – ' . date('M d, Y', strtotime($row['end_date']));
            } else {
                $datesStr = $startDateStr;
            }

            $venueDisplay = $row['facility_name'] ?: 'Official Facility';
            if (!empty($row['room_number'])) {
                $venueDisplay .= ' (Room ' . $row['room_number'] . ')';
            }

            $st = $row['status'] ?? 'Pending';
            $isApproved = ($st === 'Approved');
            $isCheckedIn = ($st === 'Checked In');
            $isNoShow = ($st === 'No-Show');
            $isCompleted = ($st === 'Completed');

            $formattedResults[] = [
                'id' => (int)$row['id'],
                'reference_no' => $row['reference_no'],
                'applicant_name' => !empty($row['applicant_name']) ? $row['applicant_name'] : 'Official Guest',
                'applicant_agency' => !empty($row['applicant_agency']) ? $row['applicant_agency'] : 'N/A',
                'applicant_email' => !empty($row['applicant_email']) ? $row['applicant_email'] : 'N/A',
                'applicant_phone' => !empty($row['applicant_phone']) ? $row['applicant_phone'] : 'N/A',
                'event_title' => !empty($row['event_title']) ? $row['event_title'] : 'Official Institutional Activity',
                'facility_name' => $venueDisplay,
                'facility_type' => $row['facility_type'] ?? 'facility',
                'room_number' => $row['room_number'] ?? '',
                'schedule_dates' => $datesStr,
                'time_slot' => !empty($row['time_slot']) ? $row['time_slot'] : 'Full Day',
                'pax_count' => (int)($row['pax_count'] ?? 1),
                'status' => $st,
                'is_approved' => $isApproved,
                'is_checked_in' => $isCheckedIn,
                'is_no_show' => $isNoShow,
                'is_completed' => $isCompleted,
                'can_check_in' => $isApproved,
                'check_in_at' => !empty($row['check_in_at']) ? date('M d, Y • h:i A', strtotime($row['check_in_at'])) : null,
                'checked_in_by_name' => $row['checked_in_by_name'] ?? null
            ];
        }

        echo json_encode([
            'success' => true,
            'match_count' => count($formattedResults),
            'reservation' => $formattedResults[0],
            'results' => $formattedResults
        ]);
        exit;
    }

    // =========================================================================
    // ACTION: CONFIRM CLIENT CHECK-IN (FRONT DESK ENTRY)
    // =========================================================================
    if ($action === 'check_in_reservation') {
        $ref = trim($_POST['ref'] ?? '');
        $resId = (int)($_POST['reservation_id'] ?? 0);

        if (empty($ref) && $resId <= 0) {
            echo json_encode(['success' => false, 'message' => 'Reservation reference number or ID is required.']);
            exit;
        }

        if (!empty($ref)) {
            $stmt = $db->prepare("SELECT id, reference_no, event_title, user_id, status, check_in_at FROM reservations WHERE reference_no = ? AND deleted_at IS NULL");
            $stmt->execute([$ref]);
        } else {
            $stmt = $db->prepare("SELECT id, reference_no, event_title, user_id, status, check_in_at FROM reservations WHERE id = ? AND deleted_at IS NULL");
            $stmt->execute([$resId]);
        }
        $res = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$res) {
            echo json_encode(['success' => false, 'message' => 'Reservation not found.']);
            exit;
        }

        if ($res['status'] === 'Checked In') {
            $checkedTime = !empty($res['check_in_at']) ? date('h:i A', strtotime($res['check_in_at'])) : 'earlier';
            echo json_encode([
                'success' => true,
                'already_checked_in' => true,
                'message' => "Reservation {$res['reference_no']} was already checked in at {$checkedTime}.",
                'reference_no' => $res['reference_no'],
                'reservation_id' => (int)$res['id']
            ]);
            exit;
        }

        // Update reservation to 'Checked In'
        $updateStmt = $db->prepare("UPDATE reservations 
                                    SET status = 'Checked In', 
                                        check_in_at = NOW(), 
                                        check_in_by = ?, 
                                        updated_at = NOW() 
                                    WHERE id = ?");
        $updateStmt->execute([$currentAdminId, $res['id']]);

        $auditLog->action = 'RESERVATION_CHECK_IN';
        $auditLog->details = "Client check-in confirmed for {$res['reference_no']} ('{$res['event_title']}') by {$currentAdminName} at Front Desk.";
        $auditLog->log();

        $checkInTimeFormatted = date('M d, Y • h:i A');

        echo json_encode([
            'success' => true,
            'message' => "Client check-in successfully recorded for {$res['reference_no']}!",
            'reference_no' => $res['reference_no'],
            'reservation_id' => (int)$res['id'],
            'check_in_at' => $checkInTimeFormatted,
            'checked_in_by_name' => $currentAdminName,
            'status' => 'Checked In'
        ]);
        exit;
    }

    // =========================================================================
    // ACTION: MARK AS NO-SHOW
    // =========================================================================
    if ($action === 'no_show_reservation') {
        $ref = trim($_POST['ref'] ?? '');
        $resId = (int)($_POST['reservation_id'] ?? 0);
        $reason = trim($_POST['reason'] ?? 'Client did not arrive on scheduled reservation date.');

        if (empty($ref) && $resId <= 0) {
            echo json_encode(['success' => false, 'message' => 'Reservation reference number or ID is required.']);
            exit;
        }

        if (!empty($ref)) {
            $stmt = $db->prepare("SELECT id, reference_no, event_title, user_id, status FROM reservations WHERE reference_no = ? AND deleted_at IS NULL");
            $stmt->execute([$ref]);
        } else {
            $stmt = $db->prepare("SELECT id, reference_no, event_title, user_id, status FROM reservations WHERE id = ? AND deleted_at IS NULL");
            $stmt->execute([$resId]);
        }
        $res = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$res) {
            echo json_encode(['success' => false, 'message' => 'Reservation not found.']);
            exit;
        }

        $updateStmt = $db->prepare("UPDATE reservations SET status = 'No-Show', updated_at = NOW() WHERE id = ?");
        $updateStmt->execute([$res['id']]);

        $auditLog->action = 'RESERVATION_NO_SHOW';
        $auditLog->details = "Marked reservation {$res['reference_no']} ('{$res['event_title']}') as No-Show by {$currentAdminName}. Reason: {$reason}";
        $auditLog->log();

        echo json_encode([
            'success' => true,
            'message' => "Reservation {$res['reference_no']} has been marked as No-Show.",
            'reference_no' => $res['reference_no'],
            'reservation_id' => (int)$res['id'],
            'status' => 'No-Show'
        ]);
        exit;
    }

    echo json_encode(['success' => false, 'message' => "Unknown action '{$action}'."]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}
