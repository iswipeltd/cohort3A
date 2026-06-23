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

// Get request details
$req = $pdo->prepare("SELECT * FROM asset_requests WHERE id = ? AND status = 'pending'");
$req->execute([$id]);
$row = $req->fetch();

if (!$row) {
    $_SESSION['error'] = 'Request not found or already processed.';
    header('Location: /HRSuite/admin_dashboard/asset_requests.php');
    exit;
}

// Create asset record from request
$assetName = !empty($row['description']) ? $row['description'] : $row['asset_type'];
$assetCode = 'ASSET-' . str_pad($row['id'], 4, '0', STR_PAD_LEFT);

$insert = $pdo->prepare("INSERT INTO assets (asset_code, name, type, assigned_to, assigned_at, status, notes) VALUES (?, ?, ?, ?, NOW(), 'assigned', ?)");
$insert->execute([
    $assetCode,
    $assetName,
    $row['asset_type'],
    $row['employee_id'],
    "Auto-created from approved request. Justification: " . $row['justification']
]);

// Mark request as fulfilled
$upd = $pdo->prepare("UPDATE asset_requests SET status = 'fulfilled', processed_at = NOW(), processed_by = ? WHERE id = ?");
$upd->execute([$_SESSION['user_id'], $id]);

$_SESSION['success'] = 'Asset request approved and asset assigned to employee.';
header('Location: /HRSuite/admin_dashboard/asset_requests.php');
exit;
