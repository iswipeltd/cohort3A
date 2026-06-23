<?php
require_once __DIR__ . '/../config/session.php';
require_admin();
require_once __DIR__ . '/../config/database.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $level = (int)($_POST['level'] ?? 1);
    $description = trim($_POST['description'] ?? '');
    if ($name) {
        $stmt = $pdo->prepare("INSERT INTO roles (name, level, description, status) VALUES (?, ?, ?, 'active')");
        $stmt->execute([$name, $level, $description]);
        log_activity($_SESSION['user_id'], 'CREATE', 'Role', $pdo->lastInsertId(), 'Created role: ' . $name);
        $_SESSION['success'] = 'Role created.';
    }
    header('Location: /HRSuite/admin_dashboard/roles.php');
    exit;
}
