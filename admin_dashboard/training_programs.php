<?php
require_once __DIR__ . '/../config/session.php';
require_admin();
$user = current_user();
$data = $pdo->query("SELECT tp.*, u.first_name, u.last_name FROM training_programs tp LEFT JOIN users u ON tp.created_by = u.id ORDER BY tp.created_at DESC")->fetchAll();
?>
<?php include 'includes/head.php'; ?><body><?php include 'includes/sidebar.php'; ?><div class="main-content"><?php include 'includes/navbar.php'; ?>
<div class="page-header"><div><h1 class="page-title">Training Programs</h1><p class="page-subtitle mb-0">Employee learning & development</p></div><a href="#" class="btn-primary-mod" data-bs-toggle="modal" data-bs-target="#addModal"><i class="fa-solid fa-plus me-2"></i>New Program</a></div>
<div class="px-4 pb-4">
<?php $err = $_SESSION['error'] ?? ''; $success = $_SESSION['success'] ?? ''; unset($_SESSION['error'], $_SESSION['success']); ?>
    <?php if($err):?><div class="alert alert-danger d-flex align-items-center py-2 mb-3" style="border-radius:10px;font-size:0.85rem;"><i class="fa-solid fa-circle-exclamation me-2"></i><?php echo htmlspecialchars($err);?></div><?php endif;?>
    <?php if($success):?><div class="alert alert-success d-flex align-items-center py-2 mb-3" style="border-radius:10px;font-size:0.85rem;"><i class="fa-solid fa-circle-check me-2"></i><?php echo htmlspecialchars($success);?></div><?php endif;?>

<div class="card-modern"><div class="card-body-modern" style="padding:0;"><div class="table-responsive"><table class="table-modern" style="margin:0;"><thead><tr><th>Title</th><th>Duration</th><th>Mode</th><th>Status</th><th>Created By</th><th>Date</th><th style="width:100px;">Action</th></tr></thead><tbody>
<?php foreach($data as $row): ?>
<tr><td class="fw-semibold small"><?php echo htmlspecialchars($row['title']); ?></td><td><?php echo $row['duration_hours']; ?> hrs</td><td><?php echo ucfirst($row['mode']); ?></td><td><span class="status-badge <?php echo $row['status']=='active'?'status-active':'status-pending'; ?>"><?php echo ucfirst($row['status']); ?></span></td><td><?php echo htmlspecialchars(($row['first_name']??'').' '.($row['last_name']??'')); ?></td><td><?php echo date('M j', strtotime($row['created_at'])); ?></td><td><a href="/HRSuite/process/delete_training_program.php?id=<?php echo $row['id']; ?>" class="btn btn-sm" style="background:rgba(244,63,94,0.15);color:#f43f5e;border-radius:6px;font-size:0.75rem;" onclick="return confirm('Delete?')"><i class="fa-solid fa-trash"></i></a></td></tr>
<?php endforeach; if(empty($data)): ?><tr><td colspan="7" class="text-center text-muted py-5">No training programs</td></tr><?php endif; ?>
</tbody></table></div></div></div>
</div>
<div class="modal fade" id="addModal" tabindex="-1"><div class="modal-dialog"><div class="modal-content" style="border-radius:16px;"><div class="modal-header"><h5 class="modal-title fw-bold">New Training Program</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
<form action="/HRSuite/process/add_training_program.php" method="POST"><div class="modal-body">
<div class="mb-3"><label class="form-label-mod">Title</label><input type="text" name="title" class="form-control form-control-mod" required></div>
<div class="mb-3"><label class="form-label-mod">Description</label><textarea name="description" class="form-control form-control-mod" rows="2"></textarea></div>
<div class="mb-3"><label class="form-label-mod">Duration (hours)</label><input type="number" name="duration_hours" class="form-control form-control-mod" value="20" required></div>
<div class="mb-3"><label class="form-label-mod">Mode</label><select name="mode" class="form-select"><option value="online">Online</option><option value="in-person">In-Person</option><option value="hybrid">Hybrid</option></select></div>
</div><div class="modal-footer"><button type="button" class="btn-outline-mod" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn-primary-mod">Create</button></div></form>
</div></div></div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script></body></html>
