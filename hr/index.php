<?php
session_start();
include("../includes/auth_check.php");
checkRole(['hr']);

include("../config/db.php");

/*
|--------------------------------------------------------------------------
| TOTAL EMPLOYEES
|--------------------------------------------------------------------------
*/

$employee_query = mysqli_query($conn, "
    SELECT COUNT(*) AS total_employees 
    FROM users
");

$employee_data = mysqli_fetch_assoc($employee_query);

/*
|--------------------------------------------------------------------------
| PENDING LEAVES
|--------------------------------------------------------------------------
*/

$leave_query = mysqli_query($conn, "
    SELECT COUNT(*) AS total_leaves 
    FROM leaves
    WHERE status='pending'
");

$leave_data = mysqli_fetch_assoc($leave_query);

/*
|--------------------------------------------------------------------------
| ATTENDANCE COUNT
|--------------------------------------------------------------------------
*/

$attendance_query = mysqli_query($conn, "
    SELECT COUNT(*) AS total_attendance
    FROM attendance
");

$attendance_data = mysqli_fetch_assoc($attendance_query);

/*
|--------------------------------------------------------------------------
| PAYROLL COUNT
|--------------------------------------------------------------------------
*/

$payroll_query = mysqli_query($conn, "
    SELECT COUNT(*) AS total_payroll
    FROM payroll
");

$payroll_data = mysqli_fetch_assoc($payroll_query);

/*
|--------------------------------------------------------------------------
| MY TASKS
|--------------------------------------------------------------------------
*/

$user_id = $_SESSION['user_id'];

$task_query = mysqli_query($conn, "
    SELECT COUNT(*) AS total_tasks
    FROM tasks
    WHERE assigned_to='$user_id'
");

$task_data = mysqli_fetch_assoc($task_query);
/*
|--------------------------------------------------------------------------
| RECENT EMPLOYEES
|--------------------------------------------------------------------------
*/

$recent_employee_query = mysqli_query($conn, "
    SELECT full_name, email, role
    FROM users
    ORDER BY id DESC
    LIMIT 5
");

/*
|--------------------------------------------------------------------------
| RECENT LEAVE REQUESTS
|--------------------------------------------------------------------------
*/

$recent_leave_query = mysqli_query($conn, "
    SELECT users.full_name, leaves.leave_type, leaves.status

    FROM leaves

    LEFT JOIN users 
    ON leaves.user_id = users.id

    ORDER BY leaves.id DESC
    LIMIT 5
");
/*
|--------------------------------------------------------------------------
| RECENT TASKS
|--------------------------------------------------------------------------
*/

$recent_task_query = mysqli_query($conn, "
    SELECT title, status
    FROM tasks
    WHERE assigned_to='$user_id'
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

<h1 class="mt-4">HR Dashboard</h1>

<div class="row">

<!-- TOTAL EMPLOYEES -->

<div class="col-xl-3 col-md-6">

<div class="card bg-primary text-white mb-4">

<div class="card-body">

<h4><?php echo $employee_data['total_employees']; ?></h4>

Total Employees

</div>

</div>

</div>

<!-- PENDING LEAVES -->

<div class="col-xl-3 col-md-6">

<div class="card bg-warning text-white mb-4">

<div class="card-body">

<h4><?php echo $leave_data['total_leaves']; ?></h4>

Pending Leaves

</div>

</div>

</div>

<!-- ATTENDANCE -->

<div class="col-xl-3 col-md-6">

<div class="card bg-success text-white mb-4">

<div class="card-body">

<h4><?php echo $attendance_data['total_attendance']; ?></h4>

Attendance Records

</div>

</div>

</div>

<!-- PAYROLL -->

<div class="col-xl-3 col-md-6">

<div class="card bg-danger text-white mb-4">

<div class="card-body">

<h4><?php echo $payroll_data['total_payroll']; ?></h4>

Payroll Records

</div>

</div>

</div>

</div>

<!-- MY TASKS CARD -->

<div class="row">

<div class="col-xl-3 col-md-6">

<div class="card bg-dark text-white mb-4">

<div class="card-body">

<h4><?php echo $task_data['total_tasks']; ?></h4>

My Tasks

</div>

</div>

</div>

</div>
<div class="row">

<!-- RECENT EMPLOYEES -->

<div class="col-xl-6">

<div class="card mb-4">

<div class="card-header">
<i class="fas fa-users me-1"></i>
Recent Employees
</div>

<div class="card-body">

<table class="table table-bordered">

<thead>

<tr>
<th>Name</th>
<th>Email</th>
<th>Role</th>
</tr>

</thead>

<tbody>

<?php while($employee = mysqli_fetch_assoc($recent_employee_query)): ?>

<tr>

<td><?php echo $employee['full_name']; ?></td>

<td><?php echo $employee['email']; ?></td>

<td><?php echo ucfirst($employee['role']); ?></td>

</tr>

<?php endwhile; ?>

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
<th>Employee</th>
<th>Leave Type</th>
<th>Status</th>
</tr>

</thead>

<tbody>

<?php while($leave = mysqli_fetch_assoc($recent_leave_query)): ?>

<tr>

<td><?php echo $leave['full_name']; ?></td>

<td><?php echo $leave['leave_type']; ?></td>

<td><?php echo ucfirst($leave['status']); ?></td>

</tr>

<?php endwhile; ?>

</tbody>

</table>

</div>

</div>

</div>

</div>

<!-- RECENT TASKS -->

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

<?php while($task = mysqli_fetch_assoc($recent_task_query)): ?>

<tr>

<td><?php echo $task['title']; ?></td>

<td><?php echo ucfirst($task['status']); ?></td>

</tr>

<?php endwhile; ?>

</tbody>

</table>

</div>

</div>
</div>

</main>

<?php include("../includes/footer.php"); ?>

</div>
</div>