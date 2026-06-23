<?php
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/session.php';
require_auth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['error'] = 'Invalid request.';
    header('Location: /HRSuite/user-dashboard/request_asset.php');
    exit;
}

$empId = get_employee_id($_SESSION['user_id']);
$assetType = trim($_POST['asset_type'] ?? '');
$description = trim($_POST['description'] ?? '');
$justification = trim($_POST['justification'] ?? '');
$urgency = $_POST['urgency'] ?? 'medium';

if (!$empId || empty($assetType) || empty($justification)) {
    $_SESSION['error'] = 'Asset type and justification are required.';
    header('Location: /HRSuite/user-dashboard/request_asset.php');
    exit;
}

$stmt = $pdo->prepare("INSERT INTO asset_requests (employee_id, asset_type, description, justification, urgency) VALUES (?, ?, ?, ?, ?)");
$stmt->execute([$empId, $assetType, $description, $justification, $urgency]);

$_SESSION['success'] = 'Asset request submitted. Awaiting HR approval.';
header('Location: /HRSuite/user-dashboard/request_asset.php');
exit;
