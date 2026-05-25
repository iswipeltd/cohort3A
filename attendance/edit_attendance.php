<?php
session_start();
include("../includes/auth_check.php");
checkRole(['admin', 'hr']);


if(!isset($_SESSION['user_id'])){
    header("Location: ../auth/login.php");
    exit();
}

include("../config/db.php");

$id = intval($_GET['id'] ?? 0);

$sql = "SELECT * FROM attendance WHERE id = $id";
$result = mysqli_query($conn, $sql);

if(!$result){
    die("Query Error: " . mysqli_error($conn));
}

$data = mysqli_fetch_assoc($result);

if(!$data){
    die("Record not found");
}

$users = mysqli_query($conn, "SELECT * FROM users");
?>

<?php include("../includes/header.php"); ?>

<div id="layoutSidenav">

<?php include("../includes/sidebar.php"); ?>

<div id="layoutSidenav_content">

<main>

<div class="container-fluid px-4">

<h1 class="mt-4">Edit Attendance</h1>

<div class="card mb-4">

<div class="card-body">

<form action="update_attendance.php" method="POST">

<input type="hidden" name="id" value="<?php echo $data['id']; ?>">

<div class="mb-3">
<label>Employee</label>

<select name="user_id" class="form-control">

<?php while($u = mysqli_fetch_assoc($users)): ?>

<option value="<?php echo $u['id']; ?>"
<?php if($u['id'] == $data['user_id']) echo 'selected'; ?>>

<?php echo $u['full_name']; ?>

</option>

<?php endwhile; ?>

</select>
</div>

<div class="mb-3">
<label>Check In</label>
<input type="time" name="check_in" class="form-control"
value="<?php echo $data['check_in']; ?>">
</div>

<div class="mb-3">
<label>Check Out</label>
<input type="time" name="check_out" class="form-control"
value="<?php echo $data['check_out']; ?>">
</div>

<div class="mb-3">
<label>Date</label>
<input type="date" name="attendance_date" class="form-control"
value="<?php echo $data['attendance_date']; ?>">
</div>

<div class="mb-3">
<label>Status</label>

<select name="status" class="form-control">

<option value="Present" <?php if($data['status']=="Present") echo "selected"; ?>>Present</option>

<option value="Late" <?php if($data['status']=="Late") echo "selected"; ?>>Late</option>

<option value="Absent" <?php if($data['status']=="Absent") echo "selected"; ?>>Absent</option>

</select>

</div>

<button type="submit" class="btn btn-primary">
Update Attendance
</button>

</form>

</div>
</div>

</div>

</main>

<?php include("../includes/footer.php"); ?>