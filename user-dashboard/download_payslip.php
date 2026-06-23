<?php
require_once __DIR__ . '/../config/session.php';
require_auth();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/pdf.php';

$recordId = (int) ($_GET['record_id'] ?? 0);
if (!$recordId) {
    die('Payslip ID required.');
}

// Security: ensure the payslip belongs to the current employee
$empId = get_employee_id($_SESSION['user_id']);
$check = $pdo->prepare("SELECT id FROM payroll_records WHERE id = ? AND employee_id = ?");
$check->execute([$recordId, $empId]);
if (!$check->fetch()) {
    die('Unauthorized: this payslip does not belong to you.');
}

$html = generatePayslipHtml($recordId);
header('Content-Type: text/html; charset=utf-8');
echo $html;
exit;
