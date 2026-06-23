<?php
session_start();
require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = strtolower(trim($_POST['email'] ?? ''));

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = 'Please enter a valid email address.';
        header('Location: /HRSuite/user-dashboard/forgot_password.php');
        exit;
    }

    // Check if user exists
    $stmt = $pdo->prepare("SELECT id, first_name, last_name FROM users WHERE email = ? LIMIT 1");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if (!$user) {
        // Don't reveal whether email exists
        $_SESSION['success'] = 'If an account exists with this email, a reset link has been sent.';
        header('Location: /HRSuite/user-dashboard/forgot_password.php');
        exit;
    }

    // Generate secure token
    $token = bin2hex(random_bytes(32));
    $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

    // Store token
    $pdo->prepare("INSERT INTO password_resets (user_id, token, expires_at, created_at) VALUES (?, ?, ?, NOW())")
        ->execute([$user['id'], password_hash($token, PASSWORD_DEFAULT), $expires]);

    // Build reset link
    $resetLink = 'http://' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . '/HRSuite/user-dashboard/reset_password.php?token=' . $token . '&email=' . urlencode($email);

    // Send email
    require_once __DIR__ . '/../config/mail.php';
    $sent = sendEmail($email, 'Password Reset - ADEEEEE', '', 'password_reset', [
        'name' => $user['first_name'] . ' ' . $user['last_name'],
        'reset_link' => $resetLink,
        'expires' => '1 hour',
    ]);

    if ($sent) {
        $_SESSION['success'] = 'Password reset link sent to your email. Link expires in 1 hour.';
    } else {
        // XAMPP/local fallback: show link on screen when mail() isn't configured
        $_SESSION['reset_link'] = $resetLink;
        $_SESSION['success'] = 'Email could not be sent (SMTP not configured). Use the reset link shown below.';
        // Also log to file for recovery
        $logFile = __DIR__ . '/../logs/password_resets.log';
        @mkdir(dirname($logFile), 0777, true);
        @file_put_contents($logFile, date('Y-m-d H:i:s') . " | {$email} | {$resetLink}\n", FILE_APPEND);
    }
    header('Location: /HRSuite/user-dashboard/forgot_password.php');
    exit;
}

header('Location: /HRSuite/user-dashboard/forgot_password.php');
exit;
