<?php
session_start();
include("../includes/auth_check.php");
checkRole(['admin','hr']);
if(!isset($_SESSION['user_id'])){
    header("Location: ../auth/login.php");
    exit();
}
include("../config/db.php");

/*
|------------------------------------------------------------
| REPORT COUNTS
|------------------------------------------------------------
*/

$employee_count = mysqli_num_rows(
    mysqli_query($conn, "SELECT * FROM users")
);

$payroll_count = mysqli_num_rows(
    mysqli_query($conn, "SELECT * FROM payroll")
);

$attendance_count = mysqli_num_rows(
    mysqli_query($conn, "SELECT * FROM attendance")
);

$leave_count = mysqli_num_rows(
    mysqli_query($conn, "SELECT * FROM leaves")
);
?>

<?php include("../includes/header.php"); ?>

<div id="layoutSidenav">

<?php include("../includes/sidebar.php"); ?>

<div id="layoutSidenav_content">

<main>

<div class="container-fluid px-4">

<h1 class="mt-4">Reports Dashboard</h1>

<div class="row">

<!-- EMPLOYEE REPORT -->

<div class="col-xl-3 col-md-6">

<div class="card bg-primary text-white mb-4">

<div class="card-body">
Employee Report
</div>

<div class="card-footer d-flex align-items-center justify-content-between">

<a class="small text-white stretched-link"
href="employee_report.php">

View Report

</a>

</div>
</div>
</div>

<!-- PAYROLL REPORT -->

<div class="col-xl-3 col-md-6">

<div class="card bg-success text-white mb-4">

<div class="card-body">
Payroll Report
</div>

<div class="card-footer d-flex align-items-center justify-content-between">

<a class="small text-white stretched-link"
href="payroll_report.php">

View Report

</a>

</div>
</div>
</div>

<!-- ATTENDANCE REPORT -->

<div class="col-xl-3 col-md-6">

<div class="card bg-warning text-white mb-4">

<div class="card-body">
Attendance Report
</div>

<div class="card-footer d-flex align-items-center justify-content-between">

<a class="small text-white stretched-link"
href="attendance_report.php">

View Report

</a>

</div>
</div>
</div>

<!-- LEAVE REPORT -->

<div class="col-xl-3 col-md-6">

<div class="card bg-danger text-white mb-4">

<div class="card-body">
Leave Report
</div>

<div class="card-footer d-flex align-items-center justify-content-between">

<a class="small text-white stretched-link"
href="leave_report.php">

View Report

</a>

</div>
</div>
</div>

</div>

<!-- QUICK REPORT OVERVIEW -->

<div class="card mb-4">

<div class="card-header">
    <i class="fas fa-chart-bar me-1"></i>
    Quick Report Overview
</div>

<div class="card-body">

<table class="table table-bordered table-striped">

<thead class="table-dark">

<tr>
    <th>Module</th>
    <th>Total Records</th>
</tr>

</thead>

<tbody>

<tr>
    <td>Total Employees</td>
    <td><?php echo $employee_count; ?></td>
</tr>

<tr>
    <td>Payroll Records</td>
    <td><?php echo $payroll_count; ?></td>
</tr>

<tr>
    <td>Attendance Records</td>
    <td><?php echo $attendance_count; ?></td>
</tr>

<tr>
    <td>Leave Requests</td>
    <td><?php echo $leave_count; ?></td>
</tr>

</tbody>

</table>

</div>

</div>

</div>

</main>

<?php include("../includes/footer.php"); ?>

</div>
</div>