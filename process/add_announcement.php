<?php
require_once __DIR__ . '/../config/session.php';
require_admin();
require_once __DIR__ . '/../config/database.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $message = trim($_POST['message'] ?? '');
    $target_audience = $_POST['target_audience'] ?? 'all';
    $pinned = isset($_POST['pinned']) ? 1 : 0;
    if ($title && $message) {
        $stmt = $pdo->prepare("INSERT INTO announcements (title, message, target_audience, pinned, posted_by) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$title, $message, $target_audience, $pinned, $_SESSION['user_id']]);
        $ann_id = $pdo->lastInsertId();
        log_activity($_SESSION['user_id'], 'CREATE', 'Announcement', $ann_id, 'Posted: ' . $title);
        $users = $pdo->query("SELECT id FROM users WHERE status='active'")->fetchAll();
        foreach ($users as $u) {
            send_notification($u['id'], 'announcement', $title, '/HRSuite/user-dashboard/index.php');
        }
        $_SESSION['success'] = 'Announcement posted.';
    }
    header('Location: /HRSuite/admin_dashboard/announcements.php');
    exit;
}
