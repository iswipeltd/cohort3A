<?php
session_start();
include("../includes/auth_check.php");
checkRole(['admin']);
if(!isset($_SESSION['user_id'])){
    header("Location: ../auth/login.php");
    exit();
}

include("../config/db.php");

$user_id = $_POST['user_id'];
$leave_type = $_POST['leave_type'];
$start_date = $_POST['start_date'];
$end_date = $_POST['end_date'];
$reason = $_POST['reason'];

$sql = "INSERT INTO leaves 
(user_id, leave_type, start_date, end_date, reason)
VALUES
('$user_id', '$leave_type', '$start_date', '$end_date', '$reason')";

$result = mysqli_query($conn, $sql);

if($result){
    header("Location: index.php?success=1");
    exit();
}else{
    echo "Error: " . mysqli_error($conn);
}
?>