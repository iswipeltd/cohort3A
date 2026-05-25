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
| FETCH EMPLOYEE PAYROLL
|--------------------------------------------------------------------------
*/
$sql = "
    SELECT payroll.*, users.full_name

    FROM payroll

    LEFT JOIN users
    ON payroll.user_id = users.id

    WHERE payroll.user_id='$user_id'

    ORDER BY payroll.id DESC
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

<h1 class="mt-4">My Payroll</h1>

<div class="card mb-4">

<div class="card-header">
Payroll Records
</div>

<div class="card-body">

<table class="table table-bordered table-striped">

<thead class="table-dark">

<tr>
    <th>ID</th>
    <th>Salary</th>
    <th>Bonus</th>
    <th>Deductions</th>
    <th>Net Salary</th>
    <th>Payment Date</th>
</tr>

</thead>

<tbody>

<?php if(mysqli_num_rows($result) > 0): ?>

<?php while($payrollData = mysqli_fetch_assoc($result)): ?>

<tr>

<td><?php echo $payrollData['id']; ?></td>

<td><?php echo $payrollData['basic_salary']; ?></td>

<td><?php echo $payrollData['allowance']; ?></td>

<td><?php echo $payrollData['deduction']; ?></td>

<td><?php echo $payrollData['net_salary']; ?></td>

<td><?php echo $payrollData['pay_month']; ?></td>

</tr>

<?php endwhile; ?>

<?php else: ?>

<tr>

<td colspan="6" class="text-center text-danger">
No payroll records found
</td>

</tr>

<?php endif; ?>

</tbody>

</table>

</div>

</div>

<a href="payslip_pdf.php" class="btn btn-danger btn-sm">
    Download My Payslip
</a>
</div>

</main>

<?php include("../includes/footer.php"); ?>

</div>
</div>