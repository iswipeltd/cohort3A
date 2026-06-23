<?php
require_once __DIR__ . '/../config/session.php';
require_admin();
$user = current_user();
$data = $pdo->query("SELECT r.*, COUNT(e.id) as assigned FROM roles r LEFT JOIN employees e ON r.id = e.role_id GROUP BY r.id ORDER BY r.level DESC")->fetchAll();
?>
<?php include 'includes/head.php'; ?><body><?php include 'includes/sidebar.php'; ?><div class="main-content"><?php include 'includes/navbar.php'; ?>
<?php $err=$_SESSION['error']??''; $success=$_SESSION['success']??''; unset($_SESSION['error'],$_SESSION['success']); ?>
<?php if($err):?><div class="alert alert-danger d-flex align-items-center py-2 mb-3" style="border-radius:10px;font-size:0.85rem;"><i class="fa-solid fa-circle-exclamation me-2"></i><?php echo htmlspecialchars($err);?></div><?php endif;?>
<?php if($success):?><div class="alert alert-success d-flex align-items-center py-2 mb-3" style="border-radius:10px;font-size:0.85rem;"><i class="fa-solid fa-circle-check me-2"></i><?php echo htmlspecialchars($success);?></div><?php endif;?>

<div class="page-header"><div><h1 class="page-title">Roles & Permissions</h1><p class="page-subtitle mb-0">Manage system roles</p></div><a href="#" class="btn-primary-mod" data-bs-toggle="modal" data-bs-target="#addModal"><i class="fa-solid fa-plus me-2"></i>New Role</a></div>
<div class="px-4 pb-4">
<div class="card-modern"><div class="card-body-modern" style="padding:0;"><div class="table-responsive"><table class="table-modern" style="margin:0;"><thead style="background:#0f172a;"><tr><th>Name</th><th>Level</th><th>Assigned</th><th>Description</th><th>Status</th><th style="width:100px;">Action</th></tr></thead><tbody>
<?php foreach($data as $row): ?>
<tr><td class="fw-semibold small"><?php echo htmlspecialchars($row['name']); ?></td><td><?php echo $row['level']; ?></td><td><?php echo $row['assigned']; ?> users</td><td><?php echo htmlspecialchars($row['description']??'-'); ?></td><td><span class="status-badge <?php echo $row['status']=='active'?'status-active':'status-pending'; ?>"><?php echo ucfirst($row['status']); ?></span></td><td><a href="/HRSuite/process/delete_role.php?id=<?php echo $row['id']; ?>" class="btn btn-sm" style="background:rgba(244,63,94,0.15);color:#f43f5e;border-radius:6px;font-size:0.75rem;" onclick="return confirm('Delete?')"><i class="fa-solid fa-trash"></i></a></td></tr>
<?php endforeach; if(empty($data)): ?><tr><td colspan="6" class="text-center text-muted py-5">No roles</td></tr><?php endif; ?>
</tbody></table></div></div></div>
</div>
<div class="modal fade" id="addModal" tabindex="-1"><div class="modal-dialog"><div class="modal-content" style="border-radius:16px;"><div class="modal-header"><h5 class="modal-title fw-bold">New Role</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
<form action="/HRSuite/process/add_role.php" method="POST"><div class="modal-body">
<div class="mb-3"><label class="form-label-mod">Name</label><input type="text" name="name" class="form-control form-control-mod" required></div>
<div class="mb-3"><label class="form-label-mod">Level</label><input type="number" name="level" class="form-control form-control-mod" value="1" required></div>
<div class="mb-3"><label class="form-label-mod">Description</label><textarea name="description" class="form-control form-control-mod" rows="2"></textarea></div>
</div><div class="modal-footer"><button type="button" class="btn-outline-mod" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn-primary-mod">Create</button></div></form>
</div></div></div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script></body></html>
