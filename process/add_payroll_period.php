<?php
require_once __DIR__ . '/../config/session.php';
require_admin();
require_once __DIR__ . '/../config/database.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $month = (int)($_POST['month'] ?? 0);
    $year = (int)($_POST['year'] ?? 0);
    $start_date = $_POST['start_date'] ?? '';
    $end_date = $_POST['end_date'] ?? '';
    if ($month && $year && $start_date && $end_date) {
        $stmt = $pdo->prepare("INSERT INTO payroll_periods (month, year, start_date, end_date, status) VALUES (?, ?, ?, ?, 'open')");
        $stmt->execute([$month, $year, $start_date, $end_date]);
        log_activity($_SESSION['user_id'], 'CREATE', 'Payroll Period', $pdo->lastInsertId(), 'Created payroll period');
        $_SESSION['success'] = 'Payroll period created.';
    }
    header('Location: /HRSuite/admin_dashboard/payroll.php');
    exit;
}
