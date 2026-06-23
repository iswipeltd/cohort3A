<?php
require_once __DIR__ . '/../config/session.php';
require_admin();
require_once __DIR__ . '/../config/database.php';

$id = (int) ($_GET['id'] ?? 0);
if (!$id) {
    $_SESSION['error'] = 'Invalid employee ID.';
    header('Location: /HRSuite/admin_dashboard/employees.php');
    exit;
}

$stmt = $pdo->prepare("
    SELECT e.id, e.employee_code, e.salary, e.status, e.start_date, e.employment_type,
           u.first_name, u.last_name, u.email, u.phone, u.avatar,
           d.name as dept, r.name as role_name
    FROM employees e
    JOIN users u ON e.user_id = u.id
    LEFT JOIN departments d ON e.department_id = d.id
    LEFT JOIN roles r ON e.role_id = r.id
    WHERE e.id = ?
");
$stmt->execute([$id]);
$emp = $stmt->fetch();

if (!$emp) {
    $_SESSION['error'] = 'Employee not found.';
    header('Location: /HRSuite/admin_dashboard/employees.php');
    exit;
}
?>
<?php include 'includes/head.php'; ?><body><?php include 'includes/sidebar.php'; ?><div class="main-content"><?php include 'includes/navbar.php'; ?>

<div class="page-header"><div><h1 class="page-title">Delete Employee</h1><p class="page-subtitle mb-0">Confirm permanent deletion</p></div></div>
<div class="px-4 pb-4">
<div class="row g-3 justify-content-center">
<div class="col-lg-5">
<div class="card-modern" style="border:1px solid var(--danger);">
<div class="card-body-modern text-center">
<div style="width:64px;height:64px;border-radius:50%;background:var(--danger-bg);display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
<i class="fa-solid fa-triangle-exclamation" style="font-size:1.6rem;color:var(--danger);"></i>
</div>
<h4 class="fw-bold mb-1" style="color:var(--text);">Are you sure?</h4>
<p style="color:var(--muted);font-size:0.85rem;">This will permanently delete the employee and their user account. This action cannot be undone.</p>

<div class="d-flex align-items-center gap-3 my-4 p-3" style="background:var(--bg);border-radius:12px;border:1px solid var(--border);">
<img src="<?php echo $emp['avatar'] ? htmlspecialchars($emp['avatar']) : 'https://ui-avatars.com/api/?name='.urlencode($emp['first_name'].'+'.$emp['last_name']).'&background=334155&color=cbd5e1&size=120'; ?>" style="width:56px;height:56px;border-radius:50%;object-fit:cover;" alt="">
<div class="text-start">
<div class="fw-bold" style="color:var(--text);"><?php echo htmlspecialchars($emp['first_name'] . ' ' . $emp['last_name']); ?></div>
<div style="color:var(--muted);font-size:0.78rem;"><?php echo htmlspecialchars($emp['employee_code']); ?> &middot; <?php echo htmlspecialchars($emp['email']); ?></div>
<div style="color:var(--muted);font-size:0.75rem;margin-top:2px;"><?php echo htmlspecialchars($emp['dept'] ?? 'No Dept'); ?> &middot; <?php echo htmlspecialchars($emp['role_name'] ?? 'No Role'); ?></div>
</div>
</div>

<div class="d-flex gap-2">
<a href="/HRSuite/process/delete_employee.php?id=<?php echo $emp['id']; ?>" class="btn btn-danger-mod flex-fill"><i class="fa-solid fa-trash me-2"></i>Yes, Delete</a>
<a href="employees.php" class="btn btn-outline-mod flex-fill">Cancel</a>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body></html>
