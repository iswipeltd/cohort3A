<?php
require_once __DIR__ . '/../config/session.php';
require_admin();
require_once __DIR__ . '/../config/database.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    $name = trim($_POST['name'] ?? '');
    $level = (int)($_POST['level'] ?? 1);
    $description = trim($_POST['description'] ?? '');
    $status = $_POST['status'] ?? 'active';
    if ($name && $id) {
        $pdo->prepare("UPDATE roles SET name=?, level=?, description=?, status=? WHERE id=?")->execute([$name, $level, $description, $status, $id]);
        log_activity($_SESSION['user_id'], 'UPDATE', 'Role', $id, 'Updated role');
        $_SESSION['success'] = 'Role updated.';
    }
    header('Location: /HRSuite/admin_dashboard/roles.php');
    exit;
}
