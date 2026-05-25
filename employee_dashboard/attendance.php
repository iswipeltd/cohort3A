<?php
session_start();

include("../includes/auth_check.php");
checkRole(['employee']);

include("../config/db.php");

/*
|--------------------------------------------------------------------------
| GET LOGGED IN USER
|--------------------------------------------------------------------------
*/
$user_id = $_SESSION['user_id'];

/*
|--------------------------------------------------------------------------
| FETCH EMPLOYEE ATTENDANCE
|--------------------------------------------------------------------------
*/
$sql = "
    SELECT attendance.*, users.full_name

    FROM attendance

    LEFT JOIN users
    ON attendance.user_id = users.id

    WHERE attendance.user_id='$user_id'

    ORDER BY attendance.id DESC
";

$result = mysqli_query($conn, $sql);

if(!$result){
    die("Query Failed: " . mysqli_error($conn));
}
?>

<?php include("../includes/header.php"); ?>

<div id="layoutSidenav">

<?php include("../includes/sidebar.php"); ?>

<div id="layoutSidenav_content">

<main>

<div class="container-fluid px-4">

<h1 class="mt-4">My Attendance</h1>

<div class="card mb-4">

<div class="card-header">
Attendance Records
</div>

<div class="card-body">

<table class="table table-bordered table-striped">

<thead class="table-dark">

<tr>
    <th>ID</th>
    <th>Date</th>
    <th>Status</th>
</tr>

</thead>

<tbody>

<?php if(mysqli_num_rows($result) > 0): ?>

<?php while($attendanceData = mysqli_fetch_assoc($result)): ?>

<tr>

<td><?php echo $attendanceData['id']; ?></td>

<td><?php echo $attendanceData['date']; ?></td>

<td>
    <?php echo ucfirst($attendanceData['status']); ?>
</td>

</tr>

<?php endwhile; ?>

<?php else: ?>

<tr>

<td colspan="3" class="text-center text-danger">
No attendance records found
</td>

</tr>

<?php endif; ?>

</tbody>

</table>

</div>

</div>

</div>

</main>

<?php include("../includes/footer.php"); ?>

</div>
</div>