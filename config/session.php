<?php
session_start();
require_once __DIR__ . '/database.php';

/**
 * Unified login check.
 * Redirects to the signin page if not authenticated.
 */
function require_auth() {
    if (empty($_SESSION['user_id'])) {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'];
        $scriptDir = dirname($_SERVER['SCRIPT_NAME']);
        $base = $protocol . '://' . $host;

        // Detect whether we are in admin_dashboard or user-dashboard
        if (strpos($_SERVER['SCRIPT_NAME'], 'admin_dashboard') !== false) {
            header("Location: {$base}/HRSuite/admin_dashboard/signin.php");
        } else {
            header("Location: {$base}/HRSuite/user-dashboard/signin.php");
        }
        exit;
    }

    // Force password change for first login with default password
    if (!empty($_SESSION['force_password_change'])) {
        $currentPage = basename($_SERVER['PHP_SELF']);
        if ($currentPage !== 'first_login.php') {
            header("Location: /HRSuite/user-dashboard/first_login.php");
            exit;
        }
    }
}

/**
 * Check if current user is admin/hr. Redirect non-admins away from admin pages.
 */
function require_admin() {
    require_auth();
    if (!in_array($_SESSION['role'] ?? '', ['admin','hr'])) {
        header("Location: /HRSuite/user-dashboard/index.php");
        exit;
    }
}

/**
 * Get current user data
 */
function current_user() {
    global $pdo;
    if (empty($_SESSION['user_id'])) return null;
    $stmt = $pdo->prepare("SELECT id, email, role, first_name, last_name, phone, avatar, status, last_login FROM users WHERE id = ? LIMIT 1");
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetch();
}

/**
 * Get current employee data
 */
function current_employee() {
    global $pdo;
    $user = current_user();
    if (!$user) return null;
    $stmt = $pdo->prepare("SELECT e.*, d.name as department_name, r.name as role_name 
                          FROM employees e 
                          LEFT JOIN departments d ON e.department_id = d.id 
                          LEFT JOIN roles r ON e.role_id = r.id 
                          WHERE e.user_id = ? LIMIT 1");
    $stmt->execute([$user['id']]);
    return $stmt->fetch();
}
