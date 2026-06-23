<?php
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/session.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['error'] = 'Invalid request.';
    header('Location: /HRSuite/admin_dashboard/faqs.php');
    exit;
}

$id = (int) ($_POST['faq_id'] ?? 0);
$question = trim($_POST['question'] ?? '');
$answer = trim($_POST['answer'] ?? '');
$category = trim($_POST['category'] ?? 'General');
$sort_order = (int) ($_POST['sort_order'] ?? 0);
$is_active = isset($_POST['is_active']) ? 1 : 0;

if (!$id || empty($question) || empty($answer)) {
    $_SESSION['error'] = 'All fields are required.';
    header('Location: /HRSuite/admin_dashboard/faqs.php');
    exit;
}

$stmt = $pdo->prepare("UPDATE faqs SET question = ?, answer = ?, category = ?, sort_order = ?, is_active = ? WHERE id = ?");
$stmt->execute([$question, $answer, $category, $sort_order, $is_active, $id]);

if (function_exists('log_activity')) {
    log_activity($_SESSION['user_id'], 'UPDATE', 'faqs', $id, 'FAQ updated: ' . $question);
}

$_SESSION['success'] = 'FAQ updated successfully.';
header('Location: /HRSuite/admin_dashboard/faqs.php');
exit;
