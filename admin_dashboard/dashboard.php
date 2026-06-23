<?php
require_once __DIR__ . '/../config/session.php';
require_admin();
$user = current_user();

$hour = (int) date('G');
$greeting = $hour < 12 ? 'Good morning' : ($hour < 17 ? 'Good afternoon' : 'Good evening');

$totalEmp = $pdo->query("SELECT COUNT(*) FROM employees WHERE status='active'")->fetchColumn();
$newHires = $pdo->query("SELECT COUNT(*) FROM employees WHERE start_date >= DATE_FORMAT(NOW(),'%Y-%m-01') AND status='active'")->fetchColumn();
$pendingLeaves = $pdo->query("SELECT COUNT(*) FROM leave_requests WHERE status='pending'")->fetchColumn();
$pendingExp = $pdo->query("SELECT COUNT(*) FROM expenses WHERE status='pending'")->fetchColumn();
$openJobs = $pdo->query("SELECT COUNT(*) FROM job_postings WHERE status='open'")->fetchColumn();
$clockedIn = $pdo->query("SELECT COUNT(*) FROM attendance WHERE record_date=CURDATE() AND status IN('present','late','remote')")->fetchColumn();

$deptData = $pdo->query("SELECT d.name,COUNT(e.id) as cnt FROM departments d LEFT JOIN employees e ON d.id=e.department_id AND e.status='active' GROUP BY d.id ORDER BY cnt DESC LIMIT 6")->fetchAll();
$recentLogs = $pdo->query("SELECT l.*,CONCAT(u.first_name,' ',u.last_name) as actor FROM activity_logs l LEFT JOIN users u ON l.user_id=u.id ORDER BY l.created_at DESC LIMIT 6")->fetchAll();

$pendingActions = $pdo->query("SELECT lr.id,lr.start_date,lr.end_date,lr.days,CONCAT(u.first_name,' ',u.last_name) as emp,lt.name as ltype FROM leave_requests lr JOIN employees e ON lr.employee_id=e.id JOIN users u ON e.user_id=u.id JOIN leave_types lt ON lr.leave_type_id=lt.id WHERE lr.status='pending' ORDER BY lr.created_at DESC LIMIT 4")->fetchAll();

$insight = $pendingLeaves > 3 ? "You have {$pendingLeaves} leave requests awaiting review." : ($newHires > 0 ? "{$newHires} new hires joined this month." : ($openJobs > 0 ? "{$openJobs} positions are open for recruitment." : "All systems running smoothly. Have a productive day!"));
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
    <div class="d-flex align-items-center gap-3">
        <img src="<?php echo $user['avatar'] ? htmlspecialchars($user['avatar']) : 'https://ui-avatars.com/api/?name='.urlencode($user['first_name'].'+'.$user['last_name']).'&background=6366f1&color=fff&size=120'; ?>" alt="" class="d-none d-md-block" style="width:56px;height:56px;border-radius:16px;object-fit:cover;border:3px solid var(--primary);">
        <div>
            <div class="text-muted small mb-1"><?php echo date('l, F j, Y'); ?></div>
            <h1 class="page-title"><?php echo $greeting; ?>, <?php echo htmlspecialchars($user['first_name']); ?>!</h1>
            <p class="page-subtitle mb-0"><?php echo htmlspecialchars($insight); ?></p>
        </div>
    </div>
</div>

