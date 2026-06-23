<?php
require_once __DIR__ . '/../config/session.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /HRSuite/admin_dashboard/leave_requests.php');
    exit;
}

$leave_id = (int) ($_POST['leave_id'] ?? 0);
$status = $_POST['status'] ?? '';
$reason = trim($_POST['reason'] ?? '');

if (empty($leave_id) || !in_array($status, ['approved','rejected'])) {
    $_SESSION['error'] = 'Invalid request.';
    header('Location: /HRSuite/admin_dashboard/leave_requests.php');
    exit;
}

$stmt = $pdo->prepare("UPDATE leave_requests SET status = ?, approved_by = ?, approved_at = NOW(), rejection_reason = ? WHERE id = ?");
$stmt->execute([$status, $_SESSION['user_id'], $reason, $leave_id]);

// Get employee user_id to send notification
$empStmt = $pdo->prepare("SELECT e.user_id, CONCAT(u.first_name, ' ', u.last_name) as emp_name, lr.days, lt.name as leave_type 
                          FROM leave_requests lr 
                          JOIN employees e ON lr.employee_id = e.id 
                          JOIN users u ON e.user_id = u.id 
                          JOIN leave_types lt ON lr.leave_type_id = lt.id 
                          WHERE lr.id = ?");
$empStmt->execute([$leave_id]);
$emp = $empStmt->fetch();

if ($emp) {
    $msg = "Your {$emp['leave_type']} request for {$emp['days']} days has been {$status}.";
    if ($status === 'rejected' && $reason) {
        $msg .= " Reason: {$reason}";
    }
    send_notification($emp['user_id'], 'leave_' . $status, $msg, '/HRSuite/user-dashboard/leave_status.php');
    
    // Send email notification
    require_once __DIR__ . '/../config/mail.php';
    $userStmt = $pdo->prepare("SELECT email, first_name, last_name FROM users WHERE id = ?");
    $userStmt->execute([$emp['user_id']]);
    $u = $userStmt->fetch();
    if ($u) {
        $template = ($status === 'approved') ? 'leave_approved' : 'leave_rejected';
        $leaveStmt = $pdo->prepare("SELECT start_date, end_date FROM leave_requests WHERE id = ?");
        $leaveStmt->execute([$leave_id]);
        $lr = $leaveStmt->fetch();
        sendEmail($u['email'], 'Leave Request ' . ucfirst($status), '', $template, [
            'name' => $u['first_name'] . ' ' . $u['last_name'],
            'leave_type' => $emp['leave_type'],
            'start_date' => date('M j, Y', strtotime($lr['start_date'])),
            'end_date' => date('M j, Y', strtotime($lr['end_date'])),
            'days' => $emp['days'],
            'reason' => $reason,
        ]);
    }
}

log_activity($_SESSION['user_id'], 'UPDATE', 'Leave', $leave_id, "Leave request {$status}");
$_SESSION['success'] = "Leave request {$status} successfully.";
header('Location: /HRSuite/admin_dashboard/leave_requests.php');
exit;
