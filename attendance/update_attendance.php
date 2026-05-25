<?php
session_start();
include("../includes/auth_check.php");
checkRole(['admin', 'hr']);

include("../config/db.php");

$id = $_POST['id'];
$user_id = $_POST['user_id'];
$check_in = $_POST['check_in'];
$check_out = $_POST['check_out'];
$attendance_date = $_POST['attendance_date'];
$status = $_POST['status'];

$sql = "UPDATE attendance SET
user_id='$user_id',
check_in='$check_in',
check_out='$check_out',
attendance_date='$attendance_date',
status='$status'

WHERE id=$id";

$result = mysqli_query($conn, $sql);

if($result){
    header("Location: index.php?updated=1");
    exit();
} else {
    die("Error: " . mysqli_error($conn));
}
?>