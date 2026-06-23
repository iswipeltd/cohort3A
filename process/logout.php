<?php
session_start();
require_once __DIR__ . '/../config/database.php';

if (!empty($_SESSION['user_id'])) {
    log_activity($_SESSION['user_id'], 'LOGOUT', 'Auth', $_SESSION['user_id'], 'Session terminated');
}

session_unset();
session_destroy();

header('Location: /HRSuite/admin_dashboard/login.php');
exit;
