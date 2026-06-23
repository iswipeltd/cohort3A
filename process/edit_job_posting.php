<?php
require_once __DIR__ . '/../config/session.php';
require_admin();
require_once __DIR__ . '/../config/database.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    $title = trim($_POST['title'] ?? '');
    $department_id = (int)($_POST['department_id'] ?? 0);
    $description = trim($_POST['description'] ?? '');
    $requirements = trim($_POST['requirements'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $type = $_POST['type'] ?? 'full-time';
    $status = $_POST['status'] ?? 'open';
    if ($title && $id) {
        $pdo->prepare("UPDATE job_postings SET title=?, department_id=?, description=?, requirements=?, location=?, type=?, status=? WHERE id=?")->execute([$title, $department_id ?: null, $description, $requirements, $location, $type, $status, $id]);
        log_activity($_SESSION['user_id'], 'UPDATE', 'Job Posting', $id, 'Updated job: ' . $title);
        $_SESSION['success'] = 'Job posting updated.';
    }
    header('Location: /HRSuite/admin_dashboard/job_postings.php');
    exit;
}
