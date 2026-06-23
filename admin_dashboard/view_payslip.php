<?php
require_once __DIR__ . '/../config/session.php';
require_admin();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/pdf.php';

$recordId = (int) ($_GET['record_id'] ?? 0);
if (!$recordId) {
    die('Payslip ID required.');
}

$html = generatePayslipHtml($recordId);
if (strpos($html, 'Payslip not found') !== false) {
    echo '<!DOCTYPE html><html><head><meta charset="utf-8"><title>Not Found</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"></head><body class="p-5"><div class="alert alert-danger">Payslip record not found.</div></body></html>';
    exit;
}
header('Content-Type: text/html; charset=utf-8');
echo $html;
exit;
