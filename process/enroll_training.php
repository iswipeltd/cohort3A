<?php
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/session.php';
require_auth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['error'] = 'Invalid request.';
    header('Location: /HRSuite/user-dashboard/enroll_courses.php');
    exit;
}

$programId = (int) ($_POST['program_id'] ?? 0);
$empId = get_employee_id($_SESSION['user_id']);

if (!$programId || !$empId) {
    $_SESSION['error'] = 'Invalid program or employee.';
    header('Location: /HRSuite/user-dashboard/enroll_courses.php');
    exit;
}

// Check if already enrolled
$check = $pdo->prepare("SELECT id FROM training_enrollments WHERE program_id = ? AND employee_id = ?");
$check->execute([$programId, $empId]);
if ($check->fetch()) {
    $_SESSION['error'] = 'You have already requested enrollment in this program.';
    header('Location: /HRSuite/user-dashboard/enroll_courses.php');
    exit;
}

$stmt = $pdo->prepare("INSERT INTO training_enrollments (program_id, employee_id, status) VALUES (?, ?, 'pending')");
$stmt->execute([$programId, $empId]);

$_SESSION['success'] = 'Enrollment request submitted. Awaiting admin approval.';
header('Location: /HRSuite/user-dashboard/enroll_courses.php');
exit;
