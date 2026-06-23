<?php
require_once __DIR__ . '/../config/session.php';
require_auth();
$user = current_user();
$employee = current_employee();
$empId = get_employee_id($_SESSION['user_id']);
$err = $_SESSION['error'] ?? ''; $success = $_SESSION['success'] ?? '';
unset($_SESSION['error'], $_SESSION['success']);
$types = $pdo->query("SELECT id, name, default_days FROM leave_types WHERE status = 'active'")->fetchAll();
?><?php include 'includes/head.php'; ?><body><?php include 'includes/sidebar.php'; ?><div class="main-content"><?php include 'includes/navbar.php'; ?>
<div class="page-header"><div><h1 class="page-title">Apply for Leave</h1><p class="page-subtitle mb-0">Submit a new leave request</p></div></div>
<div class="px-4 pb-4">
<?php if($err):?><div class="alert alert-danger d-flex align-items-center py-2 mb-3" style="border-radius:10px;font-size:0.85rem;"><i class="fa-solid fa-circle-exclamation me-2"></i><?php echo htmlspecialchars($err);?></div><?php endif;?>
<?php if($success):?><div class="alert alert-success d-flex align-items-center py-2 mb-3" style="border-radius:10px;font-size:0.85rem;"><i class="fa-solid fa-circle-check me-2"></i><?php echo htmlspecialchars($success);?></div><?php endif;?>
<div class="row g-3">
<div class="col-lg-8"><div class="card-modern"><div class="card-body-modern">
<form action="/HRSuite/process/apply_leave.php" method="POST">
<div class="row g-3">
<div class="col-md-6"><label class="form-label-mod">Leave Type *</label><select name="leave_type_id" class="form-select" required><option value="">Select Type</option><?php foreach($types as $t):?><option value="<?php echo $t['id'];?>"><?php echo htmlspecialchars($t['name']);?> (<?php echo $t['default_days'];?> days)</option><?php endforeach;?></select></div>
<div class="col-md-6"><label class="form-label-mod">Number of Days *</label><input type="number" name="days" min="1" class="form-control form-control-mod" required></div>
<div class="col-md-6"><label class="form-label-mod">Start Date *</label><input type="date" name="start_date" class="form-control form-control-mod" required></div>
<div class="col-md-6"><label class="form-label-mod">End Date *</label><input type="date" name="end_date" class="form-control form-control-mod" required></div>
<div class="col-md-12"><label class="form-label-mod">Reason</label><textarea name="reason" class="form-control form-control-mod" rows="3" placeholder="Enter reason for leave..."></textarea></div>
</div>
<div class="mt-3"><button type="submit" class="btn-primary-mod">Submit Request</button> <a href="index.php" class="btn-outline-mod">Cancel</a></div>
</form>
</div></div></div>
<div class="col-lg-4"><div class="card-modern"><div class="card-header-modern"><h6 class="fw-bold mb-0">Leave Balances</h6></div><div class="card-body-modern">
<?php foreach($types as $t):
$s = $pdo->prepare("SELECT COALESCE(SUM(days),0) FROM leave_requests WHERE employee_id=? AND leave_type_id=? AND status='approved' AND YEAR(start_date)=YEAR(CURDATE())");
$s->execute([$empId,$t['id']]); $used=(int)$s->fetchColumn(); $bal=max(0,(int)$t['default_days']-$used);
?><div class="d-flex justify-content-between align-items-center py-2" style="border-bottom:1px solid #f1f5f9;"><span class="small"><?php echo htmlspecialchars($t['name']);?></span><span class="fw-bold small" style="color:<?php echo $bal>3?'#047857':'#92400e';?>"><?php echo $bal;?> days</span></div>
<?php endforeach;?>
</div></div></div>
</div></div></div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script></body></html>
