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
| FETCH ATTENDANCE REPORT DATA
|--------------------------------------------------------------------------
*/

$sql = "SELECT 
            attendance.*,
            users.full_name,
            departments.department_name

        FROM attendance

        LEFT JOIN users
        ON attendance.user_id = users.id

        LEFT JOIN departments
        ON users.department_id = departments.id

        ORDER BY attendance.id DESC";

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

<h1 class="mt-4">Attendance Report</h1>

<div class="card mb-4">

<div class="card-body">
    
    <?php if($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'hr'): ?>
    <a href="../reports/attendance_report_pdf.php"
       class="btn btn-danger mb-3">
        Download Attendance Report
    </a>
<?php endif; ?>

<table class="table table-bordered table-striped">

<thead class="table-dark">

<tr>

<th>ID</th>
<th>Employee</th>
<th>Department</th>
<th>Check In</th>
<th>Check Out</th>
<th>Date</th>
<th>Status</th>

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
<?php echo $row['check_in']; ?>
</td>

<td>
<?php echo $row['check_out'] ?? 'N/A'; ?>
</td>

<td>
<?php echo $row['attendance_date']; ?>
</td>

<td>

<?php if($row['status'] == 'Present'): ?>

<span class="badge bg-success">
    Present
</span>

<?php elseif($row['status'] == 'Late'): ?>

<span class="badge bg-warning">
    Late
</span>

<?php else: ?>

<span class="badge bg-danger">
    Absent
</span>

<?php endif; ?>

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