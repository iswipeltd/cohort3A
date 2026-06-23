<?php
require_once __DIR__ . '/../config/session.php';
require_auth();
$user = current_user();
$employee = current_employee();
$empId = get_employee_id($_SESSION['user_id']);

$todayHours = '0h';
$todayAttendance = null;
if ($empId) {
    $stmt = $pdo->prepare("SELECT clock_in, clock_out, hours_worked FROM attendance WHERE employee_id = ? AND record_date = CURDATE()");
    $stmt->execute([$empId]);
    $todayAttendance = $stmt->fetch();
    if ($todayAttendance) {
        $todayHours = $todayAttendance['clock_out'] ? $todayAttendance['hours_worked'] . 'h' : 'Clocked In';
    } else {
        $todayHours = 'Not Clocked';
    }
}

$leaveBalance = 0;
if ($empId) {
    $totalAnnual = 20;
    $stmt = $pdo->query("SELECT default_days FROM leave_types WHERE name = 'Annual Leave' LIMIT 1");
    $annual = $stmt->fetch();
    if ($annual) $totalAnnual = (int)$annual['default_days'];
    $stmt = $pdo->prepare("SELECT COALESCE(SUM(days),0) FROM leave_requests WHERE employee_id = ? AND leave_type_id = (SELECT id FROM leave_types WHERE name = 'Annual Leave' LIMIT 1) AND status = 'approved' AND YEAR(start_date) = YEAR(CURDATE())");
    $stmt->execute([$empId]);
    $used = (int) $stmt->fetchColumn();
    $leaveBalance = $totalAnnual - $used;
}

$pendingTasks = 0;
if ($empId) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM tasks WHERE assigned_to = ? AND status IN ('open','in_progress','review')");
    $stmt->execute([$empId]);
    $pendingTasks = (int) $stmt->fetchColumn();
}

$stmt = $pdo->query("SELECT end_date FROM payroll_periods WHERE status = 'open' ORDER BY end_date ASC LIMIT 1");
$nextPayroll = $stmt->fetch();
$nextPayrollDate = $nextPayroll ? date('M j', strtotime($nextPayroll['end_date'])) : 'May 15';

$weekData = []; $weekLabels = [];
for ($i = 4; $i >= 0; $i--) {
    $day = date('D', strtotime("-{$i} days"));
    $date = date('Y-m-d', strtotime("-{$i} days"));
    $weekLabels[] = $day;
    if ($empId) {
        $s = $pdo->prepare("SELECT hours_worked FROM attendance WHERE employee_id = ? AND record_date = ?");
        $s->execute([$empId, $date]);
        $r = $s->fetch();
        $weekData[] = $r ? (float)$r['hours_worked'] : 0;
    } else { $weekData[] = 0; }
}

$leaveBreakdown = [];
if ($empId) {
    $types = $pdo->query("SELECT id, name, default_days FROM leave_types WHERE status = 'active'");
    foreach ($types as $lt) {
        $s = $pdo->prepare("SELECT COALESCE(SUM(days),0) FROM leave_requests WHERE employee_id = ? AND leave_type_id = ? AND status = 'approved' AND YEAR(start_date) = YEAR(CURDATE())");
        $s->execute([$empId, $lt['id']]);
        $used = (int) $s->fetchColumn();
        $leaveBreakdown[] = ['name' => $lt['name'], 'balance' => max(0, (int)$lt['default_days'] - $used)];
    }
}

