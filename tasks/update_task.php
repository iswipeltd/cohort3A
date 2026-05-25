<?php
session_start();
include("../includes/auth_check.php");
checkRole(['admin', 'hr', 'employee']);
include("../config/db.php");

$task_id = $_POST['task_id'];
$status = $_POST['status'];

if(empty($task_id) || empty($status)){
    die("Missing task_id or status");
}

$sql = "UPDATE tasks 
        SET status='$status' 
        WHERE id='$task_id'";

$result = mysqli_query($conn, $sql);

if(!$result){
    die("SQL Error: " . mysqli_error($conn));
}

header("Location: user_task.php");
exit();
?>