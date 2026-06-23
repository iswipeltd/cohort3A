<?php
/**
 * Admin Attendance API
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
                $date = $_GET['date'] ?? date('Y-m-d');
                $department = $_GET['department'] ?? '';
                
                $sql = "
                    SELECT a.*, CONCAT(e.first_name, ' ', e.last_name) as employee_name, e.employee_id, d.name as department
                    FROM attendance a
                    JOIN employees e ON a.employee_id = e.id
                    LEFT JOIN departments d ON e.department_id = d.id
                    WHERE a.date = ?
                ";
                $params = [$date];
                
                if ($department) {
                    $sql .= " AND e.department_id = ?";
                    $params[] = $department;
                }
                
                $sql .= " ORDER BY e.first_name";
                
                $stmt = $db->prepare($sql);
                $stmt->execute($params);
                echo json_encode(['success' => true, 'date' => $date, 'data' => $stmt->fetchAll()]);
                
            } elseif ($action === 'timesheets') {
                $status = $_GET['status'] ?? '';
                $where = $status ? "WHERE status = ?" : "";
                $params = $status ? [$status] : [];
                
                $stmt = $db->prepare("
                    SELECT t.*, CONCAT(e.first_name, ' ', e.last_name) as employee_name
                    FROM timesheets t
                    JOIN employees e ON t.employee_id = e.id
                    $where
                    ORDER BY t.submitted_at DESC
                ");
                $stmt->execute($params);
                echo json_encode(['success' => true, 'data' => $stmt->fetchAll()]);
                
            } elseif ($action === 'summary') {
                $stmt = $db->query("
                    SELECT 
                        COUNT(*) as total,
                        SUM(CASE WHEN status = 'present' THEN 1 ELSE 0 END) as present,
                        SUM(CASE WHEN status = 'absent' THEN 1 ELSE 0 END) as absent,
                        SUM(CASE WHEN status = 'late' THEN 1 ELSE 0 END) as late,
                        SUM(CASE WHEN status = 'on_leave' THEN 1 ELSE 0 END) as on_leave
                    FROM attendance
                    WHERE date = CURDATE()
                ");
                echo json_encode(['success' => true, 'data' => $stmt->fetch()]);
            }
            break;
            
        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true) ?? $_POST;
            
            if ($action === 'timesheet_approve') {
                $id = $data['id'] ?? 0;
                $adminId = getCurrentEmployee()['id'] ?? null;
                
                $stmt = $db->prepare("
                    UPDATE timesheets SET status = 'approved', approved_by = ?, approved_at = NOW()
                    WHERE id = ? AND status = 'submitted'
                ");
                $stmt->execute([$adminId, $id]);
                
                echo json_encode([
                    'success' => $stmt->rowCount() > 0,
                    'message' => $stmt->rowCount() > 0 ? 'Timesheet approved' : 'Timesheet not found'
                ]);
                
            } elseif ($action === 'timesheet_reject') {
                $id = $data['id'] ?? 0;
                $reason = $data['reason'] ?? '';
                
                $stmt = $db->prepare("
                    UPDATE timesheets SET status = 'rejected', notes = ?
                    WHERE id = ? AND status = 'submitted'
                ");
                $stmt->execute([$reason, $id]);
                
                echo json_encode([
                    'success' => $stmt->rowCount() > 0,
                    'message' => $stmt->rowCount() > 0 ? 'Timesheet rejected' : 'Timesheet not found'
                ]);
            }
            break;
    }
    
} catch (Exception $e) {
    error_log("Attendance API Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
