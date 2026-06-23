<?php
require_once __DIR__ . '/../config/session.php';
require_admin();
require_once __DIR__ . '/../config/database.php';

$record_id = (int)($_GET['record_id'] ?? 0);
$period_id = (int) ($_GET['period_id'] ?? 0);

if ($record_id) {
    $method = trim($_POST['payment_method'] ?? $_GET['payment_method'] ?? 'novac_payout');
    $reference = trim($_POST['reference_number'] ?? $_GET['reference_number'] ?? '');
    $notes = trim($_POST['payment_notes'] ?? '');
    $payment_date = $_POST['payment_date'] ?? date('Y-m-d');

    // Get payroll record details
    $stmt = $pdo->prepare("SELECT pr.*, e.id as employee_id FROM payroll_records pr JOIN employees e ON pr.employee_id = e.id WHERE pr.id = ?");
    $stmt->execute([$record_id]);
    $rec = $stmt->fetch();

    if ($rec) {
        // Mark payroll as paid
        $pdo->prepare("UPDATE payroll_records SET status='paid', paid_at=NOW(), paid_by=? WHERE id=?")->execute([$_SESSION['user_id'], $record_id]);

        // Record payment if payments table exists
        try {
            $payStmt = $pdo->prepare("INSERT INTO payments (payroll_record_id, employee_id, payment_method, reference_number, amount, payment_date, status, notes, created_by, created_at) VALUES (?, ?, ?, ?, ?, ?, 'completed', ?, ?, NOW())");
            $payStmt->execute([$record_id, $rec['employee_id'], $method, $reference, $rec['net_pay'], $payment_date, $notes, $_SESSION['user_id']]);
        } catch (PDOException $e) {
            // payments table may not exist yet — log but don't fail
            error_log('Payment table missing: ' . $e->getMessage());
        }

        log_activity($_SESSION['user_id'], 'UPDATE', 'Payroll', $record_id, 'Approved payroll payment via ' . $method . ($reference ? ' (Ref: ' . $reference . ')' : ''));
        $_SESSION['success'] = 'Payment recorded and payroll marked as paid.';
    } else {
        $_SESSION['error'] = 'Payroll record not found.';
    }
}

$redirect = '/HRSuite/admin_dashboard/payroll.php' . ($period_id ? '?period_id=' . $period_id : '');
header("Location: {$redirect}");
exit;
