<?php
require_once __DIR__ . '/../config/session.php';
require_admin();
require_once __DIR__ . '/../config/database.php';
$id = (int)($_GET['id'] ?? 0);
if ($id) {
    $pdo->prepare("DELETE FROM job_postings WHERE id=?")->execute([$id]);
    log_activity($_SESSION['user_id'], 'DELETE', 'Job Posting', $id, 'Deleted job posting');
    $_SESSION['success'] = 'Job posting deleted.';
}
header('Location: /HRSuite/admin_dashboard/job_postings.php');
exit;
