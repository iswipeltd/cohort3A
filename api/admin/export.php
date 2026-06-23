<?php
require_once __DIR__ . '/../../config/session.php';
require_admin();
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/export.php';

$type = $_GET['type'] ?? '';
$format = $_GET['format'] ?? 'csv';

try {
    switch ($type) {
        case 'employees':
            $stmt = $pdo->query("
                SELECT e.employee_code, u.first_name, u.last_name, u.email, u.phone,
                       d.name as department, r.name as role, e.salary, e.employment_type,
                       e.start_date, e.status, e.city, e.country
                FROM employees e
                JOIN users u ON e.user_id = u.id
                LEFT JOIN departments d ON e.department_id = d.id
                LEFT JOIN roles r ON e.role_id = r.id
                ORDER BY u.last_name, u.first_name
            ");
            $data = $stmt->fetchAll();
            $headers = ['Employee Code', 'First Name', 'Last Name', 'Email', 'Phone', 'Department', 'Role', 'Salary', 'Type', 'Start Date', 'Status', 'City', 'Country'];
            $rows = [];
            foreach ($data as $r) {
                $rows[] = [
                    $r['employee_code'], $r['first_name'], $r['last_name'], $r['email'], $r['phone'],
                    $r['department'], $r['role'], '₦' . number_format($r['salary'], 2),
                    $r['employment_type'], $r['start_date'], $r['status'], $r['city'], $r['country']
                ];
            }
            if ($format === 'excel') {
                exportToHtmlExcel('employees_' . date('Y-m-d') . '.xls', 'Employee Directory', $headers, $rows);
            } else {
                exportToCsv('employees_' . date('Y-m-d') . '.csv', $headers, $rows);
            }
            break;

        case 'payroll':
            $periodId = (int) ($_GET['period_id'] ?? 0);
            $sql = "
                SELECT u.first_name, u.last_name, e.employee_code, pp.month, pp.year,
                       pr.base_salary, pr.bonus, pr.overtime_pay, pr.allowances,
                       pr.deductions, pr.tax, pr.insurance, pr.pension, pr.net_pay, pr.status
                FROM payroll_records pr
                JOIN employees e ON pr.employee_id = e.id
                JOIN users u ON e.user_id = u.id
                JOIN payroll_periods pp ON pr.period_id = pp.id
                WHERE 1=1
                " . ($periodId ? " AND pr.period_id = ?" : "") . "
                ORDER BY pp.year DESC, pp.month DESC, u.last_name
            ";
            $stmt = $pdo->prepare($sql);
            $periodId ? $stmt->execute([$periodId]) : $stmt->execute();
            $data = $stmt->fetchAll();
            $headers = ['First Name', 'Last Name', 'Code', 'Month', 'Year', 'Base Salary', 'Bonus', 'Overtime', 'Allowances', 'Deductions', 'Tax', 'Insurance', 'Pension', 'Net Pay', 'Status'];
            $rows = [];
            foreach ($data as $r) {
                $rows[] = [
                    $r['first_name'], $r['last_name'], $r['employee_code'],
                    $r['month'], $r['year'],
                    '₦' . number_format($r['base_salary'], 2),
                    '₦' . number_format($r['bonus'], 2),
                    '₦' . number_format($r['overtime_pay'], 2),
                    '₦' . number_format($r['allowances'], 2),
                    '₦' . number_format($r['deductions'], 2),
                    '₦' . number_format($r['tax'], 2),
                    '₦' . number_format($r['insurance'], 2),
                    '₦' . number_format($r['pension'], 2),
                    '₦' . number_format($r['net_pay'], 2),
                    $r['status']
                ];
            }
            if ($format === 'excel') {
                exportToHtmlExcel('payroll_' . date('Y-m-d') . '.xls', 'Payroll Report', $headers, $rows);
            } else {
                exportToCsv('payroll_' . date('Y-m-d') . '.csv', $headers, $rows);
            }
            break;

        case 'attendance':
            $date = $_GET['date'] ?? date('Y-m-d');
            $stmt = $pdo->prepare("
                SELECT u.first_name, u.last_name, e.employee_code, a.record_date,
                       a.clock_in, a.clock_out, a.hours_worked, a.overtime, a.status
                FROM attendance a
                JOIN employees e ON a.employee_id = e.id
                JOIN users u ON e.user_id = u.id
                WHERE a.record_date = ?
                ORDER BY u.last_name
            ");
            $stmt->execute([$date]);
            $data = $stmt->fetchAll();
            $headers = ['First Name', 'Last Name', 'Code', 'Date', 'Clock In', 'Clock Out', 'Hours', 'Overtime', 'Status'];
            $rows = [];
            foreach ($data as $r) {
                $rows[] = [
                    $r['first_name'], $r['last_name'], $r['employee_code'],
                    $r['record_date'], $r['clock_in'] ?? '-', $r['clock_out'] ?? '-',
                    $r['hours_worked'], $r['overtime'], $r['status']
                ];
            }
            if ($format === 'excel') {
                exportToHtmlExcel('attendance_' . $date . '.xls', 'Attendance Report - ' . $date, $headers, $rows);
            } else {
                exportToCsv('attendance_' . $date . '.csv', $headers, $rows);
            }
            break;

        case 'leaves':
            $status = $_GET['status'] ?? 'all';
            $sql = "
                SELECT CONCAT(u.first_name, ' ', u.last_name) as employee, e.employee_code,
                       lt.name as leave_type, lr.start_date, lr.end_date, lr.days, lr.reason, lr.status
                FROM leave_requests lr
                JOIN employees e ON lr.employee_id = e.id
                JOIN users u ON e.user_id = u.id
                JOIN leave_types lt ON lr.leave_type_id = lt.id
                WHERE 1=1
                " . ($status !== 'all' ? " AND lr.status = ?" : "") . "
                ORDER BY lr.created_at DESC
            ";
            $stmt = $pdo->prepare($sql);
            $status !== 'all' ? $stmt->execute([$status]) : $stmt->execute();
            $data = $stmt->fetchAll();
            $headers = ['Employee', 'Code', 'Leave Type', 'From', 'To', 'Days', 'Reason', 'Status'];
            $rows = [];
            foreach ($data as $r) {
                $rows[] = [
                    $r['employee'], $r['employee_code'], $r['leave_type'],
                    $r['start_date'], $r['end_date'], $r['days'],
                    $r['reason'] ?? '-', $r['status']
                ];
            }
            if ($format === 'excel') {
                exportToHtmlExcel('leaves_' . date('Y-m-d') . '.xls', 'Leave Requests Report', $headers, $rows);
            } else {
                exportToCsv('leaves_' . date('Y-m-d') . '.csv', $headers, $rows);
            }
            break;

        default:
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Unknown export type']);
    }
} catch (Exception $e) {
    error_log("Export Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
