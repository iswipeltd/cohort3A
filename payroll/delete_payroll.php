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
|--------------------------------------------------------------------------
| CHECK PAYROLL ID
|--------------------------------------------------------------------------
*/

if(!isset($_GET['id'])){
    die("Payroll ID Missing");
}

$id = intval($_GET['id']);

/*
|--------------------------------------------------------------------------
| DELETE PAYROLL
|--------------------------------------------------------------------------
*/

$sql = "DELETE FROM payroll WHERE id='$id'";

$result = mysqli_query($conn, $sql);

if($result){

    header("Location: index.php?deleted=1");
    exit();

}else{

    echo "Delete Error: " . mysqli_error($conn);

}
?>