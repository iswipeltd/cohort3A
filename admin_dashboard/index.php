<?php
session_start();
if(!empty($_SESSION['user_id']) && in_array($_SESSION['role']??'', ['admin','hr'])) {
    header('Location: welcome.php');
} else {
    header('Location: login.php');
}
exit;
