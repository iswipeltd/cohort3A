<?php
require_once __DIR__ . '/../config/session.php';
require_admin();
$user = current_user();
$data = $pdo->query("SELECT a.*, CONCAT(u.first_name,' ',u.last_name) as emp_name, e.employee_code FROM attendance a JOIN employees e ON a.employee_id = e.id JOIN users u ON e.user_id = u.id WHERE a.overtime > 0 ORDER BY a.record_date DESC LIMIT 100")->fetchAll();
?>
<?php include 'includes/head.php'; ?><body><?php include 'includes/sidebar.php'; ?><div class="main-content"><?php include 'includes/navbar.php'; ?>
<?php $err=$_SESSION['error']??''; $success=$_SESSION['success']??''; unset($_SESSION['error'],$_SESSION['success']); ?>
<?php if($err):?><div class="alert alert-danger d-flex align-items-center py-2 mb-3" style="border-radius:10px;font-size:0.85rem;"><i class="fa-solid fa-circle-exclamation me-2"></i><?php echo htmlspecialchars($err);?></div><?php endif;?>
<?php if($success):?><div class="alert alert-success d-flex align-items-center py-2 mb-3" style="border-radius:10px;font-size:0.85rem;"><i class="fa-solid fa-circle-check me-2"></i><?php echo htmlspecialchars($success);?></div><?php endif;?>

<div class="page-header"><div><h1 class="page-title">Overtime</h1><p class="page-subtitle mb-0">Track employee overtime hours</p></div></div>
<div class="px-4 pb-4">
<div class="card-modern"><div class="card-body-modern" style="padding:0;"><div class="table-responsive"><table class="table-modern" style="margin:0;"><thead style="background:#0f172a;"><tr><th>Employee</th><th>Code</th><th>Date</th><th>Hours</th><th>Overtime</th></tr></thead><tbody>
<?php foreach($data as $row): ?>
<tr><td class="fw-semibold small"><?php echo htmlspecialchars($row['emp_name']); ?></td><td><?php echo htmlspecialchars($row['employee_code']); ?></td><td><?php echo date('M j, Y', strtotime($row['record_date'])); ?></td><td><?php echo $row['hours_worked']; ?></td><td><span class="fw-bold" style="color:#f59e0b;"><?php echo $row['overtime']; ?> hrs</span></td></tr>
<?php endforeach; if(empty($data)): ?><tr><td colspan="5" class="text-center text-muted py-5">No overtime records</td></tr><?php endif; ?>
</tbody></table></div></div></div>
</div></div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script></body></html>
