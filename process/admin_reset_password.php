<?php
session_start();
require_once __DIR__ . '/../config/session.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'GET' && !empty($_GET['user_id'])) {
    $userId = (int) $_GET['user_id'];
    
    // Get user info
    $stmt = $pdo->prepare("SELECT id, email, first_name, last_name, role FROM users WHERE id = ? LIMIT 1");
    $stmt->execute([$userId]);
    $user = $stmt->fetch();
    
    if (!$user) {
        $_SESSION['error'] = 'User not found.';
        header('Location: /HRSuite/admin_dashboard/password_reset.php');
        exit;
    }
    
    // Generate temporary password
    $tempPassword = bin2hex(random_bytes(4)); // 8 chars
    $hash = password_hash($tempPassword, PASSWORD_DEFAULT);
    
    // Update password
    $pdo->prepare("UPDATE users SET password_hash = ? WHERE id = ?")
        ->execute([$hash, $userId]);
    
    // Force password change on next login
    $pdo->prepare("UPDATE users SET force_password_change = 1 WHERE id = ?")
        ->execute([$userId]);
    
    // Log activity
    log_activity($_SESSION['user_id'], 'RESET_PASSWORD', 'Auth', $userId, 'Admin reset password for ' . $user['email']);
    
    // Try to send email
    require_once __DIR__ . '/../config/mail.php';
    $sent = sendEmail($user['email'], 'Password Reset by Admin - ADEEEEE', '', 'welcome', [
        'name' => $user['first_name'] . ' ' . $user['last_name'],
        'email' => $user['email'],
        'password' => $tempPassword,
    ]);
    
    if ($sent) {
        $_SESSION['success'] = 'Password reset for ' . htmlspecialchars($user['email']) . '. Temporary password emailed.';
    } else {
        $_SESSION['success'] = 'Password reset for ' . htmlspecialchars($user['email']) . '. Temporary password: ' . $tempPassword . ' (email failed - copy this down)';
    }
    
    header('Location: /HRSuite/admin_dashboard/password_reset.php');
    exit;
}

header('Location: /HRSuite/admin_dashboard/password_reset.php');
exit;
