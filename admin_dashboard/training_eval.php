<?php
require_once __DIR__ . '/../config/session.php';
require_admin();
$user = current_user();
$data = $pdo->query("SELECT te.*, tp.title as program, CONCAT(u.first_name,' ',u.last_name) as emp_name FROM training_enrollments te JOIN training_programs tp ON te.program_id = tp.id JOIN employees e ON te.employee_id = e.id JOIN users u ON e.user_id = u.id ORDER BY te.enrolled_at DESC LIMIT 100")->fetchAll();
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
<h1 class="page-title">Training Evaluations</h1>
<p class="page-subtitle mb-0">Employee training status</p>
</div>
</div>
<div class="px-4 pb-4">
<div class="card-modern">
<div class="card-body-modern" style="padding:0;">
<div class="table-responsive">
<table class="table-modern" style="margin:0;">
<thead style="background:#0f172a;">
<tr><th>Employee</th><th>Program</th><th>Status</th><th>Progress</th><th>Completed</th></tr>
</thead>
<tbody>
<?php foreach($data as $row): ?>
<tr>
<?php $stCls = match($row['status']){'completed'=>'status-active','in_progress'=>'status-processing','pending'=>'status-pending','dropped'=>'status-rejected',default=>'status-pending'}; ?>
<td class="fw-semibold small"><?php echo htmlspecialchars($row['emp_name']); ?></td>
<td><?php echo htmlspecialchars($row['program']); ?></td>
<td><span class="status-badge <?php echo $stCls; ?>"><?php echo ucfirst(str_replace('_',' ',$row['status'])); ?></span></td>
<td>
<div class="progress" style="height:6px;border-radius:4px;">
<div class="progress-bar" style="width:<?php echo (int)($row['progress_percent']??0); ?>%;background:var(--primary);"></div>
</div>
<?php echo (int)($row['progress_percent']??0); ?>%
</td>
<td><?php echo !empty($row['completed_at']) ? date('M j, Y', strtotime($row['completed_at'])) : '-'; ?></td>
</tr>
<?php endforeach; if(empty($data)): ?>
<tr><td colspan="5" class="text-center text-muted py-5">No evaluations</td></tr>
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
