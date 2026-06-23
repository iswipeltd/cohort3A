<?php
require_once __DIR__ . '/../config/session.php';
require_auth();
$user = current_user();
$empId = get_employee_id($_SESSION['user_id']);
$data = [];
if ($empId) {
    $stmt = $pdo->prepare("SELECT pr.*, pp.month, pp.year FROM payroll_records pr JOIN payroll_periods pp ON pr.period_id = pp.id WHERE pr.employee_id = ? ORDER BY pp.year DESC, pp.month DESC");
    $stmt->execute([$empId]);
    $data = $stmt->fetchAll();
}
?>
<?php include 'includes/head.php'; ?><body><?php include 'includes/sidebar.php'; ?><div class="main-content"><?php include 'includes/navbar.php'; ?>
<?php $err=$_SESSION['error']??''; $success=$_SESSION['success']??''; unset($_SESSION['error'],$_SESSION['success']); ?>
<?php if($err):?><div class="alert alert-danger d-flex align-items-center py-2 mb-3" style="border-radius:10px;font-size:0.85rem;"><i class="fa-solid fa-circle-exclamation me-2"></i><?php echo htmlspecialchars($err);?></div><?php endif;?>
<?php if($success):?><div class="alert alert-success d-flex align-items-center py-2 mb-3" style="border-radius:10px;font-size:0.85rem;"><i class="fa-solid fa-circle-check me-2"></i><?php echo htmlspecialchars($success);?></div><?php endif;?>

<div class="page-header"><div><h1 class="page-title">Payroll Records</h1><p class="page-subtitle mb-0">Your payment history</p></div></div>
<div class="px-4 pb-4">
<div class="card-modern"><div class="card-body-modern" style="padding:0;"><div class="table-responsive"><table class="table" style="margin:0;"><thead style="background:#f8fafc;"><tr><th>Period</th><th>Gross</th><th>Net</th><th>Status</th></tr></thead><tbody>
<?php foreach($data as $row): ?>
<tr><td class="fw-semibold small"><?php echo date('F Y', mktime(0,0,0,$row['month'],1,$row['year'])); ?></td><td>₦<?php echo number_format($row['gross_pay'],2); ?></td><td class="fw-bold" style="color:var(--primary);">₦<?php echo number_format($row['net_pay'],2); ?></td><td><span class="status-badge <?php echo $row['status']=='paid'?'status-active':'status-pending'; ?>"><?php echo ucfirst($row['status']); ?></span></td></tr>
<?php endforeach; if(empty($data)): ?><tr><td colspan="4" class="text-center text-muted py-5">No payroll records</td></tr><?php endif; ?>
</tbody></table></div></div></div>
</div></div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script></body></html>
