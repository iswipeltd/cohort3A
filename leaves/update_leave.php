<?php
include("../config/db.php");

$id = $_POST['id'];
$leave_type = $_POST['leave_type'];
$start_date = $_POST['start_date'];
$end_date = $_POST['end_date'];
$reason = $_POST['reason'];

$sql = "UPDATE leaves SET 
        leave_type='$leave_type',
        start_date='$start_date',
        end_date='$end_date',
        reason='$reason'
        WHERE id=$id";

$result = mysqli_query($conn, $sql);

if($result){
    header("Location: index.php?updated=1");
    exit();
}else{
    echo "Error: " . mysqli_error($conn);
}
?>