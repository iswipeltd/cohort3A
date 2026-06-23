<?php
require_once __DIR__ . '/../config/session.php';
require_admin();
$user = current_user();
$stats = $pdo->query("SELECT 
    (SELECT COUNT(*) FROM users) as total_users,
    (SELECT COUNT(*) FROM employees) as total_employees,
    (SELECT COUNT(*) FROM departments) as total_depts,
    (SELECT COUNT(*) FROM leave_requests WHERE status='pending') as pending_leaves,
    (SELECT COUNT(*) FROM attendance WHERE record_date=CURDATE()) as today_attendance,
    (SELECT COUNT(*) FROM job_postings WHERE status='open') as open_jobs,
    (SELECT COUNT(*) FROM candidates WHERE status='applied') as new_candidates,
    (SELECT SUM(net_pay) FROM payroll_records WHERE status='paid') as total_payroll
")->fetch();
?>
<?php include 'includes/head.php'; ?><body><?php include 'includes/sidebar.php'; ?><div class="main-content"><?php include 'includes/navbar.php'; ?>
<?php $err=$_SESSION['error']??''; $success=$_SESSION['success']??''; unset($_SESSION['error'],$_SESSION['success']); ?>
<?php if($err):?><div class="alert alert-danger d-flex align-items-center py-2 mb-3" style="border-radius:10px;font-size:0.85rem;"><i class="fa-solid fa-circle-exclamation me-2"></i><?php echo htmlspecialchars($err);?></div><?php endif;?>
<?php if($success):?><div class="alert alert-success d-flex align-items-center py-2 mb-3" style="border-radius:10px;font-size:0.85rem;"><i class="fa-solid fa-circle-check me-2"></i><?php echo htmlspecialchars($success);?></div><?php endif;?>

<div class="page-header"><div><h1 class="page-title">Dashboard Metrics</h1><p class="page-subtitle mb-0">Key performance indicators</p></div></div>
<div class="px-4 pb-4">
<div class="row g-3 mb-4">
<div class="col-md-3"><div class="card-modern"><div class="card-body-modern"><div class="text-muted small mb-1">Total Users</div><div class="fw-bold" style="font-size:1.6rem;color:var(--primary);"><?php echo $stats['total_users']; ?></div></div></div></div>
<div class="col-md-3"><div class="card-modern"><div class="card-body-modern"><div class="text-muted small mb-1">Employees</div><div class="fw-bold" style="font-size:1.6rem;color:#059669;"><?php echo $stats['total_employees']; ?></div></div></div></div>
<div class="col-md-3"><div class="card-modern"><div class="card-body-modern"><div class="text-muted small mb-1">Departments</div><div class="fw-bold" style="font-size:1.6rem;color:#d97706;"><?php echo $stats['total_depts']; ?></div></div></div></div>
<div class="col-md-3"><div class="card-modern"><div class="card-body-modern"><div class="text-muted small mb-1">Pending Leaves</div><div class="fw-bold" style="font-size:1.6rem;color:#dc2626;"><?php echo $stats['pending_leaves']; ?></div></div></div></div>
<div class="col-md-3"><div class="card-modern"><div class="card-body-modern"><div class="text-muted small mb-1">Today's Attendance</div><div class="fw-bold" style="font-size:1.6rem;color:#7c3aed;"><?php echo $stats['today_attendance']; ?></div></div></div></div>
<div class="col-md-3"><div class="card-modern"><div class="card-body-modern"><div class="text-muted small mb-1">Open Jobs</div><div class="fw-bold" style="font-size:1.6rem;color:#2563eb;"><?php echo $stats['open_jobs']; ?></div></div></div></div>
<div class="col-md-3"><div class="card-modern"><div class="card-body-modern"><div class="text-muted small mb-1">New Candidates</div><div class="fw-bold" style="font-size:1.6rem;color:#ea580c;"><?php echo $stats['new_candidates']; ?></div></div></div></div>
<div class="col-md-3"><div class="card-modern"><div class="card-body-modern"><div class="text-muted small mb-1">Total Payroll</div><div class="fw-bold" style="font-size:1.6rem;color:#0891b2;">₦<?php echo number_format($stats['total_payroll']??0,0); ?></div></div></div></div>
</div>
</div></div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script></body></html>
