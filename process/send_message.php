<?php
require_once __DIR__ . '/../config/session.php';
require_auth();
require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $to_user_id = (int) ($_POST['receiver_id'] ?? 0);
    $subject = trim($_POST['subject'] ?? '');
    $body = trim($_POST['body'] ?? '');
    
    if (empty($to_user_id) || empty($subject) || empty($body)) {
        $_SESSION['error'] = 'Please fill in all fields including selecting an HR recipient.';
    } else {
        $stmt = $pdo->prepare("INSERT INTO messages (from_user_id, to_user_id, subject, body, created_at) VALUES (?, ?, ?, ?, NOW())");
        $stmt->execute([$_SESSION['user_id'], $to_user_id, $subject, $body]);
        log_activity($_SESSION['user_id'], 'CREATE', 'Message', $pdo->lastInsertId(), 'Sent message to HR user ' . $to_user_id);
        $_SESSION['success'] = 'Message sent to HR successfully.';
    }
}
header('Location: /HRSuite/user-dashboard/contact_hr.php');
exit;
