<?php
session_start();

include("../includes/auth_check.php");
checkRole(['employee']);

include("../config/db.php");

$user_id = $_SESSION['user_id'];

/*
|--------------------------------------------------------------------------
| MY ATTENDANCE
|--------------------------------------------------------------------------
*/
$attendance_query = mysqli_query($conn, "
    SELECT COUNT(*) AS total_attendance
    FROM attendance
    WHERE user_id='$user_id'
");

$attendance = mysqli_fetch_assoc($attendance_query);

/*
|--------------------------------------------------------------------------
| MY LEAVE REQUESTS
|--------------------------------------------------------------------------
*/
$leave_query = mysqli_query($conn, "
    SELECT COUNT(*) AS total_leave
    FROM leaves
    WHERE user_id='$user_id'
");

$leave = mysqli_fetch_assoc($leave_query);

/*
|--------------------------------------------------------------------------
| MY TASKS
|--------------------------------------------------------------------------
*/
$task_query = mysqli_query($conn, "
    SELECT COUNT(*) AS total_tasks
    FROM tasks
    WHERE assigned_to='$user_id'
");

$task = mysqli_fetch_assoc($task_query);

/*
|--------------------------------------------------------------------------
| MY PAYROLL
|--------------------------------------------------------------------------
*/
$payroll_query = mysqli_query($conn, "
    SELECT COUNT(*) AS total_payroll
    FROM payroll
    WHERE user_id='$user_id'
");

$payroll = mysqli_fetch_assoc($payroll_query);


$user_id = $_SESSION['user_id'];

/*
|--------------------------------------------------------------------------
| RECENT TASKS
|--------------------------------------------------------------------------
*/
$taskQuery = mysqli_query($conn, "
    SELECT title, status
    FROM tasks
    WHERE assigned_to='$user_id'
    ORDER BY id DESC
    LIMIT 5
");

/*
|--------------------------------------------------------------------------
| RECENT LEAVES
|--------------------------------------------------------------------------
*/
$leaveQuery = mysqli_query($conn, "
    SELECT leave_type, status, start_date
    FROM leaves
    WHERE user_id='$user_id'
    ORDER BY id DESC
    LIMIT 5
");
?>

<?php include("../includes/header.php"); ?>

<div id="layoutSidenav">

<?php include("../includes/sidebar.php"); ?>

<div id="layoutSidenav_content">

<main>

<div class="container-fluid px-4">

<h1 class="mt-4">Employee Dashboard</h1>

<div class="row">

<!-- MY ATTENDANCE -->
<div class="col-xl-3 col-md-6">
<div class="card bg-primary text-white mb-4">
<div class="card-body">
<h4><?php echo $attendance['total_attendance']; ?></h4>
My Attendance
</div>
</div>
</div>

<!-- MY LEAVE -->
<div class="col-xl-3 col-md-6">
<div class="card bg-warning text-white mb-4">
<div class="card-body">
<h4><?php echo $leave['total_leave']; ?></h4>
My Leave Requests
</div>
</div>
</div>

<!-- MY TASKS -->
<div class="col-xl-3 col-md-6">
<div class="card bg-success text-white mb-4">
<div class="card-body">
<h4><?php echo $task['total_tasks']; ?></h4>
My Tasks
</div>
</div>
</div>

<!-- MY PAYROLL -->
<div class="col-xl-3 col-md-6">
<div class="card bg-danger text-white mb-4">
<div class="card-body">
<h4><?php echo $payroll['total_payroll']; ?></h4>
My Payroll
</div>
</div>
</div>

</div>

<!-- WELCOME SECTION -->
<div class="card mb-4">
<div class="card-body">
<h4>Welcome, <?php echo $_SESSION['name']; ?> 👋</h4>
<p>This is your personal dashboard. Here you can track your attendance, leave, tasks, and payroll.</p>
</div>
</div>


<div class="row">

<!-- RECENT TASKS -->

<div class="col-xl-6">

<div class="card mb-4">

<div class="card-header">
<i class="fas fa-tasks me-1"></i>
Recent Tasks
</div>

<div class="card-body">

<table class="table table-bordered">

<thead>

<tr>
<th>Task</th>
<th>Status</th>
</tr>

</thead>

<tbody>

<?php if(mysqli_num_rows($taskQuery) > 0): ?>

<?php while($taskData = mysqli_fetch_assoc($taskQuery)): ?>

<tr>

<td><?php echo $taskData['title']; ?></td>

<td><?php echo ucfirst($taskData['status']); ?></td>

</tr>

<?php endwhile; ?>

<?php else: ?>

<tr>
<td colspan="2" class="text-center text-danger">
No tasks assigned
</td>
</tr>

<?php endif; ?>

</tbody>

</table>

</div>

</div>

</div>

<!-- RECENT LEAVES -->

<div class="col-xl-6">

<div class="card mb-4">

<div class="card-header">
<i class="fas fa-calendar-days me-1"></i>
Recent Leave Requests
</div>

<div class="card-body">

<table class="table table-bordered">

<thead>

<tr>
<th>Leave Type</th>
<th>Status</th>
<th>Start Date</th>
</tr>

</thead>

<tbody>

<?php if(mysqli_num_rows($leaveQuery) > 0): ?>

<?php while($leaveData = mysqli_fetch_assoc($leaveQuery)): ?>

<tr>

<td><?php echo $leaveData['leave_type']; ?></td>

<td><?php echo ucfirst($leaveData['status']); ?></td>

<td><?php echo $leaveData['start_date']; ?></td>

</tr>

<?php endwhile; ?>

<?php else: ?>

<tr>
<td colspan="3" class="text-center text-danger">
No leave requests found
</td>
</tr>

<?php endif; ?>

</tbody>

</table>

</div>

</div>

</div>

</div>

</div>

</main>

<?php include("../includes/footer.php"); ?>

</div>
</div>