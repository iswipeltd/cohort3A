<?php
/**
 * Auto Database Fix Script
 * Run this by visiting: http://localhost/HRSuite/setup_database_fix.php
 * It will add missing bank columns to the employees table automatically.
 */

echo "<h1>HRSuite Database Fix</h1>";
echo "<p>Adding missing bank columns to your database...</p>";

try {
    require_once __DIR__ . '/config/database.php';
    
    // Check which columns are missing
    $stmt = $pdo->query("SHOW COLUMNS FROM employees");
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
    
    $missing = [];
    if (!in_array('bank_name', $columns)) $missing[] = 'bank_name';
    if (!in_array('bank_account', $columns)) $missing[] = 'bank_account';
    if (!in_array('bank_code', $columns)) $missing[] = 'bank_code';
    
    if (empty($missing)) {
        echo "<p style='color:green; font-size:18px;'>✅ All bank columns already exist! No fix needed.</p>";
    } else {
        // Add columns one by one (safer than combined ALTER)
        if (!in_array('bank_name', $columns)) {
            $pdo->exec("ALTER TABLE employees ADD COLUMN bank_name VARCHAR(100) NULL");
            echo "<p>✅ Added <code>bank_name</code> column</p>";
        }
        if (!in_array('bank_account', $columns)) {
            $pdo->exec("ALTER TABLE employees ADD COLUMN bank_account VARCHAR(50) NULL");
            echo "<p>✅ Added <code>bank_account</code> column</p>";
        }
        if (!in_array('bank_code', $columns)) {
            $pdo->exec("ALTER TABLE employees ADD COLUMN bank_code VARCHAR(20) NULL");
            echo "<p>✅ Added <code>bank_code</code> column</p>";
        }
        echo "<p style='color:green; font-size:18px;'>🎉 Database fix complete! All bank columns added.</p>";
    }
    
    echo "<p><a href='/HRSuite/admin_dashboard/employees.php' style='font-size:16px; padding:10px 20px; background:#6366f1; color:white; text-decoration:none; border-radius:8px;'>Go to Employees</a></p>";
    
} catch (PDOException $e) {
    echo "<p style='color:red; font-size:18px;'>❌ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p>Make sure your database config is correct in <code>config/database.php</code></p>";
}
