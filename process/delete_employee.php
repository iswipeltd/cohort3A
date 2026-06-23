<?php
require_once __DIR__ . '/../config/session.php';
require_admin();
require_once __DIR__ . '/../config/database.php';
$id = (int)($_GET['id'] ?? 0);
if ($id) {
    $emp = $pdo->prepare("SELECT user_id FROM employees WHERE id=?");
    $emp->execute([$id]);
    $user_id = $emp->fetchColumn();
    $pdo->prepare("DELETE FROM employees WHERE id=?")->execute([$id]);
    if ($user_id) $pdo->prepare("DELETE FROM users WHERE id=?")->execute([$user_id]);
    log_activity($_SESSION['user_id'], 'DELETE', 'Employee', $id, 'Deleted employee');
    $_SESSION['success'] = 'Employee deleted.';
}
header('Location: /HRSuite/admin_dashboard/employees.php');
exit;
