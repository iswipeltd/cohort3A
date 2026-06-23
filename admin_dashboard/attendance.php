<?php
require_once __DIR__ . '/../config/session.php';
require_admin();
$user = current_user();

$date = $_GET['date'] ?? date('Y-m-d');
$stmt = $pdo->prepare("SELECT a.*,CONCAT(u.first_name,' ',u.last_name) as emp,e.employee_code,u.avatar FROM attendance a JOIN employees e ON a.employee_id=e.id JOIN users u ON e.user_id=u.id WHERE a.record_date=? ORDER BY a.clock_in DESC");
$stmt->execute([$date]);
$records = $stmt->fetchAll();

$summary = $pdo->prepare("SELECT SUM(CASE WHEN status='present' THEN 1 ELSE 0 END) as present,SUM(CASE WHEN status='late' THEN 1 ELSE 0 END) as late,SUM(CASE WHEN status='absent' THEN 1 ELSE 0 END) as absent,SUM(CASE WHEN status='on_leave' THEN 1 ELSE 0 END) as onleave FROM attendance WHERE record_date=?");
$summary->execute([$date]);
$s = $summary->fetch();
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
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div><h1 class="page-title">Attendance Monitor</h1><p class="page-subtitle mb-0">Daily attendance tracking</p></div>
        <form method="get" class="d-flex gap-2"><input type="date" name="date" value="<?php echo $date; ?>" class="form-control form-control-mod" onchange="this.form.submit()" style="width:160px;"></form>
    </div>
</div>

<div class="px-4 pb-4">
    <div class="row g-3 mb-3">
        <div class="col-6 col-md-3"><div class="kpi-card"><div class="kpi-icon" style="background:#d1fae5;color:#047857;"><i class="fa-solid fa-check"></i></div><div class="kpi-value" style="color:#047857;"><?php echo $s['present']??0; ?></div><div class="kpi-label">Present</div></div></div>
        <div class="col-6 col-md-3"><div class="kpi-card"><div class="kpi-icon" style="background:rgba(245,158,11,0.15);color:#f59e0b;"><i class="fa-solid fa-clock"></i></div><div class="kpi-value" style="color:#92400e;"><?php echo $s['late']??0; ?></div><div class="kpi-label">Late</div></div></div>
        <div class="col-6 col-md-3"><div class="kpi-card"><div class="kpi-icon" style="background:rgba(244,63,94,0.15);color:#f43f5e;"><i class="fa-solid fa-xmark"></i></div><div class="kpi-value" style="color:#b91c1c;"><?php echo $s['absent']??0; ?></div><div class="kpi-label">Absent</div></div></div>
        <div class="col-6 col-md-3"><div class="kpi-card"><div class="kpi-icon" style="background:rgba(99,102,241,0.12);color:#818cf8;"><i class="fa-solid fa-umbrella-beach"></i></div><div class="kpi-value" style="color:#4338ca;"><?php echo $s['onleave']??0; ?></div><div class="kpi-label">On Leave</div></div></div>
    </div>

    <div class="card-modern">
        <div class="card-body-modern" style="padding:0;">
            <div class="table-responsive">
                <table class="table-modern">
                    <thead><tr><th>Employee</th><th>Clock In</th><th>Clock Out</th><th>Hours</th><th>Overtime</th><th>Status</th></tr></thead>
                    <tbody>
                    <?php foreach ($records as $r):
                        $sc = match($r['status']){'present'=>'status-active','late'=>'status-pending','absent'=>'status-rejected','on_leave'=>'status-inactive',default=>'status-inactive'};
                    ?>
                    <tr>
                        <td><div class="d-flex align-items-center gap-2"><img src="<?php echo $r['avatar'] ? htmlspecialchars($r['avatar']) : 'https://ui-avatars.com/api/?name='.urlencode($r['emp']).'&background=334155&color=cbd5e1&size=64'; ?>" style="width:32px;height:32px;border-radius:50%;object-fit:cover;"><div><div class="fw-semibold small"><?php echo htmlspecialchars($r['emp']); ?></div><div class="text-muted" style="font-size:0.7rem;"><?php echo htmlspecialchars($r['employee_code']); ?></div></div></div></td>
                        <td><?php echo $r['clock_in'] ? date('g:i A', strtotime($r['clock_in'])) : '-'; ?></td>
                        <td><?php echo $r['clock_out'] ? date('g:i A', strtotime($r['clock_out'])) : '-'; ?></td>
                        <td><?php echo $r['hours_worked'] ?? '-'; ?></td>
                        <td><?php echo $r['overtime'] ?? '0'; ?></td>
                        <td><span class="status-badge <?php echo $sc; ?>"><?php echo ucfirst($r['status']); ?></span></td>
                    </tr>
                    <?php endforeach; if(empty($records)): ?>
                    <tr><td colspan="6" class="text-center text-muted py-5">No attendance records for <?php echo date('M j, Y', strtotime($date)); ?></td></tr>
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
