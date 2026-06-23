<?php
require_once __DIR__ . '/../config/session.php';
require_admin();
$user = current_user();
$data = $pdo->query("SELECT * FROM leave_types ORDER BY name")->fetchAll();
?>
<?php include 'includes/head.php'; ?><body><?php include 'includes/sidebar.php'; ?><div class="main-content"><?php include 'includes/navbar.php'; ?>
<div class="page-header"><div><h1 class="page-title">Leave Types</h1><p class="page-subtitle mb-0">Configure leave categories</p></div><a href="#" class="btn-primary-mod" data-bs-toggle="modal" data-bs-target="#addModal"><i class="fa-solid fa-plus me-2"></i>New Type</a></div>
<div class="px-4 pb-4">
<?php $err = $_SESSION['error'] ?? ''; $success = $_SESSION['success'] ?? ''; unset($_SESSION['error'], $_SESSION['success']); ?>
    <?php if($err):?><div class="alert alert-danger d-flex align-items-center py-2 mb-3" style="border-radius:10px;font-size:0.85rem;"><i class="fa-solid fa-circle-exclamation me-2"></i><?php echo htmlspecialchars($err);?></div><?php endif;?>
    <?php if($success):?><div class="alert alert-success d-flex align-items-center py-2 mb-3" style="border-radius:10px;font-size:0.85rem;"><i class="fa-solid fa-circle-check me-2"></i><?php echo htmlspecialchars($success);?></div><?php endif;?>

<div class="card-modern"><div class="card-body-modern" style="padding:0;"><div class="table-responsive"><table class="table-modern" style="margin:0;"><thead style="background:#0f172a;"><tr><th>Name</th><th>Default Days</th><th>Carry Forward</th><th>Max Carry</th><th>Approval</th><th>Paid</th><th>Status</th><th style="width:100px;">Action</th></tr></thead><tbody>
<?php foreach($data as $row): ?>
<tr><td class="fw-semibold small"><?php echo htmlspecialchars($row['name']); ?></td><td><?php echo $row['default_days']; ?></td><td><?php echo $row['carry_forward'] ? 'Yes' : 'No'; ?></td><td><?php echo $row['max_carry_forward']; ?></td><td><?php echo $row['requires_approval'] ? 'Yes' : 'No'; ?></td><td><?php echo $row['paid'] ? 'Yes' : 'No'; ?></td><td><span class="status-badge <?php echo $row['status']=='active'?'status-active':'status-pending'; ?>"><?php echo ucfirst($row['status']); ?></span></td><td><a href="/HRSuite/process/delete_leave_type.php?id=<?php echo $row['id']; ?>" class="btn btn-sm" style="background:rgba(244,63,94,0.15);color:#f43f5e;border-radius:6px;font-size:0.75rem;" onclick="return confirm('Delete?')"><i class="fa-solid fa-trash"></i></a></td></tr>
<?php endforeach; if(empty($data)): ?><tr><td colspan="8" class="text-center text-muted py-5">No leave types</td></tr><?php endif; ?>
</tbody></table></div></div></div>
</div>
<div class="modal fade" id="addModal" tabindex="-1"><div class="modal-dialog"><div class="modal-content" style="border-radius:16px;"><div class="modal-header"><h5 class="modal-title fw-bold">New Leave Type</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
<form action="/HRSuite/process/add_leave_type.php" method="POST"><div class="modal-body">
<div class="mb-3"><label class="form-label-mod">Name</label><input type="text" name="name" class="form-control form-control-mod" required></div>
<div class="mb-3"><label class="form-label-mod">Default Days</label><input type="number" name="default_days" class="form-control form-control-mod" value="20" required></div>
<div class="row g-2 mb-3"><div class="col-4"><div class="form-check"><input class="form-check-input" type="checkbox" name="carry_forward" id="cf"><label class="form-check-label small" for="cf">Carry Forward</label></div></div><div class="col-4"><div class="form-check"><input class="form-check-input" type="checkbox" name="requires_approval" id="ra" checked><label class="form-check-label small" for="ra">Requires Approval</label></div></div><div class="col-4"><div class="form-check"><input class="form-check-input" type="checkbox" name="paid" id="pd" checked><label class="form-check-label small" for="pd">Paid Leave</label></div></div></div>
<div class="mb-3"><label class="form-label-mod">Max Carry Forward</label><input type="number" name="max_carry_forward" class="form-control form-control-mod" value="5"></div>
<div class="mb-3"><label class="form-label-mod">Color</label><input type="color" name="color" class="form-control form-control-mod" value="var(--primary)"></div>
</div><div class="modal-footer"><button type="button" class="btn-outline-mod" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn-primary-mod">Create</button></div></form>
</div></div></div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script></body></html>
