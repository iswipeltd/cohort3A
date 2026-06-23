<?php
/**
 * Bank Details Diagnostic
 * Visit: http://localhost/HRSuite/debug_bank.php?id=EMPLOYEE_ID
 */

require_once __DIR__ . '/config/database.php';

$empId = (int)($_GET['id'] ?? 0);

echo "<h1>Bank Details Diagnostic</h1>";

// 1. Show table columns
echo "<h2>1. Employees Table Columns</h2>";
$cols = $pdo->query("SHOW COLUMNS FROM employees")->fetchAll(PDO::FETCH_ASSOC);
echo "<table border='1' cellpadding='5'><tr><th>Field</th><th>Type</th><th>Null</th></tr>";
foreach ($cols as $c) {
    $highlight = (in_array($c['Field'], ['bank_name','bank_account','bank_code'])) ? "style='background:#ffcccc'" : "";
    echo "<tr $highlight><td>{$c['Field']}</td><td>{$c['Type']}</td><td>{$c['Null']}</td></tr>";
}
echo "</table>";

if ($empId <= 0) {
    // Show all employees with bank details
    echo "<h2>2. All Employees Bank Details</h2>";
    $employees = $pdo->query("
        SELECT e.id, e.employee_code, CONCAT(u.first_name,' ',u.last_name) as name,
               e.bank_name, e.bank_account, e.bank_code, e.status
        FROM employees e
        JOIN users u ON e.user_id = u.id
        ORDER BY e.id
    ")->fetchAll();
    
    echo "<table border='1' cellpadding='5'><tr><th>ID</th><th>Name</th><th>Code</th><th>Status</th><th>Bank Name</th><th>Bank Account</th><th>Bank Code</th><th>Has Bank?</th></tr>";
    foreach ($employees as $e) {
        $hasBank = (!empty($e['bank_name']) && !empty($e['bank_account']) && !empty($e['bank_code'])) ? 'YES' : 'NO';
        $color = ($hasBank === 'YES') ? 'green' : 'red';
        echo "<tr><td>{$e['id']}</td><td>{$e['name']}</td><td>{$e['employee_code']}</td><td>{$e['status']}</td>";
        echo "<td>" . ($e['bank_name'] ?: '<em>EMPTY</em>') . "</td>";
        echo "<td>" . ($e['bank_account'] ?: '<em>EMPTY</em>') . "</td>";
        echo "<td>" . ($e['bank_code'] ?: '<em>EMPTY</em>') . "</td>";
        echo "<td style='color:$color'><strong>$hasBank</strong></td></tr>";
    }
    echo "</table>";
    
    echo "<p>Add <code>?id=1</code> to the URL to check a specific employee.</p>";
    exit;
}

// Show specific employee
echo "<h2>2. Employee ID: $empId</h2>";
$stmt = $pdo->prepare("
    SELECT e.*, CONCAT(u.first_name,' ',u.last_name) as emp_name, u.email
    FROM employees e
    JOIN users u ON e.user_id = u.id
    WHERE e.id = ?
");
$stmt->execute([$empId]);
$emp = $stmt->fetch();

if (!$emp) {
    echo "<p style='color:red'>Employee not found!</p>";
    exit;
}

echo "<table border='1' cellpadding='5'>";
foreach ($emp as $key => $val) {
    $isBank = in_array($key, ['bank_name','bank_account','bank_code']);
    $bg = $isBank ? "style='background:#ffcccc'" : '';
    $display = ($val === null) ? '<em>NULL</em>' : (($val === '') ? '<em>EMPTY STRING</em>' : htmlspecialchars((string)$val));
    echo "<tr $bg><td><strong>$key</strong></td><td>$display</td></tr>";
}
echo "</table>";

// Check payroll records for this employee
echo "<h2>3. Payroll Records for This Employee</h2>";
$payrolls = $pdo->prepare("SELECT * FROM payroll_records WHERE employee_id = ? ORDER BY id DESC LIMIT 5");
$payrolls->execute([$empId]);
$records = $payrolls->fetchAll();

if (empty($records)) {
    echo "<p>No payroll records found.</p>";
} else {
    echo "<table border='1' cellpadding='5'><tr><th>Record ID</th><th>Period ID</th><th>Net Pay</th><th>Status</th></tr>";
    foreach ($records as $r) {
        echo "<tr><td>{$r['id']}</td><td>{$r['period_id']}</td><td>{$r['net_pay']}</td><td>{$r['status']}</td></tr>";
    }
    echo "</table>";
}

echo "<hr><p><a href='/HRSuite/admin_dashboard/payroll.php'>Back to Payroll</a> | <a href='/HRSuite/admin_dashboard/employee_edit.php?id=$empId'>Edit This Employee</a></p>";