$recentRequests = [];
if ($empId) {
    $recentRequests = $pdo->query("
        SELECT 'Leave' as type, CONCAT((SELECT lt.name FROM leave_types lt WHERE lt.id = lr.leave_type_id),' - ',lr.days,' days') as details, lr.created_at as dt, lr.status, '/HRSuite/user-dashboard/leave_status.php' as link FROM leave_requests lr WHERE lr.employee_id = {$empId}
        UNION ALL
        SELECT 'Expense', CONCAT(ex.type,' - ',ex.amount) as details, ex.created_at as dt, ex.status, '/HRSuite/user-dashboard/expense_status.php' as link FROM expenses ex WHERE ex.employee_id = {$empId}
        UNION ALL
        SELECT 'Task', t.title, t.created_at, t.status, '/HRSuite/user-dashboard/my_tasks.php' FROM tasks t WHERE t.assigned_to = {$empId}
        ORDER BY dt DESC LIMIT 8
    ")->fetchAll();
}

$announcements = $pdo->query("SELECT title, message, created_at, target_audience FROM announcements WHERE expires_at IS NULL OR expires_at > NOW() ORDER BY created_at DESC LIMIT 5")->fetchAll();
$notifCount = unread_notifications_count($_SESSION['user_id']);
?>
<?php include 'includes/head.php'; ?>
<body>
<?php include 'includes/sidebar.php'; ?>
<div class="main-content">
<?php include 'includes/navbar.php'; ?>

<div class="page-header">
<div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
<div>
<div class="text-muted small mb-1"><?php echo date('l, F j, Y'); ?></div>
<h1 class="page-title">Good <?php echo (int)date('G')<12?'morning':((int)date('G')<17?'afternoon':'evening'); ?>, <?php echo htmlspecialchars($user['first_name']); ?>!</h1>
<p class="page-subtitle mb-0">Welcome to your employee portal.</p>
</div>
<a href="apply_leave.php" class="btn-primary-mod"><i class="fa-solid fa-plus me-2"></i>Apply Leave</a>
</div>
</div>

<div class="px-4 pb-4">
<div class="row g-3 mb-4">
<div class="col-6 col-xl-3"><div class="kpi-card"><div class="kpi-icon" style="background:#dbeafe;color:#1d4ed8;"><i class="fa-solid fa-clock"></i></div><div class="kpi-value" style="color:#1d4ed8;"><?php echo $todayHours; ?></div><div class="kpi-label">Today's Hours</div></div></div>
<div class="col-6 col-xl-3"><div class="kpi-card"><div class="kpi-icon" style="background:#d1fae5;color:#047857;"><i class="fa-solid fa-umbrella-beach"></i></div><div class="kpi-value" style="color:#047857;"><?php echo $leaveBalance; ?></div><div class="kpi-label">Leave Balance</div></div></div>
<div class="col-6 col-xl-3"><div class="kpi-card"><div class="kpi-icon" style="background:#fef3c7;color:#92400e;"><i class="fa-solid fa-list-check"></i></div><div class="kpi-value" style="color:#92400e;"><?php echo $pendingTasks; ?></div><div class="kpi-label">Pending Tasks</div></div></div>
<div class="col-6 col-xl-3"><div class="kpi-card"><div class="kpi-icon" style="background:#e0e7ff;color:#4338ca;"><i class="fa-solid fa-money-check"></i></div><div class="kpi-value" style="color:#4338ca;"><?php echo $nextPayrollDate; ?></div><div class="kpi-label">Next Payroll</div></div></div>
</div>

<div class="row g-3 mb-4">
<div class="col-lg-6"><div class="card-modern"><div class="card-header-modern"><h6 class="fw-bold mb-0">Attendance This Week</h6></div><div class="card-body-modern"><div style="position:relative;height:220px;"><canvas id="attendance-chart"></canvas></div></div></div></div>
<div class="col-lg-6"><div class="card-modern"><div class="card-header-modern"><h6 class="fw-bold mb-0">Leave Balance</h6></div><div class="card-body-modern"><div style="position:relative;height:220px;"><canvas id="leave-chart"></canvas></div></div></div></div>
</div>

<div class="row g-3 mb-4">
<div class="col-lg-8"><div class="card-modern"><div class="card-header-modern"><h6 class="fw-bold mb-0">My Recent Requests</h6></div><div class="card-body-modern" style="padding:0;"><div class="table-responsive"><table class="table" style="margin:0;"><thead style="background:#f8fafc;"><tr><th>Type</th><th>Details</th><th>Date</th><th>Status</th><th>Action</th></tr></thead><tbody>
<?php foreach ($recentRequests as $req):
$badgeClass = 'bg-secondary';
if (in_array($req['status'], ['approved','success','completed'])) $badgeClass = 'bg-success';
elseif ($req['status'] === 'pending') $badgeClass = 'bg-warning text-dark';
elseif ($req['status'] === 'rejected') $badgeClass = 'bg-danger';
elseif ($req['status'] === 'in_progress') $badgeClass = 'bg-info text-dark';
?>
<tr><td><?php echo htmlspecialchars($req['type']); ?></td><td><?php echo htmlspecialchars($req['details']); ?></td><td><?php echo date('M j, Y', strtotime($req['dt'])); ?></td><td><span class="badge <?php echo $badgeClass; ?>"><?php echo ucfirst($req['status']); ?></span></td><td><a class="btn btn-sm" style="background:linear-gradient(135deg,var(--primary),var(--primary));color:#fff;border-radius:6px;font-size:0.75rem;font-weight:600;padding:4px 12px;" href="<?php echo htmlspecialchars($req['link']); ?>">View</a></td></tr>
<?php endforeach; if (empty($recentRequests)): ?>
<tr><td colspan="5" class="text-center text-muted py-4">No recent requests</td></tr>
<?php endif; ?>
</tbody></table></div></div></div></div>
<div class="col-lg-4"><div class="card-modern h-100"><div class="card-header-modern"><h6 class="fw-bold mb-0">Announcements</h6></div><div class="card-body-modern">
<?php foreach ($announcements as $ann): ?>
<div class="d-flex py-2" style="border-bottom:1px solid #f1f5f9;"><div style="width:8px;height:8px;border-radius:50%;background:var(--primary);margin-top:6px;margin-right:10px;flex-shrink:0;"></div><div><div class="fw-semibold small"><?php echo htmlspecialchars($ann['title']); ?></div><div class="text-muted" style="font-size:0.72rem;"><?php echo date('M j', strtotime($ann['created_at'])); ?> &middot; <?php echo htmlspecialchars($ann['target_audience']); ?></div></div></div>
<?php endforeach; if (empty($announcements)): ?><div class="text-muted text-center py-4">No announcements</div><?php endif; ?>
</div></div></div>
</div>

<h6 class="fw-bold mb-3">Quick Actions</h6>
<div class="row g-3">
<div class="col-6 col-md-4 col-lg-3 col-xl-2"><a href="clock_in_out.php" class="quick-tile"><div class="quick-tile-icon" style="background:#dbeafe;color:#1d4ed8;"><i class="fa-solid fa-clock"></i></div><div class="fw-semibold small">Clock In</div></a></div>
<div class="col-6 col-md-4 col-lg-3 col-xl-2"><a href="apply_leave.php" class="quick-tile"><div class="quick-tile-icon" style="background:#d1fae5;color:#047857;"><i class="fa-solid fa-plane-departure"></i></div><div class="fw-semibold small">Apply Leave</div></a></div>
<div class="col-6 col-md-4 col-lg-3 col-xl-2"><a href="my_payslips.php" class="quick-tile"><div class="quick-tile-icon" style="background:#e0e7ff;color:#4338ca;"><i class="fa-solid fa-money-check"></i></div><div class="fw-semibold small">Payslips</div></a></div>
<div class="col-6 col-md-4 col-lg-3 col-xl-2"><a href="submit_expense.php" class="quick-tile"><div class="quick-tile-icon" style="background:#fef3c7;color:#92400e;"><i class="fa-solid fa-wallet"></i></div><div class="fw-semibold small">Expense</div></a></div>
<div class="col-6 col-md-4 col-lg-3 col-xl-2"><a href="my_tasks.php" class="quick-tile"><div class="quick-tile-icon" style="background:#fce7f3;color:#be185d;"><i class="fa-solid fa-tasks"></i></div><div class="fw-semibold small">My Tasks</div></a></div>
<div class="col-6 col-md-4 col-lg-3 col-xl-2"><a href="my_profile.php" class="quick-tile"><div class="quick-tile-icon" style="background:#ccfbf1;color:#0f766e;"><i class="fa-solid fa-user"></i></div><div class="fw-semibold small">Profile</div></a></div>
</div>
</div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
new Chart(document.getElementById('attendance-chart'), { type: 'bar', data: { labels: [<?php echo implode(',', array_map(function($l){ return "'".$l."'"; }, $weekLabels)); ?>], datasets: [{ label:'Hours', data:[<?php echo implode(',', $weekData); ?>], backgroundColor:'rgba(79,70,229,0.7)', borderRadius:6 }] }, options: { responsive:true, maintainAspectRatio:false, plugins:{legend:{display:false}}, scales:{y:{beginAtZero:true,grid:{color:'#f1f5f9'}},x:{grid:{display:false}}}} });
new Chart(document.getElementById('leave-chart'), { type: 'doughnut', data: { labels: [<?php echo implode(',', array_map(function($lb){ return "'".$lb['name']."'"; }, $leaveBreakdown)); ?>], datasets: [{ data:[<?php echo implode(',', array_column($leaveBreakdown, 'balance')); ?>], backgroundColor:['var(--primary)','#ec4899','#22d3ee','#f59e0b','#10b981','#8b5cf6'], borderWidth:0, hoverOffset:6 }] }, options: { responsive:true, maintainAspectRatio:false, cutout:'68%', plugins:{legend:{position:'bottom',labels:{boxWidth:10,usePointStyle:true,padding:10,font:{size:10}}}}}});
</script>
</body>
</html>
