<?php
require_once __DIR__ . '/../config/session.php';
require_auth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /HRSuite/user-dashboard/clock_in_out.php');
    exit;
}

$employee_id = get_employee_id($_SESSION['user_id']);
if (!$employee_id) {
    $_SESSION['error'] = 'Employee record not found.';
    header('Location: /HRSuite/user-dashboard/clock_in_out.php');
    exit;
}

$action = $_POST['action'] ?? '';
$today = date('Y-m-d');
$now = date('H:i:s');

// Check if attendance record exists for today
$stmt = $pdo->prepare("SELECT id, clock_in, clock_out FROM attendance WHERE employee_id = ? AND record_date = ?");
$stmt->execute([$employee_id, $today]);
$record = $stmt->fetch();

if ($action === 'in' || $action === 'clock_in') {
    if ($record) {
        $_SESSION['error'] = 'You have already clocked in today.';
    } else {
        $status = strtotime($now) > strtotime('09:00:00') ? 'late' : 'present';
        $ins = $pdo->prepare("INSERT INTO attendance (employee_id, record_date, clock_in, status, device) VALUES (?, ?, ?, ?, 'web')");
        $ins->execute([$employee_id, $today, $now, $status]);
        log_activity($_SESSION['user_id'], 'CLOCK_IN', 'Attendance', null, "Clocked in at {$now}");
        $_SESSION['success'] = "Clocked in at {$now}.";
    }
} elseif ($action === 'out' || $action === 'clock_out') {
    if (!$record || !$record['clock_in']) {
        $_SESSION['error'] = 'You have not clocked in today.';
    } elseif ($record['clock_out']) {
        $_SESSION['error'] = 'You have already clocked out today.';
    } else {
        $clockIn = $record['clock_in'];
        $hours = round((strtotime($now) - strtotime($clockIn)) / 3600, 2);
        $overtime = $hours > 8 ? round($hours - 8, 2) : 0;
        $upd = $pdo->prepare("UPDATE attendance SET clock_out = ?, hours_worked = ?, overtime = ? WHERE id = ?");
        $upd->execute([$now, $hours, $overtime, $record['id']]);
        log_activity($_SESSION['user_id'], 'CLOCK_OUT', 'Attendance', null, "Clocked out at {$now}. Hours: {$hours}");
        $_SESSION['success'] = "Clocked out at {$now}. Total hours: {$hours}.";
    }
}

header('Location: /HRSuite/user-dashboard/clock_in_out.php');
exit;
