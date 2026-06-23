<?php
require_once __DIR__ . '/../config/session.php';
require_admin();
$user = current_user();

// Fetch all tasks with employee names
$tasks = $pdo->query("
    SELECT t.*, e.id as emp_id, u.first_name, u.last_name, u.email, creator.first_name as creator_fn, creator.last_name as creator_ln
    FROM tasks t
    LEFT JOIN employees e ON t.assigned_to = e.id
    LEFT JOIN users u ON e.user_id = u.id
    LEFT JOIN users creator ON t.assigned_by = creator.id
    ORDER BY t.created_at DESC
")->fetchAll();

// Fetch employees for assignment dropdown
$employees = $pdo->query("
    SELECT e.id, u.first_name, u.last_name, u.email, d.name as dept
    FROM employees e
    JOIN users u ON e.user_id = u.id
    LEFT JOIN departments d ON e.department_id = d.id
    WHERE e.status = 'active'
    ORDER BY u.first_name
")->fetchAll();

$err = $_SESSION['error'] ?? '';
$success = $_SESSION['success'] ?? '';
unset($_SESSION['error'], $_SESSION['success']);
?>
<?php include 'includes/head.php'; ?><body><?php include 'includes/sidebar.php'; ?><div class="main-content"><?php include 'includes/navbar.php'; ?>
<?php if($err):?><div class="alert alert-danger d-flex align-items-center py-2 mb-3 mx-4" style="border-radius:10px;font-size:0.85rem;"><i class="fa-solid fa-circle-exclamation me-2"></i><?php echo htmlspecialchars($err);?></div><?php endif;?>
<?php if($success):?><div class="alert alert-success d-flex align-items-center py-2 mb-3 mx-4" style="border-radius:10px;font-size:0.85rem;"><i class="fa-solid fa-circle-check me-2"></i><?php echo htmlspecialchars($success);?></div><?php endif;?>

<div class="page-header"><div><h1 class="page-title">Tasks</h1><p class="page-subtitle mb-0">Manage and assign tasks</p></div><a href="#" onclick="document.getElementById('taskForm').scrollIntoView({behavior:'smooth'});" class="btn btn-primary-mod"><i class="fa-solid fa-plus me-2"></i>New Task</a></div>
<div class="px-4 pb-4">
<div class="row g-3 mb-4">
<div class="col-md-3"><div class="card-modern"><div class="card-body-modern"><div style="color:var(--muted);font-size:0.75rem;font-weight:600;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:4px;">Total Tasks</div><div class="fw-bold" style="font-size:1.6rem;color:var(--primary);"><?php echo count($tasks); ?></div></div></div></div>
<div class="col-md-3"><div class="card-modern"><div class="card-body-modern"><div style="color:var(--muted);font-size:0.75rem;font-weight:600;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:4px;">Open</div><div class="fw-bold" style="font-size:1.6rem;color:var(--success);"><?php echo count(array_filter($tasks, fn($t)=>$t['status']=='open')); ?></div></div></div></div>
<div class="col-md-3"><div class="card-modern"><div class="card-body-modern"><div style="color:var(--muted);font-size:0.75rem;font-weight:600;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:4px;">In Progress</div><div class="fw-bold" style="font-size:1.6rem;color:var(--warning);"><?php echo count(array_filter($tasks, fn($t)=>$t['status']=='in_progress')); ?></div></div></div></div>
<div class="col-md-3"><div class="card-modern"><div class="card-body-modern"><div style="color:var(--muted);font-size:0.75rem;font-weight:600;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:4px;">Completed</div><div class="fw-bold" style="font-size:1.6rem;color:var(--info);"><?php echo count(array_filter($tasks, fn($t)=>$t['status']=='completed')); ?></div></div></div></div>
</div>

<div class="row g-3">
<div class="col-lg-4">
<div class="card-modern" id="taskForm">
<div class="card-body-modern">
<h5 class="fw-bold mb-3" style="font-size:0.95rem;color:var(--text);"><i class="fa-solid fa-plus-circle me-2" style="color:var(--primary);"></i>Create Task</h5>
<form action="/HRSuite/process/add_task.php" method="POST">
<div class="mb-3">
<label class="form-label-mod">Title <span style="color:var(--danger);">*</span></label>
<input type="text" name="title" class="form-control form-control-mod" placeholder="Task title" required>
</div>
<div class="mb-3">
<label class="form-label-mod">Description</label>
<textarea name="description" class="form-control form-control-mod" rows="2" placeholder="Task details..."></textarea>
</div>
<div class="mb-3">
<label class="form-label-mod">Project</label>
<input type="text" name="project" class="form-control form-control-mod" placeholder="e.g. Q2 Analytics">
</div>
<div class="mb-3">
<label class="form-label-mod">Assign To</label>
<select name="assigned_to" class="form-select form-control-mod" required>
<option value="">-- Select Employee --</option>
<?php foreach($employees as $e): ?>
<option value="<?php echo $e['id']; ?>"><?php echo htmlspecialchars($e['first_name'] . ' ' . $e['last_name']); ?> (<?php echo htmlspecialchars($e['dept'] ?? 'N/A'); ?>)</option>
<?php endforeach; ?>
</select>
</div>
<div class="row g-2 mb-3">
<div class="col-6">
<label class="form-label-mod">Due Date</label>
<input type="date" name="due_date" class="form-control form-control-mod">
</div>
<div class="col-6">
<label class="form-label-mod">Priority</label>
<select name="priority" class="form-select form-control-mod">
<option value="low">Low</option>
<option value="medium" selected>Medium</option>
<option value="high">High</option>
<option value="urgent">Urgent</option>
</select>
</div>
</div>
<button type="submit" class="btn btn-primary-mod w-100"><i class="fa-solid fa-paper-plane me-2"></i>Create & Assign</button>
</form>
</div>
</div>
</div>
<div class="col-lg-8">
<div class="card-modern" style="padding:0;">
<table class="table-modern" style="width:100%;">
<thead>
<tr><th>Task</th><th>Assigned To</th><th>Priority</th><th>Progress</th><th>Due</th><th>Status</th><th style="width:90px;">Action</th></tr>
</thead>
<tbody>
<?php foreach($tasks as $t):
$sc = match($t['status']){'completed'=>'status-active','in_progress'=>'status-processing','open'=>'status-pending','review'=>'status-probation','cancelled'=>'status-rejected',default=>'status-pending'};
$prioColor = match($t['priority']){'high'=>'#ef4444','urgent'=>'#dc2626','medium'=>'#f59e0b',default=>'#94a3b8'};
?>
<tr>
<td class="fw-semibold" style="font-size:0.85rem;">
<?php echo htmlspecialchars($t['title']); ?>
<?php if($t['project']): ?><div style="font-size:0.7rem;color:var(--muted);"><?php echo htmlspecialchars($t['project']); ?></div><?php endif; ?>
</td>
<td><?php echo $t['first_name'] ? htmlspecialchars($t['first_name'] . ' ' . $t['last_name']) : '<span style="color:var(--muted);">Unassigned</span>'; ?></td>
<td><span style="color:<?php echo $prioColor; ?>;font-weight:700;font-size:0.75rem;background:rgba(0,0,0,0.2);padding:3px 10px;border-radius:6px;"><?php echo ucfirst($t['priority']); ?></span></td>
<td>
<div style="height:6px;border-radius:4px;background:var(--border);overflow:hidden;"><div style="width:<?php echo $t['progress']; ?>%;height:100%;background:var(--primary);border-radius:4px;"></div></div>
<div style="font-size:0.7rem;color:var(--muted);margin-top:2px;"><?php echo $t['progress']; ?>%</div>
</td>
<td style="white-space:nowrap;"><?php echo $t['due_date'] ? date('M j, Y', strtotime($t['due_date'])) : '-'; ?></td>
<td><span class="status-badge <?php echo $sc; ?>"><?php echo ucfirst(str_replace('_',' ',$t['status'])); ?></span></td>
<td>
<a href="/HRSuite/admin_dashboard/edit_task.php?id=<?php echo $t['id']; ?>" class="btn btn-outline-mod btn-sm-mod" style="padding:5px 10px;"><i class="fa-solid fa-pen" style="font-size:0.7rem;"></i></a>
<a href="/HRSuite/process/delete_task.php?id=<?php echo $t['id']; ?>" onclick="return confirm('Delete this task?')" class="btn btn-danger-mod btn-sm-mod" style="padding:5px 10px;margin-left:4px;"><i class="fa-solid fa-trash" style="font-size:0.7rem;"></i></a>
</td>
</tr>
<?php endforeach; if(empty($tasks)): ?>
<tr><td colspan="7" style="text-align:center;color:var(--muted);padding:40px;">No tasks found. Create one on the left.</td></tr>
<?php endif; ?>
</tbody>
</table>
</div>
</div>
</div>
</div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body></html>
