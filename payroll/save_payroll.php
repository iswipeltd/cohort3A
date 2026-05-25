<?php
session_start();
include("../includes/auth_check.php");
checkRole(['admin', 'hr']);

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

$user_id = $_POST['user_id'];

$basic_salary = $_POST['basic_salary'];

$allowance = $_POST['allowance'];

$deduction = $_POST['deduction'];

$pay_month = $_POST['pay_month'];

/*
|--------------------------------------------------------------------------
| INSERT PAYROLL
|--------------------------------------------------------------------------
*/

$sql = "INSERT INTO payroll
        (user_id, basic_salary, allowance, deduction, pay_month)

        VALUES

        ('$user_id',
         '$basic_salary',
         '$allowance',
         '$deduction',
         '$pay_month')";

$result = mysqli_query($conn, $sql);

if($result){

    header("Location: index.php?success=1");
    exit();

}else{

    echo "Payroll Error: " . mysqli_error($conn);

}
?>