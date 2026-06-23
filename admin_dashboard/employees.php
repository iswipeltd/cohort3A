<?php
require_once __DIR__ . '/../config/session.php';
require_admin();
$user = current_user();

$page = max(1, (int) ($_GET['page'] ?? 1));
$limit = 12; $offset = ($page - 1) * $limit;
$search = trim($_GET['search'] ?? '');
$deptFilter = (int) ($_GET['dept'] ?? 0);
$statusFilter = $_GET['status'] ?? '';

$where = []; $params = [];
if ($search) { $where[] = "(u.first_name LIKE ? OR u.last_name LIKE ? OR e.employee_code LIKE ?)"; $like = '%'.$search.'%'; $params = array_merge($params, [$like,$like,$like]); }
if ($deptFilter) { $where[] = "e.department_id = ?"; $params[] = $deptFilter; }
if ($statusFilter) { $where[] = "e.status = ?"; $params[] = $statusFilter; }
$whereSql = $where ? 'WHERE '.implode(' AND ', $where) : '';

$countStmt = $pdo->prepare("SELECT COUNT(*) FROM employees e JOIN users u ON e.user_id=u.id {$whereSql}");
$countStmt->execute($params);
$total = (int) $countStmt->fetchColumn();
$totalPages = max(1, ceil($total / $limit));

$stmt = $pdo->prepare("SELECT e.id,e.employee_code,u.first_name,u.last_name,u.email,u.phone,u.avatar,d.name as dept,r.name as role_name,e.salary,e.status,e.start_date,e.employment_type FROM employees e JOIN users u ON e.user_id=u.id LEFT JOIN departments d ON e.department_id=d.id LEFT JOIN roles r ON e.role_id=r.id {$whereSql} ORDER BY e.created_at DESC LIMIT {$limit} OFFSET {$offset}");
$stmt->execute($params);
$employees = $stmt->fetchAll();

$depts = $pdo->query("SELECT id,name FROM departments ORDER BY name")->fetchAll();
?>
<?php include 'includes/head.php'; ?>
<body>
<?php include 'includes/sidebar.php'; ?>
<div class="main-content">
<?php include 'includes/navbar.php'; ?>

<div class="page-header">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
            <h1 class="page-title">Employee Directory</h1>
            <p class="page-subtitle mb-0">Manage and view all employee records</p>
        </div>
        <a href="employee_add.php" class="btn btn-primary-mod"><i class="fa-solid fa-plus me-2"></i>Add Employee</a>
    </div>
</div>

