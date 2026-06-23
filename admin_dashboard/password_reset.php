<?php
require_once __DIR__ . '/../config/session.php';
require_admin();
$user = current_user();
$data = $pdo->query("SELECT id, CONCAT(first_name,' ',last_name) as name, email, role, status, last_login FROM users ORDER BY created_at DESC")->fetchAll();
?>
<?php include 'includes/head.php'; ?><body><?php include 'includes/sidebar.php'; ?><div class="main-content"><?php include 'includes/navbar.php'; ?>
<?php $err=$_SESSION['error']??''; $success=$_SESSION['success']??''; unset($_SESSION['error'],$_SESSION['success']); ?>
<?php if($err):?><div class="alert alert-danger d-flex align-items-center py-2 mb-3" style="border-radius:10px;font-size:0.85rem;"><i class="fa-solid fa-circle-exclamation me-2"></i><?php echo htmlspecialchars($err);?></div><?php endif;?>
<?php if($success):?><div class="alert alert-success d-flex align-items-center py-2 mb-3" style="border-radius:10px;font-size:0.85rem;"><i class="fa-solid fa-circle-check me-2"></i><?php echo htmlspecialchars($success);?></div><?php endif;?>

<div class="page-header"><div><h1 class="page-title">Password Reset</h1><p class="page-subtitle mb-0">Manage user password resets</p></div></div>
<div class="px-4 pb-4">
<div class="card-modern"><div class="card-body-modern" style="padding:0;"><div class="table-responsive"><table class="table-modern" style="margin:0;"><thead style="background:#0f172a;"><tr><th>User</th><th>Email</th><th>Role</th><th>Status</th><th>Last Login</th><th style="width:120px;">Action</th></tr></thead><tbody>
<?php foreach($data as $row): ?>
<tr><td class="fw-semibold small"><?php echo htmlspecialchars($row['name']); ?></td><td><?php echo htmlspecialchars($row['email']); ?></td><td><?php echo ucfirst($row['role']); ?></td><td><span class="status-badge <?php echo $row['status']=='active'?'status-active':'status-pending'; ?>"><?php echo ucfirst($row['status']); ?></span></td><td><?php echo $row['last_login'] ? date('M j, Y', strtotime($row['last_login'])) : 'Never'; ?></td><td><a href="/HRSuite/process/admin_reset_password.php?user_id=<?php echo $row['id']; ?>" class="btn btn-sm" style="background:rgba(99,102,241,0.15);color:#818cf8;border-radius:6px;font-size:0.75rem;" onclick="return confirm('Reset password for this user?')"><i class="fa-solid fa-key me-1"></i>Reset</a></td></tr>
<?php endforeach; if(empty($data)): ?><tr><td colspan="6" class="text-center text-muted py-5">No users</td></tr><?php endif; ?>
</tbody></table></div></div></div>
</div></div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script></body></html>
