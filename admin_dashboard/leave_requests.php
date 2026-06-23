<?php
require_once __DIR__ . '/../config/session.php';
require_admin();
$user = current_user();

$status = $_GET['status'] ?? 'pending';
$valid = ['pending','approved','rejected','all'];
if (!in_array($status, $valid)) $status = 'pending';

$where = []; $params = [];
if ($status !== 'all') { $where[] = "lr.status = ?"; $params[] = $status; }
$whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';
$stmt = $pdo->prepare("SELECT lr.id,lr.start_date,lr.end_date,lr.days,lr.reason,lr.status,lr.created_at,lr.rejection_reason,CONCAT(u.first_name,' ',u.last_name) as emp,lt.name as ltype,u.avatar FROM leave_requests lr JOIN employees e ON lr.employee_id=e.id JOIN users u ON e.user_id=u.id JOIN leave_types lt ON lr.leave_type_id=lt.id {$whereSql} ORDER BY lr.created_at DESC");
$stmt->execute($params);
$requests = $stmt->fetchAll();

$counts = $pdo->query("SELECT status,COUNT(*) as cnt FROM leave_requests GROUP BY status")->fetchAll(PDO::FETCH_KEY_PAIR);
?>
<?php include 'includes/head.php'; ?>
<body>
<?php include 'includes/sidebar.php'; ?>
<div class="main-content">
<?php include 'includes/navbar.php'; ?>

<div class="page-header">
    <div>
        <h1 class="page-title">Leave Requests</h1>
        <p class="page-subtitle mb-0">Review and manage employee leave applications</p>
    </div>
</div>

<div class="px-4 pb-4">
<?php $err = $_SESSION['error'] ?? ''; $success = $_SESSION['success'] ?? ''; unset($_SESSION['error'], $_SESSION['success']); ?>
    <?php if($err):?><div class="alert alert-danger d-flex align-items-center py-2 mb-3" style="border-radius:10px;font-size:0.85rem;"><i class="fa-solid fa-circle-exclamation me-2"></i><?php echo htmlspecialchars($err);?></div><?php endif;?>
    <?php if($success):?><div class="alert alert-success d-flex align-items-center py-2 mb-3" style="border-radius:10px;font-size:0.85rem;"><i class="fa-solid fa-circle-check me-2"></i><?php echo htmlspecialchars($success);?></div><?php endif;?>

    <!-- Tabs -->
    <ul class="nav nav-tabs-mod mb-3">
        <li class="nav-item"><a class="nav-link <?php echo $status=='pending'?'active':''; ?>" href="?status=pending">Pending <span class="badge bg-warning text-dark ms-1" style="font-size:0.6rem;"><?php echo $counts['pending']??0; ?></span></a></li>
        <li class="nav-item"><a class="nav-link <?php echo $status=='approved'?'active':''; ?>" href="?status=approved">Approved <span class="badge bg-success ms-1" style="font-size:0.6rem;"><?php echo $counts['approved']??0; ?></span></a></li>
        <li class="nav-item"><a class="nav-link <?php echo $status=='rejected'?'active':''; ?>" href="?status=rejected">Rejected <span class="badge bg-danger ms-1" style="font-size:0.6rem;"><?php echo $counts['rejected']??0; ?></span></a></li>
        <li class="nav-item"><a class="nav-link <?php echo $status=='all'?'active':''; ?>" href="?status=all">All</a></li>
    </ul>

    <div class="card-modern">
        <div class="card-body-modern" style="padding:0;">
            <div class="table-responsive">
                <table class="table-modern">
                    <thead><tr><th>Employee</th><th>Type</th><th>From</th><th>To</th><th>Days</th><th>Reason</th><th>Status</th><th>Action</th></tr></thead>
                    <tbody>
                    <?php foreach ($requests as $req):
                        $sc = match($req['status']){'approved'=>'status-active','rejected'=>'status-rejected','cancelled'=>'status-inactive',default=>'status-pending'};
                    ?>
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <img src="<?php echo $req['avatar'] ? htmlspecialchars($req['avatar']) : 'https://ui-avatars.com/api/?name='.urlencode($req['emp']).'&background=334155&color=cbd5e1&size=64'; ?>" style="width:32px;height:32px;border-radius:50%;object-fit:cover;" alt="">
                                <span class="fw-semibold small"><?php echo htmlspecialchars($req['emp']); ?></span>
                            </div>
                        </td>
                        <td><?php echo htmlspecialchars($req['ltype']); ?></td>
                        <td><?php echo date('M j, Y', strtotime($req['start_date'])); ?></td>
                        <td><?php echo date('M j, Y', strtotime($req['end_date'])); ?></td>
                        <td><?php echo $req['days']; ?></td>
                        <td><?php echo htmlspecialchars($req['reason']??'-'); ?></td>
                        <td><span class="status-badge <?php echo $sc; ?>"><?php echo ucfirst($req['status']); ?></span></td>
                        <td>
                            <?php if ($req['status'] === 'pending'): ?>
                            <form action="/HRSuite/process/approve_leave.php" method="POST" class="d-inline">
                                <input type="hidden" name="leave_id" value="<?php echo $req['id']; ?>">
                                <input type="hidden" name="status" value="approved">
                                <button type="submit" class="btn btn-sm-mod btn-primary-mod me-1"><i class="fa-solid fa-check"></i></button>
                            </form>
                            <form action="/HRSuite/process/approve_leave.php" method="POST" class="d-inline" onsubmit="var r=prompt('Reason for rejection:');if(!r)return false;this.reason.value=r;return true;">
                                <input type="hidden" name="leave_id" value="<?php echo $req['id']; ?>">
                                <input type="hidden" name="status" value="rejected">
                                <input type="hidden" name="reason" value="">
                                <button type="submit" class="btn btn-sm-mod btn-danger-mod"><i class="fa-solid fa-xmark"></i></button>
                            </form>
                            <?php elseif($req['status']==='rejected' && $req['rejection_reason']): ?>
                            <span class="text-muted small"><?php echo htmlspecialchars($req['rejection_reason']); ?></span>
                            <?php else: ?>
                            <span class="text-muted small">-</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; if(empty($requests)): ?>
                    <tr><td colspan="8" class="text-center text-muted py-5">No leave requests found</td></tr>
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
