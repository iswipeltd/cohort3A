<?php
require_once __DIR__ . '/../config/session.php';
require_auth();
$user = current_user();
$empId = get_employee_id($_SESSION['user_id']);
$data = [];
if ($empId) {
    $stmt = $pdo->prepare("SELECT * FROM tasks WHERE assigned_to = ? ORDER BY due_date DESC");
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
<h1 class="page-title">Submit Task</h1>
<p class="page-subtitle mb-0">Create a new task for yourself</p>
</div>
</div>
<div class="px-4 pb-4">
<div class="row g-3">
<div class="col-lg-5">
<div class="card-modern">
<div class="card-body-modern">
<h5 class="fw-bold mb-3" style="font-size:0.95rem;"><i class="fa-solid fa-plus-circle me-2" style="color:var(--primary);"></i>New Task</h5>
<form action="/HRSuite/process/add_task.php" method="POST">
<div class="mb-3">
<label class="form-label small fw-semibold text-muted">Title <span class="text-danger">*</span></label>
<input type="text" name="title" class="form-control form-control-modern" placeholder="e.g. Prepare monthly report" required>
</div>
<div class="mb-3">
<label class="form-label small fw-semibold text-muted">Description</label>
<textarea name="description" class="form-control form-control-modern" rows="3" placeholder="Describe the task..."></textarea>
</div>
<div class="mb-3">
<label class="form-label small fw-semibold text-muted">Project</label>
<input type="text" name="project" class="form-control form-control-modern" placeholder="e.g. Q2 Analytics">
</div>
<div class="row g-2 mb-3">
<div class="col-6">
<label class="form-label small fw-semibold text-muted">Due Date</label>
<input type="date" name="due_date" class="form-control form-control-modern">
</div>
<div class="col-6">
<label class="form-label small fw-semibold text-muted">Priority</label>
<select name="priority" class="form-select form-control-modern">
<option value="low">Low</option>
<option value="medium" selected>Medium</option>
<option value="high">High</option>
<option value="urgent">Urgent</option>
</select>
</div>
</div>
<button type="submit" class="btn btn-primary w-100" style="background:var(--primary);border:none;font-weight:700;padding:12px;border-radius:10px;"><i class="fa-solid fa-paper-plane me-2"></i>Create Task</button>
</form>
</div>
</div>
</div>
<div class="col-lg-7">
<div class="card-modern">
<div class="card-body-modern" style="padding:0;">
<div class="table-responsive">
<table class="table" style="margin:0;">
<thead style="background:#f8fafc;">
<tr><th>Title</th><th>Priority</th><th>Progress</th><th>Due</th><th>Status</th></tr>
</thead>
<tbody>
<?php foreach($data as $row): ?>
<tr>
<td class="fw-semibold small"><?php echo htmlspecialchars($row['title'] ?? '-'); ?></td>
<td><?php echo ucfirst($row['priority'] ?? '-'); ?></td>
<td>
<div class="progress" style="height:6px;border-radius:4px;"><div class="progress-bar" style="width:<?php echo $row['progress']; ?>%;background:var(--primary);"></div></div>
<small class="text-muted"><?php echo $row['progress']; ?>%</small>
</td>
<td><?php echo $row['due_date'] ? date('M j, Y', strtotime($row['due_date'])) : '-'; ?></td>
<td><span class="status-badge <?php echo $row['status']=='completed' ? 'status-active' : ($row['status']=='in_progress' ? 'status-completed' : 'status-pending'); ?>"><?php echo ucfirst(str_replace('_',' ',$row['status'])); ?></span></td>
</tr>
<?php endforeach; if(empty($data)): ?>
<tr><td colspan="5" class="text-center text-muted py-5">No tasks yet. Create one on the left!</td></tr>
<?php endif; ?>
</tbody>
</table>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
