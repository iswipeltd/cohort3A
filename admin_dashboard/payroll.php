<?php
require_once __DIR__ . '/../config/session.php';
require_admin();
$user = current_user();

$periodId = (int) ($_GET['period_id'] ?? 0);
$periods = $pdo->query("SELECT id, month, year, start_date, end_date, status FROM payroll_periods ORDER BY year DESC, month DESC")->fetchAll();

// Get current selected period info
$currentPeriod = null;
if ($periodId) {
    foreach ($periods as $p) {
        if ($p['id'] == $periodId) { $currentPeriod = $p; break; }
    }
}

$records = [];
if ($periodId) {
    $stmt = $pdo->prepare("SELECT pr.*, u.first_name, u.last_name, e.employee_code, u.avatar FROM payroll_records pr JOIN employees e ON pr.employee_id=e.id JOIN users u ON e.user_id=u.id WHERE pr.period_id=? ORDER BY u.last_name, u.first_name");
    $stmt->execute([$periodId]);
    $records = $stmt->fetchAll();
}

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
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div><h1 class="page-title">Payroll</h1><p class="page-subtitle mb-0">Manage employee payroll and payslips</p></div>
        <button type="button" class="btn btn-primary-mod" data-bs-toggle="modal" data-bs-target="#createPeriodModal"><i class="fa-solid fa-plus me-2"></i>Create Period</button>
    </div>
</div>

