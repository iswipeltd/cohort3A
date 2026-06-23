<?php
require_once __DIR__ . '/../config/session.php';
require_admin();
$user = current_user();

$id = (int) ($_GET['id'] ?? 0);
if (!$id) {
    $_SESSION['error'] = 'Invalid enrollment ID.';
    header('Location: /HRSuite/admin_dashboard/training_enrollments.php');
    exit;
}

$stmt = $pdo->prepare("
    SELECT te.*, tp.title as program,
           CONCAT(u.first_name,' ',u.last_name) as emp_name,
           u.email, u.avatar
    FROM training_enrollments te
    JOIN training_programs tp ON te.program_id = tp.id
    JOIN employees e ON te.employee_id = e.id
    JOIN users u ON e.user_id = u.id
    WHERE te.id = ?
");
$stmt->execute([$id]);
$enr = $stmt->fetch();

if (!$enr) {
    $_SESSION['error'] = 'Enrollment not found.';
    header('Location: /HRSuite/admin_dashboard/training_enrollments.php');
    exit;
}

$err = $_SESSION['error'] ?? '';
$success = $_SESSION['success'] ?? '';
unset($_SESSION['error'], $_SESSION['success']);

// Safe date value for input
$completedDateVal = '';
if (!empty($enr['completed_at']) && $enr['completed_at'] !== '0000-00-00 00:00:00') {
    $ts = strtotime($enr['completed_at']);
    if ($ts !== false) {
        $completedDateVal = date('Y-m-d', $ts);
    }
}
?>
<?php include 'includes/head.php'; ?>
<body>
<?php include 'includes/sidebar.php'; ?>
<div class="main-content">
<?php include 'includes/navbar.php'; ?>

<?php if($err):?><div class="alert alert-danger d-flex align-items-center py-2 mb-3 mx-4" style="border-radius:10px;font-size:0.85rem;"><i class="fa-solid fa-circle-exclamation me-2"></i><?php echo htmlspecialchars($err);?></div><?php endif;?>
<?php if($success):?><div class="alert alert-success d-flex align-items-center py-2 mb-3 mx-4" style="border-radius:10px;font-size:0.85rem;"><i class="fa-solid fa-circle-check me-2"></i><?php echo htmlspecialchars($success);?></div><?php endif;?>

<div class="page-header">
<div>
<h1 class="page-title">Edit Enrollment</h1>
<p class="page-subtitle mb-0">Update training status, progress, and score</p>
</div>
</div>

<div class="px-4 pb-4">
<div class="row g-4">
<div class="col-lg-4">
<div class="card-modern">
<div class="card-body-modern">
<div class="d-flex align-items-center gap-3 mb-3">
<img src="<?php echo $enr['avatar'] ? htmlspecialchars($enr['avatar']) : 'https://ui-avatars.com/api/?name='.urlencode($enr['emp_name']).'&background=6366f1&color=fff&size=80'; ?>" style="width:48px;height:48px;border-radius:12px;object-fit:cover;" alt="">
<div>
<div class="fw-bold" style="font-size:0.95rem;color:var(--text);"><?php echo htmlspecialchars($enr['emp_name']); ?></div>
<div style="font-size:0.78rem;color:var(--muted);"><?php echo htmlspecialchars($enr['email']); ?></div>
</div>
</div>
<div style="border-top:1px solid var(--border);padding-top:12px;">
<div style="color:var(--muted);font-size:0.72rem;text-transform:uppercase;font-weight:600;letter-spacing:0.5px;margin-bottom:2px;">Program</div>
<div style="font-size:0.88rem;color:var(--text);font-weight:600;"><?php echo htmlspecialchars($enr['program']); ?></div>
</div>
<div class="mt-2">
<div style="color:var(--muted);font-size:0.72rem;text-transform:uppercase;font-weight:600;letter-spacing:0.5px;margin-bottom:2px;">Enrolled</div>
<div style="font-size:0.85rem;color:var(--text2);"><?php echo date('M j, Y g:i A', strtotime($enr['enrolled_at'])); ?></div>
</div>
</div>
</div>
</div>

<div class="col-lg-8">
<div class="card-modern">
<div class="card-body-modern">
<h5 class="fw-bold mb-3" style="font-size:0.95rem;color:var(--text);"><i class="fa-solid fa-pen-to-square me-2" style="color:var(--primary);"></i>Edit Details</h5>
<form action="/HRSuite/process/edit_training_enrollment.php" method="POST">
<input type="hidden" name="id" value="<?php echo $enr['id']; ?>">

<div class="row g-3">
<div class="col-md-6">
<label class="form-label-mod">Status</label>
<select name="status" class="form-select form-control-mod" required>
<option value="pending" <?php echo $enr['status']=='pending'?'selected':''; ?>>Pending</option>
<option value="enrolled" <?php echo $enr['status']=='enrolled'?'selected':''; ?>>Enrolled</option>
<option value="in_progress" <?php echo $enr['status']=='in_progress'?'selected':''; ?>>In Progress</option>
<option value="completed" <?php echo $enr['status']=='completed'?'selected':''; ?>>Completed</option>
<option value="dropped" <?php echo $enr['status']=='dropped'?'selected':''; ?>>Dropped</option>
</select>
</div>
<div class="col-md-6">
<label class="form-label-mod">Progress (%)</label>
<input type="number" name="progress_percent" class="form-control form-control-mod" min="0" max="100" value="<?php echo (int)$enr['progress_percent']; ?>">
</div>
<div class="col-md-6">
<label class="form-label-mod">Score (0-100)</label>
<input type="number" name="score" class="form-control form-control-mod" min="0" max="100" step="0.01" value="<?php echo $enr['score'] ?? ''; ?>">
</div>
<div class="col-md-6">
<label class="form-label-mod">Completed Date</label>
<input type="date" name="completed_at" class="form-control form-control-mod" value="<?php echo htmlspecialchars($completedDateVal); ?>">
</div>
</div>

<div class="d-flex gap-2 mt-4">
<button type="submit" class="btn btn-primary-mod"><i class="fa-solid fa-save me-2"></i>Save Changes</button>
<a href="training_enrollments.php" class="btn btn-secondary-mod">Cancel</a>
</div>
</form>
</div>
</div>
</div>
</div>
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
