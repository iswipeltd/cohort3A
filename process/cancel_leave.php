<?php
require_once __DIR__ . '/../config/session.php';
require_auth();
require_once __DIR__ . '/../config/database.php';
$empId = get_employee_id($_SESSION['user_id']);
$id = (int)($_GET['id'] ?? 0);
if ($id && $empId) {
    $check = $pdo->prepare("SELECT id FROM leave_requests WHERE id=? AND employee_id=? AND status='pending'");
    $check->execute([$id, $empId]);
    if ($check->fetch()) {
        $pdo->prepare("DELETE FROM leave_requests WHERE id=?")->execute([$id]);
        log_activity($_SESSION['user_id'], 'DELETE', 'Leave', $id, 'Cancelled leave request');
        $_SESSION['success'] = 'Leave request cancelled.';
    } else {
        $_SESSION['error'] = 'Cannot cancel this request.';
    }
}
header('Location: /HRSuite/user-dashboard/leave_status.php');
exit;
