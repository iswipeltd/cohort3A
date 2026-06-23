<?php
require_once __DIR__ . '/../config/session.php';
require_admin();
$user = current_user();

$logs = $pdo->query("SELECT l.*,CONCAT(u.first_name,' ',u.last_name) as actor FROM activity_logs l LEFT JOIN users u ON l.user_id=u.id ORDER BY l.created_at DESC LIMIT 100")->fetchAll();
?>
<?php include 'includes/head.php'; ?>
<body>
<?php include 'includes/sidebar.php'; ?>
<div class="main-content">
<?php include 'includes/navbar.php'; ?>
<?php $err=$_SESSION['error']??''; $success=$_SESSION['success']??''; unset($_SESSION['error'],$_SESSION['success']); ?>
<?php if($err):?><div class="alert alert-danger d-flex align-items-center py-2 mb-3" style="border-radius:10px;font-size:0.85rem;"><i class="fa-solid fa-circle-exclamation me-2"></i><?php echo htmlspecialchars($err);?></div><?php endif;?>
<?php if($success):?><div class="alert alert-success d-flex align-items-center py-2 mb-3" style="border-radius:10px;font-size:0.85rem;"><i class="fa-solid fa-circle-check me-2"></i><?php echo htmlspecialchars($success);?></div><?php endif;?>


<div class="page-header"><div><h1 class="page-title">Audit Logs</h1><p class="page-subtitle mb-0">System activity and changes</p></div></div>

<div class="px-4 pb-4">
    <div class="card-modern">
        <div class="card-body-modern" style="padding:0;">
            <div class="table-responsive">
                <table class="table-modern">
                    <thead><tr><th>Time</th><th>User</th><th>Action</th><th>Module</th><th>Details</th><th>IP</th></tr></thead>
                    <tbody>
                    <?php foreach ($logs as $log): ?>
                    <tr>
                        <td style="white-space:nowrap;"><?php echo date('Y-m-d H:i', strtotime($log['created_at'])); ?></td>
                        <td><?php echo htmlspecialchars($log['actor']??'System'); ?></td>
                        <td><span class="status-badge" style="background:rgba(99,102,241,0.12);color:#818cf8;"><?php echo htmlspecialchars($log['action']); ?></span></td>
                        <td><?php echo htmlspecialchars($log['module']); ?></td>
                        <td><?php echo htmlspecialchars($log['details']??'-'); ?></td>
                        <td><span class="text-muted small"><?php echo htmlspecialchars($log['ip_address']??'-'); ?></span></td>
                    </tr>
                    <?php endforeach; if(empty($logs)): ?>
                    <tr><td colspan="6" class="text-center text-muted py-5">No activity logs</td></tr>
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
