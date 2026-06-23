<?php
require_once __DIR__ . '/../config/session.php';
require_auth();
$user = current_user();
$data = $pdo->prepare("SELECT * FROM activity_logs WHERE user_id = ? AND action='LOGIN' ORDER BY created_at DESC LIMIT 30");
$data->execute([$_SESSION['user_id']]);
$logs = $data->fetchAll();
?>
<?php include 'includes/head.php'; ?><body><?php include 'includes/sidebar.php'; ?><div class="main-content"><?php include 'includes/navbar.php'; ?>
<?php $err=$_SESSION['error']??''; $success=$_SESSION['success']??''; unset($_SESSION['error'],$_SESSION['success']); ?>
<?php if($err):?><div class="alert alert-danger d-flex align-items-center py-2 mb-3" style="border-radius:10px;font-size:0.85rem;"><i class="fa-solid fa-circle-exclamation me-2"></i><?php echo htmlspecialchars($err);?></div><?php endif;?>
<?php if($success):?><div class="alert alert-success d-flex align-items-center py-2 mb-3" style="border-radius:10px;font-size:0.85rem;"><i class="fa-solid fa-circle-check me-2"></i><?php echo htmlspecialchars($success);?></div><?php endif;?>

<div class="page-header"><div><h1 class="page-title">Login History</h1><p class="page-subtitle mb-0">Your recent sessions</p></div></div>
<div class="px-4 pb-4">
<div class="card-modern"><div class="card-body-modern" style="padding:0;"><div class="table-responsive"><table class="table" style="margin:0;"><thead style="background:#f8fafc;"><tr><th>Action</th><th>IP</th><th>Date</th></tr></thead><tbody>
<?php foreach($logs as $row): ?>
<tr><td class="fw-semibold small"><?php echo htmlspecialchars($row['action']); ?></td><td><?php echo htmlspecialchars($row['ip_address']??'-'); ?></td><td><?php echo date('M j, g:i A', strtotime($row['created_at'])); ?></td></tr>
<?php endforeach; if(empty($logs)): ?><tr><td colspan="3" class="text-center text-muted py-5">No login history</td></tr><?php endif; ?>
</tbody></table></div></div></div>
</div></div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script></body></html>
