<?php
session_start();
include("../includes/auth_check.php");
checkRole(['admin', 'hr']);
include("../config/db.php");

$title = $_POST['title'];
$description = $_POST['description'];
$assigned_to = $_POST['assigned_to'];
$assigned_by = $_POST['assigned_by'];

$sql = "INSERT INTO tasks 
(title, description, assigned_to, assigned_by, status)
VALUES
('$title', '$description', '$assigned_to', '$assigned_by', 'pending')";

mysqli_query($conn, $sql);

header("Location: index.php?success=1");
exit();
?>