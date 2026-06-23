<?php
require_once __DIR__ . '/../config/session.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /HRSuite/admin_dashboard/employee_add.php');
    exit;
}

$first_name = trim($_POST['first_name'] ?? '');
$last_name = trim($_POST['last_name'] ?? '');
$email = strtolower(trim($_POST['email'] ?? ''));
$phone = trim($_POST['phone'] ?? '');
$department_id = (int)($_POST['department_id'] ?? 0);
$role_id = (int)($_POST['role_id'] ?? 0);
$manager_id = !empty($_POST['manager_id']) ? (int)$_POST['manager_id'] : null;
$salary = (float)($_POST['salary'] ?? 0);
$start_date = $_POST['start_date'] ?? date('Y-m-d');
$employment_type = $_POST['employment_type'] ?? 'full-time';
$status = $_POST['status'] ?? 'active';
$address = trim($_POST['address'] ?? '');
$city = trim($_POST['city'] ?? '');
$country = trim($_POST['country'] ?? 'USA');
$emergency_name = trim($_POST['emergency_name'] ?? '');
$emergency_phone = trim($_POST['emergency_phone'] ?? '');
$bank_name = trim($_POST['bank_name'] ?? '');
$bank_account = trim($_POST['bank_account'] ?? '');
$bank_code = trim($_POST['bank_code'] ?? '');
$tax_id = trim($_POST['tax_id'] ?? '');

if (empty($first_name) || empty($last_name) || empty($email) || empty($department_id) || empty($role_id)) {
    $_SESSION['error'] = 'Please fill all required fields.';
    header('Location: /HRSuite/admin_dashboard/employee_add.php');
    exit;
}

$dup = $pdo->prepare("SELECT id FROM users WHERE email = ?");
$dup->execute([$email]);
if ($dup->fetch()) {
    $_SESSION['error'] = 'A user with this email already exists.';
    header('Location: /HRSuite/admin_dashboard/employee_add.php');
    exit;
}

$prefix = 'EMP';
$max = $pdo->query("SELECT MAX(id) as max_id FROM employees")->fetch();
$nextId = ($max['max_id'] ?? 0) + 1;
$employee_code = $prefix . '-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);

$default_password = 'Employee@123';
$hash = password_hash($default_password, PASSWORD_DEFAULT);
$userRole = 'employee';
if ($role_id == 1) $userRole = 'admin';
elseif ($role_id == 2 || $role_id == 3) $userRole = 'manager';

$cols = $pdo->query("SHOW COLUMNS FROM employees")->fetchAll(PDO::FETCH_COLUMN, 0);

$pdo->beginTransaction();

try {
    $pdo->prepare("INSERT INTO users (email, password_hash, role, first_name, last_name, phone, status) VALUES (?, ?, ?, ?, ?, ?, 'active')")
        ->execute([$email, $hash, $userRole, $first_name, $last_name, $phone]);
    $user_id = $pdo->lastInsertId();

    // Auto-add bank columns if missing (Novac requires them)
    if (!in_array('bank_name', $cols)) {
        $pdo->exec("ALTER TABLE employees ADD COLUMN bank_name VARCHAR(100) NULL");
        $cols[] = 'bank_name';
    }
    if (!in_array('bank_account', $cols)) {
        $pdo->exec("ALTER TABLE employees ADD COLUMN bank_account VARCHAR(50) NULL");
        $cols[] = 'bank_account';
    }
    if (!in_array('bank_code', $cols)) {
        $pdo->exec("ALTER TABLE employees ADD COLUMN bank_code VARCHAR(20) NULL");
        $cols[] = 'bank_code';
    }

    $empCols = ['user_id', 'employee_code', 'department_id', 'role_id', 'manager_id', 'salary', 'start_date', 'status', 'address', 'city', 'country', 'emergency_name', 'emergency_phone', 'emergency_relationship', 'tax_id'];
    $empVals = [$user_id, $employee_code, $department_id, $role_id, $manager_id, $salary, $start_date, $status, $address, $city, $country, $emergency_name, $emergency_phone, 'Relative', $tax_id];

    if (in_array('employment_type', $cols)) {
        $empCols[] = 'employment_type';
        $empVals[] = $employment_type;
    }
    if (in_array('bank_name', $cols)) {
        $empCols[] = 'bank_name';
        $empVals[] = $bank_name;
    }
    if (in_array('bank_account', $cols)) {
        $empCols[] = 'bank_account';
        $empVals[] = $bank_account;
    }
    if (in_array('bank_code', $cols)) {
        $empCols[] = 'bank_code';
        $empVals[] = $bank_code;
    }

    $ph = implode(', ', array_fill(0, count($empCols), '?'));
    $cn = implode(', ', $empCols);
    $pdo->prepare("INSERT INTO employees ({$cn}) VALUES ({$ph})")->execute($empVals);
    $employee_id = $pdo->lastInsertId();

    if (!empty($_FILES['documents']['name'][0])) {
        $uploadDir = __DIR__ . '/../uploads/documents/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
        foreach ($_FILES['documents']['tmp_name'] as $i => $tmp) {
            if ($_FILES['documents']['error'][$i] === UPLOAD_ERR_OK) {
                $filename = time() . '_' . basename($_FILES['documents']['name'][$i]);
                move_uploaded_file($tmp, $uploadDir . $filename);
                $pdo->prepare("INSERT INTO documents (employee_id, type, title, filename, file_path, uploaded_by) VALUES (?, 'other', ?, ?, ?, ?)")
                    ->execute([$employee_id, $_FILES['documents']['name'][$i], $filename, '/HRSuite/uploads/documents/' . $filename, $_SESSION['user_id']]);
            }
        }
    }

    $pdo->commit();

    log_activity($_SESSION['user_id'], 'CREATE', 'Employee', $employee_id, "Created employee {$first_name} {$last_name}");
    send_notification($user_id, 'account_created', "Welcome! Your account has been created. Default password: {$default_password}");

    $_SESSION['success'] = "Employee created. Code: {$employee_code} | Email: {$email} | Password: {$default_password}";
    header('Location: /HRSuite/admin_dashboard/employees.php');
    exit;
} catch (Exception $e) {
    $pdo->rollBack();
    $_SESSION['error'] = 'Error: ' . $e->getMessage();
    header('Location: /HRSuite/admin_dashboard/employee_add.php');
    exit;
}
