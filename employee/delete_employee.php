<?php
session_start();
include("../includes/auth_check.php");
checkRole(['admin']);

if(!isset($_SESSION['user_id'])){
    header("Location: ../auth/login.php");
    exit();
}

include("../config/db.php");

// get ID
$id = $_GET['id'];

// delete query
$sql = "DELETE FROM users WHERE id='$id'";

if(mysqli_query($conn, $sql)){

    header("Location: index.php");
    exit();

} else {
    echo "Error deleting record: " . mysqli_error($conn);
}
?>