<?php
/**
 * Agriculture Training Institute - Facility and Dormitory Reservation System
 * Facilities Directory Router
 */
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

$isAdmin = (isset($_SESSION['user_role']) && in_array($_SESSION['user_role'], ['admin', 'super_admin', 'director', 'recommending_officer']))
  || (isset($_COOKIE['ati_role']) && in_array($_COOKIE['ati_role'], ['admin', 'super_admin', 'director']));

if ($isAdmin || isset($_GET['admin'])) {
  header('Location: admin_facilities.php');
  exit;
}

include __DIR__ . '/home.php';
