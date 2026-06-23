<?php
require_once __DIR__ . '/../config/session.php';
require_admin();
$user = current_user();
$tables = [
    ['name'=>'Users','count'=>$pdo->query("SELECT COUNT(*) FROM users")->fetchColumn(),'table'=>'users'],
    ['name'=>'Employees','count'=>$pdo->query("SELECT COUNT(*) FROM employees")->fetchColumn(),'table'=>'employees'],
    ['name'=>'Departments','count'=>$pdo->query("SELECT COUNT(*) FROM departments")->fetchColumn(),'table'=>'departments'],
    ['name'=>'Leave Requests','count'=>$pdo->query("SELECT COUNT(*) FROM leave_requests")->fetchColumn(),'table'=>'leave_requests'],
    ['name'=>'Attendance','count'=>$pdo->query("SELECT COUNT(*) FROM attendance")->fetchColumn(),'table'=>'attendance'],
    ['name'=>'Payroll Records','count'=>$pdo->query("SELECT COUNT(*) FROM payroll_records")->fetchColumn(),'table'=>'payroll_records'],
    ['name'=>'Candidates','count'=>$pdo->query("SELECT COUNT(*) FROM candidates")->fetchColumn(),'table'=>'candidates'],
    ['name'=>'Job Postings','count'=>$pdo->query("SELECT COUNT(*) FROM job_postings")->fetchColumn(),'table'=>'job_postings'],
    ['name'=>'Documents','count'=>$pdo->query("SELECT COUNT(*) FROM documents")->fetchColumn(),'table'=>'documents'],
    ['name'=>'Tasks','count'=>$pdo->query("SELECT COUNT(*) FROM tasks")->fetchColumn(),'table'=>'tasks'],
];
?>
<?php include 'includes/head.php'; ?><body><?php include 'includes/sidebar.php'; ?><div class="main-content"><?php include 'includes/navbar.php'; ?>
<?php $err=$_SESSION['error']??''; $success=$_SESSION['success']??''; unset($_SESSION['error'],$_SESSION['success']); ?>
<?php if($err):?><div class="alert alert-danger d-flex align-items-center py-2 mb-3" style="border-radius:10px;font-size:0.85rem;"><i class="fa-solid fa-circle-exclamation me-2"></i><?php echo htmlspecialchars($err);?></div><?php endif;?>
<?php if($success):?><div class="alert alert-success d-flex align-items-center py-2 mb-3" style="border-radius:10px;font-size:0.85rem;"><i class="fa-solid fa-circle-check me-2"></i><?php echo htmlspecialchars($success);?></div><?php endif;?>

<div class="page-header"><div><h1 class="page-title">Export Data</h1><p class="page-subtitle mb-0">Exportable datasets</p></div></div>
<div class="px-4 pb-4">
<div class="card-modern"><div class="card-body-modern" style="padding:0;"><div class="table-responsive"><table class="table-modern" style="margin:0;"><thead style="background:#0f172a;"><tr><th>Table</th><th>Records</th><th style="width:120px;">Action</th></tr></thead><tbody>
<?php foreach($tables as $t): ?>
<tr><td class="fw-semibold small"><?php echo htmlspecialchars($t['name']); ?></td><td><?php echo number_format($t['count']); ?></td><td><a href="/HRSuite/process/export.php?table=<?php echo $t['table']; ?>" class="btn btn-sm" style="background:rgba(99,102,241,0.15);color:#818cf8;border-radius:6px;font-size:0.75rem;"><i class="fa-solid fa-download me-1"></i>Export</a></td></tr>
<?php endforeach; ?>
</tbody></table></div></div></div>
</div></div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script></body></html>
