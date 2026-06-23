<?php
require_once __DIR__ . '/../config/session.php';
require_auth();
$user = current_user();
$empId = get_employee_id($_SESSION['user_id']);
$periods = $pdo->query("SELECT id, month, year, start_date, end_date, status FROM payroll_periods ORDER BY year DESC, month DESC")->fetchAll();
$payslips = [];
if ($_GET['period']??false) {
    $pid = (int)$_GET['period'];
    $stmt = $pdo->prepare("SELECT pr.*, pp.month, pp.year FROM payroll_records pr JOIN payroll_periods pp ON pr.period_id = pp.id WHERE pr.employee_id = ? AND pr.period_id = ?");
    $stmt->execute([$empId, $pid]);
    $payslips = $stmt->fetchAll();
}
?><?php include 'includes/head.php'; ?><body><?php include 'includes/sidebar.php'; ?><div class="main-content"><?php include 'includes/navbar.php'; ?>
<?php $err=$_SESSION['error']??''; $success=$_SESSION['success']??''; unset($_SESSION['error'],$_SESSION['success']); ?>
<?php if($err):?><div class="alert alert-danger py-2 mb-0" style="border-radius:0;font-size:0.85rem;"><i class="fa-solid fa-circle-exclamation me-2"></i><?php echo htmlspecialchars($err);?></div><?php endif;?>
<?php if($success):?><div class="alert alert-success py-2 mb-0" style="border-radius:0;font-size:0.85rem;"><i class="fa-solid fa-circle-check me-2"></i><?php echo htmlspecialchars($success);?></div><?php endif;?>
<div class="page-header"><div><h1 class="page-title">My Payslips</h1><p class="page-subtitle mb-0">View and download your payslips</p></div></div>
<div class="px-4 pb-4">
<div class="card-modern mb-3"><div class="card-body-modern">
<form method="get" class="row g-2 align-items-end"><div class="col-md-4">
<label class="form-label-mod">Payroll Period</label>
<select name="period" class="form-select" style="border:1.5px solid var(--border);border-radius:10px;padding:10px 14px;background:var(--card);color:var(--text);" onchange="this.form.submit()"><option value="">Select Period</option>
<?php foreach($periods as $p): $name=date('F Y',mktime(0,0,0,$p['month'],1,$p['year'])); ?>
<option value="<?php echo $p['id']; ?>" <?php echo ($_GET['period']??0)==$p['id']?'selected':''; ?>><?php echo $name; ?></option>
<?php endforeach;?></select></div></form>
</div></div>
<?php foreach($payslips as $payslip):?>
<div class="card-modern mb-3"><div class="card-body-modern">
<div class="row g-3"><div class="col-md-6"><h5 class="fw-bold">Payslip - <?php echo date('F Y',mktime(0,0,0,$payslip['month'],1,$payslip['year'])); ?></h5></div><div class="col-md-6 text-md-end"><span class="status-badge <?php echo $payslip['status']=='paid'?'status-active':'status-pending'; ?>"><?php echo ucfirst($payslip['status']); ?></span></div></div>
<hr><div class="row g-3"><div class="col-md-4"><div class="text-muted small">Base Salary</div><div class="fw-bold">₦<?php echo number_format($payslip['base_salary'],2);?></div></div><div class="col-md-4"><div class="text-muted small">Bonus</div><div class="fw-bold">₦<?php echo number_format($payslip['bonus'],2);?></div></div><div class="col-md-4"><div class="text-muted small">Overtime</div><div class="fw-bold">₦<?php echo number_format($payslip['overtime_pay'],2);?></div></div></div>
<hr><div class="row g-3"><div class="col-md-4"><div class="text-muted small">Allowances</div><div class="fw-bold">₦<?php echo number_format($payslip['allowances'],2);?></div></div><div class="col-md-4"><div class="text-muted small">Tax</div><div class="fw-bold text-danger">- ₦<?php echo number_format($payslip['tax'],2);?></div></div><div class="col-md-4"><div class="text-muted small">Net Pay</div><div class="fw-bold" style="font-size:1.2rem;color:var(--primary);">₦<?php echo number_format($payslip['net_pay'],2);?></div></div></div>
<div class="mt-3"><a href="download_payslip.php?record_id=<?php echo $payslip['id']; ?>" class="btn btn-sm btn-outline-mod" target="_blank"><i class="fa-solid fa-file-pdf me-1"></i> Download PDF</a></div>
</div></div>
<?php endforeach; if(empty($payslips) && ($_GET['period']??false)):?>
<div class="text-center text-muted py-5">No payslip found for this period</div>
<?php elseif(empty($payslips)):?>
<div class="text-center text-muted py-5">Select a payroll period to view payslips</div>
<?php endif;?>
</div></div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script></body></html>
