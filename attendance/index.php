<?php
session_start();
include("../includes/auth_check.php");
checkRole(['admin','hr']);

if(!isset($_SESSION['user_id'])){
    header("Location: ../auth/login.php");
    exit();
}
$limit = 5; // number of records per page
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$start = ($page - 1) * $limit;

include("../config/db.php");

/*
|--------------------------------------------------------------------------
| FETCH ATTENDANCE WITH USER DATA
|--------------------------------------------------------------------------
*/

$search = "";

if(isset($_GET['search'])){
    $search = mysqli_real_escape_string($conn, $_GET['search']);
}

$sql = "SELECT 
            attendance.*,
            users.full_name,
            departments.department_name

        FROM attendance

        LEFT JOIN users
        ON attendance.user_id = users.id

        LEFT JOIN departments
        ON users.department_id = departments.id

        WHERE
            users.full_name LIKE '%$search%'
            OR attendance.status LIKE '%$search%'
            OR attendance.attendance_date LIKE '%$search%'

        ORDER BY id DESC
        LIMIT $start, $limit";

$result = mysqli_query($conn, $sql);
$totalResult = mysqli_query($conn, "SELECT COUNT(id) AS total FROM users");
$totalRow = mysqli_fetch_assoc($totalResult);

$totalRecords = $totalRow['total'];
$totalPages = ceil($totalRecords / $limit);

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

<h1 class="mt-4">Attendance Management</h1>

<?php if(isset($_GET['success'])): ?>

<div class="alert alert-success">
    Attendance Added Successfully
</div>

<?php endif; ?>
<?php if($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'hr'): ?>

<a href="add_attendance.php" class="btn btn-primary">
    Add Attendance
</a>

<?php endif; ?>


<form method="GET" class="mb-3">

<div class="row">

<div class="col-md-4">

<input type="text"
       name="search"
       class="form-control"
       placeholder="Search attendance..."
       value="<?php echo $search; ?>">

</div>

<div class="col-md-2">

<button type="submit" class="btn btn-primary">
    Search
</button>

</div>

</div>

</form>

<div class="card mb-4">

<div class="card-body">

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
    <th>Actions</th>
</tr>

</thead>

<tbody>

<?php while($row = mysqli_fetch_assoc($result)): ?>

<tr>

<td><?php echo $row['id']; ?></td>

<td><?php echo $row['full_name']; ?></td>

<td><?php echo $row['department_name'] ?? 'N/A'; ?></td>

<td><?php echo $row['check_in']; ?></td>

<td>
    <?php echo $row['check_out'] ?? 'N/A'; ?>
</td>

<td><?php echo $row['attendance_date']; ?></td>

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

<td>
<?php if($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'hr'): ?>

<a href="edit_attendance.php?id=<?php echo $row['id']; ?>"
   class="btn btn-primary btn-sm">
   Edit
</a>

<?php endif; ?>

<?php if($_SESSION['role'] == 'admin'): ?>

<a href="delete_attendance.php?id=<?php echo $row['id']; ?>"
   class="btn btn-danger btn-sm"
   onclick="return confirm('Delete this record?')">
   Delete
</a>

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