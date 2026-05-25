<?php
session_start();
include("../includes/auth_check.php");
checkRole(['admin']);

if(!isset($_SESSION['user_id'])){
    header("Location: ../auth/login.php");
    exit();
}

include("../config/db.php");

// Check if ID exists
if(!isset($_GET['id'])){
    header("Location: index.php");
    exit();
}

$id = $_GET['id'];

// Delete query
$sql = "DELETE FROM departments WHERE id='$id'";

if(mysqli_query($conn, $sql)){
    header("Location: index.php?deleted=1");
    exit();
} else {
    echo "Error deleting department: " . mysqli_error($conn);
}
?>