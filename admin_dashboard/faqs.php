<?php
require_once __DIR__ . '/../config/session.php';
require_admin();
$user = current_user();

$faqs = $pdo->query("SELECT f.*, u.first_name, u.last_name FROM faqs f LEFT JOIN users u ON f.created_by = u.id ORDER BY f.sort_order ASC, f.created_at DESC")->fetchAll();
$categories = $pdo->query("SELECT DISTINCT category FROM faqs ORDER BY category")->fetchAll(PDO::FETCH_COLUMN);

$err = $_SESSION['error'] ?? '';
$success = $_SESSION['success'] ?? '';
unset($_SESSION['error'], $_SESSION['success']);
?>
<?php include 'includes/head.php'; ?><body><?php include 'includes/sidebar.php'; ?><div class="main-content"><?php include 'includes/navbar.php'; ?>
<?php if($err):?><div class="alert alert-danger d-flex align-items-center py-2 mb-3 mx-4" style="border-radius:10px;font-size:0.85rem;"><i class="fa-solid fa-circle-exclamation me-2"></i><?php echo htmlspecialchars($err);?></div><?php endif;?>
<?php if($success):?><div class="alert alert-success d-flex align-items-center py-2 mb-3 mx-4" style="border-radius:10px;font-size:0.85rem;"><i class="fa-solid fa-circle-check me-2"></i><?php echo htmlspecialchars($success);?></div><?php endif;?>

<div class="page-header"><div><h1 class="page-title">FAQs</h1><p class="page-subtitle mb-0">Manage frequently asked questions</p></div><a href="#faqForm" onclick="document.getElementById('faqForm').scrollIntoView({behavior:'smooth'});" class="btn btn-primary-mod"><i class="fa-solid fa-plus me-2"></i>Add FAQ</a></div>
<div class="px-4 pb-4">
<div class="row g-3">
<div class="col-lg-4">
<div class="card-modern" id="faqForm">
<div class="card-body-modern">
<h5 class="fw-bold mb-3" style="font-size:0.95rem;color:var(--text);"><i class="fa-solid fa-plus-circle me-2" style="color:var(--primary);"></i>New FAQ</h5>
<form action="/HRSuite/process/add_faq.php" method="POST">
<div class="mb-3">
<label class="form-label-mod">Question <span style="color:var(--danger);">*</span></label>
<input type="text" name="question" class="form-control form-control-mod" placeholder="e.g. How do I apply for leave?" required>
</div>
<div class="mb-3">
<label class="form-label-mod">Answer <span style="color:var(--danger);">*</span></label>
<textarea name="answer" class="form-control form-control-mod" rows="4" placeholder="Write the answer here..." required></textarea>
</div>
<div class="row g-2 mb-3">
<div class="col-7">
<label class="form-label-mod">Category</label>
<input type="text" name="category" class="form-control form-control-mod" list="catList" placeholder="e.g. Leave, Payroll, General">
<datalist id="catList">
<?php foreach($categories as $c): ?><option value="<?php echo htmlspecialchars($c); ?>"><?php endforeach; ?>
</datalist>
</div>
<div class="col-5">
<label class="form-label-mod">Sort Order</label>
<input type="number" name="sort_order" class="form-control form-control-mod" value="0" min="0">
</div>
</div>
<div class="mb-3">
<div class="form-check">
<input class="form-check-input" type="checkbox" name="is_active" value="1" id="activeCheck" checked>
<label class="form-check-label" for="activeCheck" style="color:var(--text2);font-size:0.85rem;">Active (visible to employees)</label>
</div>
</div>
<button type="submit" class="btn btn-primary-mod w-100"><i class="fa-solid fa-save me-2"></i>Save FAQ</button>
</form>
</div>
</div>
</div>
<div class="col-lg-8">
<div class="card-modern" style="padding:0;">
<table class="table-modern" style="width:100%;">
<thead>
<tr><th>Question</th><th>Category</th><th>Order</th><th>Status</th><th style="width:110px;">Action</th></tr>
</thead>
<tbody>
<?php foreach($faqs as $f):
$statusClass = $f['is_active'] ? 'status-active' : 'status-inactive';
$statusText = $f['is_active'] ? 'Active' : 'Inactive';
?>
<tr>
<td class="fw-semibold" style="font-size:0.85rem;"><?php echo htmlspecialchars($f['question']); ?></td>
<td><span style="font-size:0.75rem;color:var(--muted);background:var(--bg);padding:3px 10px;border-radius:6px;font-weight:600;"><?php echo htmlspecialchars($f['category'] ?? 'General'); ?></span></td>
<td><?php echo $f['sort_order']; ?></td>
<td><span class="status-badge <?php echo $statusClass; ?>"><?php echo $statusText; ?></span></td>
<td>
<a href="edit_faq.php?id=<?php echo $f['id']; ?>" class="btn btn-sm-mod btn-outline-mod me-1"><i class="fa-solid fa-pen" style="font-size:0.7rem;"></i></a>
<a href="/HRSuite/process/delete_faq.php?id=<?php echo $f['id']; ?>" onclick="return confirm('Delete this FAQ?')" class="btn btn-sm-mod btn-danger-mod"><i class="fa-solid fa-trash" style="font-size:0.7rem;"></i></a>
</td>
</tr>
<?php endforeach; if(empty($faqs)): ?>
<tr><td colspan="5" style="text-align:center;color:var(--muted);padding:40px;">No FAQs yet. Add one on the left.</td></tr>
<?php endif; ?>
</tbody>
</table>
</div>
</div>
</div>
</div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body></html>
