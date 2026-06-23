<?php
/**
 * Admin Payroll API
 */

require_once __DIR__ . '/../../includes/session.php';
requireRole('admin');

header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? 'list';

try {
    $db = $pdo;
    
    switch ($method) {
        case 'GET':
            if ($action === 'periods') {
                $stmt = $db->query("SELECT * FROM payroll_periods ORDER BY start_date DESC");
                echo json_encode(['success' => true, 'data' => $stmt->fetchAll()]);
            } elseif ($action === 'entries' && isset($_GET['period_id'])) {
                $stmt = $db->prepare("
                    SELECT pe.*, CONCAT(e.first_name, ' ', e.last_name) as employee_name, e.employee_id
                    FROM payroll_entries pe
                    JOIN employees e ON pe.employee_id = e.id
                    WHERE pe.payroll_period_id = ?
                ");
                $stmt->execute([$_GET['period_id']]);
                echo json_encode(['success' => true, 'data' => $stmt->fetchAll()]);
            } elseif ($action === 'payslips' && isset($_GET['employee_id'])) {
                $stmt = $db->prepare("
                    SELECT p.*, pp.name as period_name
                    FROM payslips p
                    JOIN payroll_periods pp ON p.payroll_entry_id IN (SELECT id FROM payroll_entries WHERE payroll_period_id = pp.id)
                    WHERE p.employee_id = ?
                    ORDER BY pp.start_date DESC
                ");
                $stmt->execute([$_GET['employee_id']]);
                echo json_encode(['success' => true, 'data' => $stmt->fetchAll()]);
            }
            break;
            
        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true) ?? $_POST;
            
            if ($action === 'create_period') {
                $stmt = $db->prepare("
                    INSERT INTO payroll_periods (name, start_date, end_date, pay_date)
                    VALUES (?, ?, ?, ?)
                ");
                $stmt->execute([
                    $data['name'], $data['start_date'], $data['end_date'], $data['pay_date']
                ]);
                
                echo json_encode(['success' => true, 'period_id' => $db->lastInsertId()]);
                
            } elseif ($action === 'process') {
                $periodId = $data['period_id'] ?? 0;
                $departmentId = $data['department_id'] ?? null;
                
                // Get all active employees
                $empSql = "SELECT id, salary FROM employees WHERE status = 'active'";
                $empParams = [];
                if ($departmentId) {
                    $empSql .= " AND department_id = ?";
                    $empParams[] = $departmentId;
                }
                
                $empStmt = $db->prepare($empSql);
                $empStmt->execute($empParams);
                $employees = $empStmt->fetchAll();
                
                $db->beginTransaction();
                
                $processed = 0;
                $insertStmt = $db->prepare("
                    INSERT INTO payroll_entries 
                    (payroll_period_id, employee_id, base_salary, gross_pay, net_pay, status)
                    VALUES (?, ?, ?, ?, ?, 'draft')
                    ON DUPLICATE KEY UPDATE
                    base_salary = VALUES(base_salary),
                    gross_pay = VALUES(gross_pay),
                    net_pay = VALUES(net_pay)
                ");
                
                foreach ($employees as $emp) {
                    $baseSalary = $emp['salary'] ?? 0;
                    // Simplified tax calculation (10%)
                    $tax = $baseSalary * 0.10;
                    $net = $baseSalary - $tax;
                    
                    $insertStmt->execute([
                        $periodId, $emp['id'], $baseSalary, $baseSalary, $net
                    ]);
                    $processed++;
                }
                
                // Update period status
                $db->prepare("UPDATE payroll_periods SET status = 'processing' WHERE id = ?")
                   ->execute([$periodId]);
                
                $db->commit();
                
                echo json_encode([
                    'success' => true,
                    'message' => "Payroll processed for $processed employees"
                ]);
                
            } elseif ($action === 'update_entry') {
                $entryId = $data['id'] ?? 0;
                
                $fields = [];
                $values = [];
                $allowed = ['base_salary','bonus','overtime_pay','allowances','tax_deduction','other_deductions'];
                
                $baseSalary = $data['base_salary'] ?? 0;
                $bonus = $data['bonus'] ?? 0;
                $overtime = $data['overtime_pay'] ?? 0;
                $allowances = $data['allowances'] ?? 0;
                $tax = $data['tax_deduction'] ?? 0;
                $other = $data['other_deductions'] ?? 0;
                
                $gross = $baseSalary + $bonus + $overtime + $allowances;
                $net = $gross - $tax - $other;
                
                $stmt = $db->prepare("
                    UPDATE payroll_entries 
                    SET base_salary = ?, bonus = ?, overtime_pay = ?, allowances = ?,
                        gross_pay = ?, tax_deduction = ?, other_deductions = ?, net_pay = ?,
                        status = 'processed'
                    WHERE id = ?
                ");
                $stmt->execute([$baseSalary, $bonus, $overtime, $allowances, $gross, $tax, $other, $net, $entryId]);
                
                echo json_encode(['success' => true, 'message' => 'Payroll entry updated']);
            }
            break;
    }
    
} catch (Exception $e) {
    if (isset($db) && $db->inTransaction()) {
        $db->rollBack();
    }
    error_log("Payroll API Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
