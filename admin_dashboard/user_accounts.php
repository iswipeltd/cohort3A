<?php
require_once __DIR__ . '/../config/session.php';
require_admin();
$user = current_user();
$data = $pdo->query("SELECT id, email, first_name, last_name, role, status, last_login, created_at FROM users ORDER BY created_at DESC")->fetchAll();
?>
<?php include 'includes/head.php'; ?><body><?php include 'includes/sidebar.php'; ?><div class="main-content"><?php include 'includes/navbar.php'; ?>
<div class="page-header"><div><h1 class="page-title">User Accounts</h1><p class="page-subtitle mb-0">Manage system users</p></div></div>
<div class="px-4 pb-4">
<?php $err = $_SESSION['error'] ?? ''; $success = $_SESSION['success'] ?? ''; unset($_SESSION['error'], $_SESSION['success']); ?>
    <?php if($err):?><div class="alert alert-danger d-flex align-items-center py-2 mb-3" style="border-radius:10px;font-size:0.85rem;"><i class="fa-solid fa-circle-exclamation me-2"></i><?php echo htmlspecialchars($err);?></div><?php endif;?>
    <?php if($success):?><div class="alert alert-success d-flex align-items-center py-2 mb-3" style="border-radius:10px;font-size:0.85rem;"><i class="fa-solid fa-circle-check me-2"></i><?php echo htmlspecialchars($success);?></div><?php endif;?>

<div class="card-modern"><div class="card-body-modern" style="padding:0;"><div class="table-responsive"><table class="table-modern" style="margin:0;"><thead style="background:#0f172a;"><tr><th>Name</th><th>Email</th><th>Role</th><th>Status</th><th>Last Login</th><th>Created</th></tr></thead><tbody>
<?php foreach($data as $row): $sc = match($row['status']){'active'=>'status-active','suspended'=>'status-pending',default=>'status-rejected'}; ?>
<tr><td class="fw-semibold small"><?php echo htmlspecialchars(($row['first_name']??'').' '.($row['last_name']??'')); ?></td><td><?php echo htmlspecialchars($row['email']); ?></td><td><span class="badge" style="background:rgba(99,102,241,0.12);color:#818cf8;font-size:0.72rem;"><?php echo ucfirst($row['role']); ?></span></td><td><span class="status-badge <?php echo $sc; ?>"><?php echo ucfirst($row['status']); ?></span></td><td><?php echo $row['last_login'] ? date('M j, g:i A', strtotime($row['last_login'])) : '-'; ?></td><td><?php echo date('M j, Y', strtotime($row['created_at'])); ?></td></tr>
<?php endforeach; if(empty($data)): ?><tr><td colspan="6" class="text-center text-muted py-5">No users</td></tr><?php endif; ?>
</tbody></table></div></div></div>
</div></div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script></body></html>
