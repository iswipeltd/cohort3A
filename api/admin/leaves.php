<?php
/**
 * Admin Leave Management API
 */

require_once __DIR__ . '/../../includes/session.php';
requireRole('admin');

header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? 'list';

try {
    $db = $pdo;
    
    switch ($method) {
        case 'GET':
            if ($action === 'list') {
                $page = max(1, intval($_GET['page'] ?? 1));
                $limit = max(10, min(100, intval($_GET['limit'] ?? 20)));
                $offset = ($page - 1) * $limit;
                $status = $_GET['status'] ?? '';
                $type = $_GET['type'] ?? '';
                
                $where = ['1=1'];
                $params = [];
                
                if ($status) {
                    $where[] = "lr.status = ?";
                    $params[] = $status;
                }
                if ($type) {
                    $where[] = "lr.leave_type_id = ?";
                    $params[] = $type;
                }
                
                $whereStr = implode(' AND ', $where);
                
                $countStmt = $db->prepare("SELECT COUNT(*) FROM leave_requests lr WHERE $whereStr");
                $countStmt->execute($params);
                $total = $countStmt->fetchColumn();
                
                $stmt = $db->prepare("
                    SELECT lr.*, 
                        CONCAT(e.first_name, ' ', e.last_name) as employee_name,
                        lt.name as leave_type_name,
                        CONCAT(a.first_name, ' ', a.last_name) as approved_by_name
                    FROM leave_requests lr
                    JOIN employees e ON lr.employee_id = e.id
                    JOIN leave_types lt ON lr.leave_type_id = lt.id
                    LEFT JOIN employees a ON lr.approved_by = a.id
                    WHERE $whereStr
                    ORDER BY lr.created_at DESC
                    LIMIT $limit OFFSET $offset
                ");
                $stmt->execute($params);
                $data = $stmt->fetchAll();
                
                echo json_encode([
                    'success' => true,
                    'data' => $data,
                    'pagination' => [
                        'page' => $page,
                        'limit' => $limit,
                        'total' => $total,
                        'total_pages' => ceil($total / $limit)
                    ]
                ]);
            } elseif ($action === 'types') {
                $stmt = $db->query("SELECT * FROM leave_types WHERE status = 'active'");
                echo json_encode(['success' => true, 'data' => $stmt->fetchAll()]);
            } elseif ($action === 'balances' && isset($_GET['employee_id'])) {
                $stmt = $db->prepare("
                    SELECT lb.*, lt.name as leave_type_name
                    FROM leave_balances lb
                    JOIN leave_types lt ON lb.leave_type_id = lt.id
                    WHERE lb.employee_id = ? AND lb.year = YEAR(CURDATE())
                ");
                $stmt->execute([$_GET['employee_id']]);
                echo json_encode(['success' => true, 'data' => $stmt->fetchAll()]);
            }
            break;
            
        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true) ?? $_POST;
            
            if ($action === 'approve') {
                $id = $data['id'] ?? 0;
                $employeeId = getCurrentEmployee()['id'] ?? null;
                
                $db->beginTransaction();
                
                // Update leave request
                $stmt = $db->prepare("
                    UPDATE leave_requests 
                    SET status = 'approved', approved_by = ?, approved_at = NOW()
                    WHERE id = ? AND status = 'pending'
                ");
                $stmt->execute([$employeeId, $id]);
                
                if ($stmt->rowCount() === 0) {
                    $db->rollBack();
                    echo json_encode(['success' => false, 'message' => 'Leave request not found or already processed']);
                    exit;
                }
                
                // Get request details to update balance
                $reqStmt = $db->prepare("SELECT employee_id, leave_type_id, total_days FROM leave_requests WHERE id = ?");
                $reqStmt->execute([$id]);
                $request = $reqStmt->fetch();
                
                // Update leave balance
                $balStmt = $db->prepare("
                    UPDATE leave_balances 
                    SET used_days = used_days + ?, 
                        pending_days = pending_days - ?
                    WHERE employee_id = ? AND leave_type_id = ? AND year = YEAR(CURDATE())
                ");
                $balStmt->execute([$request['total_days'], $request['total_days'], $request['employee_id'], $request['leave_type_id']]);
                
                $db->commit();
                
                // Create notification
                $notifStmt = $db->prepare("
                    INSERT INTO notifications (user_id, type, title, message)
                    SELECT user_id, 'leave', 'Leave Approved', CONCAT('Your ', ? , ' leave has been approved')
                    FROM employees WHERE id = ?
                ");
                $notifStmt->execute([$request['employee_id'], $request['employee_id']]);
                
                echo json_encode(['success' => true, 'message' => 'Leave approved successfully']);
                
            } elseif ($action === 'reject') {
                $id = $data['id'] ?? 0;
                $reason = $data['reason'] ?? 'No reason provided';
                $employeeId = getCurrentEmployee()['id'] ?? null;
                
                $db->beginTransaction();
                
                $stmt = $db->prepare("
                    UPDATE leave_requests 
                    SET status = 'rejected', approved_by = ?, approved_at = NOW(), rejection_reason = ?
                    WHERE id = ? AND status = 'pending'
                ");
                $stmt->execute([$employeeId, $reason, $id]);
                
                if ($stmt->rowCount() === 0) {
                    $db->rollBack();
                    echo json_encode(['success' => false, 'message' => 'Leave request not found or already processed']);
                    exit;
                }
                
                // Return pending days to balance
                $reqStmt = $db->prepare("SELECT employee_id, leave_type_id, total_days FROM leave_requests WHERE id = ?");
                $reqStmt->execute([$id]);
                $request = $reqStmt->fetch();
                
                $balStmt = $db->prepare("
                    UPDATE leave_balances 
                    SET pending_days = pending_days - ?
                    WHERE employee_id = ? AND leave_type_id = ? AND year = YEAR(CURDATE())
                ");
                $balStmt->execute([$request['total_days'], $request['employee_id'], $request['leave_type_id']]);
                
                $db->commit();
                
                echo json_encode(['success' => true, 'message' => 'Leave rejected']);
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
