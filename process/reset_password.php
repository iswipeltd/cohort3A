<?php
session_start();
require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['token'] ?? '';
    $email = strtolower(trim($_POST['email'] ?? ''));
    $new = trim($_POST['new_password'] ?? '');
    $confirm = trim($_POST['confirm_password'] ?? '');

    if (strlen($new) < 6) {
        $_SESSION['error'] = 'Password must be at least 6 characters.';
        header('Location: /HRSuite/user-dashboard/reset_password.php?token=' . urlencode($token) . '&email=' . urlencode($email));
        exit;
    }

    if ($new !== $confirm) {
        $_SESSION['error'] = 'Passwords do not match.';
        header('Location: /HRSuite/user-dashboard/reset_password.php?token=' . urlencode($token) . '&email=' . urlencode($email));
        exit;
    }

    // Validate token
    $stmt = $pdo->prepare("SELECT pr.*, u.id as user_id, u.role FROM password_resets pr JOIN users u ON pr.user_id = u.id WHERE u.email = ? AND pr.used = 0 AND pr.expires_at > NOW() ORDER BY pr.created_at DESC LIMIT 1");
    $stmt->execute([$email]);
    $row = $stmt->fetch();

    if (!$row || !password_verify($token, $row['token'])) {
        $_SESSION['error'] = 'Invalid or expired reset link.';
        header('Location: /HRSuite/user-dashboard/forgot_password.php');
        exit;
    }

    // Update password
    $pdo->prepare("UPDATE users SET password_hash = ? WHERE id = ?")
        ->execute([password_hash($new, PASSWORD_DEFAULT), $row['user_id']]);

    // Mark token used
    $pdo->prepare("UPDATE password_resets SET used = 1 WHERE id = ?")
        ->execute([$row['id']]);

    // Log activity
    log_activity($row['user_id'], 'UPDATE', 'Auth', $row['user_id'], 'Password reset via email link');

    $_SESSION['success'] = 'Password reset successful. Please sign in with your new password.';

    // Redirect to appropriate login page based on role
    if (in_array($row['role'], ['admin', 'hr'])) {
        header('Location: /HRSuite/admin_dashboard/login.php');
    } else {
        header('Location: /HRSuite/user-dashboard/signin.php');
    }
    exit;
}

header('Location: /HRSuite/user-dashboard/forgot_password.php');
exit;
