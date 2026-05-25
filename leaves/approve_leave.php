<?php
session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: ../auth/login.php");
    exit();
}

include("../config/db.php");


if(!isset($_GET['id'])){
    die("Leave ID missing");
}

$id = intval($_GET['id']);

$sql = "UPDATE leaves SET status='approved' WHERE id=$id";

$result = mysqli_query($conn, $sql);

if($result){
    header("Location: index.php?approved=1");
    exit();
}else{
    echo "Error: " . mysqli_error($conn);
}
?>