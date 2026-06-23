<?php
require_once __DIR__ . '/../config/session.php';
require_admin();
require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    $department_id = (int)($_POST['department_id'] ?? 0);
    $role_id = (int)($_POST['role_id'] ?? 0);
    $manager_id = !empty($_POST['manager_id']) ? (int)$_POST['manager_id'] : null;
    $salary = (float)($_POST['salary'] ?? 0);
    $status = $_POST['status'] ?? 'active';
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $country = trim($_POST['country'] ?? '');
    $employment_type = $_POST['employment_type'] ?? 'full-time';
    $bank_name = trim($_POST['bank_name'] ?? '');
    $bank_account = trim($_POST['bank_account'] ?? '');
    $bank_code = trim($_POST['bank_code'] ?? '');

    if ($id <= 0) {
        $_SESSION['error'] = 'Invalid employee ID.';
        header('Location: /HRSuite/admin_dashboard/employees.php');
        exit;
    }

    // Check which columns exist; auto-add bank columns if missing (Novac requires them)
    $cols = $pdo->query("SHOW COLUMNS FROM employees")->fetchAll(PDO::FETCH_COLUMN, 0);
    $addedCols = [];
    if (!in_array('bank_name', $cols)) {
        $pdo->exec("ALTER TABLE employees ADD COLUMN bank_name VARCHAR(100) NULL");
        $addedCols[] = 'bank_name';
    }
    if (!in_array('bank_account', $cols)) {
        $pdo->exec("ALTER TABLE employees ADD COLUMN bank_account VARCHAR(50) NULL");
        $addedCols[] = 'bank_account';
    }
    if (!in_array('bank_code', $cols)) {
        $pdo->exec("ALTER TABLE employees ADD COLUMN bank_code VARCHAR(20) NULL");
        $addedCols[] = 'bank_code';
    }
    if (!empty($addedCols)) {
        $cols = $pdo->query("SHOW COLUMNS FROM employees")->fetchAll(PDO::FETCH_COLUMN, 0);
    }

    $sets = [];
    $vals = [];

    $sets[] = "department_id = ?"; $vals[] = $department_id ?: null;
    $sets[] = "role_id = ?";       $vals[] = $role_id ?: null;
    $sets[] = "manager_id = ?";    $vals[] = $manager_id;
    $sets[] = "salary = ?";        $vals[] = $salary;
    $sets[] = "status = ?";        $vals[] = $status;

    if (in_array('employment_type', $cols)) {
        $sets[] = "employment_type = ?"; $vals[] = $employment_type;
    }
    $sets[] = "address = ?"; $vals[] = $address;
    $sets[] = "city = ?";    $vals[] = $city;
    $sets[] = "country = ?"; $vals[] = $country;

    if (in_array('bank_name', $cols)) {
        $sets[] = "bank_name = ?";    $vals[] = $bank_name;
    }
    if (in_array('bank_account', $cols)) {
        $sets[] = "bank_account = ?"; $vals[] = $bank_account;
    }
    if (in_array('bank_code', $cols)) {
        $sets[] = "bank_code = ?";    $vals[] = $bank_code;
    }

    $vals[] = $id;
    $sql = "UPDATE employees SET " . implode(', ', $sets) . " WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($vals);

    $user_id = $pdo->query("SELECT user_id FROM employees WHERE id=$id")->fetchColumn();
    if ($user_id && $phone) {
        $pdo->prepare("UPDATE users SET phone=? WHERE id=?")->execute([$phone, $user_id]);
    }

    log_activity($_SESSION['user_id'], 'UPDATE', 'Employee', $id, 'Updated employee record');
    if ($user_id) {
        send_notification($user_id, 'info', 'Your profile has been updated by HR.', '/HRSuite/user-dashboard/my_profile.php');
    }
    $_SESSION['success'] = 'Employee updated successfully.';
    header('Location: /HRSuite/admin_dashboard/employees.php');
    exit;
}
header('Location: /HRSuite/admin_dashboard/employees.php');
exit;
