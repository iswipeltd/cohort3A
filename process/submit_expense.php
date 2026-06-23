<?php
require_once __DIR__ . '/../config/session.php';
require_auth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /HRSuite/user-dashboard/submit_expense.php');
    exit;
}

$employee_id = get_employee_id($_SESSION['user_id']);
if (!$employee_id) {
    $_SESSION['error'] = 'Employee record not found. Please contact HR.';
    header('Location: /HRSuite/user-dashboard/submit_expense.php');
    exit;
}

$type = trim($_POST['category'] ?? '');
$amount = (float) ($_POST['amount'] ?? 0);
$description = trim($_POST['description'] ?? '');

if (empty($type) || empty($amount) || $amount <= 0 || empty($description)) {
    $_SESSION['error'] = 'Please fill all required fields (category, amount, description).';
    header('Location: /HRSuite/user-dashboard/submit_expense.php');
    exit;
}

$receiptPath = null;
if (!empty($_FILES['receipt']['tmp_name']) && $_FILES['receipt']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = __DIR__ . '/../uploads/receipts/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
    $filename = time() . '_' . basename($_FILES['receipt']['name']);
    move_uploaded_file($_FILES['receipt']['tmp_name'], $uploadDir . $filename);
    $receiptPath = '/HRSuite/uploads/receipts/' . $filename;
}

$stmt = $pdo->prepare("INSERT INTO expenses (employee_id, type, amount, expense_date, description, receipt_path, status) VALUES (?, ?, ?, NOW(), ?, ?, 'pending')");
$stmt->execute([$employee_id, $type, $amount, $description, $receiptPath]);

$expense_id = $pdo->lastInsertId();
log_activity($_SESSION['user_id'], 'CREATE', 'Expense', $expense_id, "Submitted expense claim for {$amount}");

// Notify HR
$hrStmt = $pdo->query("SELECT id FROM users WHERE role IN ('admin','hr')");
while ($hr = $hrStmt->fetch()) {
    send_notification($hr['id'], 'expense_request', "New expense claim from {$_SESSION['full_name']} ({$amount})", '/HRSuite/admin_dashboard/employees.php');
}

$_SESSION['success'] = 'Expense claim submitted successfully.';
header('Location: /HRSuite/user-dashboard/expense_status.php');
exit;
