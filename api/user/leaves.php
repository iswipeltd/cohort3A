<?php
/**
 * Employee Leave Request API
 */

require_once __DIR__ . '/../../includes/session.php';
requireAuth();

header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? 'list';

try {
    $db = $pdo;
    $employee = getCurrentEmployee();
    
    if (!$employee) {
        echo json_encode(['success' => false, 'message' => 'Employee record not found']);
        exit;
    }
    
    $empId = $employee['id'];
    
    switch ($method) {
        case 'GET':
            if ($action === 'list') {
                $stmt = $db->prepare("
                    SELECT lr.*, lt.name as leave_type_name
                    FROM leave_requests lr
                    JOIN leave_types lt ON lr.leave_type_id = lt.id
                    WHERE lr.employee_id = ?
                    ORDER BY lr.created_at DESC
                ");
                $stmt->execute([$empId]);
                echo json_encode(['success' => true, 'data' => $stmt->fetchAll()]);
                
            } elseif ($action === 'types') {
                $stmt = $db->query("SELECT * FROM leave_types WHERE status = 'active'");
                echo json_encode(['success' => true, 'data' => $stmt->fetchAll()]);
                
            } elseif ($action === 'balance') {
                $stmt = $db->prepare("
                    SELECT lb.*, lt.name as leave_type_name, lt.code
                    FROM leave_balances lb
                    JOIN leave_types lt ON lb.leave_type_id = lt.id
                    WHERE lb.employee_id = ? AND lb.year = YEAR(CURDATE())
                ");
                $stmt->execute([$empId]);
                echo json_encode(['success' => true, 'data' => $stmt->fetchAll()]);
            }
            break;
            
        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true) ?? $_POST;
            
            if ($action === 'apply') {
                $typeId = $data['leave_type_id'] ?? 0;
                $startDate = $data['start_date'] ?? '';
                $endDate = $data['end_date'] ?? '';
                $reason = $data['reason'] ?? '';
                
                if (!$typeId || !$startDate || !$endDate) {
                    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
                    exit;
                }
                
                $start = new DateTime($startDate);
                $end = new DateTime($endDate);
                $totalDays = $start->diff($end)->days + 1;
                
                // Check balance
                $balStmt = $db->prepare("
                    SELECT balance_days FROM leave_balances 
                    WHERE employee_id = ? AND leave_type_id = ? AND year = YEAR(CURDATE())
                ");
                $balStmt->execute([$empId, $typeId]);
                $balance = $balStmt->fetchColumn();
                
                if ($balance !== false && $balance < $totalDays) {
                    echo json_encode(['success' => false, 'message' => 'Insufficient leave balance']);
                    exit;
                }
                
                $db->beginTransaction();
                
                $stmt = $db->prepare("
                    INSERT INTO leave_requests (employee_id, leave_type_id, start_date, end_date, total_days, reason, status)
                    VALUES (?, ?, ?, ?, ?, ?, 'pending')
                ");
                $stmt->execute([$empId, $typeId, $startDate, $endDate, $totalDays, $reason]);
                
                $requestId = $db->lastInsertId();
                
                // Update balance - add to pending
                if ($balance !== false) {
                    $updStmt = $db->prepare("
                        UPDATE leave_balances 
                        SET pending_days = pending_days + ?
                        WHERE employee_id = ? AND leave_type_id = ? AND year = YEAR(CURDATE())
                    ");
                    $updStmt->execute([$totalDays, $empId, $typeId]);
                }
                
                $db->commit();
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Leave request submitted',
                    'request_id' => $requestId
                ]);
                
            } elseif ($action === 'cancel') {
                $reqId = $data['id'] ?? 0;
                
                $stmt = $db->prepare("
                    SELECT total_days, leave_type_id, status FROM leave_requests 
                    WHERE id = ? AND employee_id = ?
                ");
                $stmt->execute([$reqId, $empId]);
                $request = $stmt->fetch();
                
                if (!$request) {
                    echo json_encode(['success' => false, 'message' => 'Request not found']);
                    exit;
                }
                
                if ($request['status'] === 'approved') {
                    // Return used days
                    $db->beginTransaction();
                    $db->prepare("UPDATE leave_requests SET status = 'cancelled' WHERE id = ?")
                       ->execute([$reqId]);
                    $db->prepare("
                        UPDATE leave_balances SET used_days = used_days - ?, pending_days = pending_days + ?
                        WHERE employee_id = ? AND leave_type_id = ? AND year = YEAR(CURDATE())
                    ")->execute([$request['total_days'], $request['total_days'], $empId, $request['leave_type_id']]);
                    $db->commit();
                } else {
                    $db->prepare("UPDATE leave_requests SET status = 'cancelled' WHERE id = ?")
                       ->execute([$reqId]);
                    
                    // Return pending days
                    if ($request['status'] === 'pending') {
                        $db->prepare("
                            UPDATE leave_balances SET pending_days = pending_days - ?
                            WHERE employee_id = ? AND leave_type_id = ? AND year = YEAR(CURDATE())
                        ")->execute([$request['total_days'], $empId, $request['leave_type_id']]);
                    }
                }
                
                echo json_encode(['success' => true, 'message' => 'Leave request cancelled']);
            }
            break;
    }
    
} catch (Exception $e) {
    if (isset($db) && $db->inTransaction()) {
        $db->rollBack();
    }
    error_log("Leave API Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
