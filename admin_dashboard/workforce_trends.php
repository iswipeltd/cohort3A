<?php
require_once __DIR__ . '/../config/session.php';
require_admin();
$user = current_user();
$data = $pdo->query("SELECT d.name, COUNT(e.id) as headcount, AVG(e.salary) as avg_salary FROM departments d LEFT JOIN employees e ON d.id = e.department_id GROUP BY d.id ORDER BY headcount DESC")->fetchAll();
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
<h1 class="page-title">Workforce Trends</h1>
<p class="page-subtitle mb-0">Department headcount analysis</p>
</div>
</div>
<div class="px-4 pb-4">
<div class="card-modern">
<div class="card-body-modern" style="padding:0;">
<div class="table-responsive">
<table class="table-modern" style="margin:0;">
<thead style="background:#0f172a;">
<tr><th>Department</th><th>Headcount</th><th>Average Salary</th></tr>
</thead>
<tbody>
<?php foreach($data as $row): ?>
<tr>
<td class="fw-semibold small"><?php echo htmlspecialchars($row['name']); ?></td>
<td><?php echo $row['headcount']; ?></td>
<td>₦<?php echo number_format($row['avg_salary'] ?? 0, 2); ?></td>
</tr>
<?php endforeach; if(empty($data)): ?>
<tr><td colspan="3" class="text-center text-muted py-5">No data</td></tr>
<?php endif; ?>
</tbody>
</table>
</div>
</div>
</div>
</div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
