<?php
require_once __DIR__ . '/../config/session.php';
require_auth();
$user = current_user();
$empId = get_employee_id($_SESSION['user_id']);
$tasks = [];
if ($empId) {
    $stmt = $pdo->prepare("SELECT * FROM tasks WHERE assigned_to = ? ORDER BY created_at DESC");
    $stmt->execute([$empId]);
    $tasks = $stmt->fetchAll();
}
?><?php include 'includes/head.php'; ?><body><?php include 'includes/sidebar.php'; ?><div class="main-content"><?php include 'includes/navbar.php'; ?>
<?php $err=$_SESSION['error']??''; $success=$_SESSION['success']??''; unset($_SESSION['error'],$_SESSION['success']); ?>
<?php if($err):?><div class="alert alert-danger d-flex align-items-center py-2 mb-3" style="border-radius:10px;font-size:0.85rem;"><i class="fa-solid fa-circle-exclamation me-2"></i><?php echo htmlspecialchars($err);?></div><?php endif;?>
<?php if($success):?><div class="alert alert-success d-flex align-items-center py-2 mb-3" style="border-radius:10px;font-size:0.85rem;"><i class="fa-solid fa-circle-check me-2"></i><?php echo htmlspecialchars($success);?></div><?php endif;?>

<div class="page-header"><div><h1 class="page-title">My Tasks</h1><p class="page-subtitle mb-0">Your assigned work items</p></div></div>
<div class="px-4 pb-4">
<div class="card-modern"><div class="card-body-modern" style="padding:0;"><div class="table-responsive"><table class="table" style="margin:0;"><thead style="background:#f8fafc;"><tr><th>Title</th><th>Project</th><th>Due</th><th>Priority</th><th>Progress</th><th>Status</th></tr></thead><tbody>
<?php foreach($tasks as $t):
$sc = match($t['status']){'completed'=>'status-active','in_progress'=>'status-completed',default=>'status-pending'};
$prio = match($t['priority']){'high'=>'#ef4444','medium'=>'#f59e0b',default=>'var(--text2)'};
?>
<tr><td class="fw-semibold small"><?php echo htmlspecialchars($t['title']);?></td><td><?php echo htmlspecialchars($t['project']??'-');?></td><td><?php echo $t['due_date']?date('M j',strtotime($t['due_date'])):'-';?></td><td><span style="color:<?php echo $prio;?>;font-weight:600;font-size:0.75rem;"><?php echo ucfirst($t['priority']);?></span></td><td><div class="progress" style="height:6px;border-radius:4px;"><div class="progress-bar" style="width:<?php echo $t['progress'];?>%;background:var(--primary);"></div></div><small class="text-muted"><?php echo $t['progress'];?>%</small></td><td><span class="status-badge <?php echo $sc;?>"><?php echo ucfirst(str_replace('_',' ',$t['status']));?></span></td></tr>
<?php endforeach; if(empty($tasks)):?><tr><td colspan="6" class="text-center text-muted py-5">No tasks assigned</td></tr><?php endif;?>
</tbody></table></div></div></div>
</div></div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script></body></html>
