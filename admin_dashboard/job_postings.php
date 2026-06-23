<?php
require_once __DIR__ . '/../config/session.php';
require_admin();
$user = current_user();
$data = $pdo->query("SELECT jp.*, d.name as dept FROM job_postings jp LEFT JOIN departments d ON jp.department_id = d.id ORDER BY jp.posted_at DESC")->fetchAll();
$depts = $pdo->query("SELECT id, name FROM departments WHERE status='active'")->fetchAll();
?>
<?php include 'includes/head.php'; ?><body><?php include 'includes/sidebar.php'; ?><div class="main-content"><?php include 'includes/navbar.php'; ?>
<div class="page-header"><div><h1 class="page-title">Job Postings</h1><p class="page-subtitle mb-0">Open positions</p></div><a href="#" class="btn-primary-mod" data-bs-toggle="modal" data-bs-target="#addModal"><i class="fa-solid fa-plus me-2"></i>New Job</a></div>
<div class="px-4 pb-4">
<?php $err = $_SESSION['error'] ?? ''; $success = $_SESSION['success'] ?? ''; unset($_SESSION['error'], $_SESSION['success']); ?>
    <?php if($err):?><div class="alert alert-danger d-flex align-items-center py-2 mb-3" style="border-radius:10px;font-size:0.85rem;"><i class="fa-solid fa-circle-exclamation me-2"></i><?php echo htmlspecialchars($err);?></div><?php endif;?>
    <?php if($success):?><div class="alert alert-success d-flex align-items-center py-2 mb-3" style="border-radius:10px;font-size:0.85rem;"><i class="fa-solid fa-circle-check me-2"></i><?php echo htmlspecialchars($success);?></div><?php endif;?>

<div class="card-modern"><div class="card-body-modern" style="padding:0;"><div class="table-responsive"><table class="table-modern" style="margin:0;"><thead><tr><th>Title</th><th>Department</th><th>Location</th><th>Type</th><th>Status</th><th>Posted</th><th style="width:100px;">Action</th></tr></thead><tbody>
<?php foreach($data as $row): $sc = match($row['status']){'open'=>'status-active','closed'=>'status-rejected',default=>'status-pending'}; ?>
<tr><td class="fw-semibold small"><?php echo htmlspecialchars($row['title']); ?></td><td><?php echo htmlspecialchars($row['dept']??'-'); ?></td><td><?php echo htmlspecialchars($row['location']??'-'); ?></td><td><?php echo ucfirst($row['type']); ?></td><td><span class="status-badge <?php echo $sc; ?>"><?php echo ucfirst($row['status']); ?></span></td><td><?php echo date('M j', strtotime($row['posted_at'])); ?></td><td><a href="/HRSuite/process/delete_job_posting.php?id=<?php echo $row['id']; ?>" class="btn btn-sm" style="background:rgba(244,63,94,0.15);color:#f43f5e;border-radius:6px;font-size:0.75rem;" onclick="return confirm('Delete?')"><i class="fa-solid fa-trash"></i></a></td></tr>
<?php endforeach; if(empty($data)): ?><tr><td colspan="7" class="text-center text-muted py-5">No job postings</td></tr><?php endif; ?>
</tbody></table></div></div></div>
</div>
<div class="modal fade" id="addModal" tabindex="-1"><div class="modal-dialog"><div class="modal-content" style="border-radius:16px;"><div class="modal-header"><h5 class="modal-title fw-bold">New Job Posting</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
<form action="/HRSuite/process/add_job_posting.php" method="POST"><div class="modal-body">
<div class="mb-3"><label class="form-label-mod">Title</label><input type="text" name="title" class="form-control form-control-mod" required></div>
<div class="mb-3"><label class="form-label-mod">Department</label><select name="department_id" class="form-select"><option value="">Select</option><?php foreach($depts as $d): ?><option value="<?php echo $d['id']; ?>"><?php echo htmlspecialchars($d['name']); ?></option><?php endforeach; ?></select></div>
<div class="mb-3"><label class="form-label-mod">Description</label><textarea name="description" class="form-control form-control-mod" rows="2"></textarea></div>
<div class="mb-3"><label class="form-label-mod">Requirements</label><textarea name="requirements" class="form-control form-control-mod" rows="2"></textarea></div>
<div class="mb-3"><label class="form-label-mod">Location</label><input type="text" name="location" class="form-control form-control-mod"></div>
<div class="mb-3"><label class="form-label-mod">Type</label><select name="type" class="form-select"><option value="full-time">Full Time</option><option value="part-time">Part Time</option><option value="contract">Contract</option></select></div>
</div><div class="modal-footer"><button type="button" class="btn-outline-mod" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn-primary-mod">Post</button></div></form>
</div></div></div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script></body></html>
