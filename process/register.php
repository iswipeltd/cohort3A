<?php
session_start();
require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name  = trim($_POST['last_name'] ?? '');
    $email      = strtolower(trim($_POST['email'] ?? ''));
    $password   = trim($_POST['password'] ?? '');
    $confirm    = trim($_POST['confirm_password'] ?? '');

    if (empty($first_name) || empty($last_name) || empty($email) || empty($password)) {
        $_SESSION['error'] = 'All fields are required.';
        header('Location: /HRSuite/user-dashboard/signup.php');
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = 'Please enter a valid email address.';
        header('Location: /HRSuite/user-dashboard/signup.php');
        exit;
    }

    if (strlen($password) < 6) {
        $_SESSION['error'] = 'Password must be at least 6 characters.';
        header('Location: /HRSuite/user-dashboard/signup.php');
        exit;
    }

    if ($password !== $confirm) {
        $_SESSION['error'] = 'Passwords do not match.';
        header('Location: /HRSuite/user-dashboard/signup.php');
        exit;
    }

    // Check if email already exists
    $check = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $check->execute([$email]);
    if ($check->fetch()) {
        $_SESSION['error'] = 'An account with this email already exists. Please sign in instead.';
        header('Location: /HRSuite/user-dashboard/signup.php');
        exit;
    }

    $hash = password_hash($password, PASSWORD_DEFAULT);

    try {
        $stmt = $pdo->prepare("INSERT INTO users (email, password_hash, role, first_name, last_name, status, created_at) VALUES (?, ?, 'employee', ?, ?, 'active', NOW())");
        $stmt->execute([$email, $hash, $first_name, $last_name]);
        $user_id = $pdo->lastInsertId();

        $_SESSION['success'] = 'Account created successfully! Please sign in.';
        header('Location: /HRSuite/user-dashboard/signin.php');
        exit;
    } catch (Exception $e) {
        $_SESSION['error'] = 'Something went wrong. Please try again.';
        header('Location: /HRSuite/user-dashboard/signup.php');
        exit;
    }
}

header('Location: /HRSuite/user-dashboard/signup.php');
exit;
