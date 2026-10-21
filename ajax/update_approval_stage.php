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
$rawUserRole = strtolower($_SESSION['role'] ?? '');

// Normalize Role
if (in_array($rawUserRole, ['endorsement', 'endorser'])) {
    $normalizedRole = 'endorsement';
} elseif (in_array($rawUserRole, ['recommendation', 'recommender', 'division_chief'])) {
    $normalizedRole = 'recommendation';
} elseif (in_array($rawUserRole, ['clearance', 'director'])) {
    $normalizedRole = 'clearance';
} else {
    $normalizedRole = 'super_admin';
}

$action = trim($_POST['action'] ?? '');
$ref = trim($_POST['ref'] ?? '');
$remarks = trim($_POST['remarks'] ?? '');
$reason = trim($_POST['reason'] ?? '');
$rawRefs = $_POST['refs'] ?? [];

$ipAddress = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';

// Strict Role-Based Permission Validation
if (($action === 'stage1' || $action === 'endorse' || $action === 'bulk_endorse') && !in_array($normalizedRole, ['endorsement', 'super_admin'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized. Stage 1 Endorsement can only be performed by an Endorsement Officer.']);
    exit;
}

if (($action === 'stage2' || $action === 'recommend' || $action === 'bulk_recommend') && !in_array($normalizedRole, ['recommendation', 'super_admin'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized. Stage 2 Recommendation can only be performed by a Recommending Officer.']);
    exit;
}

if (($action === 'stage3' || $action === 'clearance' || $action === 'approve' || $action === 'bulk_clearance') && !in_array($normalizedRole, ['clearance', 'super_admin'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized. Final Clearance can only be granted by the Clearance Authority.']);
    exit;
}

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

        // Sequence Validation: Must be endorsed first
        $chkStmt = $db->prepare("SELECT status, recommendation_doc_path FROM reservations WHERE reference_no = :ref LIMIT 1");
        $chkStmt->execute([':ref' => $ref]);
        $currRow = $chkStmt->fetch(PDO::FETCH_ASSOC);
        if (!$currRow) {
            echo json_encode(['success' => false, 'message' => "Reservation {$ref} not found."]);
            exit;
        }
        $currSt = $currRow['status'];
        if (in_array($currSt, ['Pending', 'Pending Endorsement', 'Awaiting Endorsement'])) {
            echo json_encode(['success' => false, 'message' => 'This reservation cannot be recommended yet as it requires prior endorsement by an Endorsement Officer (Stage 1).']);
            exit;
        }

        // Signed Document Upload Handling
        $signedDocPath = $currRow['recommendation_doc_path'] ?? null;
        if (isset($_FILES['signed_doc']) && $_FILES['signed_doc']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../uploads/documents/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $fileTmp = $_FILES['signed_doc']['tmp_name'];
            $fileOrig = $_FILES['signed_doc']['name'];
            $fileExt = strtolower(pathinfo($fileOrig, PATHINFO_EXTENSION));
            $allowedExts = ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx'];

            if (!in_array($fileExt, $allowedExts)) {
                echo json_encode(['success' => false, 'message' => 'Invalid file format. Please upload a PDF, PNG, JPG, or DOC document.']);
                exit;
            }

            if ($_FILES['signed_doc']['size'] > 15 * 1024 * 1024) {
                echo json_encode(['success' => false, 'message' => 'File size exceeds the allowed limit. Maximum allowed size is 15MB.']);
                exit;
            }

            $cleanRef = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $ref);
            $newFileName = 'signed_rec_' . $cleanRef . '_' . time() . '.' . $fileExt;
            $destPath = $uploadDir . $newFileName;

            if (move_uploaded_file($fileTmp, $destPath)) {
                $signedDocPath = 'uploads/documents/' . $newFileName;
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to save the uploaded signed document to storage.']);
                exit;
            }
        } elseif (empty($signedDocPath)) {
            echo json_encode(['success' => false, 'message' => 'A signed routing document must be uploaded before forwarding to Stage 3 Clearance.']);
            exit;
        }

        $defaultRemarks = 'Verified equipment, schedules, and logistical feasibility. Recommended for Stage 3 final clearance.';
        $finalRemarks = !empty($remarks) ? $remarks : $defaultRemarks;

        $stmt = $db->prepare("UPDATE reservations SET 
            status = 'Pending Clearance',
            recommended_by = :user_id,
            recommended_at = NOW(),
            recommendation_remarks = :remarks,
            recommendation_doc_path = :doc_path
            WHERE reference_no = :ref");
        
        $stmt->execute([
            ':user_id' => $adminUserId,
            ':remarks' => $finalRemarks,
            ':doc_path' => $signedDocPath,
            ':ref' => $ref
        ]);

        if ($stmt->rowCount() > 0) {
            $auditLog->action = 'STAGE2_RECOMMENDATION';
            $auditLog->details = "Issued Stage 2 Recommendation for reservation [{$ref}] by {$adminFullName}. Attached signed document: {$signedDocPath}. Remarks: {$finalRemarks}";
            $auditLog->log();

            echo json_encode([
                'success' => true,
                'message' => "Stage 2 Recommendation recorded for {$ref}. Signed document attached and forwarded to Stage 3: Final Clearance.",
                'stage' => 'stage3',
                'status' => 'Pending Clearance',
                'stage2By' => $adminFullName,
                'stage2Date' => date('M d, Y h:i A'),
                'stage2Remarks' => $finalRemarks,
                'recommendationDocPath' => $signedDocPath
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

        // Sequence Validation: Must be endorsed AND recommended before final clearance
        $chkStmt = $db->prepare("SELECT status, clearance_doc_path FROM reservations WHERE reference_no = :ref LIMIT 1");
        $chkStmt->execute([':ref' => $ref]);
        $currRow = $chkStmt->fetch(PDO::FETCH_ASSOC);
        if (!$currRow) {
            echo json_encode(['success' => false, 'message' => "Reservation {$ref} not found."]);
            exit;
        }
        $currSt = $currRow['status'];
        if (in_array($currSt, ['Pending', 'Pending Endorsement', 'Awaiting Endorsement'])) {
            echo json_encode(['success' => false, 'message' => 'This reservation cannot be approved yet as it requires prior endorsement by an Endorsement Officer (Stage 1).']);
            exit;
        }
        if (in_array($currSt, ['Pending Recommendation', 'Awaiting Recommendation'])) {
            echo json_encode(['success' => false, 'message' => 'This reservation cannot be approved yet as it requires prior review and recommendation by a Recommending Officer (Stage 2).']);
            exit;
        }

        // Signed Clearance Document Upload Handling
        $clearanceDocPath = $currRow['clearance_doc_path'] ?? null;
        if (isset($_FILES['signed_doc']) && $_FILES['signed_doc']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../uploads/documents/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $fileTmp = $_FILES['signed_doc']['tmp_name'];
            $fileOrig = $_FILES['signed_doc']['name'];
            $fileExt = strtolower(pathinfo($fileOrig, PATHINFO_EXTENSION));
            $allowedExts = ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx'];

            if (!in_array($fileExt, $allowedExts)) {
                echo json_encode(['success' => false, 'message' => 'Invalid file format. Please upload a PDF, PNG, JPG, or DOC document.']);
                exit;
            }

            if ($_FILES['signed_doc']['size'] > 15 * 1024 * 1024) {
                echo json_encode(['success' => false, 'message' => 'File size exceeds the allowed limit. Maximum allowed size is 15MB.']);
                exit;
            }

            $cleanRef = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $ref);
            $newFileName = 'signed_clr_' . $cleanRef . '_' . time() . '.' . $fileExt;
            $destPath = $uploadDir . $newFileName;

            if (move_uploaded_file($fileTmp, $destPath)) {
                $clearanceDocPath = 'uploads/documents/' . $newFileName;
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to save the uploaded signed document to storage.']);
                exit;
            }
        } elseif (empty($clearanceDocPath)) {
            echo json_encode(['success' => false, 'message' => 'A signed clearance or approved permit document must be uploaded before finalizing approval.']);
            exit;
        }

        $defaultRemarks = 'Final institutional clearance granted. Official permit authorized with signed document attached.';
        $finalRemarks = !empty($remarks) ? $remarks : $defaultRemarks;

        $stmt = $db->prepare("UPDATE reservations SET 
            status = 'Approved',
            cleared_by = :user_id,
            cleared_at = NOW(),
            clearance_remarks = :remarks,
            clearance_doc_path = :doc_path
            WHERE reference_no = :ref");
        
        $stmt->execute([
            ':user_id' => $adminUserId,
            ':remarks' => $finalRemarks,
            ':doc_path' => $clearanceDocPath,
            ':ref' => $ref
        ]);

        if ($stmt->rowCount() > 0) {
            $auditLog->action = 'STAGE3_CLEARANCE';
            $auditLog->details = "Granted Stage 3 final institutional clearance for reservation [{$ref}] by {$adminFullName}. Attached signed clearance document: {$clearanceDocPath}. Remarks: {$finalRemarks}";
            $auditLog->log();

            echo json_encode([
                'success' => true,
                'message' => "Stage 3 Final Clearance granted for {$ref}. Signed document attached and reservation officially approved.",
                'stage' => 'approved',
                'status' => 'Approved',
                'stage3By' => $adminFullName,
                'stage3Date' => date('M d, Y h:i A'),
                'stage3Remarks' => $finalRemarks,
                'clearanceDocPath' => $clearanceDocPath
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

        // Verify current status and ensure only authorized role for this stage (or super_admin) can decline
        $chkStmt = $db->prepare("SELECT status FROM reservations WHERE reference_no = :ref LIMIT 1");
        $chkStmt->execute([':ref' => $ref]);
        $currSt = $chkStmt->fetchColumn();
        if (!$currSt) {
            echo json_encode(['success' => false, 'message' => "Reservation {$ref} not found."]);
            exit;
        }

        if (in_array($currSt, ['Approved', 'Completed'])) {
            echo json_encode(['success' => false, 'message' => "This reservation cannot be declined because it has already been officially approved."]);
            exit;
        }
        if (in_array($currSt, ['Rejected', 'Cancelled'])) {
            echo json_encode(['success' => false, 'message' => "This reservation has already been declined or cancelled."]);
            exit;
        }

        $isStage1 = in_array($currSt, ['Pending', 'Pending Endorsement', 'Awaiting Endorsement']);
        $isStage2 = in_array($currSt, ['Pending Recommendation', 'Awaiting Recommendation']);
        $isStage3 = in_array($currSt, ['Pending Clearance', 'Awaiting Clearance']);

        if ($isStage1 && !in_array($normalizedRole, ['endorsement', 'super_admin'])) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized. Only an Endorsement Officer or Super Administrator may decline a reservation during Stage 1 (Awaiting Endorsement).']);
            exit;
        }
        if ($isStage2 && !in_array($normalizedRole, ['recommendation', 'super_admin'])) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized. Only a Recommending Officer or Super Administrator may decline a reservation during Stage 2 (Awaiting Recommendation).']);
            exit;
        }
        if ($isStage3 && !in_array($normalizedRole, ['clearance', 'super_admin'])) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized. Only the Clearance Authority or Super Administrator may decline a reservation during Stage 3 (Awaiting Clearance).']);
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
            WHERE reference_no IN ($placeholders)
              AND status IN ('Pending', 'Pending Endorsement', 'Awaiting Endorsement')";
        
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
            WHERE reference_no IN ($placeholders)
              AND status IN ('Pending Recommendation', 'Awaiting Recommendation')";
        
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
            WHERE reference_no IN ($placeholders)
              AND status IN ('Pending Clearance', 'Awaiting Clearance')";
        
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
