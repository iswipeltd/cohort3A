<?php
require_once __DIR__ . '/../config/session.php';
require_admin();
$user = current_user();
$data = $pdo->query("SELECT al.*, CONCAT(u.first_name,' ',u.last_name) as user_name FROM activity_logs al JOIN users u ON al.user_id = u.id ORDER BY al.created_at DESC LIMIT 200")->fetchAll();
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
<h1 class="page-title">Audit Reports</h1>
<p class="page-subtitle mb-0">Audit Reports Management</p>
</div>
</div>
<div class="px-4 pb-4">
<div class="card-modern"><div class="card-body-modern" style="padding:0;"><div class="table-responsive"><table class="table-modern" style="margin:0;"><thead style="background:#0f172a;"><tr><th>User Name</th><th>Action</th><th>Module</th><th>Details</th><th>Ip Address</th><th>Created At</th></tr></thead><tbody>
<?php foreach($data as $row): ?>
<tr><td><?php echo htmlspecialchars($row['user_name'] ?? '-'); ?></td><td><?php echo htmlspecialchars($row['action'] ?? '-'); ?></td><td><?php echo htmlspecialchars($row['module'] ?? '-'); ?></td><td><?php echo htmlspecialchars($row['details'] ?? '-'); ?></td><td><?php echo htmlspecialchars($row['ip_address'] ?? '-'); ?></td><td><?php echo $row['created_at'] ? date('M j, Y', strtotime($row['created_at'])) : '-'; ?></td></tr>
<?php endforeach; if(empty($data)): ?>
<tr><td colspan="6" class="text-center text-muted py-5">No records found</td></tr>
<?php endif; ?>
</tbody></table></div></div></div>
</div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
