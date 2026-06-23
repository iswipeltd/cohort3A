<?php
require_once __DIR__ . '/../config/session.php';
require_admin();
$user = current_user();
$data = $pdo->query("SELECT p.*, CONCAT(u.first_name,' ',u.last_name) as emp_name, pp.month, pp.year FROM payslips p JOIN employees e ON p.employee_id = e.id JOIN users u ON e.user_id = u.id JOIN payroll_periods pp ON p.payroll_period_id = pp.id ORDER BY p.generated_at DESC LIMIT 100")->fetchAll();
?>
<?php include 'includes/head.php'; ?><body><?php include 'includes/sidebar.php'; ?><div class="main-content"><?php include 'includes/navbar.php'; ?>
<?php $err=$_SESSION['error']??''; $success=$_SESSION['success']??''; unset($_SESSION['error'],$_SESSION['success']); ?>
<?php if($err):?><div class="alert alert-danger d-flex align-items-center py-2 mb-3" style="border-radius:10px;font-size:0.85rem;"><i class="fa-solid fa-circle-exclamation me-2"></i><?php echo htmlspecialchars($err);?></div><?php endif;?>
<?php if($success):?><div class="alert alert-success d-flex align-items-center py-2 mb-3" style="border-radius:10px;font-size:0.85rem;"><i class="fa-solid fa-circle-check me-2"></i><?php echo htmlspecialchars($success);?></div><?php endif;?>

<div class="page-header"><div><h1 class="page-title">Payslips</h1><p class="page-subtitle mb-0">Employee payslip records</p></div></div>
<div class="px-4 pb-4">
<div class="card-modern"><div class="card-body-modern" style="padding:0;"><div class="table-responsive"><table class="table-modern" style="margin:0;"><thead style="background:#0f172a;"><tr><th>Employee</th><th>Period</th><th>Gross</th><th>Net</th><th>Status</th><th>Date</th></tr></thead><tbody>
<?php foreach($data as $row): $sc = match($row['status']){'paid'=>'status-active','pending'=>'status-pending',default=>'status-rejected'}; ?>
<tr><td class="fw-semibold small"><?php echo htmlspecialchars($row['emp_name']); ?></td><td><?php echo date('F Y', mktime(0,0,0,$row['month'],1,$row['year'])); ?></td><td>₦<?php echo number_format($row['gross_pay'],2); ?></td><td class="fw-bold" style="color:var(--primary);">₦<?php echo number_format($row['net_pay'],2); ?></td><td><span class="status-badge <?php echo $sc; ?>"><?php echo ucfirst($row['status']); ?></span></td><td><?php echo date('M j', strtotime($row['generated_at'])); ?></td></tr>
<?php endforeach; if(empty($data)): ?><tr><td colspan="6" class="text-center text-muted py-5">No payslips</td></tr><?php endif; ?>
</tbody></table></div></div></div>
</div></div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script></body></html>
