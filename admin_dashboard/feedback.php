<?php
require_once __DIR__ . '/../config/session.php';
require_admin();
$user = current_user();
$data = $pdo->query("SELECT m.*, CONCAT(s.first_name,' ',s.last_name) as sender_name FROM messages m JOIN users s ON m.from_user_id = s.id ORDER BY m.created_at DESC LIMIT 100")->fetchAll();
?>
<?php include 'includes/head.php'; ?><body><?php include 'includes/sidebar.php'; ?><div class="main-content"><?php include 'includes/navbar.php'; ?>
<?php $err=$_SESSION['error']??''; $success=$_SESSION['success']??''; unset($_SESSION['error'],$_SESSION['success']); ?>
<?php if($err):?><div class="alert alert-danger d-flex align-items-center py-2 mb-3" style="border-radius:10px;font-size:0.85rem;"><i class="fa-solid fa-circle-exclamation me-2"></i><?php echo htmlspecialchars($err);?></div><?php endif;?>
<?php if($success):?><div class="alert alert-success d-flex align-items-center py-2 mb-3" style="border-radius:10px;font-size:0.85rem;"><i class="fa-solid fa-circle-check me-2"></i><?php echo htmlspecialchars($success);?></div><?php endif;?>

<div class="page-header"><div><h1 class="page-title">Feedback</h1><p class="page-subtitle mb-0">Messages and feedback from users</p></div></div>
<div class="px-4 pb-4">
<div class="card-modern"><div class="card-body-modern" style="padding:0;"><div class="table-responsive"><table class="table-modern" style="margin:0;"><thead style="background:#0f172a;"><tr><th>From</th><th>Subject</th><th>Message</th><th>Read</th><th>Date</th></tr></thead><tbody>
<?php foreach($data as $m): ?>
<tr><td class="fw-semibold small"><?php echo htmlspecialchars($m['sender_name']); ?></td><td><?php echo htmlspecialchars($m['subject']??'-'); ?></td><td><?php echo htmlspecialchars(substr($m['body']??'',0,50)); ?>...</td><td><?php echo $m['is_read'] ? '<span class="status-badge status-active">Yes</span>' : '<span class="status-badge status-pending">No</span>'; ?></td><td><?php echo date('M j, g:i A', strtotime($m['created_at'])); ?></td></tr>
<?php endforeach; if(empty($data)): ?><tr><td colspan="5" class="text-center text-muted py-5">No feedback</td></tr><?php endif; ?>
</tbody></table></div></div></div>
</div></div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script></body></html>
