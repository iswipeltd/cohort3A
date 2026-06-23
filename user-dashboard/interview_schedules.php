<?php
require_once __DIR__ . '/../config/session.php';
require_auth();
$user = current_user();
$data = $pdo->prepare("SELECT c.*, jp.title as job_title FROM candidates c LEFT JOIN job_postings jp ON c.job_posting_id = jp.id WHERE c.interview_date IS NOT NULL ORDER BY c.interview_date DESC");
$data->execute([$_SESSION['user_id']]);
$data = $data->fetchAll();
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
<h1 class="page-title">Interview Schedules</h1>
<p class="page-subtitle mb-0">Upcoming interviews</p>
</div>
</div>
<div class="px-4 pb-4">
<div class="card-modern">
<div class="card-body-modern" style="padding:0;">
<div class="table-responsive">
<table class="table" style="margin:0;">
<thead style="background:#f8fafc;">
<tr><th>Name</th><th>Position</th><th>Date</th><th>Status</th></tr>
</thead>
<tbody>
<?php foreach($data as $row): ?>
<tr><td><?php echo htmlspecialchars($row['name'] ?? '-'); ?></td><td><?php echo htmlspecialchars($row['job_title'] ?? '-'); ?></td><td><?php echo $row['interview_date'] ? date('M j, Y', strtotime($row['interview_date'])) : '-'; ?></td><td><?php echo htmlspecialchars($row['interview_status'] ?? '-'); ?></td></tr>
<?php endforeach; if(empty($data)): ?>
<tr><td colspan="4" class="text-center text-muted py-5">No records found</td></tr>
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
