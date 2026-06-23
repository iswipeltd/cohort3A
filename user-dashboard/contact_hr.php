<?php
require_once __DIR__ . '/../config/session.php';
require_auth();
$user = current_user();
$hrUsers = $pdo->query("SELECT id, CONCAT(first_name,' ',last_name) as name FROM users WHERE role IN ('admin','hr') AND status='active'")->fetchAll();
?>
<?php include 'includes/head.php'; ?><body><?php include 'includes/sidebar.php'; ?><div class="main-content"><?php include 'includes/navbar.php'; ?>
<?php $err=$_SESSION['error']??''; $success=$_SESSION['success']??''; unset($_SESSION['error'],$_SESSION['success']); ?>
<?php if($err):?><div class="alert alert-danger d-flex align-items-center py-2 mb-3" style="border-radius:10px;font-size:0.85rem;"><i class="fa-solid fa-circle-exclamation me-2"></i><?php echo htmlspecialchars($err);?></div><?php endif;?>
<?php if($success):?><div class="alert alert-success d-flex align-items-center py-2 mb-3" style="border-radius:10px;font-size:0.85rem;"><i class="fa-solid fa-circle-check me-2"></i><?php echo htmlspecialchars($success);?></div><?php endif;?>

<div class="page-header"><div><h1 class="page-title">Contact HR</h1><p class="page-subtitle mb-0">Send a message to HR</p></div></div>
<div class="px-4 pb-4">
<div class="card-modern"><div class="card-body-modern">
<form action="/HRSuite/process/send_message.php" method="POST">
<div class="mb-3">
<label class="form-label small fw-semibold">To</label>
<select name="receiver_id" class="form-select" required>
<?php foreach($hrUsers as $hru): ?>
<option value="<?php echo $hru['id']; ?>"><?php echo htmlspecialchars($hru['name']); ?></option>
<?php endforeach; ?>
</select>
</div>
<div class="mb-3">
<label class="form-label small fw-semibold">Subject</label>
<input type="text" name="subject" class="form-control" required>
</div>
<div class="mb-3">
<label class="form-label small fw-semibold">Message</label>
<textarea name="body" class="form-control" rows="5" required></textarea>
</div>
<button type="submit" class="btn btn-primary" style="background:#2563eb;border:none;border-radius:8px;"><i class="fa-solid fa-paper-plane me-2"></i>Send Message</button>
</form>
</div></div></div></div></div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script></body></html>
