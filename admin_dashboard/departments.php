<?php require_once __DIR__.'/../config/session.php'; require_admin(); $user=current_user(); $depts=$pdo->query("SELECT d.*,COUNT(e.id) as headcount FROM departments d LEFT JOIN employees e ON d.id=e.department_id AND e.status='active' GROUP BY d.id ORDER BY d.name")->fetchAll(); ?>
<?php include 'includes/head.php'; ?><body><?php include 'includes/sidebar.php'; ?><div class="main-content"><?php include 'includes/navbar.php'; ?>
<div class="page-header"><div><h1 class="page-title">Departments</h1><p class="page-subtitle mb-0">Organizational structure</p></div></div>
<div class="px-4 pb-4">
<?php $err = $_SESSION['error'] ?? ''; $success = $_SESSION['success'] ?? ''; unset($_SESSION['error'], $_SESSION['success']); ?>
    <?php if($err):?><div class="alert alert-danger d-flex align-items-center py-2 mb-3" style="border-radius:10px;font-size:0.85rem;"><i class="fa-solid fa-circle-exclamation me-2"></i><?php echo htmlspecialchars($err);?></div><?php endif;?>
    <?php if($success):?><div class="alert alert-success d-flex align-items-center py-2 mb-3" style="border-radius:10px;font-size:0.85rem;"><i class="fa-solid fa-circle-check me-2"></i><?php echo htmlspecialchars($success);?></div><?php endif;?>

<div class="row g-3">
<?php foreach($depts as $d): ?>
<div class="col-md-6 col-lg-4"><div class="card-modern"><div class="card-body-modern">
<div class="d-flex justify-content-between align-items-start"><div><h5 class="fw-bold mb-1"><?php echo htmlspecialchars($d['name']); ?></h5><p class="text-muted small mb-0"><?php echo htmlspecialchars($d['description']??''); ?></p></div><div class="kpi-icon" style="background:rgba(99,102,241,0.15);color:#818cf8;"><i class="fa-solid fa-building"></i></div></div>
<div class="mt-3 pt-3" style="border-top:1px solid var(--border);"><div class="d-flex justify-content-between"><span class="text-muted small">Headcount</span><span class="fw-bold"><?php echo $d['headcount']; ?></span></div></div>
</div></div></div>
<?php endforeach; if(empty($depts)): ?><div class="col-12"><div class="text-center text-muted py-5">No departments</div></div><?php endif; ?>
</div></div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script></body></html>
