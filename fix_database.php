<?php
echo "<h1>HRSuite Database Fix</h1>";

$host = 'localhost';
$db   = 'hrsuite';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=$charset", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
} catch (PDOException $e) {
    die("<p style='color:red'>Cannot connect to database. Error: " . htmlspecialchars($e->getMessage()) . "</p>");
}

// Check current columns
$stmt = $pdo->query("SHOW COLUMNS FROM employees");
$columns = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);

echo "<p>Current columns in <code>employees</code> table: " . implode(', ', $columns) . "</p>";

$added = [];

if (!in_array('bank_name', $columns)) {
    $pdo->exec("ALTER TABLE employees ADD COLUMN bank_name VARCHAR(100) NULL");
    $added[] = 'bank_name';
}
if (!in_array('bank_account', $columns)) {
    $pdo->exec("ALTER TABLE employees ADD COLUMN bank_account VARCHAR(50) NULL");
    $added[] = 'bank_account';
}
if (!in_array('bank_code', $columns)) {
    $pdo->exec("ALTER TABLE employees ADD COLUMN bank_code VARCHAR(20) NULL");
    $added[] = 'bank_code';
}
if (!in_array('employment_type', $columns)) {
    $pdo->exec("ALTER TABLE employees ADD COLUMN employment_type VARCHAR(20) NULL DEFAULT 'full-time'");
    $added[] = 'employment_type';
}

if (empty($added)) {
    echo "<p style='color:green; font-size:18px;'>All required columns already exist. Nothing to fix.</p>";
} else {
    echo "<p style='color:green; font-size:18px;'>Added columns: " . implode(', ', $added) . "</p>";
}

echo "<p><a href='/HRSuite/admin_dashboard/employees.php' style='font-size:16px; padding:10px 20px; background:#6366f1; color:white; text-decoration:none; border-radius:8px;'>Go to Employees</a></p>";
echo "<p style='color:gray'>You can delete this file (<code>fix_database.php</code>) after it works.</p>";
