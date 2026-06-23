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

$question = trim($_POST['question'] ?? '');
$answer = trim($_POST['answer'] ?? '');
$category = trim($_POST['category'] ?? 'General');
$sort_order = (int) ($_POST['sort_order'] ?? 0);
$is_active = isset($_POST['is_active']) ? 1 : 0;

if (empty($question) || empty($answer)) {
    $_SESSION['error'] = 'Question and answer are required.';
    header('Location: /HRSuite/admin_dashboard/faqs.php');
    exit;
}

$stmt = $pdo->prepare("INSERT INTO faqs (question, answer, category, sort_order, is_active, created_by) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->execute([$question, $answer, $category, $sort_order, $is_active, $_SESSION['user_id']]);

if (function_exists('log_activity')) {
    log_activity($_SESSION['user_id'], 'CREATE', 'faqs', $pdo->lastInsertId(), 'FAQ added: ' . $question);
}

$_SESSION['success'] = 'FAQ added successfully.';
header('Location: /HRSuite/admin_dashboard/faqs.php');
exit;
