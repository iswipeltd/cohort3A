<?php
session_start();
include("../includes/auth_check.php");
checkRole(['admin', 'hr']);



if(!isset($_SESSION['user_id'])){
    header("Location: ../auth/login.php");
    exit();
}

include("../config/db.php");

$users = mysqli_query($conn, "SELECT * FROM users ORDER BY full_name ASC");
?>

<?php include("../includes/header.php"); ?>

<div id="layoutSidenav">

<?php include("../includes/sidebar.php"); ?>

<div id="layoutSidenav_content">

<main>

<div class="container-fluid px-4">

<h1 class="mt-4">Add Attendance</h1>

<div class="card mb-4">

<div class="card-body">

<form action="store_attendance.php" method="POST">

<div class="mb-3">
<label>Employee</label>

<select name="user_id" class="form-control" required>

<option value="">Select Employee</option>

<?php while($user = mysqli_fetch_assoc($users)): ?>

<option value="<?php echo $user['id']; ?>">
    <?php echo $user['full_name']; ?>
</option>

<?php endwhile; ?>

</select>
</div>

<div class="mb-3">
<label>Check In</label>
<input type="time" name="check_in" class="form-control" required>
</div>

<div class="mb-3">
<label>Check Out</label>
<input type="time" name="check_out" class="form-control">
</div>

<div class="mb-3">
<label>Date</label>
<input type="date" name="attendance_date" class="form-control" required>
</div>

<div class="mb-3">
<label>Status</label>

<select name="status" class="form-control">

<option value="Present">Present</option>
<option value="Late">Late</option>
<option value="Absent">Absent</option>

</select>
</div>

<button type="submit" class="btn btn-primary">
    Save Attendance
</button>

</form>

</div>
</div>
</div>
</main>

<?php include("../includes/footer.php"); ?>