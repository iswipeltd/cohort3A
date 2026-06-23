<?php
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/session.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['error'] = 'Invalid request method.';
    header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/HRSuite/user-dashboard/my_tasks.php'));
    exit;
}

// Determine if admin or employee is creating
try {
    require_admin(); // will throw if not admin
    $isAdmin = true;
} catch (Exception $e) {
    $isAdmin = false;
    require_auth();
}

$user = current_user();
$empId = get_employee_id($_SESSION['user_id']);

$title = trim($_POST['title'] ?? '');
$description = trim($_POST['description'] ?? '');
$project = trim($_POST['project'] ?? '');
$dueDate = $_POST['due_date'] ?? '';
$priority = $_POST['priority'] ?? 'medium';
$assignedTo = (int) ($_POST['assigned_to'] ?? 0);

if (empty($title)) {
    $_SESSION['error'] = 'Task title is required.';
    header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/HRSuite/user-dashboard/my_tasks.php'));
    exit;
}

// If employee creating, self-assign
if (!$isAdmin) {
    if (!$empId) {
        $_SESSION['error'] = 'Employee profile not found. Cannot create tasks.';
        header('Location: /HRSuite/user-dashboard/my_tasks.php');
        exit;
    }
    $assignedTo = $empId;
}

// Validate assigned employee exists if specified
if ($assignedTo) {
    $check = $pdo->prepare("SELECT id FROM employees WHERE id = ?");
    $check->execute([$assignedTo]);
    if (!$check->fetch()) {
        $_SESSION['error'] = 'Selected employee does not exist.';
        header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/HRSuite/admin_dashboard/tasks.php'));
        exit;
    }
}

$stmt = $pdo->prepare("INSERT INTO tasks (title, description, project, assigned_to, assigned_by, due_date, priority, progress, status) VALUES (?, ?, ?, ?, ?, ?, ?, 0, 'open')");
$stmt->execute([$title, $description, $project, $assignedTo ?: null, $_SESSION['user_id'], $dueDate ?: null, $priority]);

$taskId = $pdo->lastInsertId();

// Log activity
if (function_exists('log_activity')) {
    log_activity($_SESSION['user_id'], 'CREATE', 'tasks', $taskId, 'Task created: ' . $title);
}

$_SESSION['success'] = 'Task "' . htmlspecialchars($title) . '" created successfully.';

if ($isAdmin) {
    header('Location: /HRSuite/admin_dashboard/tasks.php');
} else {
    header('Location: /HRSuite/user-dashboard/my_tasks.php');
}
exit;
