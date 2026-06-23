<?php
/**
 * Employee Attendance Clock API
 */

require_once __DIR__ . '/../../includes/session.php';
requireAuth();

header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? 'today';

try {
    $db = $pdo;
    $employee = getCurrentEmployee();
    
    if (!$employee) {
        echo json_encode(['success' => false, 'message' => 'Employee record not found']);
        exit;
    }
    
    $empId = $employee['id'];
    $today = date('Y-m-d');
    
    switch ($method) {
        case 'GET':
            if ($action === 'today') {
                $stmt = $db->prepare("SELECT * FROM attendance WHERE employee_id = ? AND date = ?");
                $stmt->execute([$empId, $today]);
                $record = $stmt->fetch();
                
                // Calculate current hours if clocked in
                $currentHours = 0;
                if ($record && $record['clock_in'] && !$record['clock_out']) {
                    $in = new DateTime($record['clock_in']);
                    $now = new DateTime();
                    $breakDuration = 0;
                    if ($record['break_start'] && $record['break_end']) {
                        $bs = new DateTime($record['break_start']);
                        $be = new DateTime($record['break_end']);
                        $breakDuration = $bs->diff($be)->h * 3600 + $bs->diff($be)->i * 60;
                    }
                    $currentSeconds = (($now->getTimestamp() - $in->getTimestamp()) - $breakDuration);
                    $currentHours = round($currentSeconds / 3600, 2);
                }
                
                echo json_encode([
                    'success' => true,
                    'record' => $record,
                    'current_hours' => $currentHours,
                    'today' => $today
                ]);
                
            } elseif ($action === 'history') {
                $start = $_GET['start'] ?? date('Y-m-01');
                $end = $_GET['end'] ?? date('Y-m-t');
                
                $stmt = $db->prepare("
                    SELECT * FROM attendance 
                    WHERE employee_id = ? AND date BETWEEN ? AND ?
                    ORDER BY date DESC
                ");
                $stmt->execute([$empId, $start, $end]);
                
                echo json_encode(['success' => true, 'data' => $stmt->fetchAll()]);
            }
            break;
            
        case 'POST':
            if ($action === 'clock_in') {
                $data = json_decode(file_get_contents('php://input'), true) ?? $_POST;
                $notes = $data['notes'] ?? '';
                $location = $data['location'] ?? '';
                
                $stmt = $db->prepare("
                    INSERT INTO attendance (employee_id, date, clock_in, status, notes)
                    VALUES (?, ?, CURTIME(), 'present', ?)
                    ON DUPLICATE KEY UPDATE clock_in = COALESCE(clock_in, CURTIME()), status = 'present'
                ");
                $stmt->execute([$empId, $today, $notes]);
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Clock in recorded',
                    'time' => date('H:i:s')
                ]);
                
            } elseif ($action === 'clock_out') {
                $stmt = $db->prepare("
                    SELECT clock_in, break_start, break_end FROM attendance 
                    WHERE employee_id = ? AND date = ?
                ");
                $stmt->execute([$empId, $today]);
                $record = $stmt->fetch();
                
                if (!$record || !$record['clock_in']) {
                    echo json_encode(['success' => false, 'message' => 'No clock in record found']);
                    exit;
                }
                
                $in = new DateTime($record['clock_in']);
                $out = new DateTime();
                
                $breakSeconds = 0;
                if ($record['break_start'] && $record['break_end']) {
                    $bs = new DateTime($record['break_start']);
                    $be = new DateTime($record['break_end']);
                    $breakSeconds = $be->getTimestamp() - $bs->getTimestamp();
                }
                
                $totalSeconds = ($out->getTimestamp() - $in->getTimestamp()) - $breakSeconds;
                $hours = round($totalSeconds / 3600, 2);
                
                $status = $hours >= 8 ? 'present' : ($hours >= 4 ? 'half_day' : 'absent');
                
                $stmt = $db->prepare("
                    UPDATE attendance SET clock_out = CURTIME(), total_hours = ?, status = ?
                    WHERE employee_id = ? AND date = ?
                ");
                $stmt->execute([$hours, $status, $empId, $today]);
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Clock out recorded',
                    'time' => date('H:i:s'),
                    'total_hours' => $hours
                ]);
                
            } elseif ($action === 'break_start') {
                $stmt = $db->prepare("
                    UPDATE attendance SET break_start = CURTIME(), status = 'present'
                    WHERE employee_id = ? AND date = ?
                ");
                $stmt->execute([$empId, $today]);
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Break started',
                    'time' => date('H:i:s')
                ]);
                
            } elseif ($action === 'break_end') {
                $stmt = $db->prepare("
                    UPDATE attendance SET break_end = CURTIME()
                    WHERE employee_id = ? AND date = ?
                ");
                $stmt->execute([$empId, $today]);
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Break ended',
                    'time' => date('H:i:s')
                ]);
                
            } elseif ($action === 'request_correction') {
                $data = json_decode(file_get_contents('php://input'), true) ?? $_POST;
                $date = $data['date'] ?? '';
                $reason = $data['reason'] ?? '';
                
                if (!$date || !$reason) {
                    echo json_encode(['success' => false, 'message' => 'Date and reason required']);
                    exit;
                }
                
                // Create support ticket instead
                $stmt = $db->prepare("
                    INSERT INTO support_tickets (employee_id, category, subject, description, priority)
                    VALUES (?, 'Attendance', 'Attendance Correction Request', ?, 'medium')
                ");
                $stmt->execute([$empId, "Date: $date - Reason: $reason"]);
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Correction request submitted to HR'
                ]);
            }
            break;
    }
    
} catch (Exception $e) {
    error_log("Attendance API Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
