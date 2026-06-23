<?php
require_once __DIR__ . '/../config/session.php';
require_admin();
$user = current_user();
$periods = $pdo->query("SELECT * FROM payroll_periods ORDER BY year DESC, month DESC")->fetchAll();
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
<h1 class="page-title">Process Payroll</h1>
<p class="page-subtitle mb-0">Run payroll for open periods</p>
</div>
</div>
<div class="px-4 pb-4">
<div class="card-modern">
<div class="card-body-modern" style="padding:0;">
<div class="table-responsive">
<table class="table-modern" style="margin:0;">
<thead style="background:#0f172a;">
<tr><th>Month</th><th>Year</th><th>Start</th><th>End</th><th>Status</th><th style="width:120px;">Action</th></tr>
</thead>
<tbody>
<?php foreach($periods as $p): $sc = match($p['status']){'open'=>'status-pending','processing'=>'status-completed','closed'=>'status-active',default=>'status-pending'}; ?>
<tr>
<td><?php echo date('F', mktime(0,0,0,$p['month'],1)); ?></td>
<td><?php echo $p['year']; ?></td>
<td><?php echo date('M j', strtotime($p['start_date'])); ?></td>
<td><?php echo date('M j', strtotime($p['end_date'])); ?></td>
<td><span class="status-badge <?php echo $sc; ?>"><?php echo ucfirst($p['status']); ?></span></td>
<td><?php if($p['status']=='open'):?>
<a href="/HRSuite/process/process_payroll.php?period_id=<?php echo $p['id']; ?>" class="btn btn-sm" style="background:linear-gradient(135deg,var(--primary),var(--primary));color:#fff;border-radius:6px;font-size:0.75rem;" onclick="return confirm('Process payroll?')">
<i class="fa-solid fa-play me-1"></i>Run</a>
<?php endif; ?></td>
</tr>
<?php endforeach; if(empty($periods)): ?>
<tr><td colspan="6" class="text-center text-muted py-5">No payroll periods</td></tr>
<?php endif; ?>
</tbody>
</table>
</div>
</div>
</div>
</div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
