<?php
require_once __DIR__ . '/../config/session.php';
require_auth();
$user = current_user();
$empId = get_employee_id($_SESSION['user_id']);
$data = [];
if ($empId) {
    $stmt = $pdo->prepare("SELECT te.*, tp.title as program FROM training_enrollments te JOIN training_programs tp ON te.program_id = tp.id WHERE te.employee_id = ? AND te.status = 'completed' ORDER BY te.completed_at DESC");
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
<h1 class="page-title">Certificates</h1>
<p class="page-subtitle mb-0">Completed training certificates</p>
</div>
</div>
<div class="px-4 pb-4">
<div class="card-modern">
<div class="card-body-modern" style="padding:0;">
<div class="table-responsive">
<table class="table" style="margin:0;">
<thead style="background:#f8fafc;">
<tr><th>Program</th><th>Completed</th><th>Progress</th></tr>
</thead>
<tbody>
<?php foreach($data as $row): ?>
<tr><td><?php echo htmlspecialchars($row['program'] ?? '-'); ?></td><td><?php
$showDate = !empty($row['completed_at']) && $row['completed_at'] !== '0000-00-00 00:00:00';
if ($showDate) {
    $ts = strtotime($row['completed_at']);
    echo $ts !== false ? date('M j, Y', $ts) : '-';
} else {
    echo '-';
}
?></td><td><div class="progress" style="height:6px;border-radius:4px;"><div class="progress-bar" style="width:<?php echo (int)($row['progress_percent']??0); ?>%;background:var(--primary);"></div></div> <?php echo (int)($row['progress_percent']??0); ?>%</td></tr>
<?php endforeach; if(empty($data)): ?>
<tr><td colspan="3" class="text-center text-muted py-5">No records found</td></tr>
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
