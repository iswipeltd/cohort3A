<?php
session_start();
include("../includes/auth_check.php");
checkRole(['admin']);

if(!isset($_SESSION['user_id'])){
    header("Location: ../auth/login.php");
    exit();
}

include("../config/db.php");

/*
|------------------------------------------------------------
| GET TASK ID
|------------------------------------------------------------
*/

$id = $_GET['id'];

/*
|------------------------------------------------------------
| DELETE TASK
|------------------------------------------------------------
*/

$sql = "DELETE FROM tasks WHERE id='$id'";

$result = mysqli_query($conn, $sql);

if(!$result){
    die("Delete Failed: " . mysqli_error($conn));
}

/*
|------------------------------------------------------------
| REDIRECT
|------------------------------------------------------------
*/

header("Location: index.php?deleted=1");
exit();
?>