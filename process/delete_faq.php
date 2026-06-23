<?php
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/session.php';
require_admin();

$id = (int) ($_GET['id'] ?? 0);
if (!$id) {
    $_SESSION['error'] = 'Invalid FAQ ID.';
    header('Location: /HRSuite/admin_dashboard/faqs.php');
    exit;
}

$stmt = $pdo->prepare("DELETE FROM faqs WHERE id = ?");
$stmt->execute([$id]);

if (function_exists('log_activity')) {
    log_activity($_SESSION['user_id'], 'DELETE', 'faqs', $id, 'FAQ deleted');
}

$_SESSION['success'] = 'FAQ deleted successfully.';
header('Location: /HRSuite/admin_dashboard/faqs.php');
exit;
