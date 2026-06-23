<?php
require_once __DIR__ . '/../config/session.php';
require_auth();
$user = current_user();
$profile = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$profile->execute([$_SESSION['user_id']]);
$u = $profile->fetch();
?>
<?php include 'includes/head.php'; ?><body><?php include 'includes/sidebar.php'; ?><div class="main-content"><?php include 'includes/navbar.php'; ?>
<?php $err=$_SESSION['error']??''; $success=$_SESSION['success']??''; unset($_SESSION['error'],$_SESSION['success']); ?>
<?php if($err):?><div class="alert alert-danger d-flex align-items-center py-2 mb-3" style="border-radius:10px;font-size:0.85rem;"><i class="fa-solid fa-circle-exclamation me-2"></i><?php echo htmlspecialchars($err);?></div><?php endif;?>
<?php if($success):?><div class="alert alert-success d-flex align-items-center py-2 mb-3" style="border-radius:10px;font-size:0.85rem;"><i class="fa-solid fa-circle-check me-2"></i><?php echo htmlspecialchars($success);?></div><?php endif;?>

<div class="page-header"><div><h1 class="page-title">Privacy Settings</h1><p class="page-subtitle mb-0">Manage your privacy preferences</p></div></div>
<div class="px-4 pb-4">
<div class="card-modern"><div class="card-body-modern">
<?php if($u): ?>
<div class="row g-3">
<div class="col-md-6"><div class="p-3" style="background:var(--bg);border-radius:10px;"><div class="text-muted small">Name</div><div class="fw-bold"><?php echo htmlspecialchars($u['first_name'].' '.$u['last_name']); ?></div></div></div>
<div class="col-md-6"><div class="p-3" style="background:var(--bg);border-radius:10px;"><div class="text-muted small">Email</div><div class="fw-bold"><?php echo htmlspecialchars($u['email']); ?></div></div></div>
<div class="col-md-6"><div class="p-3" style="background:var(--bg);border-radius:10px;"><div class="text-muted small">Phone Visibility</div><div class="fw-bold"><?php echo $u['phone'] ? 'Visible' : 'Hidden'; ?></div></div></div>
<div class="col-md-6"><div class="p-3" style="background:var(--bg);border-radius:10px;"><div class="text-muted small">Account Status</div><div class="fw-bold"><?php echo ucfirst($u['status']); ?></div></div></div>
<div class="col-md-6"><div class="p-3" style="background:var(--bg);border-radius:10px;"><div class="text-muted small">2FA Enabled</div><div class="fw-bold"><?php echo $u['two_factor_enabled'] ? 'Yes' : 'No'; ?></div></div></div>
<div class="col-md-6"><div class="p-3" style="background:var(--bg);border-radius:10px;"><div class="text-muted small">Last Login</div><div class="fw-bold"><?php echo $u['last_login'] ? date('M j, Y g:i A', strtotime($u['last_login'])) : 'Never'; ?></div></div></div>
</div>
<?php else: ?>
<div class="text-center py-5 text-muted">Settings unavailable</div>
<?php endif; ?>
</div></div></div></div></div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script></body></html>
