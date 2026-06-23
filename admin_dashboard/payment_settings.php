<?php
require_once __DIR__ . '/../config/session.php';
require_admin();
require_once __DIR__ . '/../config/database.php';

$err = $_SESSION['error'] ?? '';
$success = $_SESSION['success'] ?? '';
unset($_SESSION['error'], $_SESSION['success']);

// Load current payment settings
$settings = [];
$s = $pdo->query("SELECT setting_key, setting_value FROM settings WHERE setting_key LIKE 'payment_%'");
if ($s) {
    foreach ($s->fetchAll() as $row) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $keys = ['payment_novac_key', 'payment_novac_link_ref'];
    foreach ($keys as $k) {
        $val = trim($_POST[$k] ?? '');
        if ($val !== '') {
            $pdo->prepare("INSERT INTO settings (setting_key, setting_value, setting_group, updated_by) VALUES (?, ?, 'payments', ?) ON DUPLICATE KEY UPDATE setting_value=VALUES(setting_value), updated_by=VALUES(updated_by), updated_at=NOW()")
                ->execute([$k, $val, $_SESSION['user_id']]);
        } else {
            $pdo->prepare("DELETE FROM settings WHERE setting_key = ?")->execute([$k]);
        }
    }
    $_SESSION['success'] = 'Payment settings saved successfully.';
    header('Location: /HRSuite/admin_dashboard/payment_settings.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Payment Settings | ADEEEEE</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
*{font-family:'Inter',sans-serif;}
body{min-height:100vh;background:#0a0a0f;display:flex;align-items:center;justify-content:center;padding:20px;}
.settings-card{background:#13131f;border:1.5px solid #252540;border-radius:16px;box-shadow:0 20px 60px rgba(0,0,0,0.5);max-width:560px;width:100%;overflow:hidden;}
.settings-header{background:#6366f1;padding:30px;text-align:center;color:#fff;}
.settings-header h2{font-weight:700;margin-bottom:4px;font-size:1.4rem;}
.settings-body{padding:30px;}
.form-control-settings{border:1.5px solid #252540;border-radius:12px;padding:12px 16px;background:#0a0a0f;color:#f8fafc;font-size:0.9rem;}
.form-control-settings:focus{border-color:#6366f1;box-shadow:0 0 0 4px rgba(99,102,241,0.15);background:#0a0a0f;color:#f8fafc;outline:none;}
.form-control-settings::placeholder{color:#64748b;}
.btn-save{background:#6366f1;border:none;color:#fff;font-weight:700;padding:14px;border-radius:12px;width:100%;transition:0.2s;}
.btn-save:hover{background:#4f46e5;box-shadow:0 10px 30px rgba(99,102,241,0.3);}
label{color:#94a3b8;font-weight:600;text-transform:uppercase;font-size:0.7rem;letter-spacing:0.3px;}
.provider-row{border:1.5px solid #252540;border-radius:10px;padding:16px;background:#0a0a0f;margin-bottom:12px;}
.provider-row .provider-label{color:#f8fafc;font-weight:600;font-size:0.9rem;margin-bottom:8px;display:flex;align-items:center;gap:8px;}
.provider-row .provider-label i{width:28px;height:28px;border-radius:8px;background:#6366f1;color:#fff;display:flex;align-items:center;justify-content:center;font-size:0.8rem;}
.provider-desc{color:#64748b;font-size:0.75rem;margin-bottom:10px;}
</style>
</head>
<body>
<div class="settings-card">
<div class="settings-header">
<i class="fa-solid fa-credit-card fa-2x mb-2" style="opacity:0.8;"></i>
<h2>Payment Gateway Settings</h2>
<p class="mb-0 opacity-75">Configure Novac Payment for salary payouts</p>
</div>
<div class="settings-body">
<?php if($err):?><div class="alert alert-danger py-2 mb-3" style="border-radius:10px;font-size:0.85rem;"><i class="fa-solid fa-circle-exclamation me-2"></i><?php echo htmlspecialchars($err);?></div><?php endif;?>
<?php if($success):?><div class="alert alert-success py-2 mb-3" style="border-radius:10px;font-size:0.85rem;"><i class="fa-solid fa-circle-check me-2"></i><?php echo htmlspecialchars($success);?></div><?php endif;?>

<form method="POST">
    <div class="provider-row">
        <div class="provider-label"><i class="fa-solid fa-credit-card"></i>Novac Payment</div>
        <div class="provider-desc">Enter your Novac Secret Key to enable direct salary payouts to employee bank accounts via <a href="https://www.novacpayment.com" target="_blank" style="color:#6366f1;">novacpayment.com</a>.</div>
        <label class="form-label">Secret Key</label>
        <input type="password" name="payment_novac_key" value="<?php echo htmlspecialchars($settings['payment_novac_key'] ?? ''); ?>" class="form-control form-control-settings mb-2" placeholder="sk_live_xxxxxxxxxxxxxxxx or sk_test_xxxxxxxxxxxxxxxx" onfocus="this.type='text'" onblur="this.type='password'">
        <div class="provider-desc" style="font-size:0.7rem;">Get this from your Novac dashboard under Settings > API Settings > Secret Key.</div>
    </div>

    <button type="submit" class="btn btn-save"><i class="fa-solid fa-floppy-disk me-2"></i>Save Settings</button>
    <a href="/HRSuite/admin_dashboard/payroll.php" class="btn btn-outline-light mt-2 w-100" style="border:1.5px solid #6366f1;color:#6366f1;font-weight:700;padding:14px;border-radius:12px;">Back to Payroll</a>
</form>
</div></div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