<div class="px-4 pb-4">
    <!-- KPIs -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="kpi-card animate-fade">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="kpi-icon" style="background:rgba(99,102,241,0.15);color:#818cf8;"><i class="fa-solid fa-users"></i></div>
                    <span class="status-badge status-active">+<?php echo $newHires; ?> new</span>
                </div>
                <div class="kpi-value" style="color:#1d4ed8;"><?php echo number_format($totalEmp); ?></div>
                <div class="kpi-label">Total Employees</div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="kpi-card animate-fade">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="kpi-icon" style="background:rgba(245,158,11,0.15);color:#f59e0b;"><i class="fa-solid fa-hourglass-half"></i></div>
                    <span class="status-badge status-pending">Action</span>
                </div>
                <div class="kpi-value" style="color:#92400e;"><?php echo $pendingLeaves; ?></div>
                <div class="kpi-label">Pending Leaves</div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="kpi-card animate-fade">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="kpi-icon" style="background:rgba(236,72,153,0.15);color:#ec4899;"><i class="fa-solid fa-file-invoice-dollar"></i></div>
                    <span class="status-badge" style="background:rgba(236,72,153,0.15);color:#ec4899;">Review</span>
                </div>
                <div class="kpi-value" style="color:#be185d;"><?php echo $pendingExp; ?></div>
                <div class="kpi-label">Expense Claims</div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="kpi-card animate-fade">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="kpi-icon" style="background:#d1fae5;color:#047857;"><i class="fa-solid fa-user-check"></i></div>
                    <span class="status-badge status-active">Today</span>
                </div>
                <div class="kpi-value" style="color:#047857;"><?php echo $clockedIn; ?></div>
                <div class="kpi-label">Clocked In</div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <!-- Chart -->
        <div class="col-lg-5">
            <div class="card-modern animate-fade">
                <div class="card-header-modern">
                    <h6 class="fw-bold mb-0">Department Headcount</h6>
                </div>
                <div class="card-body-modern">
                    <div style="position:relative;height:220px;"><canvas id="deptChart"></canvas></div>
                </div>
            </div>
        </div>
        <!-- Activity -->
        <div class="col-lg-4">
            <div class="card-modern animate-fade">
                <div class="card-header-modern">
                    <h6 class="fw-bold mb-0">Recent Activity</h6>
                    <a href="activity_logs.php" class="small text-primary fw-semibold text-decoration-none">View All</a>
                </div>
                <div class="card-body-modern" style="padding-bottom:16px;">
                    <?php foreach ($recentLogs as $log):
                        $c = match($log['action']){'LOGIN'=>'#22c55e','LOGOUT'=>'#ef4444','CREATE'=>'#3b82f6','UPDATE'=>'#f59e0b',default=>'#6b7280'};
                    ?>
                    <div class="d-flex align-items-start py-2" style="border-bottom:1px solid var(--border);">
                        <div style="width:8px;height:8px;border-radius:50%;background:<?php echo $c; ?>;margin-top:6px;margin-right:10px;flex-shrink:0;"></div>
                        <div class="flex-grow-1">
                            <div class="fw-semibold small"><?php echo htmlspecialchars($log['action']); ?> <span class="text-muted fw-normal">on <?php echo htmlspecialchars($log['module']); ?></span></div>
                            <div class="text-muted" style="font-size:0.72rem;"><?php echo htmlspecialchars($log['actor']??'System'); ?> &middot; <?php echo date('g:i A',strtotime($log['created_at'])); ?></div>
                        </div>
                    </div>
                    <?php endforeach; if(empty($recentLogs)): ?>
                    <div class="text-muted text-center py-4">No recent activity</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <!-- Pending -->
        <div class="col-lg-3">
            <div class="card-modern animate-fade">
                <div class="card-header-modern">
                    <h6 class="fw-bold mb-0">Needs Attention</h6>
                    <span class="badge bg-danger rounded-pill" style="font-size:0.65rem;"><?php echo count($pendingActions); ?></span>
                </div>
                <div class="card-body-modern" style="padding-bottom:16px;">
                    <?php foreach ($pendingActions as $pa): ?>
                    <div class="d-flex align-items-center p-2 mb-2" style="background:var(--card-hover);border-radius:10px;">
                        <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($pa['emp']); ?>&background=e0e7ff&color=6366f1&size=64" style="width:36px;height:36px;border-radius:50%;margin-right:10px;" alt="">
                        <div class="flex-grow-1">
                            <div class="fw-semibold small"><?php echo htmlspecialchars($pa['emp']); ?></div>
                            <div class="text-muted" style="font-size:0.72rem;"><?php echo htmlspecialchars($pa['ltype']); ?> &middot; <?php echo $pa['days']; ?> days</div>
                        </div>
                        <a href="leave_requests.php" class="btn btn-sm-mod" style="background:rgba(99,102,241,0.1);color:#a5b4fc;font-weight:600;font-size:0.7rem;">Review</a>
                    </div>
                    <?php endforeach; if(empty($pendingActions)): ?>
                    <div class="text-muted text-center py-4"><i class="fa-solid fa-circle-check mb-2" style="color:#22c55e;font-size:1.5rem;"></i><br>All caught up!</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <h6 class="fw-bold mb-3">Quick Actions</h6>
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-4 col-lg-3 col-xl-2">
            <a href="employee_add.php" class="quick-tile d-block">
                <div class="quick-tile-icon" style="background:rgba(99,102,241,0.15);color:#818cf8;"><i class="fa-solid fa-user-plus"></i></div>
                <div class="fw-semibold small">Add Employee</div>
            </a>
        </div>
        <div class="col-6 col-md-4 col-lg-3 col-xl-2">
            <a href="leave_requests.php" class="quick-tile d-block">
                <div class="quick-tile-icon" style="background:rgba(245,158,11,0.15);color:#f59e0b;"><i class="fa-solid fa-clipboard-check"></i></div>
                <div class="fw-semibold small">Approve Leaves</div>
            </a>
        </div>
        <div class="col-6 col-md-4 col-lg-3 col-xl-2">
            <a href="payroll.php" class="quick-tile d-block">
                <div class="quick-tile-icon" style="background:rgba(236,72,153,0.15);color:#ec4899;"><i class="fa-solid fa-file-invoice-dollar"></i></div>
                <div class="fw-semibold small">Payroll</div>
            </a>
        </div>
        <div class="col-6 col-md-4 col-lg-3 col-xl-2">
            <a href="job_postings.php" class="quick-tile d-block">
                <div class="quick-tile-icon" style="background:#d1fae5;color:#047857;"><i class="fa-solid fa-briefcase"></i></div>
                <div class="fw-semibold small">Post Job</div>
            </a>
        </div>
        <div class="col-6 col-md-4 col-lg-3 col-xl-2">
            <a href="reports.php" class="quick-tile d-block">
                <div class="quick-tile-icon" style="background:rgba(99,102,241,0.12);color:#818cf8;"><i class="fa-solid fa-chart-pie"></i></div>
                <div class="fw-semibold small">Reports</div>
            </a>
        </div>
        <div class="col-6 col-md-4 col-lg-3 col-xl-2">
            <a href="employees.php" class="quick-tile d-block">
                <div class="quick-tile-icon" style="background:#ccfbf1;color:#0f766e;"><i class="fa-solid fa-users-viewfinder"></i></div>
                <div class="fw-semibold small">Directory</div>
            </a>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
new Chart(document.getElementById('deptChart'),{
    type:'doughnut',
    data:{labels:[<?php echo implode(',',array_map(fn($d)=>"'".htmlspecialchars($d['name'])."'",$deptData)); ?>],datasets:[{data:[<?php echo implode(',',array_column($deptData,'cnt')); ?>],backgroundColor:['var(--primary)','#ec4899','#22d3ee','#f59e0b','#10b981','#8b5cf6'],borderWidth:0,hoverOffset:6}]},
    options:{responsive:true,maintainAspectRatio:false,cutout:'68%',plugins:{legend:{position:'bottom',labels:{boxWidth:10,usePointStyle:true,padding:12,font:{size:10}}}},animation:{animateScale:true,animateRotate:true}}
});
</script>
</body>
</html>
