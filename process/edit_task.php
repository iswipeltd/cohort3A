<?php
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/session.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['error'] = 'Invalid request.';
    header('Location: /HRSuite/admin_dashboard/tasks.php');
    exit;
}

$taskId = (int) ($_POST['task_id'] ?? 0);
$title = trim($_POST['title'] ?? '');
$description = trim($_POST['description'] ?? '');
$project = trim($_POST['project'] ?? '');
$assignedTo = $_POST['assigned_to'] ?? '';
$dueDate = $_POST['due_date'] ?? '';
$priority = $_POST['priority'] ?? 'medium';
$status = $_POST['status'] ?? 'open';
$progress = (int) ($_POST['progress'] ?? 0);

if (!$taskId || empty($title)) {
    $_SESSION['error'] = 'Task ID and title are required.';
    header('Location: /HRSuite/admin_dashboard/tasks.php');
    exit;
}

$progress = max(0, min(100, $progress));

// Auto-complete if progress is 100
if ($progress >= 100 && $status !== 'completed') {
    $status = 'completed';
}

$completedAt = null;
if ($status === 'completed') {
    $completedAt = date('Y-m-d H:i:s');
}

$stmt = $pdo->prepare("
    UPDATE tasks SET
        title = ?,
        description = ?,
        project = ?,
        assigned_to = ?,
        due_date = ?,
        priority = ?,
        status = ?,
        progress = ?,
        completed_at = ?
    WHERE id = ?
");
$stmt->execute([
    $title, $description, $project,
    $assignedTo !== '' ? (int)$assignedTo : null,
    $dueDate ?: null,
    $priority, $status, $progress,
    $completedAt,
    $taskId
]);

if (function_exists('log_activity')) {
    log_activity($_SESSION['user_id'], 'UPDATE', 'tasks', $taskId, 'Task updated: ' . $title);
}

$_SESSION['success'] = 'Task updated successfully.';
header('Location: /HRSuite/admin_dashboard/tasks.php');
exit;
