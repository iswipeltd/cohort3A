<?php
require_once __DIR__ . '/../config/session.php';
require_auth();
$user = current_user();
$empId = get_employee_id($_SESSION['user_id']);
$data = [];
if ($empId) {
    $stmt = $pdo->prepare("SELECT * FROM expenses WHERE employee_id = ? ORDER BY created_at DESC LIMIT 50");
    $stmt->execute([$empId]);
    $data = $stmt->fetchAll();
}
?>
<?php include 'includes/head.php'; ?><body><?php include 'includes/sidebar.php'; ?><div class="main-content"><?php include 'includes/navbar.php'; ?>
<?php $err=$_SESSION['error']??''; $success=$_SESSION['success']??''; unset($_SESSION['error'],$_SESSION['success']); ?>
<?php if($err):?><div class="alert alert-danger d-flex align-items-center py-2 mb-3" style="border-radius:10px;font-size:0.85rem;"><i class="fa-solid fa-circle-exclamation me-2"></i><?php echo htmlspecialchars($err);?></div><?php endif;?>
<?php if($success):?><div class="alert alert-success d-flex align-items-center py-2 mb-3" style="border-radius:10px;font-size:0.85rem;"><i class="fa-solid fa-circle-check me-2"></i><?php echo htmlspecialchars($success);?></div><?php endif;?>

<div class="page-header"><div><h1 class="page-title">Expense History</h1><p class="page-subtitle mb-0">Your submitted expenses</p></div></div>
<div class="px-4 pb-4">
<div class="card-modern"><div class="card-body-modern" style="padding:0;"><div class="table-responsive"><table class="table" style="margin:0;"><thead style="background:#f8fafc;"><tr><th>Category</th><th>Amount</th><th>Description</th><th>Status</th><th>Date</th></tr></thead><tbody>
<?php foreach($data as $row): $sc = match($row['status']){'approved'=>'status-active','rejected'=>'status-rejected',default=>'status-pending'}; ?>
<tr><td class="fw-semibold small"><?php echo htmlspecialchars($row['type']??'-'); ?></td><td class="fw-bold" style="color:var(--primary);">₦<?php echo number_format($row['amount'],2); ?></td><td><?php echo htmlspecialchars($row['description']??'-'); ?></td><td><span class="status-badge <?php echo $sc; ?>"><?php echo ucfirst($row['status']); ?></span></td><td><?php echo date('M j, Y', strtotime($row['created_at'])); ?></td></tr>
<?php endforeach; if(empty($data)): ?><tr><td colspan="5" class="text-center text-muted py-5">No expenses</td></tr><?php endif; ?>
</tbody></table></div></div></div>
</div></div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script></body></html>
