<?php
require_once __DIR__ . '/../config/session.php';
require_admin();
require_once __DIR__ . '/../config/database.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $location = trim($_POST['location'] ?? '');
    if ($name) {
        $stmt = $pdo->prepare("INSERT INTO departments (name, description, location, status) VALUES (?, ?, ?, 'active')");
        $stmt->execute([$name, $description, $location]);
        log_activity($_SESSION['user_id'], 'CREATE', 'Department', $pdo->lastInsertId(), 'Created department: ' . $name);
        $_SESSION['success'] = 'Department created.';
    }
    header('Location: /HRSuite/admin_dashboard/departments.php');
    exit;
}
