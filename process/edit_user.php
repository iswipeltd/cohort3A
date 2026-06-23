<?php
require_once __DIR__ . '/../config/session.php';
require_admin();
require_once __DIR__ . '/../config/database.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $role = $_POST['role'] ?? 'employee';
    $status = $_POST['status'] ?? 'active';
    $phone = trim($_POST['phone'] ?? '');
    if ($id && $first_name && $last_name && $email) {
        $pdo->prepare("UPDATE users SET first_name=?, last_name=?, email=?, role=?, status=?, phone=? WHERE id=?")->execute([$first_name, $last_name, $email, $role, $status, $phone, $id]);
        log_activity($_SESSION['user_id'], 'UPDATE', 'User', $id, 'Updated user account');
        $_SESSION['success'] = 'User updated.';
    }
    header('Location: /HRSuite/admin_dashboard/user_accounts.php');
    exit;
}
