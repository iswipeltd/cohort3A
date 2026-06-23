<?php
require_once __DIR__ . '/../config/session.php';
require_auth();
$user = current_user();
$empId = get_employee_id($_SESSION['user_id']);
$data = [];
if ($empId) {
    $stmt = $pdo->prepare("SELECT pr.*, pp.month, pp.year FROM payroll_records pr JOIN payroll_periods pp ON pr.period_id = pp.id WHERE pr.employee_id = ? AND pr.status='paid' ORDER BY pp.year DESC, pp.month DESC LIMIT 1");
    $stmt->execute([$empId]);
    $data = $stmt->fetchAll();
}
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
<h1 class="page-title">Payment Details</h1>
<p class="page-subtitle mb-0">Latest payment breakdown</p>
</div>
</div>
<div class="px-4 pb-4">
<div class="card-modern">
<div class="card-body-modern">
<?php if(!empty($data)): $row = $data[0]; ?>
<div class="row g-3">
<div class="col-md-6"><div class="p-3" style="background:var(--bg);border-radius:10px;"><div class="text-muted small">Period</div><div class="fw-bold"><?php echo date('F Y', mktime(0,0,0,$row['month'],1,$row['year'])); ?></div></div></div>
<div class="col-md-6"><div class="p-3" style="background:var(--bg);border-radius:10px;"><div class="text-muted small">Base Salary</div><div class="fw-bold">₦<?php echo number_format($row['base_salary'],2); ?></div></div></div>
<div class="col-md-6"><div class="p-3" style="background:#f0fdf4;border-radius:10px;"><div class="text-muted small">Bonus</div><div class="fw-bold" style="color:#047857;">+₦<?php echo number_format($row['bonus'],2); ?></div></div></div>
<div class="col-md-6"><div class="p-3" style="background:#f0fdf4;border-radius:10px;"><div class="text-muted small">Overtime</div><div class="fw-bold" style="color:#047857;">+₦<?php echo number_format($row['overtime_pay'],2); ?></div></div></div>
<div class="col-md-6"><div class="p-3" style="background:#f0fdf4;border-radius:10px;"><div class="text-muted small">Allowances</div><div class="fw-bold" style="color:#047857;">+₦<?php echo number_format($row['allowances'],2); ?></div></div></div>
<div class="col-md-6"><div class="p-3" style="background:#fef2f2;border-radius:10px;"><div class="text-muted small">Tax</div><div class="fw-bold" style="color:#b91c1c;">-₦<?php echo number_format($row['tax'],2); ?></div></div></div>
<div class="col-md-12"><div class="p-3" style="background:#e0e7ff;border-radius:10px;"><div class="text-muted small">Net Pay</div><div class="fw-bold" style="color:var(--primary);font-size:1.2rem;">₦<?php echo number_format($row['net_pay'],2); ?></div></div></div>
</div>
<?php else: ?>
<div class="text-center py-5 text-muted">No payment details available</div>
<?php endif; ?>
</div>
</div>
</div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
