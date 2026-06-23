<?php
session_start();
require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = strtolower(trim($_POST['email'] ?? ''));
    $password = trim($_POST['password'] ?? '');
    $redirect = trim($_POST['redirect'] ?? '');
    $portal = trim($_POST['portal'] ?? '');
    
    // Determine which login page to redirect back to on error
    $loginPage = '/HRSuite/admin_dashboard/login.php';
    if (stripos($portal, 'employee') !== false || stripos($redirect, 'user-dashboard') !== false || (isset($_SERVER['HTTP_REFERER']) && stripos($_SERVER['HTTP_REFERER'], 'user-dashboard') !== false)) {
        $loginPage = '/HRSuite/user-dashboard/signin.php';
    }

    if (empty($email) || empty($password)) {
        $_SESSION['error'] = 'Please enter both email and password.';
        header('Location: ' . $loginPage);
        exit;
    }

    $stmt = $pdo->prepare("SELECT id, email, password_hash, role, first_name, last_name, status, two_factor_enabled, avatar FROM users WHERE email = ? LIMIT 1");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if (!$user) {
        $_SESSION['error'] = 'No account found with this email. Please check your email or ask HR to verify your account was created.';
        header('Location: ' . $loginPage);
        exit;
    }

    if ($user['status'] !== 'active') {
        $_SESSION['error'] = 'Your account is not active. Please contact HR.';
        header('Location: ' . $loginPage);
        exit;
    }

    if (!password_verify($password, $user['password_hash'])) {
        $_SESSION['error'] = 'Incorrect password. If you were added by HR, your default password is Employee@123';
        header('Location: ' . $loginPage);
        exit;
    }

    // Check if 2FA is enabled
    if ($user['two_factor_enabled']) {
        $_SESSION['pending_2fa_user_id'] = $user['id'];
        $_SESSION['pending_2fa_email'] = $user['email'];
        $_SESSION['pending_2fa_role'] = $user['role'];
        $_SESSION['pending_2fa_name'] = $user['first_name'] . ' ' . $user['last_name'];
        $_SESSION['pending_2fa_redirect'] = $redirect;
        header('Location: /HRSuite/user-dashboard/verify_2fa.php');
        exit;
    }

    // Admin without avatar -> onboarding
    if (in_array($user['role'], ['admin','hr']) && empty($user['avatar'])) {
        $_SESSION['pending_onboarding_user_id'] = $user['id'];
        header('Location: /HRSuite/admin_dashboard/onboarding.php');
        exit;
    }

    // Check if still using default password
    if (password_verify('Employee@123', $user['password_hash'])) {
        $_SESSION['user_id']   = $user['id'];
        $_SESSION['email']     = $user['email'];
        $_SESSION['role']      = $user['role'];
        $_SESSION['full_name'] = $user['first_name'] . ' ' . $user['last_name'];
        $_SESSION['force_password_change'] = true;
        header('Location: /HRSuite/user-dashboard/first_login.php');
        exit;
    }

    // Success - create session
    $_SESSION['user_id']   = $user['id'];
    $_SESSION['email']     = $user['email'];
    $_SESSION['role']      = $user['role'];
    $_SESSION['full_name'] = $user['first_name'] . ' ' . $user['last_name'];
    $_SESSION['onboarding_completed'] = true;

    // Update last login
    $update = $pdo->prepare("UPDATE users SET last_login = NOW(), last_login_ip = ? WHERE id = ?");
    $update->execute([$_SERVER['REMOTE_ADDR'] ?? '0.0.0.0', $user['id']]);

    log_activity($user['id'], 'LOGIN', 'Auth', $user['id'], 'Successful login');

    // Determine redirect
    if ($redirect) {
        header('Location: ' . $redirect);
        exit;
    }

    if (in_array($user['role'], ['admin','hr'])) {
        header('Location: /HRSuite/admin_dashboard/welcome.php');
    } else {
        header('Location: /HRSuite/user-dashboard/index.php');
    }
    exit;
}

header('Location: /HRSuite/admin_dashboard/login.php');
exit;
