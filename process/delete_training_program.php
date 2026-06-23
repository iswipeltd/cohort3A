<?php
require_once __DIR__ . '/../config/session.php';
require_admin();
require_once __DIR__ . '/../config/database.php';
$id = (int)($_GET['id'] ?? 0);
if ($id) {
    $pdo->prepare("DELETE FROM training_programs WHERE id=?")->execute([$id]);
    log_activity($_SESSION['user_id'], 'DELETE', 'Training', $id, 'Deleted training');
    $_SESSION['success'] = 'Training program deleted.';
}
header('Location: /HRSuite/admin_dashboard/training_programs.php');
exit;
