<?php
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/session.php';
require_admin();

$recordId = (int) ($_POST['record_id'] ?? $_GET['record_id'] ?? 0);
$periodId = (int) ($_POST['period_id'] ?? $_GET['period_id'] ?? 0);
$notes = trim($_POST['payment_notes'] ?? $_GET['payment_notes'] ?? '');

if (!$recordId) {
    $_SESSION['error'] = 'Invalid payroll record.';
    header('Location: /HRSuite/admin_dashboard/payroll.php' . ($periodId ? '?period_id=' . $periodId : ''));
    exit;
}

// Ensure employees table has the bank_code column required by Novac
$empColumns = $pdo->query("SHOW COLUMNS FROM employees")->fetchAll(PDO::FETCH_COLUMN, 0);
if (!in_array('bank_code', $empColumns)) {
    $_SESSION['error'] = 'Database is missing the bank_code column. Please run http://localhost/HRSuite/fix_database.php to add it, then re-save employee bank details.';
    header('Location: /HRSuite/admin_dashboard/payroll.php' . ($periodId ? '?period_id=' . $periodId : ''));
    exit;
}

// Fetch payroll record with employee + bank details
$stmt = $pdo->prepare("
    SELECT pr.id as record_id, pr.employee_id, pr.net_pay, pr.period_id,
           u.first_name, u.last_name, u.email,
           e.bank_name, e.bank_account, e.bank_code
    FROM payroll_records pr
    JOIN employees e ON pr.employee_id = e.id
    JOIN users u ON e.user_id = u.id
    WHERE pr.id = ?
");
$stmt->execute([$recordId]);
$rec = $stmt->fetch();

if (!$rec) {
    $_SESSION['error'] = 'Payroll record not found.';
    header('Location: /HRSuite/admin_dashboard/payroll.php' . ($periodId ? '?period_id=' . $periodId : ''));
    exit;
}

// Prevent re-paying an already-paid record
$check = $pdo->prepare("SELECT status FROM payroll_records WHERE id = ?");
$check->execute([$recordId]);
$currentStatus = $check->fetchColumn();
if ($currentStatus === 'paid') {
    $_SESSION['error'] = 'This payroll record has already been paid.';
    header('Location: /HRSuite/admin_dashboard/payroll.php' . ($periodId ? '?period_id=' . $periodId : ''));
    exit;
}

$employeeName = $rec['first_name'] . ' ' . $rec['last_name'];

// Validate bank details
$bn = trim($rec['bank_name'] ?? '');
$ba = trim($rec['bank_account'] ?? '');
$bc = trim($rec['bank_code'] ?? '');

if (empty($bn) || empty($ba) || empty($bc)) {
    $editUrl = '/HRSuite/admin_dashboard/employee_edit.php?id=' . $rec['employee_id'];
    $_SESSION['error'] = 'Employee bank details are missing. <a href="' . $editUrl . '" class="alert-link">Click here to edit</a> ' . htmlspecialchars($employeeName) . "'s profile.";
    header('Location: /HRSuite/admin_dashboard/payroll.php' . ($periodId ? '?period_id=' . $periodId : ''));
    exit;
}

// Get Novac key
$novacKey = '';
try {
    $s = $pdo->query("SELECT setting_value FROM settings WHERE setting_key = 'payment_novac_key' LIMIT 1");
    if ($s) {
        $row = $s->fetch();
        $novacKey = $row['setting_value'] ?? '';
    }
} catch (PDOException $e) {
}

if (!$novacKey) {
    $_SESSION['error'] = 'Novac Payment is not configured. Please add your Secret Key in Admin > Payment Settings.';
    header('Location: /HRSuite/admin_dashboard/payroll.php' . ($periodId ? '?period_id=' . $periodId : ''));
    exit;
}

$amount = number_format($rec['net_pay'], 2, '.', '');
$reference = 'HRSUITE-PAYOUT-' . $recordId . '-' . time();

$payload = json_encode([
    'currency' => 'NGN',
    'amount' => (float) $amount,
    'bankCode' => $bc,
    'accountNumber' => $ba,
    'narration' => 'Salary Payment - ' . $employeeName,
    'reference' => $reference,
    'bankName' => $bn,
    'accountName' => $employeeName,
    'metaData' => json_encode([
        'payroll_record_id' => $recordId,
        'employee_id' => $rec['employee_id'],
        'period_id' => $periodId
    ])
]);

$ch = curl_init('https://api.novacpayment.com/api/v1/transfers');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $novacKey,
    'Content-Type: application/json',
    'Accept: application/json'
]);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

$result = $response ? json_decode($response, true) : null;

if ($httpCode === 200 && $result && !empty($result['success']) && !empty($result['data']['status'])) {
    $txStatus = strtolower($result['data']['status']);
    $txRef = $result['data']['reference'] ?? $reference;

    try {
        $pdo->prepare("UPDATE payroll_records SET status='paid', paid_at=NOW(), paid_by=? WHERE id=?")
            ->execute([$_SESSION['user_id'], $recordId]);
    } catch (PDOException $e) {
        $pdo->prepare("UPDATE payroll_records SET status='paid' WHERE id=?")
            ->execute([$recordId]);
    }

    try {
        $pdo->prepare("INSERT INTO payments (payroll_record_id, employee_id, payment_method, reference_number, amount, payment_date, status, notes, created_by, created_at) VALUES (?, ?, ?, ?, ?, ?, 'completed', ?, ?, NOW())")
            ->execute([
                $recordId, $rec['employee_id'], 'novac_payout', $txRef,
                $rec['net_pay'], date('Y-m-d'), 'Novac payout initiated. Status: ' . $txStatus . ($notes ? ' | ' . $notes : ''), $_SESSION['user_id']
            ]);
    } catch (PDOException $e) {
        error_log('Payment record insert failed: ' . $e->getMessage());
    }

    log_activity($_SESSION['user_id'], 'UPDATE', 'Payroll', $recordId, 'Salary payout initiated via Novac. Ref: ' . $txRef . ' | Status: ' . $txStatus);
    $_SESSION['success'] = 'Salary payout of NGN' . number_format($rec['net_pay'], 2) . ' initiated for ' . htmlspecialchars($employeeName) . '. Transaction Ref: ' . htmlspecialchars($txRef);
} else {
    $apiMessage = $result['message'] ?? '';
    $errorMsg = $apiMessage ?: ($curlError ?: 'Unknown error (HTTP ' . $httpCode . ')');

    $causes = [];
    if ($httpCode == 401) $causes[] = 'Invalid API key.';
    if ($httpCode == 400 || $httpCode == 422) $causes[] = 'Invalid request format or missing fields.';
    if ($httpCode == 403) $causes[] = 'Account needs KYC or is in test mode.';
    if (empty($causes)) $causes[] = 'Check Novac dashboard: ensure sufficient balance and correct API key.';

    log_activity($_SESSION['user_id'], 'FAILED', 'Payroll', $recordId, 'Novac payout failed: ' . $errorMsg);
    $_SESSION['error'] = 'Novac payout failed: ' . htmlspecialchars($errorMsg) . ' | HTTP ' . $httpCode . '. ' . implode(' ', $causes);
}

header('Location: /HRSuite/admin_dashboard/payroll.php' . ($periodId ? '?period_id=' . $periodId : ''));
exit;
