<?php
require_once __DIR__ . '/../config/session.php';
require_admin();
require_once __DIR__ . '/../config/database.php';

$id = (int) ($_GET['id'] ?? 0);
if (!$id) {
    $_SESSION['error'] = 'Invalid FAQ ID.';
    header('Location: /HRSuite/admin_dashboard/faqs.php');
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM faqs WHERE id = ?");
$stmt->execute([$id]);
$f = $stmt->fetch();

if (!$f) {
    $_SESSION['error'] = 'FAQ not found.';
    header('Location: /HRSuite/admin_dashboard/faqs.php');
    exit;
}

$categories = $pdo->query("SELECT DISTINCT category FROM faqs ORDER BY category")->fetchAll(PDO::FETCH_COLUMN);
$err = $_SESSION['error'] ?? '';
$success = $_SESSION['success'] ?? '';
unset($_SESSION['error'], $_SESSION['success']);
?>
<?php include 'includes/head.php'; ?><body><?php include 'includes/sidebar.php'; ?><div class="main-content"><?php include 'includes/navbar.php'; ?>
<?php if($err):?><div class="alert alert-danger d-flex align-items-center py-2 mb-3 mx-4" style="border-radius:10px;font-size:0.85rem;"><i class="fa-solid fa-circle-exclamation me-2"></i><?php echo htmlspecialchars($err);?></div><?php endif;?>
<?php if($success):?><div class="alert alert-success d-flex align-items-center py-2 mb-3 mx-4" style="border-radius:10px;font-size:0.85rem;"><i class="fa-solid fa-circle-check me-2"></i><?php echo htmlspecialchars($success);?></div><?php endif;?>

<div class="page-header"><div><h1 class="page-title">Edit FAQ</h1><p class="page-subtitle mb-0">Update question and answer</p></div></div>
<div class="px-4 pb-4">
<div class="row g-3 justify-content-center">
<div class="col-lg-6">
<div class="card-modern">
<div class="card-body-modern">
<form action="/HRSuite/process/edit_faq.php" method="POST">
<input type="hidden" name="faq_id" value="<?php echo $f['id']; ?>">
<div class="mb-3">
<label class="form-label-mod">Question <span style="color:var(--danger);">*</span></label>
<input type="text" name="question" class="form-control form-control-mod" value="<?php echo htmlspecialchars($f['question']); ?>" required>
</div>
<div class="mb-3">
<label class="form-label-mod">Answer <span style="color:var(--danger);">*</span></label>
<textarea name="answer" class="form-control form-control-mod" rows="5" required><?php echo htmlspecialchars($f['answer']); ?></textarea>
</div>
<div class="row g-2 mb-3">
<div class="col-7">
<label class="form-label-mod">Category</label>
<input type="text" name="category" class="form-control form-control-mod" list="catList" value="<?php echo htmlspecialchars($f['category'] ?? 'General'); ?>">
<datalist id="catList">
<?php foreach($categories as $c): ?><option value="<?php echo htmlspecialchars($c); ?>"><?php endforeach; ?>
</datalist>
</div>
<div class="col-5">
<label class="form-label-mod">Sort Order</label>
<input type="number" name="sort_order" class="form-control form-control-mod" value="<?php echo $f['sort_order']; ?>" min="0">
</div>
</div>
<div class="mb-3">
<div class="form-check">
<input class="form-check-input" type="checkbox" name="is_active" value="1" id="activeCheck" <?php echo $f['is_active']?'checked':''; ?>>
<label class="form-check-label" for="activeCheck" style="color:var(--text2);font-size:0.85rem;">Active (visible to employees)</label>
</div>
</div>
<div class="d-flex gap-2">
<button type="submit" class="btn btn-primary-mod flex-fill"><i class="fa-solid fa-save me-2"></i>Save Changes</button>
<a href="/HRSuite/admin_dashboard/faqs.php" class="btn btn-outline-mod flex-fill">Cancel</a>
</div>
</form>
</div>
</div>
</div>
</div>
</div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body></html>
