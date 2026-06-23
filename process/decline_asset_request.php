<?php
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/session.php';
require_admin();

$id = (int) ($_GET['id'] ?? 0);
if (!$id) {
    $_SESSION['error'] = 'Invalid request ID.';
    header('Location: /HRSuite/admin_dashboard/asset_requests.php');
    exit;
}

$stmt = $pdo->prepare("UPDATE asset_requests SET status = 'rejected', processed_at = NOW(), processed_by = ? WHERE id = ? AND status = 'pending'");
$stmt->execute([$_SESSION['user_id'], $id]);

if ($stmt->rowCount()) {
    $_SESSION['success'] = 'Asset request declined.';
} else {
    $_SESSION['error'] = 'Request not found or already processed.';
}

header('Location: /HRSuite/admin_dashboard/asset_requests.php');
exit;
