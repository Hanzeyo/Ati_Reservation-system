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

// Redirect to home if user is not an admin
if (!in_array($_SESSION['role'], ['admin', 'super_admin'])) {
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
