<?php
require_once __DIR__ . '/../config/session.php';
require_admin();
$user = current_user();

$requests = $pdo->query("
    SELECT ar.*,
           e.id as emp_id, u.first_name, u.last_name, u.email, u.avatar,
           d.name as dept
    FROM asset_requests ar
    JOIN employees e ON ar.employee_id = e.id
    JOIN users u ON e.user_id = u.id
    LEFT JOIN departments d ON e.department_id = d.id
    ORDER BY ar.requested_at DESC
")->fetchAll();

$pending = count(array_filter($requests, fn($r) => $r['status'] == 'pending'));
$approved = count(array_filter($requests, fn($r) => in_array($r['status'], ['approved', 'fulfilled'])));
$rejected = count(array_filter($requests, fn($r) => $r['status'] == 'rejected'));

$err = $_SESSION['error'] ?? '';
$success = $_SESSION['success'] ?? '';
unset($_SESSION['error'], $_SESSION['success']);
?>
<?php include 'includes/head.php'; ?><body><?php include 'includes/sidebar.php'; ?><div class="main-content"><?php include 'includes/navbar.php'; ?>
<?php if($err):?><div class="alert alert-danger d-flex align-items-center py-2 mb-3 mx-4" style="border-radius:10px;font-size:0.85rem;"><i class="fa-solid fa-circle-exclamation me-2"></i><?php echo htmlspecialchars($err);?></div><?php endif;?>
<?php if($success):?><div class="alert alert-success d-flex align-items-center py-2 mb-3 mx-4" style="border-radius:10px;font-size:0.85rem;"><i class="fa-solid fa-circle-check me-2"></i><?php echo htmlspecialchars($success);?></div><?php endif;?>

<div class="page-header"><div><h1 class="page-title">Asset Requests</h1><p class="page-subtitle mb-0">Review and approve employee equipment requests</p></div></div>
<div class="px-4 pb-4">
<div class="row g-3 mb-4">
<div class="col-md-4"><div class="card-modern"><div class="card-body-modern"><div style="color:var(--muted);font-size:0.75rem;font-weight:600;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:4px;">Pending</div><div class="fw-bold" style="font-size:1.6rem;color:var(--warning);"><?php echo $pending; ?></div></div></div></div>
<div class="col-md-4"><div class="card-modern"><div class="card-body-modern"><div style="color:var(--muted);font-size:0.75rem;font-weight:600;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:4px;">Approved / Fulfilled</div><div class="fw-bold" style="font-size:1.6rem;color:var(--success);"><?php echo $approved; ?></div></div></div></div>
<div class="col-md-4"><div class="card-modern"><div class="card-body-modern"><div style="color:var(--muted);font-size:0.75rem;font-weight:600;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:4px;">Rejected</div><div class="fw-bold" style="font-size:1.6rem;color:var(--danger);"><?php echo $rejected; ?></div></div></div></div>
</div>

<div class="card-modern" style="padding:0;">
<table class="table-modern" style="width:100%;">
<thead>
<tr><th>Employee</th><th>Asset Type</th><th>Description</th><th>Justification</th><th>Urgency</th><th>Status</th><th>Date</th><th style="width:130px;">Action</th></tr>
</thead>
<tbody>
<?php foreach($requests as $r):
$urgColor = match($r['urgency']){'high'=>'#ef4444','medium'=>'#f59e0b',default=>'#94a3b8'};
$stClass = match($r['status']){'approved'=>'status-processing','fulfilled'=>'status-active','rejected'=>'status-rejected',default=>'status-pending'};
?>
<tr>
<td>
<div class="d-flex align-items-center gap-2">
<img src="<?php echo $r['avatar'] ? htmlspecialchars($r['avatar']) : 'https://ui-avatars.com/api/?name='.urlencode($r['first_name'].'+'.$r['last_name']).'&background=6366f1&color=fff&size=80'; ?>" style="width:32px;height:32px;border-radius:8px;object-fit:cover;" alt="">
<div>
<div class="fw-semibold" style="font-size:0.85rem;color:var(--text);"><?php echo htmlspecialchars($r['first_name'].' '.$r['last_name']); ?></div>
<div style="font-size:0.72rem;color:var(--muted);"><?php echo htmlspecialchars($r['dept'] ?? 'N/A'); ?></div>
</div>
</div>
</td>
<td class="fw-semibold" style="font-size:0.85rem;"><?php echo htmlspecialchars($r['asset_type']); ?></td>
<td style="font-size:0.8rem;color:var(--text2);"><?php echo htmlspecialchars($r['description'] ?? '-'); ?></td>
<td style="font-size:0.8rem;color:var(--text2);"><?php echo htmlspecialchars($r['justification'] ?? '-'); ?></td>
<td><span style="color:<?php echo $urgColor; ?>;font-weight:700;font-size:0.75rem;background:rgba(0,0,0,0.2);padding:3px 10px;border-radius:6px;"><?php echo ucfirst($r['urgency']); ?></span></td>
<td><span class="status-badge <?php echo $stClass; ?>"><?php echo ucfirst($r['status']); ?></span></td>
<td style="white-space:nowrap;font-size:0.78rem;color:var(--muted);"><?php echo date('M j, Y', strtotime($r['requested_at'])); ?></td>
<td>
<?php if($r['status']=='pending'): ?>
<a href="/HRSuite/process/approve_asset_request.php?id=<?php echo $r['id']; ?>" class="btn btn-sm-mod btn-success-mod me-1" title="Approve"><i class="fa-solid fa-check" style="font-size:0.7rem;"></i></a>
<a href="/HRSuite/process/decline_asset_request.php?id=<?php echo $r['id']; ?>" class="btn btn-sm-mod btn-danger-mod" title="Decline" onclick="return confirm('Decline this asset request?')"><i class="fa-solid fa-xmark" style="font-size:0.7rem;"></i></a>
<?php elseif($r['status']=='rejected'): ?>
<span style="font-size:0.75rem;color:var(--muted);">Declined</span>
<?php else: ?>
<span style="font-size:0.75rem;color:var(--muted);">Processed</span>
<?php endif; ?>
</td>
</tr>
<?php endforeach; if(empty($requests)): ?>
<tr><td colspan="8" style="text-align:center;color:var(--muted);padding:40px;">No asset requests found.</td></tr>
<?php endif; ?>
</tbody>
</table>
</div>
</div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body></html>
