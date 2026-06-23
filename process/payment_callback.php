<?php
require_once __DIR__ . '/../config/session.php';
require_admin();
require_once __DIR__ . '/../config/database.php';

$recordId = (int) ($_GET['record_id'] ?? 0);
$periodId = (int) ($_GET['period_id'] ?? 0);
$status = trim($_GET['status'] ?? 'success');
$reference = trim($_GET['tx_ref'] ?? $_GET['ref'] ?? $_GET['transaction_id'] ?? '');

if ($recordId && $status === 'success') {
    $pdo->prepare("UPDATE payroll_records SET status='paid', paid_at=NOW(), paid_by=? WHERE id=?")
        ->execute([$_SESSION['user_id'], $recordId]);
    log_activity($_SESSION['user_id'], 'UPDATE', 'Payroll', $recordId, 'Payment confirmed via gateway. Ref: ' . $reference);
    $_SESSION['success'] = 'Payment confirmed successfully.';
} else {
    $_SESSION['error'] = 'Payment was cancelled or failed. Please try again.';
}

header('Location: /HRSuite/admin_dashboard/payroll.php' . ($periodId ? '?period_id=' . $periodId : ''));
exit;
