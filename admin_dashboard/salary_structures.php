<?php
require_once __DIR__ . '/../config/session.php';
require_admin();
$user = current_user();
$data = $pdo->query("SELECT e.*, CONCAT(u.first_name,' ',u.last_name) as emp_name, d.name as dept FROM employees e JOIN users u ON e.user_id = u.id LEFT JOIN departments d ON e.department_id = d.id ORDER BY e.salary DESC")->fetchAll();
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
<h1 class="page-title">Salary Structures</h1>
<p class="page-subtitle mb-0">Salary Structures Management</p>
</div>
</div>
<div class="px-4 pb-4">
<div class="card-modern"><div class="card-body-modern" style="padding:0;"><div class="table-responsive"><table class="table-modern" style="margin:0;"><thead style="background:#0f172a;"><tr><th>Emp Name</th><th>Dept</th><th>Salary</th><th>Employment Type</th><th>Status</th></tr></thead><tbody>
<?php foreach($data as $row): ?>
<tr><td><?php echo htmlspecialchars($row['emp_name'] ?? '-'); ?></td><td><?php echo htmlspecialchars($row['dept'] ?? '-'); ?></td><td>₦<?php echo number_format($row['salary'] ?? 0, 2); ?></td><td><?php echo htmlspecialchars($row['employment_type'] ?? '-'); ?></td><td><span class="status-badge <?php echo $row['status']=='active'||$row['status']=='approved'||$row['status']=='completed'||$row['status']=='present'||$row['status']=='paid' ? 'status-active' : ($row['status']=='pending' ? 'status-pending' : 'status-rejected'); ?>"><?php echo ucfirst($row['status']); ?></span></td></tr>
<?php endforeach; if(empty($data)): ?>
<tr><td colspan="5" class="text-center text-muted py-5">No records found</td></tr>
<?php endif; ?>
</tbody></table></div></div></div>
</div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
