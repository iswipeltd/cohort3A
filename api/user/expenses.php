<?php
/**
 * Employee Expense Claims API
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
            $stmt = $db->prepare("
                SELECT * FROM expense_claims 
                WHERE employee_id = ?
                ORDER BY created_at DESC
            ");
            $stmt->execute([$empId]);
            echo json_encode(['success' => true, 'data' => $stmt->fetchAll()]);
            break;
            
        case 'POST':
            if ($action === 'submit') {
                $data = json_decode(file_get_contents('php://input'), true) ?? $_POST;
                
                $stmt = $db->prepare("
                    INSERT INTO expense_claims (employee_id, expense_type, amount, currency, expense_date, description)
                    VALUES (?, ?, ?, ?, ?, ?)
                ");
                $stmt->execute([
                    $empId,
                    $data['expense_type'] ?? 'Other',
                    $data['amount'] ?? 0,
                    $data['currency'] ?? 'NGN',
                    $data['expense_date'] ?? date('Y-m-d'),
                    $data['description'] ?? ''
                ]);
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Expense claim submitted',
                    'claim_id' => $db->lastInsertId()
                ]);
            }
            break;
    }
    
} catch (Exception $e) {
    error_log("Expense API Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
