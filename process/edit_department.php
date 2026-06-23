<?php
require_once __DIR__ . '/../config/session.php';
require_admin();
require_once __DIR__ . '/../config/database.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $status = $_POST['status'] ?? 'active';
    if ($name && $id) {
        $pdo->prepare("UPDATE departments SET name=?, description=?, location=?, status=? WHERE id=?")->execute([$name, $description, $location, $status, $id]);
        log_activity($_SESSION['user_id'], 'UPDATE', 'Department', $id, 'Updated department');
        $_SESSION['success'] = 'Department updated.';
    }
    header('Location: /HRSuite/admin_dashboard/departments.php');
    exit;
}
