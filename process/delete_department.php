<?php
require_once __DIR__ . '/../config/session.php';
require_admin();
require_once __DIR__ . '/../config/database.php';
$id = (int)($_GET['id'] ?? 0);
if ($id) {
    $pdo->prepare("DELETE FROM departments WHERE id=?")->execute([$id]);
    log_activity($_SESSION['user_id'], 'DELETE', 'Department', $id, 'Deleted department');
    $_SESSION['success'] = 'Department deleted.';
}
header('Location: /HRSuite/admin_dashboard/departments.php');
exit;
