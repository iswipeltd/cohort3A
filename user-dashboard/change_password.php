<?php
require_once __DIR__ . '/../config/session.php';
require_auth();
$user = current_user();
?>
<?php include 'includes/head.php'; ?>
<body>
<?php include 'includes/sidebar.php'; ?>
<div class="main-content">
<?php include 'includes/navbar.php'; ?>
<?php $err=$_SESSION['error']??''; $success=$_SESSION['success']??''; unset($_SESSION['error'],$_SESSION['success']); ?>
<?php if($err):?><div class="alert alert-danger d-flex align-items-center py-2 mb-3" style="border-radius:10px;font-size:0.85rem;"><i class="fa-solid fa-circle-exclamation me-2"></i><?php echo htmlspecialchars($err);?></div><?php endif;?>
<?php if($success):?><div class="alert alert-success d-flex align-items-center py-2 mb-3" style="border-radius:10px;font-size:0.85rem;"><i class="fa-solid fa-circle-check me-2"></i><?php echo htmlspecialchars($success);?></div><?php endif;?>

<div class="page-header">
<div>
<h1 class="page-title">Change Password</h1>
<p class="page-subtitle mb-0">Update your account password</p>
</div>
</div>
<div class="px-4 pb-4">
<div class="card-modern" style="max-width:500px;">
<div class="card-body-modern">
<form action="/HRSuite/process/change_password.php" method="POST">
<div class="mb-3">
<label class="form-label small fw-semibold">Current Password</label>
<input type="password" name="current_password" class="form-control" required>
</div>
<div class="mb-3">
<label class="form-label small fw-semibold">New Password</label>
<input type="password" name="new_password" class="form-control" required minlength="6">
</div>
<div class="mb-3">
<label class="form-label small fw-semibold">Confirm New Password</label>
<input type="password" name="confirm_password" class="form-control" required minlength="6">
</div>
<button type="submit" class="btn btn-primary" style="background:linear-gradient(135deg,var(--primary),var(--primary));border:none;border-radius:8px;"><i class="fa-solid fa-key me-2"></i>Update Password</button>
</form>
</div>
</div>
</div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
