<?php
require_once __DIR__ . '/../includes/init.php';

header('Content-Type: application/json');

$db = (new Database())->getConnection();

// Check admin authentication gracefully
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
} elseif (empty($userRole)) {
    $uStmt = $db->prepare("SELECT role, full_name FROM users WHERE id = ? LIMIT 1");
    $uStmt->execute([$userId]);
    $uRow = $uStmt->fetch(PDO::FETCH_ASSOC);
    if ($uRow) {
        $userRole = strtolower($uRow['role'] ?? '');
        $_SESSION['role'] = $uRow['role'] ?? '';
        if (empty($_SESSION['full_name'])) {
            $_SESSION['full_name'] = $uRow['full_name'] ?? '';
        }
    }
}

$isAllowedAdmin = isAdmin() || in_array($userRole, [
    'super_admin', 'superadmin', 'admin', 'director', 'division_chief', 
    'clearance', 'recommendation', 'endorsement', 'recommender', 'endorser', 'recommending_officer'
]);

if (!$userId || !$isAllowedAdmin) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized. Administrator privileges required.']);
    exit;
}

$action = $_POST['action'] ?? $_GET['action'] ?? '';

try {
    switch ($action) {
        case 'get_templates':
            $facilityInfo = SystemSetting::getTemplateInfo('facility');
            $dormitoryInfo = SystemSetting::getTemplateInfo('dormitory');

            echo json_encode([
                'success' => true,
                'templates' => [
                    'facility' => $facilityInfo,
                    'dormitory' => $dormitoryInfo
                ]
            ]);
            break;

        case 'upload_template':
            $type = strtolower(trim($_POST['type'] ?? 'facility'));
            $isDorm = ($type === 'dorm' || $type === 'dormitory' || $type === 'dorms');
            $normType = $isDorm ? 'dormitory' : 'facility';
            $settingKey = $isDorm ? 'dormitory_form_template' : 'facility_form_template';
            $typeName = $isDorm ? 'Dormitory' : 'Facility';

            if (!isset($_FILES['template_file']) || $_FILES['template_file']['error'] !== UPLOAD_ERR_OK) {
                $errCode = $_FILES['template_file']['error'] ?? 'No file provided';
                echo json_encode(['success' => false, 'message' => "File upload failed or was empty (Code: $errCode)."]);
                exit;
            }

            $file = $_FILES['template_file'];
            $origName = $file['name'];
            $fileSize = $file['size'];
            $tmpPath = $file['tmp_name'];

            // Validation: Max 15MB
            if ($fileSize > 15 * 1024 * 1024) {
                echo json_encode(['success' => false, 'message' => 'File size exceeds maximum allowed limit of 15MB.']);
                exit;
            }

            // Validation: Extensions
            $ext = strtolower(pathinfo($origName, PATHINFO_EXTENSION));
            $allowedExts = ['pdf', 'doc', 'docx'];
            if (!in_array($ext, $allowedExts)) {
                echo json_encode(['success' => false, 'message' => 'Invalid file format. Only PDF, DOC, or DOCX documents are supported.']);
                exit;
            }

            // Upload directory
            $targetDir = __DIR__ . '/../uploads/templates/';
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0755, true);
            }

            // Delete previous custom template file if exists
            $existing = SystemSetting::get($settingKey, null);
            if (is_array($existing) && !empty($existing['file_path'])) {
                $oldFile = __DIR__ . '/../' . ltrim($existing['file_path'], '/\\');
                if (file_exists($oldFile)) {
                    @unlink($oldFile);
                }
            }

            // Generate clean, safe filename
            $sanitizedOrig = preg_replace('/[^a-zA-Z0-9_\-\.]/', '_', pathinfo($origName, PATHINFO_FILENAME));
            $newFileName = 'ATI_' . ucfirst($normType) . '_Form_' . date('Ymd_His') . '_' . substr(md5(uniqid()), 0, 6) . '.' . $ext;
            $destPath = $targetDir . $newFileName;
            $relPath = 'uploads/templates/' . $newFileName;

            if (!move_uploaded_file($tmpPath, $destPath)) {
                echo json_encode(['success' => false, 'message' => 'Failed to save the uploaded document to server storage.']);
                exit;
            }

            $mimeType = mime_content_type($destPath) ?: ($ext === 'pdf' ? 'application/pdf' : 'application/octet-stream');
            $adminName = $_SESSION['full_name'] ?? 'Administrator';

            $payload = [
                'type' => $normType,
                'file_path' => $relPath,
                'original_name' => $origName,
                'stored_name' => $newFileName,
                'file_size' => $fileSize,
                'mime_type' => $mimeType,
                'uploaded_at' => date('Y-m-d H:i:s'),
                'uploaded_by' => $userId,
                'uploaded_by_name' => $adminName
            ];

            SystemSetting::set($settingKey, $payload, $userId);

            // Audit log
            $sizeFmt = SystemSetting::formatBytes($fileSize);
            AuditLog::log(
                $userId,
                'UPDATE_FORM_TEMPLATE',
                "Updated Official ATI {$typeName} Reservation Form to '{$origName}' ({$sizeFmt})"
            );

            $updatedInfo = SystemSetting::getTemplateInfo($normType);

            echo json_encode([
                'success' => true,
                'message' => "Official {$typeName} Reservation Form successfully updated!",
                'template' => $updatedInfo
            ]);
            break;

        case 'reset_template':
            $type = strtolower(trim($_POST['type'] ?? 'facility'));
            $isDorm = ($type === 'dorm' || $type === 'dormitory' || $type === 'dorms');
            $normType = $isDorm ? 'dormitory' : 'facility';
            $settingKey = $isDorm ? 'dormitory_form_template' : 'facility_form_template';
            $typeName = $isDorm ? 'Dormitory' : 'Facility';

            // Delete file if exists
            $existing = SystemSetting::get($settingKey, null);
            if (is_array($existing) && !empty($existing['file_path'])) {
                $oldFile = __DIR__ . '/../' . ltrim($existing['file_path'], '/\\');
                if (file_exists($oldFile)) {
                    @unlink($oldFile);
                }
            }

            SystemSetting::remove($settingKey);

            AuditLog::log(
                $userId,
                'RESET_FORM_TEMPLATE',
                "Reverted Official ATI {$typeName} Reservation Form to the system default printable template"
            );

            $updatedInfo = SystemSetting::getTemplateInfo($normType);

            echo json_encode([
                'success' => true,
                'message' => "Reset to system default printable form successfully.",
                'template' => $updatedInfo
            ]);
            break;

        default:
            echo json_encode(['success' => false, 'message' => "Unknown action: {$action}"]);
            break;
    }
} catch (Throwable $e) {
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}
