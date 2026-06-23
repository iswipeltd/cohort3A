<?php
require_once __DIR__ . '/../config/session.php';
require_auth();
$user = current_user();
require_once __DIR__ . '/../config/totp.php';
// TOTP uses static methods
$err = ''; $success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['enable_2fa'])) {
        $code = $_POST['code'] ?? '';
        $secret = $_SESSION['pending_2fa_secret'] ?? '';
        if (TOTP::verify($secret, $code)) {
            $pdo->prepare("UPDATE users SET two_factor_secret=?, two_factor_enabled=1 WHERE id=?")->execute([$secret, $_SESSION['user_id']]);
            unset($_SESSION['pending_2fa_secret']);
            $success = 'Two-factor authentication enabled successfully.';
        } else {
            $err = 'Invalid verification code. Please try again.';
        }
    } elseif (isset($_POST['disable_2fa'])) {
        $pdo->prepare("UPDATE users SET two_factor_secret=NULL, two_factor_enabled=0 WHERE id=?")->execute([$_SESSION['user_id']]);
        $success = 'Two-factor authentication disabled.';
    }
}
$isEnabled = $user['two_factor_enabled'] ?? false;
$qrUrl = '';
$secret = '';
if (!$isEnabled && empty($err)) {
    $secret = TOTP::generateSecret();
    $_SESSION['pending_2fa_secret'] = $secret;
    $qrUrl = TOTP::getQRCodeUrl($user['email'], $secret, 'ADEEEEE');
}
?><?php include 'includes/head.php'; ?><body><?php include 'includes/sidebar.php'; ?><div class="main-content"><?php include 'includes/navbar.php'; ?>
<?php $err=$_SESSION['error']??''; $success=$_SESSION['success']??''; unset($_SESSION['error'],$_SESSION['success']); ?>
<?php if($err):?><div class="alert alert-danger d-flex align-items-center py-2 mb-3" style="border-radius:10px;font-size:0.85rem;"><i class="fa-solid fa-circle-exclamation me-2"></i><?php echo htmlspecialchars($err);?></div><?php endif;?>
<?php if($success):?><div class="alert alert-success d-flex align-items-center py-2 mb-3" style="border-radius:10px;font-size:0.85rem;"><i class="fa-solid fa-circle-check me-2"></i><?php echo htmlspecialchars($success);?></div><?php endif;?>

<div class="page-header"><div><h1 class="page-title">Two-Factor Authentication</h1><p class="page-subtitle mb-0">Secure your account</p></div></div>
<div class="px-4 pb-4">
<?php if($err):?><div class="alert alert-danger d-flex align-items-center py-2 mb-3" style="border-radius:10px;font-size:0.85rem;"><i class="fa-solid fa-circle-exclamation me-2"></i><?php echo htmlspecialchars($err);?></div><?php endif;?>
<?php if($success):?><div class="alert alert-success d-flex align-items-center py-2 mb-3" style="border-radius:10px;font-size:0.85rem;"><i class="fa-solid fa-circle-check me-2"></i><?php echo htmlspecialchars($success);?></div><?php endif;?>
<div class="row g-3">
<div class="col-lg-6">
<div class="card-modern">
<div class="card-header-modern"><h6 class="fw-bold mb-0">Status</h6></div><div class="card-body-modern">
<?php if($isEnabled):?>
<div class="d-flex align-items-center gap-3 mb-3"><div style="width:48px;height:48px;border-radius:50%;background:#d1fae5;display:flex;align-items:center;justify-content:center;color:#047857;"><i class="fa-solid fa-shield-halved fa-lg"></i></div><div><div class="fw-bold">2FA is Enabled</div><div class="text-muted small">Your account is protected</div></div></div>
<form method="POST"><button type="submit" name="disable_2fa" class="btn-outline-mod text-danger border-danger"><i class="fa-solid fa-shield-xmark me-2"></i>Disable 2FA</button></form>
<?php else:?>
<div class="d-flex align-items-center gap-3 mb-3"><div style="width:48px;height:48px;border-radius:50%;background:#fef3c7;display:flex;align-items:center;justify-content:center;color:#92400e;"><i class="fa-solid fa-lock-open fa-lg"></i></div><div><div class="fw-bold">2FA is Disabled</div><div class="text-muted small">Enable for extra security</div></div></div>
<p class="text-muted small">Scan the QR code with your authenticator app and enter the 6-digit code to verify.</p>
<div class="text-center p-3" style="background:#f8fafc;border-radius:12px;margin-bottom:16px;">
<img src="<?php echo htmlspecialchars($qrUrl); ?>" alt="QR Code" style="max-width:200px;border-radius:8px;">
<div class="mt-2"><code style="background:var(--border);padding:4px 10px;border-radius:6px;font-size:0.85rem;"><?php echo htmlspecialchars($secret); ?></code></div>
</div>
<form method="POST"><label class="form-label-mod">Verification Code</label><input type="text" name="code" class="form-control form-control-mod mb-3" maxlength="6" placeholder="000000" required><button type="submit" name="enable_2fa" class="btn-primary-mod"><i class="fa-solid fa-shield-halved me-2"></i>Enable 2FA</button></form>
<?php endif;?>
</div></div></div>
<div class="col-lg-6">
<div class="card-modern"><div class="card-header-modern"><h6 class="fw-bold mb-0">Recommended Apps</h6></div><div class="card-body-modern">
<div class="d-flex align-items-center p-2 mb-2" style="background:#f8fafc;border-radius:10px;"><i class="fa-brands fa-google me-3" style="color:#ea4335;font-size:1.3rem;"></i><div><div class="fw-semibold small">Google Authenticator</div><div class="text-muted" style="font-size:0.72rem;">Free on iOS & Android</div></div></div>
<div class="d-flex align-items-center p-2 mb-2" style="background:#f8fafc;border-radius:10px;"><i class="fa-solid fa-shield-halved me-3" style="color:#3b82f6;font-size:1.3rem;"></i><div><div class="fw-semibold small">Microsoft Authenticator</div><div class="text-muted" style="font-size:0.72rem;">Free on iOS & Android</div></div></div>
<div class="d-flex align-items-center p-2" style="background:#f8fafc;border-radius:10px;"><i class="fa-brands fa-apple me-3" style="color:#000;font-size:1.3rem;"></i><div><div class="fw-semibold small">2FAS Authenticator</div><div class="text-muted" style="font-size:0.72rem;">Free on iOS & Android</div></div></div>
</div></div></div>
</div></div></div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script></body></html>
