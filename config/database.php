<?php
/**
 * ADEEEEE Database Configuration
 * Update DB_HOST, DB_USER, DB_PASS, DB_NAME to match your environment.
 */

ini_set('display_errors', '1');
error_reporting(E_ALL);

// Set timezone to Nigeria (WAT, UTC+1)
date_default_timezone_set('Africa/Lagos');

define('DB_HOST', 'localhost');
define('DB_USER', 'root');       // MySQL username
define('DB_PASS', '');           // MySQL password (empty for default XAMPP)
define('DB_NAME', 'hrsuite');
define('DB_CHARSET', 'utf8mb4');

// PDO connection
try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET,
        DB_USER,
        DB_PASS
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

    // Sync MySQL timezone with PHP (Nigeria WAT = UTC+1)
    $pdo->exec("SET time_zone = '+01:00'");
} catch (PDOException $e) {
    $err = htmlspecialchars($e->getMessage());
    echo "<!DOCTYPE html><html><head><meta charset='utf-8'><title>Database Error</title>
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css' rel='stylesheet'></head>
    <body class='bg-light'><div class='container py-5'><div class='card border-danger shadow-sm'>
    <div class='card-header bg-danger text-white fw-bold'><i class='fa-solid fa-triangle-exclamation me-2'></i>Database Connection Failed</div>
    <div class='card-body'><p class='card-text'><strong>Error:</strong> {$err}</p>
    <hr><h6 class='fw-bold'>Fix this by:</h6>
    <ol class='mb-0'><li>Open phpMyAdmin (or MySQL client)</li>
    <li>Create database: <code>CREATE DATABASE hrsuite;</code></li>
    <li>Import <code>hrsuite.sql</code> into the database</li>
    <li>Verify credentials in <code>config/database.php</code> match your MySQL user/password</li></ol>
    </div></div></div></body></html>";
    exit;
}

// Legacy mysqli connection (optional compatibility)
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($mysqli->connect_errno) {
    echo "<!DOCTYPE html><html><body style='font-family:sans-serif;padding:40px;'><h2 style='color:#dc2626;'>MySQLi connection failed: " . htmlspecialchars($mysqli->connect_error) . "</h2></body></html>";
    exit;
}
$mysqli->set_charset(DB_CHARSET);

/**
 * Helper: Log activity to audit trail
 */
function log_activity($user_id, $action, $module, $record_id = null, $details = '') {
    global $pdo;
    $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
    $stmt = $pdo->prepare("INSERT INTO activity_logs (user_id, action, module, record_id, details, ip_address, user_agent) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$user_id, $action, $module, $record_id, $details, $ip, $ua]);
}

/**
 * Helper: Send notification to user
 */
function send_notification($user_id, $type, $message, $link = '') {
    global $pdo;
    $stmt = $pdo->prepare("INSERT INTO notifications (user_id, type, message, link) VALUES (?, ?, ?, ?)");
    $stmt->execute([$user_id, $type, $message, $link]);
}

/**
 * Helper: Get unread notification count
 */
function unread_notifications_count($user_id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM notifications WHERE user_id = ? AND read_at IS NULL");
    $stmt->execute([$user_id]);
    return (int) $stmt->fetchColumn();
}

/**
 * Helper: Get employee ID from user ID
 */
function get_employee_id($user_id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT id FROM employees WHERE user_id = ? LIMIT 1");
    $stmt->execute([$user_id]);
    $row = $stmt->fetch();
    return $row ? $row['id'] : null;
}

/**
 * Helper: Format currency
 */
function format_currency($amount, $currency = 'NGN') {
    return '₦' . number_format($amount, 2);
}
