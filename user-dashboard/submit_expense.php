<?php
require_once __DIR__ . '/../config/session.php';
require_auth();
$user = current_user();
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
<div>
<h1 class="page-title">Submit Expense</h1>
<p class="page-subtitle mb-0">Submit a new expense claim</p>
</div>
</div>
<div class="px-4 pb-4">
<div class="card-modern" style="max-width:600px;">
<div class="card-body-modern">
<?php if($err):?><div class="alert alert-danger py-2 mb-3" style="border-radius:10px;font-size:0.85rem;"><i class="fa-solid fa-circle-exclamation me-2"></i><?php echo htmlspecialchars($err);?></div><?php endif;?>
<?php if($success):?><div class="alert alert-success py-2 mb-3" style="border-radius:10px;font-size:0.85rem;"><i class="fa-solid fa-circle-check me-2"></i><?php echo htmlspecialchars($success);?></div><?php endif;?>
<form action="/HRSuite/process/submit_expense.php" method="POST" enctype="multipart/form-data">
<div class="mb-3">
<label class="form-label small fw-semibold">Category</label>
<select name="category" class="form-select" required>
<option value="">Select category</option>
<option value="Travel">Travel</option>
<option value="Meals">Meals</option>
<option value="Office">Office Supplies</option>
<option value="Training">Training</option>
<option value="Equipment">Equipment</option>
<option value="Other">Other</option>
</select>
</div>
<div class="mb-3">
<label class="form-label small fw-semibold">Amount (₦)</label>
<input type="number" name="amount" class="form-control" step="0.01" min="0.01" required>
</div>
<div class="mb-3">
<label class="form-label small fw-semibold">Description</label>
<textarea name="description" class="form-control" rows="4" required></textarea>
</div>
<div class="mb-3">
<label class="form-label small fw-semibold">Receipt (optional)</label>
<input type="file" name="receipt" class="form-control" accept="image/*,.pdf">
</div>
<button type="submit" class="btn btn-primary" style="background:linear-gradient(135deg,var(--primary),var(--primary));border:none;border-radius:8px;"><i class="fa-solid fa-paper-plane me-2"></i>Submit Expense</button>
</form>
</div>
</div>
</div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
