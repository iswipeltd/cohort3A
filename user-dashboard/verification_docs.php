<?php
require_once __DIR__ . '/../config/session.php';
require_auth();
$user = current_user();
$data = $pdo->query("SELECT * FROM documents WHERE type IN ('id_card','other') ORDER BY uploaded_at DESC")->fetchAll();
?>
<?php include 'includes/head.php'; ?><body><?php include 'includes/sidebar.php'; ?><div class="main-content"><?php include 'includes/navbar.php'; ?>
<?php $err=$_SESSION['error']??''; $success=$_SESSION['success']??''; unset($_SESSION['error'],$_SESSION['success']); ?>
<?php if($err):?><div class="alert alert-danger d-flex align-items-center py-2 mb-3" style="border-radius:10px;font-size:0.85rem;"><i class="fa-solid fa-circle-exclamation me-2"></i><?php echo htmlspecialchars($err);?></div><?php endif;?>
<?php if($success):?><div class="alert alert-success d-flex align-items-center py-2 mb-3" style="border-radius:10px;font-size:0.85rem;"><i class="fa-solid fa-circle-check me-2"></i><?php echo htmlspecialchars($success);?></div><?php endif;?>

<div class="page-header"><div><h1 class="page-title">Verification Documents</h1><p class="page-subtitle mb-0">Identity and verification</p></div></div>
<div class="px-4 pb-4">
<div class="card-modern"><div class="card-body-modern" style="padding:0;"><div class="table-responsive"><table class="table" style="margin:0;"><thead style="background:#f8fafc;"><tr><th>File</th><th>Type</th><th>Type</th><th>Date</th></tr></thead><tbody>
<?php foreach($data as $row): ?>
<tr><td class="fw-semibold small"><?php echo htmlspecialchars($row['filename']); ?></td><td><?php echo htmlspecialchars($row['type']??'-'); ?></td><td><?php echo htmlspecialchars($row['type']??'-'); ?></td><td><?php echo date('M j, Y', strtotime($row['uploaded_at'])); ?></td></tr>
<?php endforeach; if(empty($data)): ?><tr><td colspan="4" class="text-center text-muted py-5">No verification documents</td></tr><?php endif; ?>
</tbody></table></div></div></div>
</div></div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script></body></html>
