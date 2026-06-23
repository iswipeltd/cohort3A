<?php
require_once __DIR__ . '/../config/session.php';
require_auth();
$user = current_user();
$empId = get_employee_id($_SESSION['user_id']);
$todayRec = null;
if ($empId) {
    $today = date('Y-m-d');
    $stmt = $pdo->prepare("SELECT * FROM attendance WHERE employee_id = ? AND record_date = ?");
    $stmt->execute([$empId, $today]);
    $todayRec = $stmt->fetch();
}
?><?php include 'includes/head.php'; ?><body><?php include 'includes/sidebar.php'; ?><div class="main-content"><?php include 'includes/navbar.php'; ?>
<?php $err=$_SESSION['error']??''; $success=$_SESSION['success']??''; unset($_SESSION['error'],$_SESSION['success']); ?>
<?php if($err):?><div class="alert alert-danger d-flex align-items-center py-2 mb-3" style="border-radius:10px;font-size:0.85rem;"><i class="fa-solid fa-circle-exclamation me-2"></i><?php echo htmlspecialchars($err);?></div><?php endif;?>
<?php if($success):?><div class="alert alert-success d-flex align-items-center py-2 mb-3" style="border-radius:10px;font-size:0.85rem;"><i class="fa-solid fa-circle-check me-2"></i><?php echo htmlspecialchars($success);?></div><?php endif;?>

<div class="page-header"><div><h1 class="page-title">Clock In / Out</h1><p class="page-subtitle mb-0">Record your attendance</p></div></div>
<div class="px-4 pb-4">
<div class="row g-3 justify-content-center">
<div class="col-md-6">
<div class="card-modern text-center py-5">
<div class="card-body-modern">
<div style="width:80px;height:80px;border-radius:50%;background:<?php echo $todayRec&&$todayRec['clock_out']?'#d1fae5':($todayRec?'#fef3c7':'#dbeafe');?>;color:<?php echo $todayRec&&$todayRec['clock_out']?'#047857':($todayRec?'#92400e':'#1d4ed8');?>;display:flex;align-items:center;justify-content:center;font-size:1.8rem;margin:0 auto 20px;">
<i class="fa-solid fa-<?php echo $todayRec&&$todayRec['clock_out']?'check':($todayRec?'clock':'fingerprint');?>"></i>
</div>
<h4 class="fw-bold mb-1"><?php echo $todayRec&&$todayRec['clock_out']?'Clocked Out':($todayRec?'Clocked In':'Not Clocked');?></h4>
<p class="text-muted small mb-4"><?php echo date('l, F j, Y');?></p>
<?php if(!$todayRec):?>
<form action="/HRSuite/process/clock_in.php" method="POST"><input type="hidden" name="action" value="clock_in"><button type="submit" class="btn-primary-mod" style="padding:14px 40px;font-size:1rem;"><i class="fa-solid fa-fingerprint me-2"></i>Clock In</button></form>
<?php elseif(!$todayRec['clock_out']):?>
<form action="/HRSuite/process/clock_in.php" method="POST"><input type="hidden" name="action" value="clock_out"><button type="submit" class="btn-primary-mod" style="padding:14px 40px;font-size:1rem;background:linear-gradient(135deg,#047857,#10b981);"><i class="fa-solid fa-right-from-bracket me-2"></i>Clock Out</button></form>
<?php else:?>
<div class="alert alert-success py-2 d-inline-block" style="border-radius:10px;font-size:0.85rem;"><i class="fa-solid fa-circle-check me-2"></i>Attendance recorded for today</div>
<?php endif;?>
<?php if($todayRec):?>
<div class="mt-4 pt-3" style="border-top:1px solid #f1f5f9;">
<div class="row g-2 text-center">
<div class="col-4"><div class="text-muted small">Clock In</div><div class="fw-bold"><?php echo $todayRec['clock_in']?date('g:i A',strtotime($todayRec['clock_in'])):'-';?></div></div>
<div class="col-4"><div class="text-muted small">Clock Out</div><div class="fw-bold"><?php echo $todayRec['clock_out']?date('g:i A',strtotime($todayRec['clock_out'])):'-';?></div></div>
<div class="col-4"><div class="text-muted small">Hours</div><div class="fw-bold"><?php echo $todayRec['hours_worked']??'-';?></div></div>
</div>
</div>
<?php endif;?>
</div>
</div>
</div>
</div>
</div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script></body></html>
