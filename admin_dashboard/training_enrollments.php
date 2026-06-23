<?php
require_once __DIR__ . '/../config/session.php';
require_admin();
$user = current_user();

// Get all enrollments with employee and program details
$enrollments = $pdo->query("
    SELECT te.id, te.status, te.enrolled_at, te.progress_percent, te.score,
           tp.title as program, tp.duration_hours, tp.mode,
           e.id as emp_id, u.first_name, u.last_name, u.email, u.avatar,
           d.name as dept
    FROM training_enrollments te
    JOIN training_programs tp ON te.program_id = tp.id
    JOIN employees e ON te.employee_id = e.id
    JOIN users u ON e.user_id = u.id
    LEFT JOIN departments d ON e.department_id = d.id
    ORDER BY te.enrolled_at DESC
")->fetchAll();

// Stats
$pending = count(array_filter($enrollments, fn($e) => $e['status'] == 'pending'));
$approved = count(array_filter($enrollments, fn($e) => in_array($e['status'], ['enrolled', 'in_progress', 'completed'])));
$rejected = count(array_filter($enrollments, fn($e) => $e['status'] == 'dropped'));

$err = $_SESSION['error'] ?? '';
$success = $_SESSION['success'] ?? '';
unset($_SESSION['error'], $_SESSION['success']);
?>
<?php include 'includes/head.php'; ?><body><?php include 'includes/sidebar.php'; ?><div class="main-content"><?php include 'includes/navbar.php'; ?>
<?php if($err):?><div class="alert alert-danger d-flex align-items-center py-2 mb-3 mx-4" style="border-radius:10px;font-size:0.85rem;"><i class="fa-solid fa-circle-exclamation me-2"></i><?php echo htmlspecialchars($err);?></div><?php endif;?>
<?php if($success):?><div class="alert alert-success d-flex align-items-center py-2 mb-3 mx-4" style="border-radius:10px;font-size:0.85rem;"><i class="fa-solid fa-circle-check me-2"></i><?php echo htmlspecialchars($success);?></div><?php endif;?>

<div class="page-header"><div><h1 class="page-title">Training Enrollments</h1><p class="page-subtitle mb-0">Approve or decline employee course requests</p></div></div>
<div class="px-4 pb-4">
<div class="row g-3 mb-4">
<div class="col-md-4"><div class="card-modern"><div class="card-body-modern"><div style="color:var(--muted);font-size:0.75rem;font-weight:600;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:4px;">Pending</div><div class="fw-bold" style="font-size:1.6rem;color:var(--warning);"><?php echo $pending; ?></div></div></div></div>
<div class="col-md-4"><div class="card-modern"><div class="card-body-modern"><div style="color:var(--muted);font-size:0.75rem;font-weight:600;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:4px;">Approved / Active</div><div class="fw-bold" style="font-size:1.6rem;color:var(--success);"><?php echo $approved; ?></div></div></div></div>
<div class="col-md-4"><div class="card-modern"><div class="card-body-modern"><div style="color:var(--muted);font-size:0.75rem;font-weight:600;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:4px;">Declined / Dropped</div><div class="fw-bold" style="font-size:1.6rem;color:var(--danger);"><?php echo $rejected; ?></div></div></div></div>
</div>

<div class="card-modern" style="padding:0;">
<table class="table-modern" style="width:100%;">
<thead>
<tr><th>Employee</th><th>Program</th><th>Duration</th><th>Status</th><th>Requested</th><th>Progress</th><th style="width:130px;">Action</th></tr>
</thead>
<tbody>
<?php foreach($enrollments as $e):
$sc = match($e['status']){'completed'=>'status-active','in_progress'=>'status-processing','enrolled'=>'status-active','pending'=>'status-pending','dropped'=>'status-rejected',default=>'status-pending'};
?>
<tr>
<td>
<div class="d-flex align-items-center gap-2">
<img src="<?php echo $e['avatar'] ? htmlspecialchars($e['avatar']) : 'https://ui-avatars.com/api/?name='.urlencode($e['first_name'].'+'.$e['last_name']).'&background=6366f1&color=fff&size=80'; ?>" style="width:32px;height:32px;border-radius:8px;object-fit:cover;" alt="">
<div>
<div class="fw-semibold" style="font-size:0.85rem;color:var(--text);"><?php echo htmlspecialchars($e['first_name'].' '.$e['last_name']); ?></div>
<div style="font-size:0.72rem;color:var(--muted);"><?php echo htmlspecialchars($e['dept'] ?? 'N/A'); ?></div>
</div>
</div>
</td>
<td class="fw-semibold" style="font-size:0.85rem;"><?php echo htmlspecialchars($e['program']); ?></td>
<td style="font-size:0.8rem;color:var(--muted);"><?php echo $e['duration_hours']; ?> hrs &middot; <?php echo ucfirst($e['mode']); ?></td>
<td><span class="status-badge <?php echo $sc; ?>"><?php echo ucfirst(str_replace('_',' ',$e['status'])); ?></span></td>
<td style="white-space:nowrap;font-size:0.78rem;color:var(--muted);"><?php echo date('M j, Y', strtotime($e['enrolled_at'])); ?></td>
<td>
<div style="height:5px;border-radius:4px;background:var(--border);overflow:hidden;"><div style="width:<?php echo $e['progress_percent']; ?>%;height:100%;background:var(--primary);border-radius:4px;"></div></div>
<div style="font-size:0.7rem;color:var(--muted);margin-top:2px;"><?php echo $e['progress_percent']; ?>%</div>
</td>
<td>
<?php if($e['status']=='pending'): ?>
<a href="/HRSuite/process/approve_enrollment.php?id=<?php echo $e['id']; ?>" class="btn btn-sm-mod btn-success-mod me-1" title="Approve"><i class="fa-solid fa-check" style="font-size:0.7rem;"></i></a>
<a href="/HRSuite/process/decline_enrollment.php?id=<?php echo $e['id']; ?>" class="btn btn-sm-mod btn-danger-mod" title="Decline" onclick="return confirm('Decline this enrollment?')"><i class="fa-solid fa-xmark" style="font-size:0.7rem;"></i></a>
<?php endif; ?>
<a href="edit_training_enrollment.php?id=<?php echo $e['id']; ?>" class="btn btn-sm-mod btn-primary-mod" title="Edit"><i class="fa-solid fa-pen" style="font-size:0.7rem;"></i></a>
</td>
</tr>
<?php endforeach; if(empty($enrollments)): ?>
<tr><td colspan="7" style="text-align:center;color:var(--muted);padding:40px;">No training enrollments found.</td></tr>
<?php endif; ?>
</tbody>
</table>
</div>
</div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body></html>