<div class="px-4 pb-4">
<?php $err = $_SESSION['error'] ?? ''; $success = $_SESSION['success'] ?? ''; unset($_SESSION['error'], $_SESSION['success']); ?>
    <?php if($err):?><div class="alert alert-danger d-flex align-items-center py-2 mb-3" style="border-radius:10px;font-size:0.85rem;"><i class="fa-solid fa-circle-exclamation me-2"></i><?php echo htmlspecialchars($err);?></div><?php endif;?>
    <?php if($success):?><div class="alert alert-success d-flex align-items-center py-2 mb-3" style="border-radius:10px;font-size:0.85rem;"><i class="fa-solid fa-circle-check me-2"></i><?php echo htmlspecialchars($success);?></div><?php endif;?>

    <!-- Filters -->
    <div class="card-modern mb-3">
        <div class="card-body-modern">
            <form method="get" class="row g-2 align-items-end">
                <div class="col-md-4">
                    <label class="form-label-mod">Search</label>
                    <div class="input-group">
                        <span class="input-group-text border-end-0" style="border-radius:10px 0 0 10px;background:var(--bg);border-color:var(--border);"><i class="fa-solid fa-search" style="font-size:0.75rem;color:var(--muted);"></i></span>
                        <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" class="form-control form-control-mod border-start-0" style="border-radius:0 10px 10px 0;" placeholder="Name, code, email...">
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label-mod">Department</label>
                    <select name="dept" class="form-select">
                        <option value="">All Departments</option>
                        <?php foreach ($depts as $d): ?><option value="<?php echo $d['id']; ?>" <?php echo $deptFilter==$d['id']?'selected':''; ?>><?php echo htmlspecialchars($d['name']); ?></option><?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label-mod">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="active" <?php echo $statusFilter=='active'?'selected':''; ?>>Active</option>
                        <option value="inactive" <?php echo $statusFilter=='inactive'?'selected':''; ?>>Inactive</option>
                        <option value="on_leave" <?php echo $statusFilter=='on_leave'?'selected':''; ?>>On Leave</option>
                        <option value="probation" <?php echo $statusFilter=='probation'?'selected':''; ?>>Probation</option>
                        <option value="terminated" <?php echo $statusFilter=='terminated'?'selected':''; ?>>Terminated</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary-mod w-100">Filter</button>
                    <?php if($search||$deptFilter||$statusFilter): ?><a href="employees.php" class="btn btn-outline-mod"><i class="fa-solid fa-rotate-left"></i></a><?php endif; ?>
                </div>
            </form>
        </div>
    </div>

    <!-- Table -->
    <div class="card-modern">
        <div class="card-body-modern" style="padding:0;">
            <div class="table-responsive">
                <table class="table-modern">
                    <thead><tr><th>Employee</th><th>Department</th><th>Role</th><th>Type</th><th>Salary</th><th>Joined</th><th>Status</th><th style="width:120px;">Actions</th></tr></thead>
                    <tbody>
                    <?php foreach ($employees as $emp):
                        $statusClass = match($emp['status']){'active'=>'status-active','on_leave'=>'status-pending','probation'=>'status-probation','terminated'=>'status-rejected',default=>'status-inactive'};
                    ?>
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <img src="<?php echo $emp['avatar'] ? htmlspecialchars($emp['avatar']) : 'https://ui-avatars.com/api/?name='.urlencode($emp['first_name'].'+'.$emp['last_name']).'&background=334155&color=cbd5e1&size=64'; ?>" style="width:36px;height:36px;border-radius:50%;object-fit:cover;" alt="">
                                <div>
                                    <div class="fw-semibold small"><?php echo htmlspecialchars($emp['first_name'].' '.$emp['last_name']); ?></div>
                                    <div class="text-muted" style="font-size:0.72rem;"><?php echo htmlspecialchars($emp['employee_code']); ?> &middot; <?php echo htmlspecialchars($emp['email']); ?></div>
                                </div>
                            </div>
                        </td>
                        <td><?php echo htmlspecialchars($emp['dept']??'-'); ?></td>
                        <td><?php echo htmlspecialchars($emp['role_name']??'-'); ?></td>
                        <td><?php echo ucfirst($emp['employment_type']??'-'); ?></td>
                        <td><?php echo format_currency($emp['salary']); ?></td>
                        <td><?php echo $emp['start_date'] ? date('M j, Y', strtotime($emp['start_date'])) : '-'; ?></td>
                        <td><span class="status-badge <?php echo $statusClass; ?>"><?php echo ucfirst($emp['status']); ?></span></td>
                        <td>
                            <a href="employee_view.php?id=<?php echo $emp['id']; ?>" class="btn btn-sm-mod btn-outline-mod me-1"><i class="fa-solid fa-eye"></i></a>
                            <a href="employee_edit.php?id=<?php echo $emp['id']; ?>" class="btn btn-sm-mod btn-primary-mod me-1"><i class="fa-solid fa-pen"></i></a>
                            <a href="delete_employee.php?id=<?php echo $emp['id']; ?>" class="btn btn-sm-mod btn-danger-mod"><i class="fa-solid fa-trash"></i></a>
                        </td>
                    </tr>
                    <?php endforeach; if(empty($employees)): ?>
                    <tr><td colspan="8" class="text-center text-muted py-5">No employees found</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php if($totalPages>1): ?>
        <div class="card-header-modern" style="border-top:1px solid var(--border);border-bottom:none;">
            <div class="d-flex justify-content-between align-items-center w-100">
                <span class="text-muted small">Showing <?php echo $offset+1; ?>-<?php echo min($offset+$limit,$total); ?> of <?php echo $total; ?></span>
                <ul class="pagination pagination-mod mb-0">
                    <?php for($i=1;$i<=$totalPages;$i++): ?>
                    <li class="page-item <?php echo $i==$page?'active':''; ?>"><a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&dept=<?php echo $deptFilter; ?>&status=<?php echo $statusFilter; ?>"><?php echo $i; ?></a></li>
                    <?php endfor; ?>
                </ul>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
