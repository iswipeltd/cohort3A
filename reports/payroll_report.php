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
|--------------------------------------------------------------------------
| FETCH PAYROLL REPORT DATA
|--------------------------------------------------------------------------
*/

$sql = "SELECT 
            payroll.*,
            users.full_name,
            departments.department_name

        FROM payroll

        LEFT JOIN users
        ON payroll.user_id = users.id

        LEFT JOIN departments
        ON users.department_id = departments.id

        ORDER BY payroll.id DESC";

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

<h1 class="mt-4">Payroll Report</h1>

<div class="card mb-4">

<div class="card-body">
    
    <?php if($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'hr'): ?>
    <a href="payroll_report_pdf.php"
       class="btn btn-danger mb-3">

        Download Payroll Report (PDF)

    </a>
<?php endif; ?>

<table class="table table-bordered table-striped">

<thead class="table-dark">

<tr>

<th>ID</th>
<th>Employee</th>
<th>Department</th>
<th>Basic Salary</th>
<th>Allowance</th>
<th>Deduction</th>
<th>Net Salary</th>
<th>Month</th>

</tr>

</thead>

<tbody>

<?php while($row = mysqli_fetch_assoc($result)): ?>

<tr>

<td><?php echo $row['id']; ?></td>

<td><?php echo $row['full_name']; ?></td>

<td>
<?php echo $row['department_name'] ?? 'N/A'; ?>
</td>

<td>
₦<?php echo number_format($row['basic_salary'], 2); ?>
</td>

<td>
₦<?php echo number_format($row['allowance'], 2); ?>
</td>

<td>
₦<?php echo number_format($row['deduction'], 2); ?>
</td>

<td>
₦<?php echo number_format($row['net_salary'], 2); ?>
</td>

<td>
<?php echo $row['pay_month']; ?>
</td>

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