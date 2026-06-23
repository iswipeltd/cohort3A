<?php
require_once __DIR__ . '/../config/session.php';
require_admin();
$user = current_user();

$status = $_GET['status'] ?? 'pending';
$valid = ['pending','approved','rejected','all'];
if (!in_array($status, $valid)) $status = 'pending';

$where = []; $params = [];
if ($status !== 'all') { $where[] = "ex.status = ?"; $params[] = $status; }
$whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$stmt = $pdo->prepare("SELECT ex.*,CONCAT(u.first_name,' ',u.last_name) as emp,e.employee_code,u.avatar,e.user_id as emp_user_id FROM expenses ex JOIN employees e ON ex.employee_id=e.id JOIN users u ON e.user_id=u.id {$whereSql} ORDER BY ex.created_at DESC");
$stmt->execute($params);
$expenses = $stmt->fetchAll();

$err = $_SESSION['error'] ?? '';
$success = $_SESSION['success'] ?? '';
unset($_SESSION['error'], $_SESSION['success']);
?>
<?php include 'includes/head.php'; ?>
<body>
<?php include 'includes/sidebar.php'; ?>
<div class="main-content">
<?php include 'includes/navbar.php'; ?>

<div class="page-header">
    <div><h1 class="page-title">Expense Claims</h1><p class="page-subtitle mb-0">Review and manage employee expense submissions</p></div>
</div>

<div class="px-4 pb-4">
    <?php if($err):?><div class="alert alert-danger d-flex align-items-center py-2 mb-3" style="border-radius:10px;font-size:0.85rem;"><i class="fa-solid fa-circle-exclamation me-2"></i><?php echo htmlspecialchars($err);?></div><?php endif;?>
    <?php if($success):?><div class="alert alert-success d-flex align-items-center py-2 mb-3" style="border-radius:10px;font-size:0.85rem;"><i class="fa-solid fa-circle-check me-2"></i><?php echo htmlspecialchars($success);?></div><?php endif;?>

    <ul class="nav nav-tabs-mod mb-3">
        <li class="nav-item"><a class="nav-link <?php echo $status=='pending'?'active':''; ?>" href="?status=pending">Pending</a></li>
        <li class="nav-item"><a class="nav-link <?php echo $status=='approved'?'active':''; ?>" href="?status=approved">Approved</a></li>
        <li class="nav-item"><a class="nav-link <?php echo $status=='rejected'?'active':''; ?>" href="?status=rejected">Rejected</a></li>
        <li class="nav-item"><a class="nav-link <?php echo $status=='all'?'active':''; ?>" href="?status=all">All</a></li>
    </ul>

    <div class="card-modern">
        <div class="card-body-modern" style="padding:0;">
            <div class="table-responsive">
                <table class="table-modern">
                    <thead><tr><th>Employee</th><th>Type</th><th>Amount</th><th>Date</th><th>Description</th><th>Receipt</th><th>Status</th><th class="text-end">Actions</th></tr></thead>
                    <tbody>
                    <?php foreach ($expenses as $ex):
                        $sc = match($ex['status']){'approved'=>'status-active','rejected'=>'status-rejected',default=>'status-pending'};
                    ?>
                    <tr>
                        <td><div class="d-flex align-items-center gap-2"><img src="<?php echo $ex['avatar'] ? htmlspecialchars($ex['avatar']) : 'https://ui-avatars.com/api/?name='.urlencode($ex['emp']).'&background=334155&color=cbd5e1&size=64'; ?>" style="width:32px;height:32px;border-radius:50%;object-fit:cover;"><span class="fw-semibold small"><?php echo htmlspecialchars($ex['emp']); ?></span></div></td>
                        <td><?php echo htmlspecialchars($ex['type']); ?></td>
                        <td><strong><?php echo format_currency($ex['amount']); ?></strong></td>
                        <td><?php echo date('M j, Y', strtotime($ex['expense_date'])); ?></td>
                        <td><?php echo htmlspecialchars($ex['description']??'-'); ?></td>
                        <td><?php echo $ex['receipt_path'] ? '<a href="'.htmlspecialchars($ex['receipt_path']).'" target="_blank" class="text-primary"><i class="fa-solid fa-file-pdf"></i></a>' : '-'; ?></td>
                        <td><span class="status-badge <?php echo $sc; ?>"><?php echo ucfirst($ex['status']); ?></span></td>
                        <td class="text-end">
                            <?php if ($ex['status'] === 'pending'): ?>
                            <form action="/HRSuite/process/approve_expense.php" method="POST" class="d-inline" onsubmit="return confirm('Approve this expense claim?');">
                                <input type="hidden" name="expense_id" value="<?php echo $ex['id']; ?>">
                                <button type="submit" class="btn btn-sm-mod btn-primary-mod" style="padding:5px 10px;font-size:0.75rem;"><i class="fa-solid fa-check me-1"></i>Approve</button>
                            </form>
                            <button type="button" class="btn btn-sm-mod btn-danger-mod" style="padding:5px 10px;font-size:0.75rem;" data-bs-toggle="modal" data-bs-target="#rejectModal<?php echo $ex['id']; ?>"><i class="fa-solid fa-xmark me-1"></i>Reject</button>
                            <!-- Reject Modal -->
                            <div class="modal fade" id="rejectModal<?php echo $ex['id']; ?>" tabindex="-1" aria-hidden="true">
                              <div class="modal-dialog modal-sm modal-dialog-centered">
                                <div class="modal-content" style="border-radius:14px;border:1px solid var(--border);background:var(--card);">
                                  <div class="modal-header border-0 pb-0"><h6 class="modal-title fw-bold" style="color:var(--text);">Reject Expense</h6><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div>
                                  <form action="/HRSuite/process/reject_expense.php" method="POST">
                                    <div class="modal-body pt-2">
                                      <input type="hidden" name="expense_id" value="<?php echo $ex['id']; ?>">
                                      <label class="form-label-mod">Reason (optional)</label>
                                      <textarea name="reason" class="form-control form-control-mod" rows="2" placeholder="Why is this being rejected?"></textarea>
                                    </div>
                                    <div class="modal-footer border-0 pt-0"><button type="button" class="btn btn-outline-mod btn-sm-mod" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-danger-mod btn-sm-mod">Reject</button></div>
                                  </form>
                                </div>
                              </div>
                            </div>
                            <?php elseif ($ex['status'] === 'approved'): ?>
                                <span class="text-muted small"><i class="fa-solid fa-check-circle me-1" style="color:var(--success);"></i>Approved</span>
                            <?php else: ?>
                                <span class="text-muted small"><i class="fa-solid fa-circle-xmark me-1" style="color:var(--danger);"></i>Rejected</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; if(empty($expenses)): ?>
                    <tr><td colspan="8" class="text-center text-muted py-5">No expense claims</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
