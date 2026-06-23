<?php
/**
 * Admin Employee Management API
 * CRUD operations for employees
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
                $search = $_GET['search'] ?? '';
                $department = $_GET['department'] ?? '';
                $status = $_GET['status'] ?? '';
                
                $where = ['1=1'];
                $params = [];
                
                if ($search) {
                    $where[] = "(e.first_name LIKE ? OR e.last_name LIKE ? OR e.email LIKE ? OR e.employee_id LIKE ?)";
                    $searchLike = "%$search%";
                    $params = array_fill(0, 4, $searchLike);
                }
                
                if ($department) {
                    $where[] = "e.department_id = ?";
                    $params[] = $department;
                }
                
                if ($status) {
                    $where[] = "e.status = ?";
                    $params[] = $status;
                }
                
                $whereStr = implode(' AND ', $where);
                
                // Count total
                $countStmt = $db->prepare("SELECT COUNT(*) FROM employees e WHERE $whereStr");
                $countStmt->execute($params);
                $total = $countStmt->fetchColumn();
                
                // Get data
                $stmt = $db->prepare("
                    SELECT e.*, d.name as department_name, r.name as role_name,
                        CONCAT(m.first_name, ' ', m.last_name) as manager_name
                    FROM employees e
                    LEFT JOIN departments d ON e.department_id = d.id
                    LEFT JOIN roles r ON e.role_id = r.id
                    LEFT JOIN employees m ON e.manager_id = m.id
                    WHERE $whereStr
                    ORDER BY e.created_at DESC
                    LIMIT $limit OFFSET $offset
                ");
                $stmt->execute($params);
                $employees = $stmt->fetchAll();
                
                echo json_encode([
                    'success' => true,
                    'data' => $employees,
                    'pagination' => [
                        'page' => $page,
                        'limit' => $limit,
                        'total' => $total,
                        'total_pages' => ceil($total / $limit)
                    ]
                ]);
                
            } elseif ($action === 'detail' && isset($_GET['id'])) {
                $stmt = $db->prepare("
                    SELECT e.*, d.name as department_name, r.name as role_name,
                        CONCAT(m.first_name, ' ', m.last_name) as manager_name,
                        u.email as user_email, u.role as user_role
                    FROM employees e
                    LEFT JOIN departments d ON e.department_id = d.id
                    LEFT JOIN roles r ON e.role_id = r.id
                    LEFT JOIN employees m ON e.manager_id = m.id
                    LEFT JOIN users u ON e.user_id = u.id
                    WHERE e.id = ?
                ");
                $stmt->execute([$_GET['id']]);
                $employee = $stmt->fetch();
                
                if (!$employee) {
                    http_response_code(404);
                    echo json_encode(['success' => false, 'message' => 'Employee not found']);
                    exit;
                }
                
                // Get documents
                $docStmt = $db->prepare("SELECT * FROM employee_documents WHERE employee_id = ? ORDER BY uploaded_at DESC");
                $docStmt->execute([$_GET['id']]);
                $employee['documents'] = $docStmt->fetchAll();
                
                echo json_encode(['success' => true, 'data' => $employee]);
            }
            break;
            
        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true) ?? $_POST;
            
            $db->beginTransaction();
            
            // Create user account
            $email = filter_var($data['email'], FILTER_SANITIZE_EMAIL);
            $password = bin2hex(random_bytes(8)); // Generate random password
            $hash = password_hash($password, PASSWORD_BCRYPT);
            
            $userStmt = $db->prepare("INSERT INTO users (email, password_hash, role) VALUES (?, ?, ?)");
            $userStmt->execute([$email, $hash, $data['role'] ?? 'employee']);
            $userId = $db->lastInsertId();
            
            // Create employee
            $empId = 'EMP-' . str_pad($userId, 4, '0', STR_PAD_LEFT);
            $empStmt = $db->prepare("
                INSERT INTO employees 
                (user_id, employee_id, first_name, last_name, email, phone, 
                 department_id, role_id, manager_id, salary, hire_date, status)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $empStmt->execute([
                $userId, $empId, $data['first_name'], $data['last_name'], $email,
                $data['phone'] ?? '', $data['department_id'] ?? null,
                $data['role_id'] ?? null, $data['manager_id'] ?? null,
                $data['salary'] ?? 0, $data['hire_date'] ?? date('Y-m-d'),
                $data['status'] ?? 'active'
            ]);
            $employeeId = $db->lastInsertId();
            
            // Initialize leave balances for current year
            $currentYear = date('Y');
            $leaveTypes = $db->query("SELECT id, days_allowed FROM leave_types WHERE status = 'active'")->fetchAll();
            
            foreach ($leaveTypes as $lt) {
                $balStmt = $db->prepare("
                    INSERT INTO leave_balances (employee_id, leave_type_id, year, total_days)
                    VALUES (?, ?, ?, ?)
                ");
                $balStmt->execute([$employeeId, $lt['id'], $currentYear, $lt['days_allowed']]);
            }
            
            $db->commit();
            
            // Create audit log
            $auditStmt = $db->prepare("
                INSERT INTO audit_logs (user_id, action, module, entity_type, entity_id, new_values, ip_address)
                VALUES (?, 'CREATE', 'Employee', 'employee', ?, ?, ?)
            ");
            $auditStmt->execute([$_SESSION['user_id'], $employeeId, json_encode($data), $_SERVER['REMOTE_ADDR']]);
            
            echo json_encode([
                'success' => true,
                'message' => 'Employee created successfully',
                'employee_id' => $empId,
                'temp_password' => $password
            ]);
            break;
            
        case 'PUT':
        case 'PATCH':
            parse_str(file_get_contents('php://input'), $data);
            if (empty($data)) {
                $data = json_decode(file_get_contents('php://input'), true) ?? [];
            }
            
            if (!isset($data['id'])) {
                echo json_encode(['success' => false, 'message' => 'Employee ID required']);
                exit;
            }
            
            // Get old values for audit
            $oldStmt = $db->prepare("SELECT * FROM employees WHERE id = ?");
            $oldStmt->execute([$data['id']]);
            $oldData = $oldStmt->fetch(PDO::FETCH_ASSOC);
            
            $fields = [];
            $values = [];
            $allowed = ['first_name','last_name','phone','department_id','role_id','manager_id','salary','status','address'];
            
            foreach ($allowed as $field) {
                if (isset($data[$field])) {
                    $fields[] = "$field = ?";
                    $values[] = $data[$field];
                }
            }
            
            if (empty($fields)) {
                echo json_encode(['success' => false, 'message' => 'No fields to update']);
                exit;
            }
            
            $values[] = $data['id'];
            $stmt = $db->prepare("UPDATE employees SET " . implode(', ', $fields) . " WHERE id = ?");
            $stmt->execute($values);
            
            // Audit log
            $auditStmt = $db->prepare("
                INSERT INTO audit_logs (user_id, action, module, entity_type, entity_id, old_values, new_values, ip_address)
                VALUES (?, 'UPDATE', 'Employee', 'employee', ?, ?, ?, ?)
            ");
            $auditStmt->execute([
                $_SESSION['user_id'], $data['id'],
                json_encode($oldData), json_encode($data), $_SERVER['REMOTE_ADDR']
            ]);
            
            echo json_encode(['success' => true, 'message' => 'Employee updated successfully']);
            break;
            
        case 'DELETE':
            $id = $_GET['id'] ?? null;
            if (!$id) {
                echo json_encode(['success' => false, 'message' => 'Employee ID required']);
                exit;
            }
            
            $db->beginTransaction();
            
            // Soft delete - set status to terminated
            $stmt = $db->prepare("UPDATE employees SET status = 'terminated', termination_date = CURDATE() WHERE id = ?");
            $stmt->execute([$id]);
            
            // Deactivate user account
            $userStmt = $db->prepare("
                UPDATE users SET status = 'inactive' 
                WHERE id = (SELECT user_id FROM employees WHERE id = ?)
            ");
            $userStmt->execute([$id]);
            
            $db->commit();
            
            // Audit log
            $auditStmt = $db->prepare("
                INSERT INTO audit_logs (user_id, action, module, entity_type, entity_id, ip_address)
                VALUES (?, 'DELETE', 'Employee', 'employee', ?, ?)
            ");
            $auditStmt->execute([$_SESSION['user_id'], $id, $_SERVER['REMOTE_ADDR']]);
            
            echo json_encode(['success' => true, 'message' => 'Employee marked as terminated']);
            break;
    }
    
} catch (Exception $e) {
    if (isset($db) && $db->inTransaction()) {
        $db->rollBack();
    }
    error_log("Employee API Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
