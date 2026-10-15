<?php
// Ensure session is started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Redirect to login if user is not authenticated
if (!isset($_SESSION['user_id'])) {
    header("Location: auth.php?tab=login");
    exit();
}

// Redirect to home if user is not an authorized administrator or governance officer
$rawUserRole = strtolower($_SESSION['role'] ?? '');
if (!in_array($rawUserRole, [
    'admin', 'super_admin', 'director', 'division_chief', 
    'clearance', 'recommendation', 'endorsement', 'recommender', 'endorser'
])) {
    header("Location: home.php");
    exit();
}

// Normalize Admin Role
if (in_array($rawUserRole, ['endorsement', 'endorser'])) {
    $currentAdminRole = 'endorsement';
    $roleBadgeLabel = 'Endorsement';
    $roleOfficerTitle = 'Endorsement Officer';
    $roleOfficerSubtitle = 'Dorm & Facilities In-Charge';
    $roleInitials = 'EO';
} elseif (in_array($rawUserRole, ['recommendation', 'recommender', 'division_chief'])) {
    $currentAdminRole = 'recommendation';
    $roleBadgeLabel = 'Recommendation';
    $roleOfficerTitle = 'Recommending Officer';
    $roleOfficerSubtitle = 'Admin & Logistics Division';
    $roleInitials = 'RO';
} elseif (in_array($rawUserRole, ['clearance', 'director'])) {
    $currentAdminRole = 'clearance';
    $roleBadgeLabel = 'Clearance';
    $roleOfficerTitle = 'Clearance Authority';
    $roleOfficerSubtitle = 'Office of the Director';
    $roleInitials = 'DIR';
} else {
    $currentAdminRole = 'super_admin';
    $roleBadgeLabel = 'Super Admin';
    $roleOfficerTitle = 'System Administrator';
    $roleOfficerSubtitle = 'Executive Directorate';
    $roleInitials = 'SA';
}

// Role Capabilities Matrix
$canManageUsers = ($currentAdminRole === 'super_admin');
$canViewAuditLogs = ($currentAdminRole === 'super_admin');
$canManageFacilities = in_array($currentAdminRole, ['super_admin', 'endorsement']);

// Strict Page-Level Protection
// Non-Super Admins are blocked from accessing user management and system audit trail
$currentAdminScript = basename($_SERVER['PHP_SELF'] ?? '');
if (in_array($currentAdminScript, ['admin_users.php', 'admin_audit.php']) && !$canManageUsers) {
    header("Location: admin_dashboard.php?error=unauthorized");
    exit();
}

// Helper to get initials
if (!function_exists('getUserInitials')) {
    function getUserInitials($name) {
        $words = preg_split('/\s+/', trim($name));
        $initials = '';
        foreach ($words as $w) {
            if (!empty($w)) {
                $initials .= strtoupper($w[0]);
            }
        }
        return substr($initials, 0, 2) ?: 'AD';
    }
}
?>
