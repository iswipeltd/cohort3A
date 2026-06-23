<?php
/**
 * ADEEEEE Session & Authentication Helpers
 */

session_start();

require_once __DIR__ . '/../config/database.php';

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

// Get current user data
function getCurrentUser() {
    if (!isLoggedIn()) return null;
    
    try {
        $db = getDB();
        $stmt = $db->prepare("SELECT id, email, role, status, last_login FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        return $stmt->fetch();
    } catch (PDOException $e) {
        return null;
    }
}

// Get current employee data
function getCurrentEmployee() {
    if (!isLoggedIn()) return null;
    
    try {
        $db = getDB();
        $stmt = $db->prepare("
            SELECT e.*, d.name as department_name, r.name as role_name 
            FROM employees e
            LEFT JOIN departments d ON e.department_id = d.id
            LEFT JOIN roles r ON e.role_id = r.id
            WHERE e.user_id = ?
        ");
        $stmt->execute([$_SESSION['user_id']]);
        return $stmt->fetch();
    } catch (PDOException $e) {
        return null;
    }
}

// Check role
function hasRole($role) {
    $user = getCurrentUser();
    if (!$user) return false;
    return $user['role'] === $role || ($role === 'admin' && in_array($user['role'], ['admin', 'hr']));
}

// Require authentication
function requireAuth() {
    if (!isLoggedIn()) {
        header('Location: signin.php');
        exit;
    }
}

// Require specific role
function requireRole($role) {
    requireAuth();
    if (!hasRole($role)) {
        header('Location: 403.php');
        exit;
    }
}

// Flash messages
function setFlash($type, $message) {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function getFlash() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

// Logout
function logout() {
    session_destroy();
    header('Location: signin.php');
    exit;
}
