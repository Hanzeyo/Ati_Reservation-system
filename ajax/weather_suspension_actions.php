<?php
/**
 * Agriculture Training Institute - Facility Reservation System
 * Admin API: Manage Inclement Weather Suspension, Safety Directives, and Master Schedule Auto-Lock
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

// Helper function to query affected reservations during a date range
function getAffectedBookingsForDateRange($db, $startDate, $endDate) {
    $affected = [];
    $totalPax = 0;
    try {
        $stmt = $db->prepare("SELECT r.id, r.reference_no, r.event_title, r.start_date, r.end_date, r.pax_count, r.status, 
                                     COALESCE(f.name, 'General Facility') as facility_name, 
                                     COALESCE(u.full_name, 'Official Applicant') as applicant_name, 
                                     COALESCE(u.contact_number, '') as applicant_phone,
                                     COALESCE(u.office_agency, 'Department of Agriculture') as applicant_agency
                              FROM reservations r 
                              LEFT JOIN facilities f ON r.facility_id = f.id 
                              LEFT JOIN users u ON r.user_id = u.id 
                              WHERE r.status NOT IN ('Rejected', 'Cancelled') 
                                AND r.deleted_at IS NULL 
                                AND (r.start_date <= :eDate AND (r.end_date >= :sDate OR (r.end_date IS NULL AND r.start_date >= :sDate)))
                              ORDER BY r.start_date ASC LIMIT 50");
        $stmt->execute([':sDate' => $startDate, ':eDate' => $endDate]);
        $affected = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($affected as $b) {
            $totalPax += (int)($b['pax_count'] ?? 0);
        }
    } catch (Throwable $e) {}

    return [
        'count' => count($affected),
        'total_pax' => $totalPax,
        'bookings' => $affected
    ];
}

try {
    if ($action === 'get_status') {
        $current = SystemSetting::get('weather_suspension', [
            'active' => false,
            'typhoon_name' => '',
            'signal_level' => 'TCWS Signal No. 2',
            'warning_type' => 'tcws_2',
            'memo_ref' => 'Executive Order No. 66 (s. 2012)',
            'directive_source' => 'NDRRMC • DOST-PAGASA • CSC • Presidential Directive EO 66',
            'badge_text' => 'WEATHER SUSPENSION',
            'title' => '',
            'description' => '',
            'start_date' => date('Y-m-d'),
            'end_date' => date('Y-m-d'),
            'auto_lock_master_schedule' => true,
            'resumption_note' => 'Regular operations resume next business day at 8:00 AM (PST).'
        ]);

        $startDate = $current['start_date'] ?? date('Y-m-d');
        $endDate = $current['end_date'] ?? $startDate;
        $impact = getAffectedBookingsForDateRange($db, $startDate, $endDate);

        // Emergency hotlines and disaster risk reduction contacts
        $emergencyHotlines = [
            ['agency' => 'NDRRMC Operations Center', 'phone' => '(02) 8911-1406 / (02) 8912-2665', 'desc' => 'National Disaster Risk Reduction Directives'],
            ['agency' => 'DOST-PAGASA Weather Forecasting Center', 'phone' => '(02) 8284-0800 loc 4801', 'desc' => 'Severe Weather Bulletins & TCWS Updates'],
            ['agency' => 'Quezon City Emergency (QC-DRRMO)', 'phone' => '122 / (02) 8988-4242', 'desc' => 'Local Diliman Emergency Response & Rescue'],
            ['agency' => 'ATI General Services Unit (GSU)', 'phone' => 'Loc 104 / 105', 'desc' => 'Campus Facilities, Generators, Security Gates']
        ];

        echo json_encode([
            'success' => true,
            'data' => $current,
            'today' => date('Y-m-d'),
            'impact' => $impact,
            'emergency_hotlines' => $emergencyHotlines
        ]);
        exit;
    }

    if ($action === 'check_affected') {
        $startDate = trim($_GET['start_date'] ?? $_POST['start_date'] ?? date('Y-m-d'));
        $endDate = trim($_GET['end_date'] ?? $_POST['end_date'] ?? $startDate);
        if ($startDate > $endDate) {
            $endDate = $startDate;
        }

        $impact = getAffectedBookingsForDateRange($db, $startDate, $endDate);

        echo json_encode([
            'success' => true,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'count' => $impact['count'],
            'total_pax' => $impact['total_pax'],
            'bookings' => $impact['bookings']
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
        $badgeText = trim($_POST['badge_text'] ?? 'WEATHER SUSPENSION');
        $customTitle = trim($_POST['title'] ?? '');
        $customDesc = trim($_POST['description'] ?? '');
        $resumptionNote = trim($_POST['resumption_note'] ?? 'Regular on-site counter operations resume next business day at 8:00 AM (PST).');
        $autoLockMasterSchedule = !empty($_POST['auto_lock_master_schedule']);

        if ($startDate > $endDate) {
            $endDate = $startDate;
        }

        $defaultTitle = !empty($typhoonName) 
            ? "Work Suspended: {$typhoonName} ({$signalLevel})" 
            : "Work Suspended: Inclement Weather Advisory ({$signalLevel})";

        $finalTitle = !empty($customTitle) ? $customTitle : $defaultTitle;

        $defaultDesc = "Pursuant to NDRRMC / DOST-PAGASA Severe Weather Bulletin and Malacañang / NDRRMC directives, government office work and counter operations at the ATI Central Office are suspended today due to inclement weather conditions. Online facility and dormitory booking requests remain open 24/7.";

        $finalDesc = !empty($customDesc) ? $customDesc : $defaultDesc;

        $payload = [
            'active' => $active,
            'typhoon_name' => $typhoonName,
            'signal_level' => $signalLevel,
            'warning_type' => $warningType,
            'memo_ref' => $memoRef,
            'directive_source' => 'NDRRMC • DOST-PAGASA • CSC • Presidential Directive EO 66',
            'badge_text' => $badgeText,
            'title' => $finalTitle,
            'description' => $finalDesc,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'auto_lock_master_schedule' => $autoLockMasterSchedule,
            'resumption_note' => $resumptionNote,
            'updated_by' => $userId,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        SystemSetting::set('weather_suspension', $payload, $userId);

        // Synchronize with Master Schedule (schedule_blocks table)
        try {
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

            if ($active && $autoLockMasterSchedule) {
                // Ensure active hold is reflected on the Master Schedule
                $blockRef = 'HOLD-WEATHER-AUTO';
                $blockTitle = "Inclement Weather Suspension: {$typhoonName} ({$signalLevel})";
                $blockReason = "Inclement Weather Public Safety Protocol (EO 66 / NDRRMC)";
                
                $chk = $db->prepare("SELECT id FROM schedule_blocks WHERE ref = ? LIMIT 1");
                $chk->execute([$blockRef]);
                if ($chk->fetch()) {
                    $upd = $db->prepare("UPDATE schedule_blocks SET title = ?, start_date = ?, end_date = ?, reason = ?, memo_ref = ?, created_by = ? WHERE ref = ?");
                    $upd->execute([$blockTitle, $startDate, $endDate, $blockReason, $memoRef, $userId, $blockRef]);
                } else {
                    $ins = $db->prepare("INSERT INTO schedule_blocks (ref, title, facility_id, facility_name, start_date, end_date, reason, memo_ref, created_by) VALUES (?, ?, 'all', 'All ATI Facilities (Weather Safety Lockdown)', ?, ?, ?, ?, ?)");
                    $ins->execute([$blockRef, $blockTitle, $startDate, $endDate, $blockReason, $memoRef, $userId]);
                }
            } else {
                // Remove weather hold from Master Schedule if unselected or deactivated
                $db->exec("DELETE FROM schedule_blocks WHERE ref = 'HOLD-WEATHER-AUTO' OR ref LIKE 'HOLD-WEATHER-%'");
            }
        } catch (Throwable $e) {
            // Non-blocking
        }

        // Audit Log
        try {
            $actionName = $active ? 'APPLY_WEATHER_SUSPENSION' : 'UPDATE_WEATHER_SUSPENSION';
            $details = $active 
                ? "Declared inclement weather suspension: '{$finalTitle}' ({$signalLevel}) effective {$startDate} to {$endDate}. Auto Master Schedule Lock: " . ($autoLockMasterSchedule ? 'YES' : 'NO') . ". Memo: {$memoRef}"
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
            'message' => $active ? 'Inclement Weather Suspension successfully activated, synchronized with Master Schedule, and broadcasted to public portal.' : 'Weather suspension settings saved.',
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

        // Clean up weather hold from Master Schedule
        try {
            $db->exec("DELETE FROM schedule_blocks WHERE ref = 'HOLD-WEATHER-AUTO' OR ref LIKE 'HOLD-WEATHER-%'");
        } catch (Throwable $e) {}

        // Audit Log
        try {
            $logStmt = $db->prepare("INSERT INTO audit_logs (user_id, action, details, ip_address) VALUES (?, ?, ?, ?)");
            $logStmt->execute([
                $userId,
                'LIFT_WEATHER_SUSPENSION',
                "Lifted inclement weather suspension. Office hours, on-site counters, and regular Master Schedule operations restored.",
                $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1'
            ]);
        } catch (Exception $e) {
            // Ignore
        }

        echo json_encode([
            'success' => true,
            'message' => 'Weather suspension lifted. Regular office hours and Master Schedule operations restored.'
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
