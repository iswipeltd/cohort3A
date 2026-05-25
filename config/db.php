<?php
$host = "localhost";
$user = "root";
$password = "";
$database = "hr_suite_db";

$conn = mysqli_connect(
    "zephyr.proxy.rlwy.net",
    "root",
    "QQTNGduqoNDquPCINkcVtfyujjLNapDE",
    "railway",
    46894
);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>