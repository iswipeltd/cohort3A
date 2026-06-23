<?php
require_once __DIR__ . '/../config/session.php';
require_admin();
$user = current_user();
$data = $pdo->query("SELECT a.*, u.first_name, u.last_name FROM announcements a LEFT JOIN users u ON a.posted_by = u.id ORDER BY a.pinned DESC, a.created_at DESC")->fetchAll();
?>
<?php include 'includes/head.php'; ?>
<body>
<?php include 'includes/sidebar.php'; ?>
<div class="main-content">
<?php include 'includes/navbar.php'; ?>
<div class="page-header">
<div><h1 class="page-title">Announcements</h1><p class="page-subtitle mb-0">Manage company announcements</p></div>
<a href="#" class="btn-primary-mod" data-bs-toggle="modal" data-bs-target="#addModal"><i class="fa-solid fa-plus me-2"></i>New Announcement</a>
</div>
<div class="px-4 pb-4">
<?php $err = $_SESSION['error'] ?? ''; $success = $_SESSION['success'] ?? ''; unset($_SESSION['error'], $_SESSION['success']); ?>
    <?php if($err):?><div class="alert alert-danger d-flex align-items-center py-2 mb-3" style="border-radius:10px;font-size:0.85rem;"><i class="fa-solid fa-circle-exclamation me-2"></i><?php echo htmlspecialchars($err);?></div><?php endif;?>
    <?php if($success):?><div class="alert alert-success d-flex align-items-center py-2 mb-3" style="border-radius:10px;font-size:0.85rem;"><i class="fa-solid fa-circle-check me-2"></i><?php echo htmlspecialchars($success);?></div><?php endif;?>

<div class="card-modern"><div class="card-body-modern" style="padding:0;"><div class="table-responsive"><table class="table-modern" style="margin:0;"><thead><tr><th>Title</th><th>Message</th><th>Audience</th><th>Pinned</th><th>Posted By</th><th>Date</th><th style="width:100px;">Action</th></tr></thead><tbody>
<?php foreach($data as $row): ?>
<tr><td class="fw-semibold small"><?php echo htmlspecialchars($row['title']); ?></td><td><?php echo htmlspecialchars(substr($row['message'],0,60)); ?>...</td><td><?php echo ucfirst($row['target_audience']); ?></td><td><?php echo $row['pinned'] ? '<span class="status-badge status-active">Yes</span>' : '<span class="status-badge status-inactive">No</span>'; ?></td><td><?php echo htmlspecialchars(($row['first_name']??'').' '.($row['last_name']??'')); ?></td><td><?php echo date('M j', strtotime($row['created_at'])); ?></td><td><a href="/HRSuite/process/delete_announcement.php?id=<?php echo $row['id']; ?>" class="btn btn-sm" style="background:rgba(244,63,94,0.15);color:#f43f5e;border-radius:6px;font-size:0.75rem;" onclick="return confirm('Delete?')"><i class="fa-solid fa-trash"></i></a></td></tr>
<?php endforeach; if(empty($data)): ?><tr><td colspan="7" class="text-center text-muted py-5">No announcements</td></tr><?php endif; ?>
</tbody></table></div></div></div>
</div>
<div class="modal fade" id="addModal" tabindex="-1"><div class="modal-dialog"><div class="modal-content" style="border-radius:16px;"><div class="modal-header"><h5 class="modal-title fw-bold">New Announcement</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
<form action="/HRSuite/process/add_announcement.php" method="POST"><div class="modal-body">
<div class="mb-3"><label class="form-label-mod">Title</label><input type="text" name="title" class="form-control form-control-mod" required></div>
<div class="mb-3"><label class="form-label-mod">Message</label><textarea name="message" class="form-control form-control-mod" rows="3" required></textarea></div>
<div class="mb-3"><label class="form-label-mod">Target Audience</label><select name="target_audience" class="form-select"><option value="all">All</option><option value="admin">Admin Only</option><option value="manager">Managers</option><option value="employee">Employees</option></select></div>
<div class="form-check"><input class="form-check-input" type="checkbox" name="pinned" id="pinned"><label class="form-check-label small" for="pinned">Pin to top</label></div>
</div><div class="modal-footer"><button type="button" class="btn-outline-mod" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn-primary-mod">Post</button></div></form>
</div></div></div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