<div class="px-4 pb-4">
    <?php if($err):?><div class="alert alert-danger d-flex align-items-center py-2 mb-3" style="border-radius:10px;font-size:0.85rem;"><i class="fa-solid fa-circle-exclamation me-2"></i><?php echo htmlspecialchars($err);?></div><?php endif;?>
    <?php if($success):?><div class="alert alert-success d-flex align-items-center py-2 mb-3" style="border-radius:10px;font-size:0.85rem;"><i class="fa-solid fa-circle-check me-2"></i><?php echo htmlspecialchars($success);?></div><?php endif;?>

    <div class="card-modern mb-3">
        <div class="card-body-modern">
            <form method="get" class="row g-2 align-items-end">
                <div class="col-md-4">
                    <label class="form-label-mod">Payroll Period</label>
                    <select name="period_id" class="form-select" style="border:1.5px solid var(--border);border-radius:10px;padding:10px 14px;background:var(--card);color:var(--text);" onchange="this.form.submit()">
                        <option value="">Select Period</option>
                        <?php foreach ($periods as $p):
                            $name = date('F Y', mktime(0,0,0,$p['month'],1,$p['year']));
                        ?>
                        <option value="<?php echo $p['id']; ?>" <?php echo $periodId==$p['id']?'selected':''; ?>><?php echo $name; ?> (<?php echo ucfirst($p['status']); ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php if ($periodId && $currentPeriod): ?>
                <div class="col-md-8 text-md-end d-flex gap-2 justify-content-md-end align-items-center">
                    <?php if ($currentPeriod['status'] === 'open'): ?>
                    <a href="/HRSuite/process/process_payroll.php?period_id=<?php echo $periodId; ?>" class="btn btn-primary-mod btn-sm-mod" onclick="return confirm('Process payroll for <?php echo date('F Y', mktime(0,0,0,$currentPeriod['month'],1,$currentPeriod['year'])); ?>? This will generate records for all active employees.');"><i class="fa-solid fa-gears me-1"></i>Process Payroll</a>
                    <?php endif; ?>
                    <a href="/HRSuite/api/admin/export.php?type=payroll&period_id=<?php echo $periodId; ?>&format=csv" class="btn btn-outline-mod btn-sm-mod"><i class="fa-solid fa-file-csv me-1"></i>CSV</a>
                    <a href="/HRSuite/api/admin/export.php?type=payroll&period_id=<?php echo $periodId; ?>&format=excel" class="btn btn-outline-mod btn-sm-mod"><i class="fa-solid fa-file-excel me-1"></i>Excel</a>
                </div>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <?php if ($periodId): ?>
    <div class="card-modern">
        <div class="card-body-modern" style="padding:0;">
            <div class="table-responsive">
                <table class="table-modern">
                    <thead><tr><th>Employee</th><th>Base</th><th>Bonus</th><th>Overtime</th><th>Allowances</th><th>Deductions</th><th>Tax</th><th>Net Pay</th><th>Status</th><th class="text-end">Actions</th></tr></thead>
                    <tbody>
                    <?php foreach ($records as $r):
                        $st = ($r['status']==='paid')?'status-active':(($r['status']==='generated')?'status-pending':'status-inactive');
                        $deductions = (float)$r['deductions'];
                    ?>
                    <tr>
                        <td><div class="d-flex align-items-center gap-2"><img src="<?php echo $r['avatar'] ? htmlspecialchars($r['avatar']) : 'https://ui-avatars.com/api/?name='.urlencode($r['first_name'].'+'.$r['last_name']).'&background=334155&color=cbd5e1&size=64'; ?>" style="width:32px;height:32px;border-radius:50%;object-fit:cover;"><div><div class="fw-semibold small"><?php echo htmlspecialchars($r['first_name'].' '.$r['last_name']); ?></div><div class="text-muted" style="font-size:0.7rem;"><?php echo htmlspecialchars($r['employee_code']); ?></div></div></div></td>
                        <td><?php echo format_currency($r['base_salary']); ?></td>
                        <td><?php echo format_currency($r['bonus']); ?></td>
                        <td><?php echo format_currency($r['overtime_pay']); ?></td>
                        <td><?php echo format_currency($r['allowances']); ?></td>
                        <td><?php echo format_currency($deductions); ?></td>
                        <td><?php echo format_currency($r['tax']); ?></td>
                        <td><strong><?php echo format_currency($r['net_pay']); ?></strong></td>
                        <td><span class="status-badge <?php echo $st; ?>"><?php echo ucfirst($r['status']); ?></span></td>
                        <td class="text-end">
                            <a href="/HRSuite/admin_dashboard/view_payslip.php?record_id=<?php echo $r['id']; ?>" class="btn btn-sm btn-outline-mod me-1" target="_blank"><i class="fa-solid fa-file-lines"></i> View</a>
                            <?php if ($r['status'] !== 'paid'): ?>
                            <a href="/HRSuite/admin_dashboard/make_payment.php?record_id=<?php echo $r['id']; ?>&period_id=<?php echo $periodId; ?>" class="btn btn-sm-mod btn-primary-mod"><i class="fa-solid fa-credit-card me-1"></i>Pay with Novac</a>
                            <?php else: ?>
                                <span class="text-muted small"><i class="fa-solid fa-check-circle me-1" style="color:var(--success);"></i>Paid</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; if(empty($records)): ?>
                    <tr><td colspan="10" class="text-center text-muted py-5">No payroll records for this period. Click "Process Payroll" to generate them.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <?php else: ?>
    <div class="card-modern p-5 text-center text-muted">
        <i class="fa-solid fa-calendar-days mb-3" style="font-size:2rem;color:var(--text2);opacity:0.5;"></i>
        <p class="mb-0">Select a payroll period from the dropdown above, or <button type="button" class="btn btn-link p-0 text-primary" data-bs-toggle="modal" data-bs-target="#createPeriodModal">create a new one</button>.</p>
    </div>
    <?php endif; ?>
</div>

<!-- Create Period Modal -->
<div class="modal fade" id="createPeriodModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="border-radius:14px;border:1px solid var(--border);background:var(--card);">
      <div class="modal-header border-0"><h6 class="modal-title fw-bold" style="color:var(--text);">Create Payroll Period</h6><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div>
      <form action="/HRSuite/process/add_payroll_period.php" method="POST">
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label-mod">Month</label>
              <select name="month" class="form-select" style="border:1.5px solid var(--border);border-radius:10px;padding:10px 14px;background:var(--card);color:var(--text);" required>
                <?php for ($m=1;$m<=12;$m++): ?>
                <option value="<?php echo $m; ?>" <?php echo $m==date('n')?'selected':''; ?>><?php echo date('F', mktime(0,0,0,$m,1)); ?></option>
                <?php endfor; ?>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label-mod">Year</label>
              <select name="year" class="form-select" style="border:1.5px solid var(--border);border-radius:10px;padding:10px 14px;background:var(--card);color:var(--text);" required>
                <?php for ($y=date('Y');$y>=date('Y')-2;$y--): ?>
                <option value="<?php echo $y; ?>" <?php echo $y==date('Y')?'selected':''; ?>><?php echo $y; ?></option>
                <?php endfor; ?>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label-mod">Start Date</label>
              <input type="date" name="start_date" class="form-control form-control-mod" required>
            </div>
            <div class="col-md-6">
              <label class="form-label-mod">End Date</label>
              <input type="date" name="end_date" class="form-control form-control-mod" required>
            </div>
          </div>
        </div>
        <div class="modal-footer border-0">
          <button type="button" class="btn btn-outline-mod btn-sm-mod" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary-mod btn-sm-mod">Create Period</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
