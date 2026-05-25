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
| FETCH LEAVE REPORT DATA
|--------------------------------------------------------------------------
*/

$sql = "SELECT 
            leaves.*,
            users.full_name,
            departments.department_name

        FROM leaves

        LEFT JOIN users
        ON leaves.user_id = users.id

        LEFT JOIN departments
        ON users.department_id = departments.id

        ORDER BY leaves.id DESC";

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

<h1 class="mt-4">Leave Report</h1>

<div class="card mb-4">

<div class="card-body">
    
    <?php if($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'hr'): ?>
    <a href="../reports/leave_report_pdf.php"
       class="btn btn-primary mb-3">
        Download Leave Report
    </a>
<?php endif; ?>

<table class="table table-bordered table-striped">

<thead class="table-dark">

<tr>

<th>ID</th>
<th>Employee</th>
<th>Department</th>
<th>Leave Type</th>
<th>Start Date</th>
<th>End Date</th>
<th>Status</th>
<th>Reason</th>

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
<?php echo $row['leave_type']; ?>
</td>

<td>
<?php echo $row['start_date']; ?>
</td>

<td>
<?php echo $row['end_date']; ?>
</td>

<td>

<?php if($row['status'] == 'approved'): ?>

<span class="badge bg-success">
    Approved
</span>

<?php elseif($row['status'] == 'pending'): ?>

<span class="badge bg-warning">
    Pending
</span>

<?php else: ?>

<span class="badge bg-danger">
    Rejected
</span>

<?php endif; ?>

</td>

<td>
<?php echo $row['reason']; ?>
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