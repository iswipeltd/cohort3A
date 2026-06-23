<?php require_once __DIR__.'/../config/session.php'; require_admin(); $user=current_user(); $roles=$pdo->query("SELECT r.*,COUNT(e.id) as count FROM roles r LEFT JOIN employees e ON r.id=e.role_id AND e.status='active' GROUP BY r.id ORDER BY r.name")->fetchAll(); ?>
<?php include 'includes/head.php'; ?><body><?php include 'includes/sidebar.php'; ?><div class="main-content"><?php include 'includes/navbar.php'; ?>
<div class="page-header"><div><h1 class="page-title">Roles</h1><p class="page-subtitle mb-0">Job roles and responsibilities</p></div></div>
<div class="px-4 pb-4">
<?php $err = $_SESSION['error'] ?? ''; $success = $_SESSION['success'] ?? ''; unset($_SESSION['error'], $_SESSION['success']); ?>
    <?php if($err):?><div class="alert alert-danger d-flex align-items-center py-2 mb-3" style="border-radius:10px;font-size:0.85rem;"><i class="fa-solid fa-circle-exclamation me-2"></i><?php echo htmlspecialchars($err);?></div><?php endif;?>
    <?php if($success):?><div class="alert alert-success d-flex align-items-center py-2 mb-3" style="border-radius:10px;font-size:0.85rem;"><i class="fa-solid fa-circle-check me-2"></i><?php echo htmlspecialchars($success);?></div><?php endif;?>

<div class="card-modern"><div class="card-body-modern" style="padding:0;"><div class="table-responsive"><table class="table-modern"><thead><tr><th>Role</th><th>Description</th><th>Employees</th></tr></thead><tbody>
<?php foreach($roles as $r): ?><tr><td class="fw-semibold"><?php echo htmlspecialchars($r['name']); ?></td><td><?php echo htmlspecialchars($r['description']??'-'); ?></td><td><?php echo $r['count']; ?></td></tr><?php endforeach; if(empty($roles)): ?><tr><td colspan="3" class="text-center text-muted py-5">No roles</td></tr><?php endif; ?>
</tbody></table></div></div></div></div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script></body></html>
