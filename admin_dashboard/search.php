<?php
require_once __DIR__ . '/../config/session.php';
require_auth();
$user = current_user();
$q = trim($_GET['q'] ?? '');
$results = [];

if ($q) {
    $like = '%' . $q . '%';

    // Search Employees
    $employees = $pdo->prepare("SELECT CONCAT(u.first_name,' ',u.last_name) as title, e.employee_code as description, 'Employee' as type, CONCAT('employee_view.php?id=',e.id) as link FROM employees e JOIN users u ON e.user_id = u.id WHERE u.first_name LIKE ? OR u.last_name LIKE ? OR e.employee_code LIKE ? LIMIT 10");
    $employees->execute([$like, $like, $like]);
    foreach ($employees->fetchAll() as $r) { $results[] = $r; }

    // Search Leave Requests
    $leaves = $pdo->prepare("SELECT CONCAT(u.first_name,' ',u.last_name) as title, lr.status as description, 'Leave' as type, 'leave_requests.php' as link FROM leave_requests lr JOIN employees e ON lr.employee_id = e.id JOIN users u ON e.user_id = u.id WHERE u.first_name LIKE ? OR u.last_name LIKE ? LIMIT 10");
    $leaves->execute([$like, $like]);
    foreach ($leaves->fetchAll() as $r) { $results[] = $r; }

    // Search Job Postings
    $jobs = $pdo->prepare("SELECT title, description, 'Job' as type, 'job_postings.php' as link FROM job_postings WHERE status = 'open' AND (title LIKE ? OR description LIKE ?) LIMIT 10");
    $jobs->execute([$like, $like]);
    foreach ($jobs->fetchAll() as $r) { $results[] = $r; }

    // Search Announcements
    $ann = $pdo->prepare("SELECT title, message as description, 'Announcement' as type, 'announcements.php' as link FROM announcements WHERE (title LIKE ? OR message LIKE ?) AND (expires_at IS NULL OR expires_at > NOW()) LIMIT 10");
    $ann->execute([$like, $like]);
    foreach ($ann->fetchAll() as $r) { $results[] = $r; }
}
?>
<?php include 'includes/head.php'; ?>
<body>
<?php include 'includes/sidebar.php'; ?>
<div class="main-content">
<?php include 'includes/navbar.php'; ?>

<div class="page-header">
<div>
<h1 class="page-title">Search Results</h1>
<p class="page-subtitle mb-0"><?php echo $q ? 'Results for "' . htmlspecialchars($q) . '"' : 'Enter a search term above'; ?></p>
</div>
</div>
<div class="px-4 pb-4">
<div class="card-modern">
<div class="card-body-modern" style="padding:0;">
<div class="table-responsive">
<table class="table" style="margin:0;">
<thead style="background:#f8fafc;">
<tr><th>Type</th><th>Title</th><th>Description</th><th>Action</th></tr>
</thead>
<tbody>
<?php foreach ($results as $r): ?>
<tr>
<td><span class="badge" style="background:#e0e7ff;color:#4338ca;font-size:0.72rem;"><?php echo htmlspecialchars($r['type']); ?></span></td>
<td class="fw-semibold small"><?php echo htmlspecialchars($r['title']); ?></td>
<td style="max-width:400px;" class="text-muted small text-truncate"><?php echo htmlspecialchars(strip_tags($r['description'])); ?></td>
<td><a href="<?php echo htmlspecialchars($r['link']); ?>" class="btn btn-sm" style="background:linear-gradient(135deg,var(--primary),var(--primary));color:#fff;border-radius:6px;font-size:0.75rem;font-weight:600;padding:4px 12px;">View</a></td>
</tr>
<?php endforeach; if (empty($results) && $q): ?>
<tr><td colspan="4" class="text-center text-muted py-5">No results found for "<?php echo htmlspecialchars($q); ?>"</td></tr>
<?php elseif (empty($results)): ?>
<tr><td colspan="4" class="text-center text-muted py-5">Type a keyword in the top search bar and press Enter</td></tr>
<?php endif; ?>
</tbody>
</table>
</div>
</div>
</div>
</div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
