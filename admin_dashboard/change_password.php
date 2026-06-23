<?php
require_once __DIR__ . '/../config/session.php';
require_admin();
$user = current_user();
$err = $_SESSION['error'] ?? '';
$success = $_SESSION['success'] ?? '';
unset($_SESSION['error'], $_SESSION['success']);
?>
<?php include 'includes/head.php'; ?>
<body>
<?php include 'includes/sidebar.php'; ?>
<div class="main-content">
<?php include 'includes/navbar.php'; ?>
<div class="page-header">
<div>
<h1 class="page-title">Change Password</h1>
<p class="page-subtitle mb-0">Update your admin account password</p>
</div>
</div>
<div class="px-4 pb-4">
<div class="card-modern" style="max-width:500px;">
<div class="card-body-modern">
<?php if($err): ?><div class="alert alert-danger d-flex align-items-center py-2 mb-3" style="border-radius:10px;font-size:0.85rem;"><i class="fa-solid fa-circle-exclamation me-2"></i><?php echo htmlspecialchars($err); ?></div><?php endif; ?>
<?php if($success): ?><div class="alert alert-success d-flex align-items-center py-2 mb-3" style="border-radius:10px;font-size:0.85rem;"><i class="fa-solid fa-circle-check me-2"></i><?php echo htmlspecialchars($success); ?></div><?php endif; ?>
<form action="/HRSuite/process/change_admin_password.php" method="POST">
<div class="mb-3">
<label class="form-label-mod">Current Password</label>
<div class="input-group"><span class="input-group-text bg-dark border-secondary text-muted" style="border-radius:10px 0 0 10px;"><i class="fa-solid fa-lock" style="font-size:0.8rem;"></i></span><input type="password" name="current_password" class="form-control form-control-mod bg-dark text-white border-secondary" style="border-radius:0 10px 10px 0;" placeholder="Enter current password" required></div>
</div>
<div class="mb-3">
<label class="form-label-mod">New Password</label>
<div class="input-group"><span class="input-group-text bg-dark border-secondary text-muted" style="border-radius:10px 0 0 10px;"><i class="fa-solid fa-key" style="font-size:0.8rem;"></i></span><input type="password" name="new_password" class="form-control form-control-mod bg-dark text-white border-secondary" style="border-radius:0 10px 10px 0;" placeholder="Min. 6 characters" required minlength="6"></div>
</div>
<div class="mb-4">
<label class="form-label-mod">Confirm New Password</label>
<div class="input-group"><span class="input-group-text bg-dark border-secondary text-muted" style="border-radius:10px 0 0 10px;"><i class="fa-solid fa-key" style="font-size:0.8rem;"></i></span><input type="password" name="confirm_password" class="form-control form-control-mod bg-dark text-white border-secondary" style="border-radius:0 10px 10px 0;" placeholder="Repeat new password" required minlength="6"></div>
</div>
<div class="d-flex gap-2">
<button type="submit" class="btn btn-primary-mod"><i class="fa-solid fa-check me-2"></i>Update Password</button>
<a href="welcome.php" class="btn btn-outline-mod">Cancel</a>
</div>
</form>
</div>
</div>
</div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
