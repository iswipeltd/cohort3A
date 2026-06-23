<?php
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../config/database.php';

if (empty($_SESSION['user_id']) || empty($_SESSION['force_password_change'])) {
    header('Location: /HRSuite/user-dashboard/signin.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new = trim($_POST['new_password'] ?? '');
    $confirm = trim($_POST['confirm_password'] ?? '');

    if (strlen($new) < 6) {
        $_SESSION['error'] = 'Password must be at least 6 characters.';
        header('Location: /HRSuite/user-dashboard/first_login.php');
        exit;
    }

    if ($new !== $confirm) {
        $_SESSION['error'] = 'Passwords do not match.';
        header('Location: /HRSuite/user-dashboard/first_login.php');
        exit;
    }

    try {
        $pdo->prepare("UPDATE users SET password_hash = ? WHERE id = ?")
            ->execute([password_hash($new, PASSWORD_DEFAULT), $_SESSION['user_id']]);

        log_activity($_SESSION['user_id'], 'UPDATE', 'Auth', $_SESSION['user_id'], 'Changed default password on first login');

        unset($_SESSION['force_password_change']);
        $_SESSION['success'] = 'Password updated successfully. Welcome to ADEEEEE!';

        if (in_array($_SESSION['role'] ?? '', ['admin', 'hr'])) {
            header('Location: /HRSuite/admin_dashboard/welcome.php');
        } else {
            header('Location: /HRSuite/user-dashboard/index.php');
        }
        exit;
    } catch (Exception $e) {
        $_SESSION['error'] = 'Something went wrong. Please try again.';
        header('Location: /HRSuite/user-dashboard/first_login.php');
        exit;
    }
}

header('Location: /HRSuite/user-dashboard/first_login.php');
exit;
