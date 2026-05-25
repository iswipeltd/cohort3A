<?php
session_start();
include("../includes/auth_check.php");
checkRole(['admin', 'hr']);

if(!isset($_SESSION['user_id'])){
    header("Location: ../auth/login.php");
    exit();
}

include("../config/db.php");

if(!isset($_GET['id'])){
    die("Leave ID missing");
}

$id = intval($_GET['id']);

$sql = "SELECT * FROM leaves WHERE id=$id";
$result = mysqli_query($conn, $sql);

if(!$result){
    die("Query Error: " . mysqli_error($conn));
}

$data = mysqli_fetch_assoc($result);

if(!$data){
    die("Leave not found");
}
?>

<?php include("../includes/header.php"); ?>

<div id="layoutSidenav">

<?php include("../includes/sidebar.php"); ?>

<div id="layoutSidenav_content">

<main>

<div class="container-fluid px-4">

<h1 class="mt-4">Edit Leave</h1>

<div class="card mb-4">
<div class="card-body">

<form action="update_leave.php" method="POST">

<input type="hidden" name="id" value="<?php echo $data['id']; ?>">

<div class="mb-3">
    <label>Leave Type</label>
    <input type="text" name="leave_type" class="form-control"
           value="<?php echo $data['leave_type']; ?>">
</div>

<div class="mb-3">
    <label>Start Date</label>
    <input type="date" name="start_date" class="form-control"
           value="<?php echo $data['start_date']; ?>">
</div>

<div class="mb-3">
    <label>End Date</label>
    <input type="date" name="end_date" class="form-control"
           value="<?php echo $data['end_date']; ?>">
</div>

<div class="mb-3">
    <label>Reason</label>
    <textarea name="reason" class="form-control"><?php echo $data['reason']; ?></textarea>
</div>

<button class="btn btn-primary">Update Leave</button>

</form>

</div>
</div>

</div>

</main>

<?php include("../includes/footer.php"); ?>

</div>
</div>