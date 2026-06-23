<?php
require_once __DIR__ . '/../config/session.php';
require_admin();
$user = current_user();

// Fetch messages sent TO this admin/HR user
$stmt = $pdo->prepare("
    SELECT m.*, CONCAT(u.first_name, ' ', u.last_name) as sender_name, u.email as sender_email
    FROM messages m
    JOIN users u ON m.from_user_id = u.id
    WHERE m.to_user_id = ?
    ORDER BY m.created_at DESC
");
$stmt->execute([$_SESSION['user_id']]);
$messages = $stmt->fetchAll();

// Mark all as read when viewing
$pdo->prepare("UPDATE messages SET read_at = NOW() WHERE to_user_id = ? AND read_at IS NULL")->execute([$_SESSION['user_id']]);

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
        <div><h1 class="page-title">Inbox</h1><p class="page-subtitle mb-0">Messages from employees</p></div>
    </div>
</div>

<div class="px-4 pb-4">
    <?php if($err):?><div class="alert alert-danger d-flex align-items-center py-2 mb-3" style="border-radius:10px;font-size:0.85rem;"><i class="fa-solid fa-circle-exclamation me-2"></i><?php echo htmlspecialchars($err);?></div><?php endif;?>
    <?php if($success):?><div class="alert alert-success d-flex align-items-center py-2 mb-3" style="border-radius:10px;font-size:0.85rem;"><i class="fa-solid fa-circle-check me-2"></i><?php echo htmlspecialchars($success);?></div><?php endif;?>

    <div class="card-modern">
        <div class="card-body-modern" style="padding:0;">
            <div class="table-responsive">
                <table class="table-modern" style="margin:0;">
                    <thead><tr><th>From</th><th>Subject</th><th>Message</th><th>Date</th><th>Status</th></tr></thead>
                    <tbody>
                    <?php foreach($messages as $m): ?>
                    <tr>
                        <td><div class="fw-semibold small"><?php echo htmlspecialchars($m['sender_name']); ?></div><div class="text-muted" style="font-size:0.7rem;"><?php echo htmlspecialchars($m['sender_email']); ?></div></td>
                        <td class="fw-semibold small"><?php echo htmlspecialchars($m['subject']); ?></td>
                        <td style="max-width:300px;"><div style="font-size:0.8rem;color:var(--muted);white-space:pre-wrap;"><?php echo nl2br(htmlspecialchars(substr($m['body'],0,200))); ?><?php echo strlen($m['body'])>200?'...':''; ?></div></td>
                        <td style="white-space:nowrap;font-size:0.75rem;color:var(--muted);"><?php echo date('M j, Y g:i A', strtotime($m['created_at'])); ?></td>
                        <td><?php if($m['read_at']): ?><span class="status-badge status-completed">Read</span><?php else: ?><span class="status-badge status-pending">Unread</span><?php endif; ?></td>
                    </tr>
                    <?php endforeach; if(empty($messages)): ?>
                    <tr><td colspan="5" class="text-center text-muted py-5">No messages yet</td></tr>
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
