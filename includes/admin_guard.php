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
$userRole = strtolower($_SESSION['role'] ?? '');
if (!in_array($userRole, [
    'admin', 'super_admin', 'director', 'division_chief', 
    'clearance', 'recommendation', 'endorsement', 'recommender', 'endorser'
])) {
    header("Location: home.php");
    exit();
}

// Helper to get initials
if (!function_exists('getUserInitials')) {
    function getUserInitials($name) {
        $words = explode(' ', $name);
        $initials = '';
        foreach ($words as $word) {
            if (!empty($word)) {
                $initials .= strtoupper($word[0]);
            }
        }
        return substr($initials, 0, 2);
    }
}
?>
