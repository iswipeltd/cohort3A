<?php
require_once __DIR__ . '/../config/session.php';
require_admin();
require_once __DIR__ . '/../config/database.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $default_days = (int)($_POST['default_days'] ?? 0);
    $carry_forward = isset($_POST['carry_forward']) ? 1 : 0;
    $max_carry_forward = (int)($_POST['max_carry_forward'] ?? 0);
    $requires_approval = isset($_POST['requires_approval']) ? 1 : 0;
    $paid = isset($_POST['paid']) ? 1 : 0;
    $color = trim($_POST['color'] ?? '#0d6efd');
    if ($name) {
        $stmt = $pdo->prepare("INSERT INTO leave_types (name, default_days, carry_forward, max_carry_forward, requires_approval, paid, color, status) VALUES (?, ?, ?, ?, ?, ?, ?, 'active')");
        $stmt->execute([$name, $default_days, $carry_forward, $max_carry_forward, $requires_approval, $paid, $color]);
        log_activity($_SESSION['user_id'], 'CREATE', 'Leave Type', $pdo->lastInsertId(), 'Created leave type: ' . $name);
        $_SESSION['success'] = 'Leave type created.';
    }
    header('Location: /HRSuite/admin_dashboard/leave_types.php');
    exit;
}
