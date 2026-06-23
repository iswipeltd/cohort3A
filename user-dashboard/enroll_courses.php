<?php
require_once __DIR__ . '/../config/session.php';
require_auth();
$user = current_user();
$empId = get_employee_id($_SESSION['user_id']);

$programs = $pdo->query("SELECT * FROM training_programs WHERE status='active' ORDER BY created_at DESC")->fetchAll();

// Get already enrolled program IDs
$enrolledIds = [];
if ($empId) {
    $stmt = $pdo->prepare("SELECT program_id, status FROM training_enrollments WHERE employee_id = ?");
    $stmt->execute([$empId]);
    $enrolledIds = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
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
<h1 class="page-title">Enroll Courses</h1>
<p class="page-subtitle mb-0">Available training programs</p>
</div>
</div>
<div class="px-4 pb-4">
<div class="row g-3">
<?php foreach($programs as $p):
$isEnrolled = isset($enrolledIds[$p['id']]);
$enrollStatus = $isEnrolled ? $enrolledIds[$p['id']] : null;
?>
<div class="col-md-6 col-lg-4">
<div class="card-modern h-100">
<div class="card-body-modern d-flex flex-column h-100">
<div class="d-flex align-items-start justify-content-between mb-2">
<div class="fw-bold" style="font-size:0.95rem;color:var(--text);"><?php echo htmlspecialchars($p['title']); ?></div>
<span style="font-size:0.7rem;font-weight:600;background:var(--bg);color:var(--muted);padding:3px 8px;border-radius:6px;text-transform:uppercase;"><?php echo ucfirst($p['mode']); ?></span>
</div>
<p style="color:var(--text2);font-size:0.82rem;line-height:1.5;flex:1;"><?php echo htmlspecialchars($p['description'] ?? 'No description available.'); ?></p>
<div class="d-flex align-items-center gap-3 mb-3" style="font-size:0.78rem;color:var(--muted);">
<span><i class="fa-solid fa-clock me-1" style="color:var(--primary);"></i><?php echo $p['duration_hours']; ?> hrs</span>
<span><i class="fa-solid fa-calendar me-1" style="color:var(--primary);"></i><?php echo date('M j, Y', strtotime($p['created_at'])); ?></span>
</div>
<?php if($isEnrolled): ?>
<div class="d-flex align-items-center gap-2">
<span class="status-badge <?php echo $enrollStatus=='pending'?'status-pending':($enrollStatus=='enrolled'||$enrollStatus=='in_progress'?'status-active':'status-rejected'); ?>" style="font-size:0.75rem;"><?php echo ucfirst(str_replace('_',' ',$enrollStatus)); ?></span>
<span style="font-size:0.75rem;color:var(--muted);">Already requested</span>
</div>
<?php else: ?>
<form action="/HRSuite/process/enroll_training.php" method="POST" class="mt-auto">
<input type="hidden" name="program_id" value="<?php echo $p['id']; ?>">
<button type="submit" class="btn btn-primary-mod w-100"><i class="fa-solid fa-user-plus me-2"></i>Request Enrollment</button>
</form>
<?php endif; ?>
</div>
</div>
</div>
<?php endforeach; if(empty($programs)): ?>
<div class="col-12">
<div class="card-modern"><div class="card-body-modern text-center py-5"><i class="fa-solid fa-graduation-cap mb-3" style="font-size:2rem;color:var(--muted);"></i><div style="color:var(--muted);">No training programs available at the moment.</div></div></div>
</div>
<?php endif; ?>
</div>
</div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
