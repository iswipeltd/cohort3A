<?php
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/session.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['error'] = 'Invalid request.';
    header('Location: /HRSuite/admin_dashboard/training_enrollments.php');
    exit;
}

$id = (int) ($_POST['id'] ?? 0);
$status = $_POST['status'] ?? '';
$progress = (int) ($_POST['progress_percent'] ?? 0);
$score = trim($_POST['score'] ?? '');
$completedAt = trim($_POST['completed_at'] ?? '');

$allowed = ['pending', 'enrolled', 'in_progress', 'completed', 'dropped'];
if (!$id || !in_array($status, $allowed)) {
    $_SESSION['error'] = 'Invalid enrollment or status.';
    header('Location: /HRSuite/admin_dashboard/training_enrollments.php');
    exit;
}

if ($progress < 0) $progress = 0;
if ($progress > 100) $progress = 100;

// Auto-complete if progress is 100
if ($progress == 100 && $status != 'completed') {
    $status = 'completed';
}

// Fetch existing record
$check = $pdo->prepare("SELECT started_at, completed_at FROM training_enrollments WHERE id = ?");
$check->execute([$id]);
$existing = $check->fetch();

if (!$existing) {
    $_SESSION['error'] = 'Enrollment not found.';
    header('Location: /HRSuite/admin_dashboard/training_enrollments.php');
    exit;
}

$params = [$status, $progress];
$sql = "UPDATE training_enrollments SET status = ?, progress_percent = ?";

if ($score !== '') {
    $sql .= ", score = ?";
    $params[] = (float) $score;
} else {
    $sql .= ", score = NULL";
}

// Handle completed date
if (!empty($completedAt)) {
    // Date input gives YYYY-MM-DD, append time for DATETIME column
    $sql .= ", completed_at = ?";
    $params[] = $completedAt . ' 00:00:00';
} elseif ($status === 'completed') {
    // Auto-set today if status is completed and no date provided
    $sql .= ", completed_at = NOW()";
} else {
    $sql .= ", completed_at = NULL";
}

// started_at logic
if (in_array($status, ['in_progress', 'completed']) && empty($existing['started_at'])) {
    $sql .= ", started_at = NOW()";
}

$sql .= " WHERE id = ?";
$params[] = $id;

$stmt = $pdo->prepare($sql);
$stmt->execute($params);

$_SESSION['success'] = 'Enrollment updated successfully.';
header('Location: /HRSuite/admin_dashboard/training_enrollments.php');
exit;
