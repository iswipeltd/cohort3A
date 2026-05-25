<?php
session_start();
include("../includes/auth_check.php");
checkRole(['admin', 'hr']);

if(!isset($_SESSION['user_id'])){
    header("Location: ../auth/login.php");
    exit();
}

include("../config/db.php");

$id = $_POST['id'];
$full_name = $_POST['full_name'];
$email = $_POST['email'];
$role = $_POST['role'];

$department_id = $_POST['department_id'];

$sql = "UPDATE users 
        SET full_name='$full_name',
            email='$email',
            role='$role',
            department_id='$department_id'
        WHERE id='$id'";

if(mysqli_query($conn, $sql)){
    header("Location: index.php");
    exit();
} else {
    echo "Error: " . mysqli_error($conn);
}
?>