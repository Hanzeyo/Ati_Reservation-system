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
        $adminRoles = ['admin', 'super_admin', 'director', 'division_chief', 'clearance', 'recommendation', 'endorsement', 'recommender', 'endorser'];
        $redirectUrl = in_array(strtolower($user->role), $adminRoles) ? 'admin_dashboard.php' : 'home.php';

        echo json_encode(['status' => 'success', 'message' => 'Login successful!', 'redirect' => $redirectUrl, 'user' => [
            'id' => $user->id,
            'full_name' => $user->full_name,
            'role' => $user->role
        ]]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid email or password.']);
    }
} elseif ($action == 'forgot_password') {
    $email = trim($_POST['email'] ?? '');
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['status' => 'error', 'message' => 'Please provide a valid official email address.']);
        exit();
    }

    $user->email = $email;
    if ($user->emailExists()) {
        try {
            $db->prepare("UPDATE users SET password_reset_requested_at = NOW() WHERE id = ?")->execute([$user->id]);

            $auditLog = new AuditLog($db);
            $auditLog->user_id = $user->id;
            $auditLog->action = 'PASSWORD_RESET_REQUEST';
            $auditLog->details = "User {$user->full_name} ({$email}) submitted a password assistance request from login portal.";
            $auditLog->ip_address = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
            $auditLog->log();
        } catch (Exception $e) {
            // Ignore audit log error
        }

        echo json_encode([
            'status' => 'success',
            'message' => "Password reset request acknowledged for {$user->full_name}. Please coordinate with your Division Chief or contact the ATI ICT Unit to obtain your temporary credentials."
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => "No registered account found matching '{$email}'. Please verify your email or register a new personnel account."
        ]);
    }
    exit();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid action.']);
}
?>
