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
|--------------------------------------------------------------------------
| GET FORM DATA
|--------------------------------------------------------------------------
*/

$id = $_POST['id'];

$user_id = $_POST['user_id'];

$basic_salary = $_POST['basic_salary'];

$allowance = $_POST['allowance'];

$deduction = $_POST['deduction'];

$pay_month = $_POST['pay_month'];

/*
|--------------------------------------------------------------------------
| UPDATE PAYROLL
|--------------------------------------------------------------------------
*/

$sql = "UPDATE payroll

        SET

        user_id='$user_id',
        basic_salary='$basic_salary',
        allowance='$allowance',
        deduction='$deduction',
        pay_month='$pay_month'

        WHERE id='$id'";

$result = mysqli_query($conn, $sql);

if($result){

    header("Location: index.php?updated=1");
    exit();

}else{

    echo "Update Error: " . mysqli_error($conn);

}
?>