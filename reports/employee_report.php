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
| FETCH EMPLOYEE REPORT DATA
|--------------------------------------------------------------------------
*/

$sql = "SELECT 
            users.*,
            departments.department_name

        FROM users

        LEFT JOIN departments
        ON users.department_id = departments.id

        ORDER BY users.id DESC";

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

<h1 class="mt-4">Employee Report</h1>

<div class="card mb-4">

<div class="card-body">
    
    <?php if($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'hr'): ?>
    <a href="../reports/employee_report_pdf.php"
       class="btn btn-success mb-3">
        Download Employee Report
    </a>
<?php endif; ?>

<table class="table table-bordered table-striped">

<thead class="table-dark">

<tr>
    <th>ID</th>
    <th>Full Name</th>
    <th>Email</th>
    <th>Role</th>
    <th>Department</th>
    <th>Created At</th>
</tr>

</thead>

<tbody>

<?php while($row = mysqli_fetch_assoc($result)): ?>

<tr>

<td><?php echo $row['id']; ?></td>

<td><?php echo $row['full_name']; ?></td>

<td><?php echo $row['email']; ?></td>

<td>
    <?php echo ucfirst($row['role']); ?>
</td>

<td>
<?php echo $row['department_name'] ?? 'N/A'; ?>
</td>

<td>
<?php echo $row['created_at']; ?>
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