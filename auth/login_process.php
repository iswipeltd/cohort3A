<?php
session_start();
include("../config/db.php");

$email = $_POST['email'];
$password = $_POST['password'];

// prevent empty submission crash
if(empty($email) || empty($password)){
    die("Please fill all fields");
}

// get user
$sql = "SELECT * FROM users WHERE email='$email' LIMIT 1";
$result = mysqli_query($conn, $sql);

if(mysqli_num_rows($result) == 1){

    $user = mysqli_fetch_assoc($result);

    // verify password
    if(password_verify($password, $user['password'])){

        // create session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['name'] = $user['full_name'];
        $_SESSION['role'] = $user['role'];

        // redirect based on role
        if($user['role'] == 'admin'){
            header("Location: ../admin/index.php");
        } elseif($user['role'] == 'hr'){
           header("Location: ../hr/index.php");
        } else {
           header("Location: ../employee_dashboard/index.php");
        }

        exit();

    } else {
        echo "Incorrect password";
    }

} else {
    echo "User not found";
}
?>