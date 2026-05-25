<?php
session_start();
include("../includes/auth_check.php");
checkRole(['admin']);

if(!isset($_SESSION['user_id'])){
    header("Location: ../auth/login.php");
    exit();
}

include("../config/db.php");

$full_name = $_POST['full_name'];
$email = $_POST['email'];
$password = password_hash($_POST['password'], PASSWORD_DEFAULT);
$role = $_POST['role'];


// check if email already exists
$check_email = "SELECT * FROM users WHERE email='$email'";
$result = mysqli_query($conn, $check_email);

if(mysqli_num_rows($result) > 0){
    die("Email already exists");
}


// insert employee
$sql = "INSERT INTO users(full_name, email, password, role)
VALUES('$full_name', '$email', '$password', '$role')";

if(mysqli_query($conn, $sql)){
$_SESSION['success'] = "Employee Added Successfully!";

header("Location: add_employee.php");
exit();

} else {

    echo "Error: " . mysqli_error($conn);

}
?>