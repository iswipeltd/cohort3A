<?php
/**
 * ADEEEEE Authentication API
 * Endpoints: login, register, logout, me
 */

require_once __DIR__ . '/../includes/session.php';

header('Content-Type: application/json');

$action = $_GET['action'] ?? '';

try {
    $db = $pdo;
    
    switch ($action) {
        case 'login':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                http_response_code(405);
                echo json_encode(['success' => false, 'message' => 'Method not allowed']);
                exit;
            }
            
            $data = json_decode(file_get_contents('php://input'), true) ?? $_POST;
            $email = filter_var($data['email'] ?? '', FILTER_SANITIZE_EMAIL);
            $password = $data['password'] ?? '';
            
            if (empty($email) || empty($password)) {
                echo json_encode(['success' => false, 'message' => 'Email and password are required']);
                exit;
            }
            
            $stmt = $db->prepare("SELECT id, email, password_hash, role, status FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();
            
            if (!$user) {
                echo json_encode(['success' => false, 'message' => 'Invalid credentials']);
                exit;
            }
            
            if ($user['status'] !== 'active') {
                echo json_encode(['success' => false, 'message' => 'Account is not active']);
                exit;
            }
            
            if (!password_verify($password, $user['password_hash'])) {
                echo json_encode(['success' => false, 'message' => 'Invalid credentials']);
                exit;
            }
            
            // Update last login
            $db->prepare("UPDATE users SET last_login = NOW() WHERE id = ?")->execute([$user['id']]);
            
            // Set session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['role'];
            
            // Get employee info if exists
            $empStmt = $db->prepare("SELECT id, first_name, last_name FROM employees WHERE user_id = ?");
            $empStmt->execute([$user['id']]);
            $employee = $empStmt->fetch();
            
            echo json_encode([
                'success' => true,
                'message' => 'Login successful',
                'user' => [
                    'id' => $user['id'],
                    'email' => $user['email'],
                    'role' => $user['role'],
                    'employee' => $employee ?: null
                ]
            ]);
            break;
            
        case 'register':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                http_response_code(405);
                echo json_encode(['success' => false, 'message' => 'Method not allowed']);
                exit;
            }
            
            $data = json_decode(file_get_contents('php://input'), true) ?? $_POST;
            $email = filter_var($data['email'] ?? '', FILTER_SANITIZE_EMAIL);
            $password = $data['password'] ?? '';
            $first_name = htmlspecialchars($data['first_name'] ?? '');
            $last_name = htmlspecialchars($data['last_name'] ?? '');
            $role = $data['role'] ?? 'employee';
            
            if (empty($email) || empty($password) || empty($first_name) || empty($last_name)) {
                echo json_encode(['success' => false, 'message' => 'All fields are required']);
                exit;
            }
            
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                echo json_encode(['success' => false, 'message' => 'Invalid email format']);
                exit;
            }
            
            if (strlen($password) < 8) {
                echo json_encode(['success' => false, 'message' => 'Password must be at least 8 characters']);
                exit;
            }
            
            // Check if email exists
            $checkStmt = $db->prepare("SELECT id FROM users WHERE email = ?");
            $checkStmt->execute([$email]);
            if ($checkStmt->rowCount() > 0) {
                echo json_encode(['success' => false, 'message' => 'Email already registered']);
                exit;
            }
            
            $hash = password_hash($password, PASSWORD_BCRYPT);
            
            $db->beginTransaction();
            
            $stmt = $db->prepare("INSERT INTO users (email, password_hash, role) VALUES (?, ?, ?)");
            $stmt->execute([$email, $hash, $role]);
            $userId = $db->lastInsertId();
            
            // Create employee record
            $empId = 'EMP-' . str_pad($userId, 4, '0', STR_PAD_LEFT);
            $empStmt = $db->prepare("
                INSERT INTO employees (user_id, employee_id, first_name, last_name, email, hire_date, status)
                VALUES (?, ?, ?, ?, ?, CURDATE(), 'active')
            ");
            $empStmt->execute([$userId, $empId, $first_name, $last_name, $email]);
            
            $db->commit();
            
            echo json_encode([
                'success' => true,
                'message' => 'Registration successful',
                'user_id' => $userId
            ]);
            break;
            
        case 'logout':
            session_destroy();
            echo json_encode(['success' => true, 'message' => 'Logged out successfully']);
            break;
            
        case 'me':
            $user = getCurrentUser();
            if (!$user) {
                echo json_encode(['success' => false, 'message' => 'Not authenticated']);
                exit;
            }
            
            $employee = getCurrentEmployee();
            
            echo json_encode([
                'success' => true,
                'user' => $user,
                'employee' => $employee
            ]);
            break;
            
        default:
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Unknown action']);
    }
    
} catch (Exception $e) {
    if (isset($db) && $db->inTransaction()) {
        $db->rollBack();
    }
    error_log("Auth API Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Internal server error']);
}
