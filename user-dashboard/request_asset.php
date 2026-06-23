<?php
require_once __DIR__ . '/../config/session.php';
require_auth();
$user = current_user();
$empId = get_employee_id($_SESSION['user_id']);

// Fetch existing asset requests
$requests = [];
if ($empId) {
    $stmt = $pdo->prepare("SELECT * FROM asset_requests WHERE employee_id = ? ORDER BY requested_at DESC");
    $stmt->execute([$empId]);
    $requests = $stmt->fetchAll();
}
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
<h1 class="page-title">Request Asset</h1>
<p class="page-subtitle mb-0">Request equipment from HR</p>
</div>
</div>
<div class="px-4 pb-4">
<div class="row g-3">
<div class="col-lg-5">
<div class="card-modern">
<div class="card-body-modern">
<h5 class="fw-bold mb-3" style="font-size:0.95rem;color:var(--text);"><i class="fa-solid fa-plus-circle me-2" style="color:var(--primary);"></i>New Asset Request</h5>
<form action="/HRSuite/process/request_asset.php" method="POST">
<div class="mb-3">
<label class="form-label-mod">Asset Type <span style="color:var(--danger);">*</span></label>
<select name="asset_type" class="form-select form-control-mod" required>
<option value="">-- Select Type --</option>
<option value="Laptop">Laptop</option>
<option value="Monitor">Monitor</option>
<option value="Phone">Phone</option>
<option value="Tablet">Tablet</option>
<option value="Headset">Headset</option>
<option value="Keyboard">Keyboard</option>
<option value="Mouse">Mouse</option>
<option value="Webcam">Webcam</option>
<option value="Printer">Printer</option>
<option value="Other">Other</option>
</select>
</div>
<div class="mb-3">
<label class="form-label-mod">Description</label>
<input type="text" name="description" class="form-control form-control-mod" placeholder="e.g. Dell XPS 15, 16GB RAM">
</div>
<div class="mb-3">
<label class="form-label-mod">Justification <span style="color:var(--danger);">*</span></label>
<textarea name="justification" class="form-control form-control-mod" rows="3" placeholder="Why do you need this asset?" required></textarea>
</div>
<div class="mb-3">
<label class="form-label-mod">Urgency</label>
<select name="urgency" class="form-select form-control-mod">
<option value="low">Low</option>
<option value="medium" selected>Medium</option>
<option value="high">High</option>
</select>
</div>
<button type="submit" class="btn btn-primary-mod w-100"><i class="fa-solid fa-paper-plane me-2"></i>Submit Request</button>
</form>
</div>
</div>
</div>
<div class="col-lg-7">
<div class="card-modern" style="padding:0;">
<table class="table-modern" style="width:100%;">
<thead>
<tr><th>Type</th><th>Description</th><th>Urgency</th><th>Status</th><th>Date</th></tr>
</thead>
<tbody>
<?php foreach($requests as $r):
$urgColor = match($r['urgency']){'high'=>'#ef4444','medium'=>'#f59e0b',default=>'#94a3b8'};
$stClass = match($r['status']){'approved'=>'status-active','rejected'=>'status-rejected','fulfilled'=>'status-completed',default=>'status-pending'};
?>
<tr>
<td class="fw-semibold" style="font-size:0.85rem;"><?php echo htmlspecialchars($r['asset_type']); ?></td>
<td style="font-size:0.82rem;color:var(--text2);"><?php echo htmlspecialchars($r['description'] ?? '-'); ?></td>
<td><span style="color:<?php echo $urgColor; ?>;font-weight:700;font-size:0.75rem;background:rgba(0,0,0,0.2);padding:3px 10px;border-radius:6px;"><?php echo ucfirst($r['urgency']); ?></span></td>
<td><span class="status-badge <?php echo $stClass; ?>"><?php echo ucfirst($r['status']); ?></span></td>
<td style="white-space:nowrap;font-size:0.8rem;color:var(--muted);"><?php echo date('M j, Y', strtotime($r['requested_at'])); ?></td>
</tr>
<?php endforeach; if(empty($requests)): ?>
<tr><td colspan="5" style="text-align:center;color:var(--muted);padding:40px;">No asset requests yet. Submit one on the left.</td></tr>
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
