<?php
/**
 * ADEEEEE Lightweight PDF Generator
 * Generates payslip HTML that can be saved/printed to PDF by the browser.
 * Also provides a server-side text-based PDF stub for basic download.
 */

/**
 * Generate a payslip HTML view for a given payroll record
 */
function generatePayslipHtml($recordId) {
    global $pdo;
    
    $stmt = $pdo->prepare("
        SELECT pr.*, pp.month, pp.year, pp.start_date, pp.end_date,
               e.employee_code, e.start_date as hire_date,
               u.first_name, u.last_name, u.email,
               d.name as department, r.name as role_name,
               e.bank_name, e.bank_account
        FROM payroll_records pr
        JOIN payroll_periods pp ON pr.period_id = pp.id
        JOIN employees e ON pr.employee_id = e.id
        JOIN users u ON e.user_id = u.id
        LEFT JOIN departments d ON e.department_id = d.id
        LEFT JOIN roles r ON e.role_id = r.id
        WHERE pr.id = ?
        LIMIT 1
    ");
    $stmt->execute([$recordId]);
    $p = $stmt->fetch();
    
    if (!$p) return '<div class="alert alert-danger">Payslip not found.</div>';
    
    $periodName = date('F Y', mktime(0, 0, 0, $p['month'], 1, $p['year']));
    $generatedAt = date('F j, Y \a\t g:i A');
    
    return '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Payslip - ' . htmlspecialchars($p['first_name'] . ' ' . $p['last_name']) . ' - ' . $periodName . '</title>
    <style>
        @media print { .no-print { display:none; } body { background:#fff; } }
        * { box-sizing:border-box; }
        body { font-family:Arial,Helvetica,sans-serif; background:#f5f5f5; margin:0; padding:20px; color:#333; }
        .payslip { max-width:800px; margin:0 auto; background:#fff; padding:40px; border-radius:8px; box-shadow:0 2px 20px rgba(0,0,0,0.1); }
        .header { display:flex; justify-content:space-between; align-items:flex-start; border-bottom:3px solid #0d6efd; padding-bottom:20px; margin-bottom:30px; }
        .company h1 { margin:0; color:#0d6efd; font-size:28px; }
        .company p { margin:5px 0 0; color:#666; font-size:13px; }
        .payslip-info { text-align:right; }
        .payslip-info h2 { margin:0; font-size:22px; color:#333; }
        .payslip-info p { margin:5px 0 0; color:#666; font-size:13px; }
        .employee-section { display:flex; justify-content:space-between; margin-bottom:30px; }
        .employee-box { width:48%; }
        .employee-box h4 { margin:0 0 10px; font-size:14px; text-transform:uppercase; color:#0d6efd; border-bottom:1px solid #eee; padding-bottom:5px; }
        .employee-box p { margin:4px 0; font-size:13px; }
        .table-wrap { margin-bottom:30px; }
        table { width:100%; border-collapse:collapse; font-size:13px; }
        th { background:#f8f9fa; text-align:left; padding:10px; border-bottom:2px solid #dee2e6; }
        td { padding:10px; border-bottom:1px solid #eee; }
        .amount { text-align:right; font-family:"Courier New",monospace; }
        .total-row { font-weight:bold; background:#f8f9fa; }
        .net-row { font-weight:bold; font-size:16px; background:#0d6efd; color:#fff; }
        .net-row td { border:none; padding:15px 10px; }
        .footer { margin-top:30px; padding-top:20px; border-top:1px solid #eee; font-size:11px; color:#888; text-align:center; }
        .btn-print { background:#0d6efd; color:#fff; border:none; padding:12px 30px; border-radius:5px; cursor:pointer; font-size:14px; }
        .btn-print:hover { background:#0b5ed7; }
        .actions { text-align:center; margin-bottom:20px; }
    </style>
</head>
<body>
    <div class="actions no-print">
        <button class="btn-print" onclick="window.print()">Print / Save as PDF</button>
        <button class="btn-print" style="background:#6c757d;margin-left:10px;" onclick="window.close()">Close</button>
    </div>
    <div class="payslip">
        <div class="header">
            <div class="company">
                <h1>ADEEEEE Corporation</h1>
                <p>123 Business Avenue, New York, NY 10001<br>Email: hr@hrsuite.com | Phone: +1-555-0100</p>
            </div>
            <div class="payslip-info">
                <h2>PAYSLIP</h2>
                <p>Period: <strong>' . $periodName . '</strong><br>Generated: ' . $generatedAt . '</p>
            </div>
        </div>
        
        <div class="employee-section">
            <div class="employee-box">
                <h4>Employee Details</h4>
                <p><strong>Name:</strong> ' . htmlspecialchars($p['first_name'] . ' ' . $p['last_name']) . '</p>
                <p><strong>Employee Code:</strong> ' . htmlspecialchars($p['employee_code']) . '</p>
                <p><strong>Department:</strong> ' . htmlspecialchars($p['department'] ?? 'N/A') . '</p>
                <p><strong>Designation:</strong> ' . htmlspecialchars($p['role_name'] ?? 'N/A') . '</p>
                <p><strong>Date Joined:</strong> ' . ($p['hire_date'] ? date('M j, Y', strtotime($p['hire_date'])) : 'N/A') . '</p>
            </div>
            <div class="employee-box">
                <h4>Payment Details</h4>
                <p><strong>Bank Name:</strong> ' . htmlspecialchars($p['bank_name'] ?? 'N/A') . '</p>
                <p><strong>Bank Account:</strong> ' . htmlspecialchars($p['bank_account'] ?? 'N/A') . '</p>
                <p><strong>Pay Period:</strong> ' . date('M j', strtotime($p['start_date'])) . ' - ' . date('M j, Y', strtotime($p['end_date'])) . '</p>
                <p><strong>Payment Date:</strong> ' . date('M j, Y') . '</p>
            </div>
        </div>
        
        <div class="table-wrap">
            <table>
                <thead>
                    <tr><th>Earnings</th><th class="amount">Amount</th><th>Deductions</th><th class="amount">Amount</th></tr>
                </thead>
                <tbody>
                    <tr><td>Base Salary</td><td class="amount">' . format_currency($p['base_salary']) . '</td><td>Tax</td><td class="amount">' . format_currency($p['tax']) . '</td></tr>
                    <tr><td>Bonus</td><td class="amount">' . format_currency($p['bonus']) . '</td><td>Insurance</td><td class="amount">' . format_currency($p['insurance']) . '</td></tr>
                    <tr><td>Overtime Pay</td><td class="amount">' . format_currency($p['overtime_pay']) . '</td><td>Pension</td><td class="amount">' . format_currency($p['pension']) . '</td></tr>
                    <tr><td>Allowances</td><td class="amount">' . format_currency($p['allowances']) . '</td><td>Other Deductions</td><td class="amount">' . format_currency($p['deductions']) . '</td></tr>
                    <tr class="total-row"><td><strong>Gross Pay</strong></td><td class="amount"><strong>' . format_currency($p['base_salary'] + $p['bonus'] + $p['overtime_pay'] + $p['allowances']) . '</strong></td><td><strong>Total Deductions</strong></td><td class="amount"><strong>' . format_currency($p['tax'] + $p['insurance'] + $p['pension'] + $p['deductions']) . '</strong></td></tr>
                    <tr class="net-row"><td colspan="3">NET PAY</td><td class="amount">' . format_currency($p['net_pay']) . '</td></tr>
                </tbody>
            </table>
        </div>
        
        <div class="footer">
            <p>This is a computer-generated payslip and does not require a signature.</p>
            <p>For any payroll queries, please contact the HR department at hr@hrsuite.com</p>
        </div>
    </div>
</body>
</html>';
}

/**
 * Save payslip HTML to file and return path
 */
function savePayslipPdf($recordId) {
    $html = generatePayslipHtml($recordId);
    $filename = 'payslip_' . $recordId . '_' . time() . '.html';
    $filepath = __DIR__ . '/../uploads/payslips/' . $filename;
    
    $dir = dirname($filepath);
    if (!is_dir($dir)) mkdir($dir, 0777, true);
    
    file_put_contents($filepath, $html);
    return ['filename' => $filename, 'path' => '/uploads/payslips/' . $filename];
}
