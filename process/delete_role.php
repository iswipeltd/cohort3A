<?php
require_once __DIR__ . '/../config/session.php';
require_admin();
require_once __DIR__ . '/../config/database.php';
$id = (int)($_GET['id'] ?? 0);
if ($id) {
    $pdo->prepare("DELETE FROM roles WHERE id=?")->execute([$id]);
    log_activity($_SESSION['user_id'], 'DELETE', 'Role', $id, 'Deleted role');
    $_SESSION['success'] = 'Role deleted.';
}
header('Location: /HRSuite/admin_dashboard/roles.php');
exit;
