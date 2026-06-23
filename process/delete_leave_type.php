<?php
require_once __DIR__ . '/../config/session.php';
require_admin();
require_once __DIR__ . '/../config/database.php';
$id = (int)($_GET['id'] ?? 0);
if ($id) {
    $pdo->prepare("DELETE FROM leave_types WHERE id=?")->execute([$id]);
    log_activity($_SESSION['user_id'], 'DELETE', 'Leave Type', $id, 'Deleted leave type');
    $_SESSION['success'] = 'Leave type deleted.';
}
header('Location: /HRSuite/admin_dashboard/leave_types.php');
exit;
