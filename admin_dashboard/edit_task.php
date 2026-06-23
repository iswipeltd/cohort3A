<?php
require_once __DIR__ . '/../config/session.php';
require_admin();
require_once __DIR__ . '/../config/database.php';

$taskId = (int) ($_GET['id'] ?? 0);
if (!$taskId) {
    $_SESSION['error'] = 'Invalid task ID.';
    header('Location: /HRSuite/admin_dashboard/tasks.php');
    exit;
}

$task = $pdo->prepare("
    SELECT t.*, e.id as emp_id, u.first_name, u.last_name
    FROM tasks t
    LEFT JOIN employees e ON t.assigned_to = e.id
    LEFT JOIN users u ON e.user_id = u.id
    WHERE t.id = ?
");
$task->execute([$taskId]);
$t = $task->fetch();

if (!$t) {
    $_SESSION['error'] = 'Task not found.';
    header('Location: /HRSuite/admin_dashboard/tasks.php');
    exit;
}

$employees = $pdo->query("
    SELECT e.id, u.first_name, u.last_name, d.name as dept
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

<div class="page-header"><div><h1 class="page-title">Edit Task</h1><p class="page-subtitle mb-0">Update task details and progress</p></div></div>
<div class="px-4 pb-4">
<div class="row g-3 justify-content-center">
<div class="col-lg-6">
<div class="card-modern">
<div class="card-body-modern">
<form action="/HRSuite/process/edit_task.php" method="POST">
<input type="hidden" name="task_id" value="<?php echo $t['id']; ?>">
<div class="mb-3">
<label class="form-label-mod">Title <span style="color:var(--danger);">*</span></label>
<input type="text" name="title" class="form-control form-control-mod" value="<?php echo htmlspecialchars($t['title']); ?>" required>
</div>
<div class="mb-3">
<label class="form-label-mod">Description</label>
<textarea name="description" class="form-control form-control-mod" rows="3"><?php echo htmlspecialchars($t['description'] ?? ''); ?></textarea>
</div>
<div class="mb-3">
<label class="form-label-mod">Project</label>
<input type="text" name="project" class="form-control form-control-mod" value="<?php echo htmlspecialchars($t['project'] ?? ''); ?>">
</div>
<div class="mb-3">
<label class="form-label-mod">Assign To</label>
<select name="assigned_to" class="form-select form-control-mod">
<option value="">-- Unassigned --</option>
<?php foreach($employees as $e): ?>
<option value="<?php echo $e['id']; ?>" <?php echo $t['assigned_to'] == $e['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($e['first_name'] . ' ' . $e['last_name']); ?> (<?php echo htmlspecialchars($e['dept'] ?? 'N/A'); ?>)</option>
<?php endforeach; ?>
</select>
</div>
<div class="row g-2 mb-3">
<div class="col-6">
<label class="form-label-mod">Due Date</label>
<input type="date" name="due_date" class="form-control form-control-mod" value="<?php echo $t['due_date'] ?? ''; ?>">
</div>
<div class="col-6">
<label class="form-label-mod">Priority</label>
<select name="priority" class="form-select form-control-mod">
<option value="low" <?php echo $t['priority']=='low'?'selected':''; ?>>Low</option>
<option value="medium" <?php echo $t['priority']=='medium'?'selected':''; ?>>Medium</option>
<option value="high" <?php echo $t['priority']=='high'?'selected':''; ?>>High</option>
<option value="urgent" <?php echo $t['priority']=='urgent'?'selected':''; ?>>Urgent</option>
</select>
</div>
</div>
<div class="row g-2 mb-3">
<div class="col-6">
<label class="form-label-mod">Status</label>
<select name="status" class="form-select form-control-mod">
<option value="open" <?php echo $t['status']=='open'?'selected':''; ?>>Open</option>
<option value="in_progress" <?php echo $t['status']=='in_progress'?'selected':''; ?>>In Progress</option>
<option value="review" <?php echo $t['status']=='review'?'selected':''; ?>>Review</option>
<option value="completed" <?php echo $t['status']=='completed'?'selected':''; ?>>Completed</option>
<option value="cancelled" <?php echo $t['status']=='cancelled'?'selected':''; ?>>Cancelled</option>
</select>
</div>
<div class="col-6">
<label class="form-label-mod">Progress (%)</label>
<input type="number" name="progress" class="form-control form-control-mod" min="0" max="100" value="<?php echo $t['progress']; ?>">
</div>
</div>
<div class="d-flex gap-2">
<button type="submit" class="btn btn-primary-mod flex-fill"><i class="fa-solid fa-save me-2"></i>Save Changes</button>
<a href="/HRSuite/admin_dashboard/tasks.php" class="btn btn-outline-mod flex-fill">Cancel</a>
</div>
</form>
</div>
</div>
</div>
</div>
</div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body></html>
