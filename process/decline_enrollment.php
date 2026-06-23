<?php
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/session.php';
require_admin();

$id = (int) ($_GET['id'] ?? 0);
if (!$id) {
    $_SESSION['error'] = 'Invalid enrollment ID.';
    header('Location: /HRSuite/admin_dashboard/training_enrollments.php');
    exit;
}

$stmt = $pdo->prepare("UPDATE training_enrollments SET status = 'dropped' WHERE id = ? AND status = 'pending'");
$stmt->execute([$id]);

if ($stmt->rowCount()) {
    $_SESSION['success'] = 'Enrollment declined.';
} else {
    $_SESSION['error'] = 'Enrollment not found or already processed.';
}

header('Location: /HRSuite/admin_dashboard/training_enrollments.php');
exit;
