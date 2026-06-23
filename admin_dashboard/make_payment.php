<?php
require_once __DIR__ . '/../config/session.php';
require_admin();
require_once __DIR__ . '/../config/database.php';

$recordId = (int) ($_GET['record_id'] ?? 0);
$periodId = (int) ($_GET['period_id'] ?? 0);

if (!$recordId) {
    $_SESSION['error'] = 'Invalid payroll record.';
    header('Location: /HRSuite/admin_dashboard/payroll.php');
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
    SELECT pr.*, u.first_name, u.last_name, u.email, e.employee_code, e.bank_name, e.bank_account, e.bank_code
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

// Validate bank details
$bn = trim($rec['bank_name'] ?? '');
$ba = trim($rec['bank_account'] ?? '');
$bc = trim($rec['bank_code'] ?? '');

if (empty($bn) || empty($ba) || empty($bc)) {
    $editUrl = '/HRSuite/admin_dashboard/employee_edit.php?id=' . $rec['employee_id'];
    $_SESSION['error'] = 'Employee bank details are missing. <a href="' . $editUrl . '" class="alert-link">Click here to edit</a> ' . htmlspecialchars($rec['first_name'] . ' ' . $rec['last_name']) . "'s profile.";
    header('Location: /HRSuite/admin_dashboard/payroll.php' . ($periodId ? '?period_id=' . $periodId : ''));
    exit;
}

// Check Novac key is configured
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

$err = $_SESSION['error'] ?? '';
$success = $_SESSION['success'] ?? '';
unset($_SESSION['error'], $_SESSION['success']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Confirm Novac Payout | ADEEEEE</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
*{font-family:'Inter',sans-serif;}
body{min-height:100vh;background:#0a0a0f;display:flex;align-items:center;justify-content:center;padding:20px;}
.pay-card{background:#13131f;border:1.5px solid #252540;border-radius:16px;box-shadow:0 20px 60px rgba(0,0,0,0.5);max-width:520px;width:100%;overflow:hidden;}
.pay-header{background:#6366f1;padding:30px;text-align:center;color:#fff;}
.pay-header h2{font-weight:700;margin-bottom:4px;font-size:1.4rem;}
.pay-body{padding:30px;}
.form-control-pay{border:1.5px solid #252540;border-radius:12px;padding:12px 16px;background:#0a0a0f;color:#f8fafc;font-size:0.9rem;}
.form-control-pay:focus{border-color:#6366f1;box-shadow:0 0 0 4px rgba(99,102,241,0.15);background:#0a0a0f;color:#f8fafc;outline:none;}
.form-control-pay::placeholder{color:#64748b;}
.btn-pay{background:#6366f1;border:none;color:#fff;font-weight:700;padding:14px;border-radius:12px;width:100%;transition:0.2s;}
.btn-pay:hover{background:#4f46e5;box-shadow:0 10px 30px rgba(99,102,241,0.3);}
.btn-outline-pay{background:transparent;border:1.5px solid #6366f1;color:#6366f1;font-weight:700;padding:14px;border-radius:12px;width:100%;transition:0.2s;text-align:center;text-decoration:none;}
.btn-outline-pay:hover{background:#6366f1;color:#fff;}
label{color:#94a3b8;font-weight:600;text-transform:uppercase;font-size:0.7rem;letter-spacing:0.3px;}
.amount-box{background:rgba(16,185,129,0.1);border:1px solid rgba(16,185,129,0.3);border-radius:10px;padding:16px;text-align:center;margin-bottom:20px;}
.amount-box .amount{color:#10b981;font-size:1.6rem;font-weight:800;}
.amount-box .label{color:#64748b;font-size:0.75rem;text-transform:uppercase;letter-spacing:1px;}
.emp-info{color:#94a3b8;font-size:0.85rem;margin-bottom:20px;text-align:center;}
.detail-row{display:flex;justify-content:space-between;padding:10px 0;border-bottom:1px solid #252540;}
.detail-row:last-child{border-bottom:none;}
.detail-row .label{color:#64748b;font-size:0.78rem;}
.detail-row .value{color:#f8fafc;font-weight:500;font-size:0.85rem;}
.badge-novac{display:inline-flex;align-items:center;gap:6px;background:rgba(99,102,241,0.15);color:#6366f1;border:1px solid rgba(99,102,241,0.3);border-radius:8px;padding:6px 12px;font-size:0.78rem;font-weight:600;}
</style>
</head>
<body>
<div class="pay-card">
<div class="pay-header">
<i class="fa-solid fa-credit-card fa-2x mb-2" style="opacity:0.8;"></i>
<h2>Confirm Novac Payout</h2>
<p class="mb-0 opacity-75">Review and send salary payment</p>
</div>
<div class="pay-body">
<?php if($err):?><div class="alert alert-danger py-2 mb-3" style="border-radius:10px;font-size:0.85rem;"><i class="fa-solid fa-circle-exclamation me-2"></i><?php echo htmlspecialchars($err);?></div><?php endif;?>
<?php if($success):?><div class="alert alert-success py-2 mb-3" style="border-radius:10px;font-size:0.85rem;"><i class="fa-solid fa-circle-check me-2"></i><?php echo htmlspecialchars($success);?></div><?php endif;?>

<div class="text-center mb-3">
<span class="badge-novac"><i class="fa-solid fa-bolt"></i> Powered by Novac Payment</span>
</div>

<div class="emp-info">
    <strong style="color:#f8fafc;"><?php echo htmlspecialchars($rec['first_name'] . ' ' . $rec['last_name']); ?></strong><br>
    <?php echo htmlspecialchars($rec['employee_code']); ?> &bull; <?php echo htmlspecialchars($rec['email']); ?>
</div>

<div class="amount-box">
    <div class="label">Net Pay</div>
    <div class="amount">₦<?php echo number_format($rec['net_pay'], 2); ?></div>
</div>

<div class="mb-4">
    <div class="detail-row">
        <span class="label">Bank Name</span>
        <span class="value"><?php echo htmlspecialchars($bn); ?></span>
    </div>
    <div class="detail-row">
        <span class="label">Account Number</span>
        <span class="value"><?php echo htmlspecialchars($ba); ?></span>
    </div>
    <div class="detail-row">
        <span class="label">Bank Code</span>
        <span class="value"><?php echo htmlspecialchars($bc); ?></span>
    </div>
</div>

<form method="POST" action="/HRSuite/process/make_payment.php" id="novacPaymentForm">
    <input type="hidden" name="record_id" value="<?php echo $recordId; ?>">
    <input type="hidden" name="period_id" value="<?php echo $periodId; ?>">
    <input type="hidden" name="payment_method" value="novac_payout">
    <input type="hidden" name="payment_date" value="<?php echo date('Y-m-d'); ?>">

    <div class="mb-3">
        <label class="form-label">Notes (optional)</label>
        <textarea name="payment_notes" class="form-control form-control-pay" rows="2" placeholder="Optional notes about this payout..."></textarea>
    </div>

    <button type="submit" class="btn btn-pay" id="payBtn"><i class="fa-solid fa-paper-plane me-2"></i>Send Novac Payout</button>
    <a href="/HRSuite/admin_dashboard/payroll.php<?php echo $periodId ? '?period_id=' . $periodId : ''; ?>" class="btn btn-outline-pay mt-2 d-block">Cancel</a>
</form>

</div></div>

<script>
document.getElementById('novacPaymentForm').addEventListener('submit', function() {
    const btn = document.getElementById('payBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin me-2"></i>Processing Novac Payout...';
});
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
