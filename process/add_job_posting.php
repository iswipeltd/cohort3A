<?php
require_once __DIR__ . '/../config/session.php';
require_admin();
require_once __DIR__ . '/../config/database.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $department_id = (int)($_POST['department_id'] ?? 0);
    $description = trim($_POST['description'] ?? '');
    $requirements = trim($_POST['requirements'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $type = $_POST['type'] ?? 'full-time';
    if ($title) {
        $stmt = $pdo->prepare("INSERT INTO job_postings (title, department_id, description, requirements, location, type, status, posted_by) VALUES (?, ?, ?, ?, ?, ?, 'open', ?)");
        $stmt->execute([$title, $department_id ?: null, $description, $requirements, $location, $type, $_SESSION['user_id']]);
        log_activity($_SESSION['user_id'], 'CREATE', 'Job Posting', $pdo->lastInsertId(), 'Posted job: ' . $title);
        $_SESSION['success'] = 'Job posting created.';
    }
    header('Location: /HRSuite/admin_dashboard/job_postings.php');
    exit;
}
