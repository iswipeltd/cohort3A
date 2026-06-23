<?php
require_once __DIR__ . '/../config/session.php';
require_auth();
$user = current_user();
$data = $pdo->prepare("SELECT e.*, d.name as dept, r.name as role FROM employees e LEFT JOIN departments d ON e.department_id = d.id LEFT JOIN roles r ON e.role_id = r.id WHERE e.user_id = ?");
$data->execute([$_SESSION['user_id']]);
$emp = $data->fetch();
?>
<?php include 'includes/head.php'; ?><body><?php include 'includes/sidebar.php'; ?><div class="main-content"><?php include 'includes/navbar.php'; ?>
<?php $err=$_SESSION['error']??''; $success=$_SESSION['success']??''; unset($_SESSION['error'],$_SESSION['success']); ?>
<?php if($err):?><div class="alert alert-danger d-flex align-items-center py-2 mb-3" style="border-radius:10px;font-size:0.85rem;"><i class="fa-solid fa-circle-exclamation me-2"></i><?php echo htmlspecialchars($err);?></div><?php endif;?>
<?php if($success):?><div class="alert alert-success d-flex align-items-center py-2 mb-3" style="border-radius:10px;font-size:0.85rem;"><i class="fa-solid fa-circle-check me-2"></i><?php echo htmlspecialchars($success);?></div><?php endif;?>

<div class="page-header"><div><h1 class="page-title">Emergency Contacts</h1><p class="page-subtitle mb-0">Your emergency contact details</p></div></div>
<div class="px-4 pb-4">
<div class="card-modern"><div class="card-body-modern">
<?php if($emp): ?>
<div class="row g-3">
<div class="col-md-6"><div class="p-3" style="background:var(--bg);border-radius:10px;"><div class="text-muted small">Employee Code</div><div class="fw-bold"><?php echo htmlspecialchars($emp['employee_code']??'-'); ?></div></div></div>
<div class="col-md-6"><div class="p-3" style="background:var(--bg);border-radius:10px;"><div class="text-muted small">Department</div><div class="fw-bold"><?php echo htmlspecialchars($emp['dept']??'-'); ?></div></div></div>
<div class="col-md-6"><div class="p-3" style="background:var(--bg);border-radius:10px;"><div class="text-muted small">Role</div><div class="fw-bold"><?php echo htmlspecialchars($emp['role']??'-'); ?></div></div></div>
<div class="col-md-6"><div class="p-3" style="background:var(--bg);border-radius:10px;"><div class="text-muted small">Employment Type</div><div class="fw-bold"><?php echo ucfirst($emp['employment_type']??'-'); ?></div></div></div>
<div class="col-md-6"><div class="p-3" style="background:#fef2f2;border-radius:10px;"><div class="text-muted small">Status</div><div class="fw-bold" style="color:#dc2626;"><?php echo ucfirst($emp['status']??'-'); ?></div></div></div>
<div class="col-md-6"><div class="p-3" style="background:#fef2f2;border-radius:10px;"><div class="text-muted small">Manager ID</div><div class="fw-bold" style="color:#dc2626;"><?php echo $emp['manager_id'] ?? 'N/A'; ?></div></div></div>
</div>
<?php else: ?>
<div class="text-center py-5 text-muted">No emergency contact data</div>
<?php endif; ?>
</div></div></div></div></div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script></body></html>
