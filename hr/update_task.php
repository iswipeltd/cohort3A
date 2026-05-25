<?php
session_start();
include("../config/db.php");

$task_id = $_POST['task_id'];
$status = $_POST['status'];

$sql = "UPDATE tasks 
        SET status='$status' 
        WHERE id=$task_id";

$result = mysqli_query($conn, $sql);

if($result){
    header("Location: hr_tasks.php");
    exit();
} else {
    die("Error: " . mysqli_error($conn));
}
?>