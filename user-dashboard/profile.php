<?php
require_once __DIR__ . '/../config/session.php';
require_auth();
$user = current_user();
$data = $pdo->prepare("SELECT u.*, e.employee_code, e.salary, e.employment_type, e.status as emp_status, d.name as dept, r.name as role FROM users u LEFT JOIN employees e ON u.id = e.user_id LEFT JOIN departments d ON e.department_id = d.id LEFT JOIN roles r ON e.role_id = r.id WHERE u.id = ?");
$data->execute([$_SESSION['user_id']]);
$profile = $data->fetch();
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
<h1 class="page-title">My Profile</h1>
<p class="page-subtitle mb-0">Personal information</p>
</div>
</div>
<div class="px-4 pb-4">
<div class="card-modern">
<div class="card-body-modern">
<?php if($profile): ?>
<div class="row g-3">
<div class="col-md-6"><div class="p-3" style="background:var(--bg);border-radius:10px;"><div class="text-muted small">Full Name</div><div class="fw-bold"><?php echo htmlspecialchars(($profile['first_name']??'').' '.($profile['last_name']??'')); ?></div></div></div>
<div class="col-md-6"><div class="p-3" style="background:var(--bg);border-radius:10px;"><div class="text-muted small">Email</div><div class="fw-bold"><?php echo htmlspecialchars($profile['email']); ?></div></div></div>
<div class="col-md-6"><div class="p-3" style="background:var(--bg);border-radius:10px;"><div class="text-muted small">Phone</div><div class="fw-bold"><?php echo htmlspecialchars($profile['phone']??'-'); ?></div></div></div>
<div class="col-md-6"><div class="p-3" style="background:var(--bg);border-radius:10px;"><div class="text-muted small">Department</div><div class="fw-bold"><?php echo htmlspecialchars($profile['dept']??'-'); ?></div></div></div>
<div class="col-md-6"><div class="p-3" style="background:var(--bg);border-radius:10px;"><div class="text-muted small">Role</div><div class="fw-bold"><?php echo htmlspecialchars($profile['role']??'-'); ?></div></div></div>
<div class="col-md-6"><div class="p-3" style="background:var(--bg);border-radius:10px;"><div class="text-muted small">Employee Code</div><div class="fw-bold"><?php echo htmlspecialchars($profile['employee_code']??'-'); ?></div></div></div>
<div class="col-md-6"><div class="p-3" style="background:var(--bg);border-radius:10px;"><div class="text-muted small">Salary</div><div class="fw-bold">₦<?php echo number_format($profile['salary']??0,2); ?></div></div></div>
<div class="col-md-6"><div class="p-3" style="background:var(--bg);border-radius:10px;"><div class="text-muted small">Status</div><div class="fw-bold"><?php echo ucfirst($profile['emp_status']??'-'); ?></div></div></div>
</div>
<?php else: ?>
<div class="text-center py-5 text-muted">No profile found</div>
<?php endif; ?>
</div>
</div>
</div>
</div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
