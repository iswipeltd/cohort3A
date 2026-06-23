<?php
require_once __DIR__ . '/../config/session.php';
require_admin();
$user = current_user();
$stats = $pdo->query("SELECT 
    (SELECT COUNT(*) FROM users WHERE status='active') as total_users,
    (SELECT COUNT(*) FROM employees WHERE status='active') as total_employees,
    (SELECT COUNT(*) FROM departments WHERE status='active') as total_depts,
    (SELECT COUNT(*) FROM leave_requests WHERE status='pending') as pending_leaves,
    (SELECT COUNT(*) FROM job_postings WHERE status='open') as open_jobs
")->fetch();
?>
<?php include 'includes/head.php'; ?><body><?php include 'includes/sidebar.php'; ?><div class="main-content"><?php include 'includes/navbar.php'; ?>
<div class="page-header"><div><h1 class="page-title">Welcome</h1><p class="page-subtitle mb-0">HR Dashboard Overview</p></div></div>
<div class="px-4 pb-4">
<?php $err = $_SESSION['error'] ?? ''; $success = $_SESSION['success'] ?? ''; unset($_SESSION['error'], $_SESSION['success']); ?>
    <?php if($err):?><div class="alert alert-danger d-flex align-items-center py-2 mb-3" style="border-radius:10px;font-size:0.85rem;"><i class="fa-solid fa-circle-exclamation me-2"></i><?php echo htmlspecialchars($err);?></div><?php endif;?>
    <?php if($success):?><div class="alert alert-success d-flex align-items-center py-2 mb-3" style="border-radius:10px;font-size:0.85rem;"><i class="fa-solid fa-circle-check me-2"></i><?php echo htmlspecialchars($success);?></div><?php endif;?>

<div class="row g-3 mb-4">
<div class="col-md-3"><div class="card-modern"><div class="card-body-modern"><div class="text-muted small mb-1">Active Users</div><div class="fw-bold" style="font-size:1.6rem;color:var(--primary);"><?php echo $stats['total_users']; ?></div></div></div></div>
<div class="col-md-3"><div class="card-modern"><div class="card-body-modern"><div class="text-muted small mb-1">Employees</div><div class="fw-bold" style="font-size:1.6rem;color:#059669;"><?php echo $stats['total_employees']; ?></div></div></div></div>
<div class="col-md-3"><div class="card-modern"><div class="card-body-modern"><div class="text-muted small mb-1">Departments</div><div class="fw-bold" style="font-size:1.6rem;color:#d97706;"><?php echo $stats['total_depts']; ?></div></div></div></div>
<div class="col-md-3"><div class="card-modern"><div class="card-body-modern"><div class="text-muted small mb-1">Pending Leaves</div><div class="fw-bold" style="font-size:1.6rem;color:#dc2626;"><?php echo $stats['pending_leaves']; ?></div></div></div></div>
</div>
<div class="card-modern"><div class="card-body-modern">
<div class="d-flex align-items-center p-3 mb-3" style="background:var(--card-hover);border-radius:12px;border-left:4px solid var(--accent);">
<i class="fa-solid fa-circle-info me-3" style="color:var(--accent);font-size:1.2rem;"></i>
<div><div class="fw-semibold small">Welcome to ADEEEEE</div><div class="text-muted" style="font-size:0.8rem;">Manage your organization from this centralized admin dashboard.</div></div>
</div>
</div></div>
</div></div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script></body></html>
