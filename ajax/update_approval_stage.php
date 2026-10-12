<?php
require_once __DIR__ . '/../includes/init.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

// Security Check: Only logged in admins can approve or update stages
if (!isset($_SESSION['user_id']) || !isAdmin()) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access. Administrator privileges required.']);
    exit;
}

$adminUserId = (int)$_SESSION['user_id'];
$adminFullName = $_SESSION['full_name'] ?? 'Authorized Officer';

$action = trim($_POST['action'] ?? '');
$ref = trim($_POST['ref'] ?? '');
$remarks = trim($_POST['remarks'] ?? '');
$reason = trim($_POST['reason'] ?? '');
$rawRefs = $_POST['refs'] ?? [];

$ipAddress = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';

// Ensure audit log helper is ready
$auditLog = new AuditLog($db);
$auditLog->user_id = $adminUserId;
$auditLog->ip_address = $ipAddress;

try {
    // =========================================================================
    // STAGE 1: ENDORSEMENT (Division Head / Originating Unit Endorsement)
    // =========================================================================
    if ($action === 'stage1' || $action === 'endorse') {
        if (empty($ref)) {
            echo json_encode(['success' => false, 'message' => 'Reference number is required.']);
            exit;
        }

        $defaultRemarks = 'Official endorsement issued. Verified division activity alignment and supporting documentation.';
        $finalRemarks = !empty($remarks) ? $remarks : $defaultRemarks;

        $stmt = $db->prepare("UPDATE reservations SET 
            status = 'Pending Recommendation',
            endorsed_by = :user_id,
            endorsed_at = NOW(),
            endorsement_remarks = :remarks
            WHERE reference_no = :ref");
        
        $stmt->execute([
            ':user_id' => $adminUserId,
            ':remarks' => $finalRemarks,
            ':ref' => $ref
        ]);

        if ($stmt->rowCount() > 0) {
            $auditLog->action = 'STAGE1_ENDORSEMENT';
            $auditLog->details = "Issued Stage 1 Endorsement for reservation [{$ref}] by {$adminFullName}. Remarks: {$finalRemarks}";
            $auditLog->log();

            echo json_encode([
                'success' => true,
                'message' => "Stage 1 Endorsement granted for {$ref}. Forwarded to Stage 2: Recommendation.",
                'stage' => 'stage2',
                'status' => 'Pending Recommendation',
                'stage1By' => $adminFullName,
                'stage1Date' => date('M d, Y h:i A'),
                'stage1Remarks' => $finalRemarks
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => "Reservation {$ref} not found or no changes made."]);
        }
        exit;
    }

    // =========================================================================
    // STAGE 2: RECOMMENDATION (Facility / Administrative Section Review)
    // =========================================================================
    if ($action === 'stage2' || $action === 'recommend') {
        if (empty($ref)) {
            echo json_encode(['success' => false, 'message' => 'Reference number is required.']);
            exit;
        }

        $defaultRemarks = 'Verified equipment, schedules, and logistical feasibility. Recommended for Stage 3 final clearance.';
        $finalRemarks = !empty($remarks) ? $remarks : $defaultRemarks;

        $stmt = $db->prepare("UPDATE reservations SET 
            status = 'Pending Clearance',
            recommended_by = :user_id,
            recommended_at = NOW(),
            recommendation_remarks = :remarks
            WHERE reference_no = :ref");
        
        $stmt->execute([
            ':user_id' => $adminUserId,
            ':remarks' => $finalRemarks,
            ':ref' => $ref
        ]);

        if ($stmt->rowCount() > 0) {
            $auditLog->action = 'STAGE2_RECOMMENDATION';
            $auditLog->details = "Issued Stage 2 Recommendation for reservation [{$ref}] by {$adminFullName}. Remarks: {$finalRemarks}";
            $auditLog->log();

            echo json_encode([
                'success' => true,
                'message' => "Stage 2 Recommendation recorded for {$ref}. Forwarded to Stage 3: Final Clearance.",
                'stage' => 'stage3',
                'status' => 'Pending Clearance',
                'stage2By' => $adminFullName,
                'stage2Date' => date('M d, Y h:i A'),
                'stage2Remarks' => $finalRemarks
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => "Reservation {$ref} not found or no changes made."]);
        }
        exit;
    }

    // =========================================================================
    // STAGE 3: CLEARANCE (Director / Clearance Authority Final Approval)
    // =========================================================================
    if ($action === 'stage3' || $action === 'clearance' || $action === 'approve') {
        if (empty($ref)) {
            echo json_encode(['success' => false, 'message' => 'Reference number is required.']);
            exit;
        }

        $defaultRemarks = 'Final institutional clearance granted. Official permit and venue gate pass authorized.';
        $finalRemarks = !empty($remarks) ? $remarks : $defaultRemarks;

        $stmt = $db->prepare("UPDATE reservations SET 
            status = 'Approved',
            cleared_by = :user_id,
            cleared_at = NOW(),
            clearance_remarks = :remarks
            WHERE reference_no = :ref");
        
        $stmt->execute([
            ':user_id' => $adminUserId,
            ':remarks' => $finalRemarks,
            ':ref' => $ref
        ]);

        if ($stmt->rowCount() > 0) {
            $auditLog->action = 'STAGE3_CLEARANCE';
            $auditLog->details = "Granted Stage 3 final institutional clearance for reservation [{$ref}] by {$adminFullName}. Remarks: {$finalRemarks}";
            $auditLog->log();

            echo json_encode([
                'success' => true,
                'message' => "Stage 3 Final Clearance granted for {$ref}. Official permit issued.",
                'stage' => 'approved',
                'status' => 'Approved',
                'stage3By' => $adminFullName,
                'stage3Date' => date('M d, Y h:i A'),
                'stage3Remarks' => $finalRemarks
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => "Reservation {$ref} not found or no changes made."]);
        }
        exit;
    }

    // =========================================================================
    // REJECTION / DISAPPROVAL (Can be executed at any stage)
    // =========================================================================
    if ($action === 'reject' || $action === 'disapprove') {
        if (empty($ref)) {
            echo json_encode(['success' => false, 'message' => 'Reference number is required.']);
            exit;
        }

        $reasonPrefix = !empty($reason) ? "[{$reason}] " : '';
        $finalRemarks = $reasonPrefix . (!empty($remarks) ? $remarks : 'Request returned on administrative/operational grounds.');

        $stmt = $db->prepare("UPDATE reservations SET 
            status = 'Rejected',
            admin_remarks = :remarks
            WHERE reference_no = :ref");
        
        $stmt->execute([
            ':remarks' => $finalRemarks,
            ':ref' => $ref
        ]);

        if ($stmt->rowCount() > 0) {
            $auditLog->action = 'REJECT_RESERVATION';
            $auditLog->details = "Declined/Returned reservation [{$ref}] by {$adminFullName}. Reason: {$reason}. Remarks: {$finalRemarks}";
            $auditLog->log();

            echo json_encode([
                'success' => true,
                'message' => "Reservation {$ref} has been officially returned / declined.",
                'stage' => 'rejected',
                'status' => 'Rejected',
                'rejectionGround' => $reason,
                'rejectionNotes' => $finalRemarks
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => "Reservation {$ref} not found or no changes made."]);
        }
        exit;
    }

    // =========================================================================
    // BATCH / BULK ACTIONS
    // =========================================================================
    if ($action === 'bulk_endorse') {
        $refs = is_array($rawRefs) ? $rawRefs : explode(',', (string)$rawRefs);
        $refs = array_filter(array_map('trim', $refs));

        if (empty($refs)) {
            echo json_encode(['success' => false, 'message' => 'No reservations selected for batch endorsement.']);
            exit;
        }

        $placeholders = implode(',', array_fill(0, count($refs), '?'));
        $defaultRemarks = 'Batch endorsed by Division Head / Endorsing Officer.';
        $finalRemarks = !empty($remarks) ? $remarks : $defaultRemarks;

        $sql = "UPDATE reservations SET 
            status = 'Pending Recommendation',
            endorsed_by = ?,
            endorsed_at = NOW(),
            endorsement_remarks = ?
            WHERE reference_no IN ($placeholders)";
        
        $stmt = $db->prepare($sql);
        $params = array_merge([$adminUserId, $finalRemarks], $refs);
        $stmt->execute($params);

        $affectedCount = $stmt->rowCount();

        $auditLog->action = 'BULK_STAGE1_ENDORSE';
        $auditLog->details = "Batch endorsed {$affectedCount} reservations (" . implode(', ', $refs) . ") for Stage 2 Recommendation by {$adminFullName}.";
        $auditLog->log();

        echo json_encode([
            'success' => true,
            'message' => "Batch endorsement completed: {$affectedCount} requests forwarded to Stage 2.",
            'affected' => $affectedCount,
            'refs' => $refs,
            'stage1By' => $adminFullName,
            'stage1Date' => date('M d, Y h:i A')
        ]);
        exit;
    }

    if ($action === 'bulk_recommend') {
        $refs = is_array($rawRefs) ? $rawRefs : explode(',', (string)$rawRefs);
        $refs = array_filter(array_map('trim', $refs));

        if (empty($refs)) {
            echo json_encode(['success' => false, 'message' => 'No reservations selected for batch recommendation.']);
            exit;
        }

        $placeholders = implode(',', array_fill(0, count($refs), '?'));
        $defaultRemarks = 'Batch recommended by Facility / Administrative Officer.';
        $finalRemarks = !empty($remarks) ? $remarks : $defaultRemarks;

        $sql = "UPDATE reservations SET 
            status = 'Pending Clearance',
            recommended_by = ?,
            recommended_at = NOW(),
            recommendation_remarks = ?
            WHERE reference_no IN ($placeholders)";
        
        $stmt = $db->prepare($sql);
        $params = array_merge([$adminUserId, $finalRemarks], $refs);
        $stmt->execute($params);

        $affectedCount = $stmt->rowCount();

        $auditLog->action = 'BULK_STAGE2_RECOMMEND';
        $auditLog->details = "Batch recommended {$affectedCount} reservations (" . implode(', ', $refs) . ") for Stage 3 Clearance by {$adminFullName}.";
        $auditLog->log();

        echo json_encode([
            'success' => true,
            'message' => "Batch recommendation completed: {$affectedCount} requests forwarded to Stage 3.",
            'affected' => $affectedCount,
            'refs' => $refs,
            'stage2By' => $adminFullName,
            'stage2Date' => date('M d, Y h:i A')
        ]);
        exit;
    }

    if ($action === 'bulk_clearance' || $action === 'bulk_approve') {
        $refs = is_array($rawRefs) ? $rawRefs : explode(',', (string)$rawRefs);
        $refs = array_filter(array_map('trim', $refs));

        if (empty($refs)) {
            echo json_encode(['success' => false, 'message' => 'No reservations selected for batch clearance.']);
            exit;
        }

        $placeholders = implode(',', array_fill(0, count($refs), '?'));
        $defaultRemarks = 'Batch final institutional clearance granted.';
        $finalRemarks = !empty($remarks) ? $remarks : $defaultRemarks;

        $sql = "UPDATE reservations SET 
            status = 'Approved',
            cleared_by = ?,
            cleared_at = NOW(),
            clearance_remarks = ?
            WHERE reference_no IN ($placeholders)";
        
        $stmt = $db->prepare($sql);
        $params = array_merge([$adminUserId, $finalRemarks], $refs);
        $stmt->execute($params);

        $affectedCount = $stmt->rowCount();

        $auditLog->action = 'BULK_STAGE3_CLEARANCE';
        $auditLog->details = "Batch granted Stage 3 clearance for {$affectedCount} reservations (" . implode(', ', $refs) . ") by {$adminFullName}.";
        $auditLog->log();

        echo json_encode([
            'success' => true,
            'message' => "Batch final clearance granted: {$affectedCount} permits authorized.",
            'affected' => $affectedCount,
            'refs' => $refs,
            'stage3By' => $adminFullName,
            'stage3Date' => date('M d, Y h:i A')
        ]);
        exit;
    }

    echo json_encode(['success' => false, 'message' => 'Invalid action specified.']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
