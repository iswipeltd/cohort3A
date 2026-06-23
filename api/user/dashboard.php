<?php
/**
 * Employee Dashboard Data API
 */

require_once __DIR__ . '/../../includes/session.php';
requireAuth();

header('Content-Type: application/json');

try {
    $db = $pdo;
    $employee = getCurrentEmployee();
    
    if (!$employee) {
        echo json_encode(['success' => false, 'message' => 'Employee record not found']);
        exit;
    }
    
    $empId = $employee['id'];
    
    // Get today's attendance
    $today = date('Y-m-d');
    $attStmt = $db->prepare("SELECT * FROM attendance WHERE employee_id = ? AND date = ?");
    $attStmt->execute([$empId, $today]);
    $attendance = $attStmt->fetch();
    
    // Get leave balances
    $balStmt = $db->prepare("
        SELECT SUM(balance_days) as total_balance FROM leave_balances
        WHERE employee_id = ? AND year = YEAR(CURDATE())
    ");
    $balStmt->execute([$empId]);
    $leaveBalance = $balStmt->fetchColumn() ?? 0;
    
    // Get pending tasks count  
    $taskStmt = $db->prepare("
        SELECT COUNT(*) as pending FROM timesheets
        WHERE employee_id = ? AND status = 'draft'
    ");
    $taskStmt->execute([$empId]);
    $pendingTasks = $taskStmt->fetchColumn() ?? 0;
    
    // Get latest payslip
    $payStmt = $db->prepare("
        SELECT pp.pay_date, pp.name as period_name
        FROM payroll_entries pe
        JOIN payroll_periods pp ON pe.payroll_period_id = pp.id
        WHERE pe.employee_id = ? AND pe.status = 'paid'
        ORDER BY pp.pay_date DESC
        LIMIT 1
    ");
    $payStmt->execute([$empId]);
    $latestPayroll = $payStmt->fetch();
    
    // Get upcoming leave
    $leaveStmt = $db->prepare("
        SELECT lr.*, lt.name as leave_type_name
        FROM leave_requests lr
        JOIN leave_types lt ON lr.leave_type_id = lt.id
        WHERE lr.employee_id = ? AND lr.status = 'approved' AND lr.start_date >= CURDATE()
        ORDER BY lr.start_date ASC
        LIMIT 1
    ");
    $leaveStmt->execute([$empId]);
    $upcomingLeave = $leaveStmt->fetch();
    
    // Get notifications
    $notifStmt = $db->prepare("
        SELECT * FROM notifications
        WHERE user_id = ? AND is_read = 0
        ORDER BY created_at DESC
        LIMIT 5
    ");
    $notifStmt->execute([$_SESSION['user_id']]);
    $notifications = $notifStmt->fetchAll();
    
    // Get announcements
    $annStmt = $db->prepare("
        SELECT * FROM announcements
        WHERE status = 'active' AND (expires_at IS NULL OR expires_at >= CURDATE())
        AND (target_audience = 'all' OR target_ids IS NULL OR JSON_CONTAINS(target_ids, ?))
        ORDER BY posted_at DESC
        LIMIT 5
    ");
    $annStmt->execute([$empId]);
    $announcements = $annStmt->fetchAll();
    
    echo json_encode([
        'success' => true,
        'employee' => $employee,
        'attendance' => [
            'today' => $attendance,
            'is_clocked_in' => $attendance && $attendance['clock_in'] && !$attendance['clock_out'],
            'hours_today' => $attendance['total_hours'] ?? 0
        ],
        'leave' => [
            'balance' => $leaveBalance,
            'upcoming' => $upcomingLeave
        ],
        'tasks' => [
            'pending_count' => $pendingTasks
        ],
        'payroll' => [
            'next_pay_date' => $latestPayroll ? date('M d', strtotime($latestPayroll['pay_date'])) : 'N/A'
        ],
        'notifications' => $notifications,
        'announcements' => $announcements
    ]);
    
} catch (Exception $e) {
    error_log("Dashboard API Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
