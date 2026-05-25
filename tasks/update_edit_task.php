<?php
session_start();
include("../includes/auth_check.php");
checkRole(['admin','hr']);

if(!isset($_SESSION['user_id'])){
    header("Location: ../auth/login.php");
    exit();
}

include("../config/db.php");

/*
|------------------------------------------------------------
| GET FORM DATA
|------------------------------------------------------------
*/

$id = $_POST['id'];
$title = $_POST['title'];
$description = $_POST['description'];
$assigned_to = $_POST['assigned_to'];
$status = $_POST['status'];

/*
|------------------------------------------------------------
| UPDATE TASK
|------------------------------------------------------------
*/

$sql = "UPDATE tasks SET

        title='$title',
        description='$description',
        assigned_to='$assigned_to',
        status='$status'

        WHERE id='$id'";

$result = mysqli_query($conn, $sql);

if(!$result){
    die("Update Failed: " . mysqli_error($conn));
}

/*
|------------------------------------------------------------
| REDIRECT
|------------------------------------------------------------
*/

header("Location: index.php?updated=1");
exit();
?>