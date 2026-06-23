<?php require_once __DIR__.'/../config/session.php'; require_admin(); $user=current_user(); $programs=$pdo->query("SELECT t.*,COUNT(te.id) as enrolled FROM training_programs t LEFT JOIN training_enrollments te ON t.id=te.program_id GROUP BY t.id ORDER BY t.created_at DESC")->fetchAll(); ?>
<?php include 'includes/head.php'; ?><body><?php include 'includes/sidebar.php'; ?><div class="main-content"><?php include 'includes/navbar.php'; ?>
<?php $err=$_SESSION['error']??''; $success=$_SESSION['success']??''; unset($_SESSION['error'],$_SESSION['success']); ?>
<?php if($err):?><div class="alert alert-danger d-flex align-items-center py-2 mb-3" style="border-radius:10px;font-size:0.85rem;"><i class="fa-solid fa-circle-exclamation me-2"></i><?php echo htmlspecialchars($err);?></div><?php endif;?>
<?php if($success):?><div class="alert alert-success d-flex align-items-center py-2 mb-3" style="border-radius:10px;font-size:0.85rem;"><i class="fa-solid fa-circle-check me-2"></i><?php echo htmlspecialchars($success);?></div><?php endif;?>

<div class="page-header"><div><h1 class="page-title">Training Programs</h1><p class="page-subtitle mb-0">Employee development and learning</p></div></div>
<div class="px-4 pb-4">
<div class="row g-3">
<?php foreach($programs as $p): ?>
<div class="col-md-6 col-lg-4"><div class="card-modern h-100"><div class="card-body-modern">
<div class="d-flex justify-content-between align-items-start"><div><h5 class="fw-bold mb-1"><?php echo htmlspecialchars($p['title']); ?></h5><p class="text-muted small mb-2"><?php echo htmlspecialchars($p['description']??''); ?></p></div><div class="kpi-icon" style="background:rgba(99,102,241,0.12);color:#818cf8;"><i class="fa-solid fa-graduation-cap"></i></div></div>
<div class="mt-2"><span class="status-badge <?php echo $p['status']=='active'?'status-active':'status-inactive'; ?>"><?php echo ucfirst($p['status']); ?></span></div>
<div class="mt-3 pt-3" style="border-top:1px solid var(--border);"><div class="d-flex justify-content-between"><span class="text-muted small">Duration</span><span class="fw-semibold small"><?php echo $p['duration_hours']; ?> hrs</span></div><div class="d-flex justify-content-between mt-1"><span class="text-muted small">Enrolled</span><span class="fw-semibold small"><?php echo $p['enrolled']; ?></span></div></div>
</div></div></div>
<?php endforeach; if(empty($programs)): ?><div class="col-12"><div class="text-center text-muted py-5">No training programs</div></div><?php endif; ?>
</div></div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script></body></html>
