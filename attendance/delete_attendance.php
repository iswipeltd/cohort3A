<?php
session_start();
include("../includes/auth_check.php");
checkRole(['admin']);


include("../config/db.php");

$id = intval($_GET['id']);

$sql = "DELETE FROM attendance WHERE id=$id";

$result = mysqli_query($conn, $sql);

if($result){
    header("Location: index.php?deleted=1");
    exit();
} else {
    die("Error: " . mysqli_error($conn));
}
?>