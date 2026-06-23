<?php
require_once __DIR__ . '/../config/session.php';
require_auth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /HRSuite/user-dashboard/apply_leave.php');
    exit;
}

$employee_id = get_employee_id($_SESSION['user_id']);
if (!$employee_id) {
    $_SESSION['error'] = 'Employee record not found.';
    header('Location: /HRSuite/user-dashboard/apply_leave.php');
    exit;
}

$leave_type_id = (int) ($_POST['leave_type_id'] ?? 0);
$start_date = $_POST['start_date'] ?? '';
$end_date = $_POST['end_date'] ?? '';
$reason = trim($_POST['reason'] ?? '');

if (empty($leave_type_id) || empty($start_date) || empty($end_date)) {
    $_SESSION['error'] = 'Please fill all required fields.';
    header('Location: /HRSuite/user-dashboard/apply_leave.php');
    exit;
}

$days = (int) ((strtotime($end_date) - strtotime($start_date)) / 86400) + 1;

$stmt = $pdo->prepare("INSERT INTO leave_requests (employee_id, leave_type_id, start_date, end_date, days, reason, status) VALUES (?, ?, ?, ?, ?, ?, 'pending')");
$stmt->execute([$employee_id, $leave_type_id, $start_date, $end_date, $days, $reason]);

$request_id = $pdo->lastInsertId();
log_activity($_SESSION['user_id'], 'CREATE', 'Leave', $request_id, "Applied for leave from {$start_date} to {$end_date}");

// Notify HR/admins
$hrStmt = $pdo->query("SELECT id FROM users WHERE role IN ('admin','hr')");
while ($hr = $hrStmt->fetch()) {
    send_notification($hr['id'], 'leave_request', "New leave request from {$_SESSION['full_name']} ({$days} days)", '/HRSuite/admin_dashboard/leave_requests.php');
}

$_SESSION['success'] = 'Leave request submitted successfully.';
header('Location: /HRSuite/user-dashboard/leave_status.php');
exit;
