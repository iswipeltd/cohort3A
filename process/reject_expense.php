<?php
require_once __DIR__ . '/../config/session.php';
require_admin();
require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int) ($_POST['expense_id'] ?? 0);
    $reason = trim($_POST['reason'] ?? 'Not approved');
    if ($id) {
        $pdo->prepare("UPDATE expenses SET status='rejected', approved_at=NOW(), approved_by=? WHERE id=? AND status='pending'")
            ->execute([$_SESSION['user_id'], $id]);
        $exp = $pdo->prepare("SELECT e.user_id, ex.amount, ex.type FROM expenses ex JOIN employees e ON ex.employee_id=e.id WHERE ex.id=?");
        $exp->execute([$id]);
        $row = $exp->fetch();
        if ($row) {
            send_notification($row['user_id'], 'expense_status', "Your expense claim of {$row['amount']} ({$row['type']}) has been REJECTED. Reason: {$reason}", '/HRSuite/user-dashboard/expense_status.php');
            log_activity($_SESSION['user_id'], 'REJECT', 'Expense', $id, 'Rejected expense claim: ' . $reason);
        }
        $_SESSION['success'] = 'Expense claim rejected.';
    }
}

$redirect = $_SERVER['HTTP_REFERER'] ?? '/HRSuite/admin_dashboard/expenses.php?status=pending';
header("Location: {$redirect}");
exit;
