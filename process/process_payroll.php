<?php
require_once __DIR__ . '/../config/session.php';
require_admin();
require_once __DIR__ . '/../config/database.php';
$period_id = (int)($_GET['period_id'] ?? 0);
if ($period_id) {
    $period = $pdo->prepare("SELECT * FROM payroll_periods WHERE id=?");
    $period->execute([$period_id]);
    $p = $period->fetch();
    if ($p && $p['status'] === 'open') {
        $emps = $pdo->query("SELECT id, salary FROM employees WHERE status='active'")->fetchAll();
        foreach ($emps as $e) {
            $base = (float)$e['salary'];
            $bonus = $base * 0.1;
            $overtime = $base * 0.05;
            $allowances = $base * 0.08;
            $gross = $base + $bonus + $overtime + $allowances;
            $tax = $gross * 0.15;
            $net = $gross - $tax;
            $check = $pdo->prepare("SELECT id FROM payroll_records WHERE employee_id=? AND period_id=?");
            $check->execute([$e['id'], $period_id]);
            if (!$check->fetch()) {
                $stmt = $pdo->prepare("INSERT INTO payroll_records (employee_id, period_id, base_salary, bonus, overtime_pay, allowances, gross_pay, tax, net_pay, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'generated')");
                $stmt->execute([$e['id'], $period_id, $base, $bonus, $overtime, $allowances, $gross, $tax, $net]);
            }
        }
        $pdo->prepare("UPDATE payroll_periods SET status='processing' WHERE id=?")->execute([$period_id]);
        log_activity($_SESSION['user_id'], 'PROCESS', 'Payroll', $period_id, 'Processed payroll');
        $_SESSION['success'] = 'Payroll processed for ' . $p['month'] . '/' . $p['year'];
    }
}
header('Location: /HRSuite/admin_dashboard/payroll.php?period_id=' . $period_id);
exit;
