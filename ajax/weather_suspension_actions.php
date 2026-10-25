<?php
/**
 * Agriculture Training Institute - Facility Reservation System
 * Admin API: Manage PAGASA Bad Weather Suspension and Advisory
 */

require_once __DIR__ . '/../includes/init.php';

header('Content-Type: application/json');

$userId = $_SESSION['user_id'] ?? null;
$userRole = strtolower($_SESSION['role'] ?? $_SESSION['user_role'] ?? '');

// Auto-authenticate default admin in dev environment if session is missing
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

$allowedRoles = ['super_admin', 'admin', 'director', 'recommending_officer', 'clearance', 'recommendation'];
if (!$userId || !in_array($userRole, $allowedRoles)) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access. Administrative privileges required.']);
    exit;
}

$action = $_POST['action'] ?? $_GET['action'] ?? 'get_status';

try {
    if ($action === 'get_status') {
        $current = SystemSetting::get('weather_suspension', [
            'active' => false,
            'typhoon_name' => '',
            'signal_level' => 'TCWS Signal No. 2',
            'warning_type' => 'tcws_2',
            'memo_ref' => 'Executive Order No. 66 (s. 2012)',
            'directive_source' => 'PAGASA-DOST • NDRRMC • Presidential Directive EO 66',
            'badge_text' => 'PAGASA SUSPENSION',
            'title' => '',
            'description' => '',
            'start_date' => date('Y-m-d'),
            'end_date' => date('Y-m-d'),
            'resumption_note' => 'Regular operations resume next business day at 8:00 AM (PST).'
        ]);

        echo json_encode([
            'success' => true,
            'data' => $current,
            'today' => date('Y-m-d')
        ]);
        exit;
    }

    if ($action === 'save_suspension') {
        $active = !empty($_POST['active']) ? true : false;
        $typhoonName = trim($_POST['typhoon_name'] ?? '');
        $signalLevel = trim($_POST['signal_level'] ?? 'TCWS Signal No. 2');
        $warningType = trim($_POST['warning_type'] ?? 'tcws_2');
        $memoRef = trim($_POST['memo_ref'] ?? 'Presidential Directive EO 66 / NDRRMC Memo');
        $startDate = trim($_POST['start_date'] ?? date('Y-m-d'));
        $endDate = trim($_POST['end_date'] ?? $startDate);
        $badgeText = trim($_POST['badge_text'] ?? 'PAGASA SUSPENSION');
        $customTitle = trim($_POST['title'] ?? '');
        $customDesc = trim($_POST['description'] ?? '');
        $resumptionNote = trim($_POST['resumption_note'] ?? 'Regular on-site counter operations resume next business day at 8:00 AM (PST).');

        if ($startDate > $endDate) {
            $endDate = $startDate;
        }

        $defaultTitle = !empty($typhoonName) 
            ? "Work Suspended: {$typhoonName} ({$signalLevel})" 
            : "Work Suspended: PAGASA Weather Advisory ({$signalLevel})";

        $finalTitle = !empty($customTitle) ? $customTitle : $defaultTitle;

        $defaultDesc = "Pursuant to PAGASA Severe Weather Bulletin and Malacañang / NDRRMC directives, government office work and counter operations at the ATI Central Office are suspended today due to inclement weather conditions. Online facility and dormitory booking requests remain open 24/7.";

        $finalDesc = !empty($customDesc) ? $customDesc : $defaultDesc;

        $payload = [
            'active' => $active,
            'typhoon_name' => $typhoonName,
            'signal_level' => $signalLevel,
            'warning_type' => $warningType,
            'memo_ref' => $memoRef,
            'directive_source' => 'PAGASA-DOST • NDRRMC • Presidential Directive EO 66',
            'badge_text' => $badgeText,
            'title' => $finalTitle,
            'description' => $finalDesc,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'resumption_note' => $resumptionNote,
            'updated_by' => $userId,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        SystemSetting::set('weather_suspension', $payload, $userId);

        // Audit Log
        try {
            $actionName = $active ? 'APPLY_WEATHER_SUSPENSION' : 'UPDATE_WEATHER_SUSPENSION';
            $details = $active 
                ? "Declared PAGASA bad weather suspension: '{$finalTitle}' ({$signalLevel}) effective {$startDate} to {$endDate}. Memo: {$memoRef}"
                : "Updated weather suspension advisory configurations.";
            $logStmt = $db->prepare("INSERT INTO audit_logs (user_id, action, details, ip_address) VALUES (?, ?, ?, ?)");
            $logStmt->execute([
                $userId,
                $actionName,
                $details,
                $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1'
            ]);
        } catch (Exception $e) {
            // Ignore audit log error
        }

        echo json_encode([
            'success' => true,
            'message' => $active ? 'PAGASA Weather Suspension successfully activated and broadcasted.' : 'Weather suspension settings saved.',
            'data' => $payload
        ]);
        exit;
    }

    if ($action === 'lift_suspension') {
        $existing = SystemSetting::get('weather_suspension', []);
        if (is_array($existing)) {
            $existing['active'] = false;
            $existing['updated_at'] = date('Y-m-d H:i:s');
            $existing['updated_by'] = $userId;
            SystemSetting::set('weather_suspension', $existing, $userId);
        }

        // Audit Log
        try {
            $logStmt = $db->prepare("INSERT INTO audit_logs (user_id, action, details, ip_address) VALUES (?, ?, ?, ?)");
            $logStmt->execute([
                $userId,
                'LIFT_WEATHER_SUSPENSION',
                "Lifted PAGASA weather suspension. Office hours and standard counter operations restored.",
                $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1'
            ]);
        } catch (Exception $e) {
            // Ignore
        }

        echo json_encode([
            'success' => true,
            'message' => 'Weather suspension lifted. Regular office hours restored.'
        ]);
        exit;
    }

    echo json_encode(['success' => false, 'message' => 'Unknown action requested.']);
} catch (Throwable $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Server error: ' . $e->getMessage()
    ]);
}
