<?php
session_start();
include("../includes/auth_check.php");
checkRole(['admin']);

if(!isset($_SESSION['user_id'])){
    header("Location: ../auth/login.php");
    exit();
}

include("../config/db.php");

if(!isset($_GET['id'])){
    die("Leave ID missing");
}

$id = intval($_GET['id']);

$sql = "DELETE FROM leaves WHERE id=$id";

$result = mysqli_query($conn, $sql);

if($result){
    header("Location: index.php?deleted=1");
    exit();
}else{
    echo "Error: " . mysqli_error($conn);
}
?>