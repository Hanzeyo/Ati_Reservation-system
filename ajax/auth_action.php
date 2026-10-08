<?php
require_once __DIR__ . '/../includes/init.php';

header('Content-Type: application/json');

$action = isset($_POST['action']) ? $_POST['action'] : '';

if ($action == 'register') {
    $user->full_name = $_POST['full_name'];
    $user->email = $_POST['email'];
    $user->password = $_POST['password'];
    $user->office_agency = $_POST['office_agency'];
    $user->category = $_POST['category'];
    $user->contact_number = ''; // Optional, handled later if added to form

    if ($user->emailExists()) {
        echo json_encode(['status' => 'error', 'message' => 'Email address already exists.']);
        exit();
    }

    if ($user->register()) {
        echo json_encode(['status' => 'success', 'message' => 'Account registration successful! You can now log in.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Registration failed. Please try again.']);
    }
} elseif ($action == 'login') {
    $user->email = $_POST['email'];
    $password = $_POST['password'];

    if ($user->emailExists() && password_verify($password, $user->password)) {
        if ($user->status == 'inactive') {
            echo json_encode(['status' => 'error', 'message' => 'Your account is inactive. Please contact the administrator.']);
            exit();
        }

        // Set session variables
        $_SESSION['user_id'] = $user->id;
        $_SESSION['full_name'] = $user->full_name;
        $_SESSION['role'] = $user->role;

        // Determine redirect URL based on role
        $redirectUrl = ($user->role == 'admin' || $user->role == 'super_admin') ? 'admin_dashboard.php' : 'home.php';

        echo json_encode(['status' => 'success', 'message' => 'Login successful!', 'redirect' => $redirectUrl, 'user' => [
            'id' => $user->id,
            'full_name' => $user->full_name,
            'role' => $user->role
        ]]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid email or password.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid action.']);
}
?>
