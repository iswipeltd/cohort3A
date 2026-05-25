<?php
session_start();
include("../includes/auth_check.php");
checkRole(['admin']);


include("../config/db.php");

$user_id = $_POST['user_id'];
$check_in = $_POST['check_in'];
$check_out = $_POST['check_out'];
$attendance_date = $_POST['attendance_date'];
$status = $_POST['status'];

$sql = "INSERT INTO attendance
(user_id, check_in, check_out, attendance_date, status)

VALUES

('$user_id', '$check_in', '$check_out', '$attendance_date', '$status')";

$result = mysqli_query($conn, $sql);

if($result){

    header("Location: index.php?success=1");
    exit();

} else {

    die("Error: " . mysqli_error($conn));
}
?>