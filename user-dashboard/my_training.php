<?php
require_once __DIR__ . '/../config/session.php';
require_auth();
$user = current_user();
$empId = get_employee_id($_SESSION['user_id']);
$data = [];
if ($empId) {
    $stmt = $pdo->prepare("SELECT te.*, tp.title as program FROM training_enrollments te JOIN training_programs tp ON te.program_id = tp.id WHERE te.employee_id = ? ORDER BY te.enrolled_at DESC");
    $stmt->execute([$empId]);
    $data = $stmt->fetchAll();
}
?>
<?php include 'includes/head.php'; ?><body><?php include 'includes/sidebar.php'; ?><div class="main-content"><?php include 'includes/navbar.php'; ?>
<?php $err=$_SESSION['error']??''; $success=$_SESSION['success']??''; unset($_SESSION['error'],$_SESSION['success']); ?>
<?php if($err):?><div class="alert alert-danger d-flex align-items-center py-2 mb-3" style="border-radius:10px;font-size:0.85rem;"><i class="fa-solid fa-circle-exclamation me-2"></i><?php echo htmlspecialchars($err);?></div><?php endif;?>
<?php if($success):?><div class="alert alert-success d-flex align-items-center py-2 mb-3" style="border-radius:10px;font-size:0.85rem;"><i class="fa-solid fa-circle-check me-2"></i><?php echo htmlspecialchars($success);?></div><?php endif;?>

<div class="page-header"><div><h1 class="page-title">My Training</h1><p class="page-subtitle mb-0">Your enrolled programs</p></div></div>
<div class="px-4 pb-4">
<div class="card-modern"><div class="card-body-modern" style="padding:0;"><div class="table-responsive"><table class="table" style="margin:0;"><thead style="background:#f8fafc;"><tr><th>Program</th><th>Status</th><th>Progress</th><th>Completed</th></tr></thead><tbody>
<?php foreach($data as $row):
$stCls = match($row['status']){'completed'=>'status-active','in_progress'=>'status-processing','pending'=>'status-pending','dropped'=>'status-rejected',default=>'status-pending'};
?>
<tr><td class="fw-semibold small"><?php echo htmlspecialchars($row['program']); ?></td><td><span class="status-badge <?php echo $stCls; ?>"><?php echo ucfirst(str_replace('_',' ',$row['status'])); ?></span></td><td><div class="progress" style="height:6px;border-radius:4px;"><div class="progress-bar" style="width:<?php echo (int)($row['progress_percent']??0); ?>%;background:var(--primary);"></div></div> <?php echo (int)($row['progress_percent']??0); ?>%</td><td><?php
$showDate = !empty($row['completed_at']) && $row['completed_at'] !== '0000-00-00 00:00:00';
if ($showDate) {
    $ts = strtotime($row['completed_at']);
    echo $ts !== false ? date('M j, Y', $ts) : '-';
} else {
    echo '-';
}
?></td></tr>
<?php endforeach; if(empty($data)): ?><tr><td colspan="4" class="text-center text-muted py-5">No enrolled programs</td></tr><?php endif; ?>
</tbody></table></div></div></div>
</div></div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script></body></html>
