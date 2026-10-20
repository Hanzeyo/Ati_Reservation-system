<?php
require_once __DIR__ . '/includes/init.php';
$stmt = $db->query("SELECT * FROM users WHERE role = 'admin' LIMIT 1");
$user = $stmt->fetch(PDO::FETCH_ASSOC);
if ($user) {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['role'] = $user['role'];
    $_SESSION['full_name'] = $user['full_name'];
    $_SESSION['email'] = $user['email'];
    header('Location: admin_dashboard.php');
    exit();
}
echo "No admin user found";
