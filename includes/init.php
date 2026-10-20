<?php
// Ensure system timezone is set to Philippine Standard Time (PST / Asia/Manila, UTC+8)
date_default_timezone_set('Asia/Manila');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../classes/BaseModel.php';
require_once __DIR__ . '/../classes/User.php';
require_once __DIR__ . '/../classes/Facility.php';
require_once __DIR__ . '/../classes/Reservation.php';
require_once __DIR__ . '/../classes/AuditLog.php';

$database = new Database();
$db = $database->getConnection();

$user = new User($db);
$facility = new Facility($db);
$reservation = new Reservation($db);
$auditLog = new AuditLog($db);

// Helper to check if current logged-in user is an admin
if (!function_exists('isAdmin')) {
    function isAdmin() {
        if (!isset($_SESSION['role'])) return false;
        $r = strtolower($_SESSION['role']);
        return in_array($r, [
            'admin', 'super_admin', 'director', 'division_chief', 
            'clearance', 'recommendation', 'endorsement', 'recommender', 'endorser'
        ]);
    }
}

// Calculate dynamic reservation count for the logged-in user
$currentUserResCount = 0;
if (isset($_SESSION['user_id']) && isset($db)) {
    try {
        $stmtCount = $db->prepare("SELECT COUNT(*) FROM reservations WHERE user_id = :uid AND deleted_at IS NULL");
        $stmtCount->execute([':uid' => (int)$_SESSION['user_id']]);
        $currentUserResCount = (int)$stmtCount->fetchColumn();
    } catch (Exception $e) {
        $currentUserResCount = 0;
    }
}
?>
