<?php
require_once __DIR__ . '/../config/session.php';
require_admin();
require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current = $_POST['current_password'] ?? '';
    $new = $_POST['new_password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    $stmt = $pdo->prepare("SELECT password_hash FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $hash = $stmt->fetchColumn();

    if (!password_verify($current, $hash)) {
        $_SESSION['error'] = 'Current password is incorrect.';
    } elseif (strlen($new) < 6) {
        $_SESSION['error'] = 'New password must be at least 6 characters.';
    } elseif ($new !== $confirm) {
        $_SESSION['error'] = 'Passwords do not match.';
    } else {
        $pdo->prepare("UPDATE users SET password_hash = ? WHERE id = ?")
            ->execute([password_hash($new, PASSWORD_DEFAULT), $_SESSION['user_id']]);
        log_activity($_SESSION['user_id'], 'UPDATE', 'Auth', $_SESSION['user_id'], 'Admin changed password');
        $_SESSION['success'] = 'Password updated successfully.';
    }
    header('Location: /HRSuite/admin_dashboard/change_password.php');
    exit;
}

header('Location: /HRSuite/admin_dashboard/change_password.php');
exit;
