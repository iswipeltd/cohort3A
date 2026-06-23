<?php
require_once __DIR__ . '/../config/session.php';
require_auth();
$user = current_user();
$data = $pdo->prepare("SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT 50");
$data->execute([$_SESSION['user_id']]);
$notifications = $data->fetchAll();

// Mark all notifications as read when viewing this page
$pdo->prepare("UPDATE notifications SET read_at = NOW() WHERE user_id = ? AND read_at IS NULL")
    ->execute([$_SESSION['user_id']]);
?>
<?php include 'includes/head.php'; ?><body><?php include 'includes/sidebar.php'; ?><div class="main-content"><?php include 'includes/navbar.php'; ?>
<?php $err=$_SESSION['error']??''; $success=$_SESSION['success']??''; unset($_SESSION['error'],$_SESSION['success']); ?>
<?php if($err):?><div class="alert alert-danger d-flex align-items-center py-2 mb-3" style="border-radius:10px;font-size:0.85rem;"><i class="fa-solid fa-circle-exclamation me-2"></i><?php echo htmlspecialchars($err);?></div><?php endif;?>
<?php if($success):?><div class="alert alert-success d-flex align-items-center py-2 mb-3" style="border-radius:10px;font-size:0.85rem;"><i class="fa-solid fa-circle-check me-2"></i><?php echo htmlspecialchars($success);?></div><?php endif;?>

<div class="page-header"><div><h1 class="page-title">My Notifications</h1><p class="page-subtitle mb-0">Your recent alerts</p></div></div>
<div class="px-4 pb-4">
<div class="card-modern"><div class="card-body-modern" style="padding:0;"><div class="table-responsive"><table class="table" style="margin:0;"><thead style="background:#f8fafc;"><tr><th>Type</th><th>Message</th><th>Date</th><th>Read</th></tr></thead><tbody>
<?php foreach($notifications as $n): ?>
<tr><td><span class="badge" style="background:#e0e7ff;color:#4338ca;font-size:0.72rem;"><?php echo ucfirst($n['type']); ?></span></td><td><?php echo htmlspecialchars($n['message']); ?></td><td><?php echo date('M j, g:i A', strtotime($n['created_at'])); ?></td><td><?php echo $n['read_at'] ? '<span class="status-badge status-active">Yes</span>' : '<span class="status-badge status-pending">No</span>'; ?></td></tr>
<?php endforeach; if(empty($notifications)): ?><tr><td colspan="4" class="text-center text-muted py-5">No notifications</td></tr><?php endif; ?>
</tbody></table></div></div></div>
</div></div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script></body></html>
