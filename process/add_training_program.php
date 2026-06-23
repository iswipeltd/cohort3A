<?php
require_once __DIR__ . '/../config/session.php';
require_admin();
require_once __DIR__ . '/../config/database.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $duration_hours = (int)($_POST['duration_hours'] ?? 0);
    $mode = $_POST['mode'] ?? 'online';
    if ($title) {
        $stmt = $pdo->prepare("INSERT INTO training_programs (title, description, duration_hours, mode, status, created_by) VALUES (?, ?, ?, ?, 'active', ?)");
        $stmt->execute([$title, $description, $duration_hours, $mode, $_SESSION['user_id']]);
        log_activity($_SESSION['user_id'], 'CREATE', 'Training', $pdo->lastInsertId(), 'Created training: ' . $title);
        $_SESSION['success'] = 'Training program created.';
    }
    header('Location: /HRSuite/admin_dashboard/training_programs.php');
    exit;
}
