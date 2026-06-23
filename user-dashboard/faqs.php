<?php
require_once __DIR__ . '/../config/session.php';
require_auth();
$user = current_user();

// Fetch active FAQs grouped by category
$faqs = $pdo->query("
    SELECT * FROM faqs WHERE is_active = 1 ORDER BY category, sort_order, created_at DESC
")->fetchAll();

$categories = [];
foreach ($faqs as $f) {
    $cat = $f['category'] ?: 'General';
    $categories[$cat][] = $f;
}
?>
<?php include 'includes/head.php'; ?><body><?php include 'includes/sidebar.php'; ?><div class="main-content"><?php include 'includes/navbar.php'; ?>
<?php $err=$_SESSION['error']??''; $success=$_SESSION['success']??''; unset($_SESSION['error'],$_SESSION['success']); ?>
<?php if($err):?><div class="alert alert-danger d-flex align-items-center py-2 mb-3" style="border-radius:10px;font-size:0.85rem;"><i class="fa-solid fa-circle-exclamation me-2"></i><?php echo htmlspecialchars($err);?></div><?php endif;?>
<?php if($success):?><div class="alert alert-success d-flex align-items-center py-2 mb-3" style="border-radius:10px;font-size:0.85rem;"><i class="fa-solid fa-circle-check me-2"></i><?php echo htmlspecialchars($success);?></div><?php endif;?>

<div class="page-header"><div><h1 class="page-title">FAQs</h1><p class="page-subtitle mb-0">Frequently asked questions and answers</p></div></div>
<div class="px-4 pb-4">
<div class="row g-3">
<div class="col-lg-3">
<div class="card-modern" style="position:sticky;top:80px;">
<div class="card-body-modern">
<h5 class="fw-bold mb-3" style="font-size:0.9rem;color:var(--text);"><i class="fa-solid fa-folder-open me-2" style="color:var(--primary);"></i>Categories</h5>
<div class="d-flex flex-column gap-1">
<?php $idx = 0; foreach ($categories as $cat => $items): ?>
<a href="#cat-<?php echo $idx; ?>" style="color:var(--text2);text-decoration:none;font-size:0.85rem;font-weight:600;padding:8px 12px;border-radius:8px;transition:0.15s;" onmouseover="this.style.background='var(--primary-glow)';this.style.color='var(--primary-light)';" onmouseout="this.style.background='transparent';this.style.color='var(--text2)';">
<i class="fa-solid fa-tag me-2" style="color:var(--accent);"></i><?php echo htmlspecialchars($cat); ?> <span style="color:var(--muted);font-size:0.75rem;">(<?php echo count($items); ?>)</span>
</a>
<?php $idx++; endforeach; if(empty($categories)): ?>
<div style="color:var(--muted);font-size:0.8rem;">No categories available.</div>
<?php endif; ?>
</div>
</div>
</div>
</div>
<div class="col-lg-9">
<?php if(empty($categories)): ?>
<div class="card-modern"><div class="card-body-modern text-center py-5"><i class="fa-solid fa-circle-question mb-3" style="font-size:2rem;color:var(--muted);"></i><h5 style="color:var(--text);">No FAQs yet</h5><p style="color:var(--muted);font-size:0.85rem;">Check back later or contact HR for assistance.</p></div></div>
<?php endif; ?>
<?php $idx = 0; foreach ($categories as $cat => $items): ?>
<div class="card-modern mb-3" id="cat-<?php echo $idx; ?>">
<div class="card-header-modern" style="border-bottom:1px solid var(--border);">
<h5 class="fw-bold mb-0" style="font-size:0.95rem;color:var(--text);"><i class="fa-solid fa-layer-group me-2" style="color:var(--primary);"></i><?php echo htmlspecialchars($cat); ?></h5>
</div>
<div class="card-body-modern" style="padding:0;">
<div class="accordion" id="accordion-<?php echo $idx; ?>">
<?php foreach ($items as $i => $f): ?>
<div class="accordion-item" style="background:transparent;border:none;border-bottom:1px solid var(--border);">
<h2 class="accordion-header">
<button class="accordion-button <?php echo $i > 0 ? 'collapsed' : ''; ?>" type="button" data-bs-toggle="collapse" data-bs-target="#faq-<?php echo $f['id']; ?>" aria-expanded="<?php echo $i === 0 ? 'true' : 'false'; ?>" style="background:transparent;color:var(--text);font-weight:600;font-size:0.85rem;padding:16px 20px;box-shadow:none;">
<i class="fa-solid fa-circle-question me-2" style="color:var(--primary);font-size:0.8rem;"></i><?php echo htmlspecialchars($f['question']); ?>
</button>
</h2>
<div id="faq-<?php echo $f['id']; ?>" class="accordion-collapse collapse <?php echo $i === 0 ? 'show' : ''; ?>" data-bs-parent="#accordion-<?php echo $idx; ?>">
<div class="accordion-body" style="color:var(--text2);font-size:0.85rem;padding:0 20px 16px 44px;line-height:1.6;">
<?php echo nl2br(htmlspecialchars($f['answer'])); ?>
</div>
</div>
</div>
<?php endforeach; ?>
</div>
</div>
</div>
<?php $idx++; endforeach; ?>
</div>
</div>
</div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body></html>
