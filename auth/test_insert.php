<?php
include("../config/db.php");

// $full_name = "Admin User";
// $email = "admin@gmail.com";
// $role = "admin";
// $password = password_hash("12345678", PASSWORD_DEFAULT);

// $sql = "INSERT INTO users (full_name, email, password, role)
// VALUES ('$full_name', '$email', '$password', '$role')";

if(mysqli_query($conn, $sql)){
    echo "Admin Created Successfully";
} else {
    die("Error: " . mysqli_error($conn));
}
?>