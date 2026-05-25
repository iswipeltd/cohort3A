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
| SUBMIT LEAVE
|--------------------------------------------------------------------------
*/
if(isset($_POST['submit_leave'])){

    $leave_type = $_POST['leave_type'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $reason = $_POST['reason'];

    $sql = "INSERT INTO leaves
    (user_id, leave_type, start_date, end_date, reason, status)

    VALUES
    ('$user_id', '$leave_type', '$start_date', '$end_date', '$reason', 'pending')";

    $insert = mysqli_query($conn, $sql);

    if($insert){
        header("Location: leave.php?success=1");
        exit();
    } else {
        die("Insert Failed: " . mysqli_error($conn));
    }
}

/*
|--------------------------------------------------------------------------
| FETCH EMPLOYEE LEAVES
|--------------------------------------------------------------------------
*/
$leaveQuery = mysqli_query($conn, "
    SELECT *
    FROM leaves
    WHERE user_id='$user_id'
    ORDER BY id DESC
");
?>

<?php include("../includes/header.php"); ?>

<div id="layoutSidenav">

<?php include("../includes/sidebar.php"); ?>

<div id="layoutSidenav_content">

<main>

<div class="container-fluid px-4">

<h1 class="mt-4">My Leave Requests</h1>

<?php if(isset($_GET['success'])): ?>

<div class="alert alert-success">
    Leave request submitted successfully
</div>

<?php endif; ?>

<!-- LEAVE FORM -->

<div class="card mb-4">

<div class="card-header">
Submit Leave Request
</div>

<div class="card-body">

<form method="POST">

<div class="mb-3">
<label>Leave Type</label>

<select name="leave_type" class="form-control" required>

<option value="">Select Leave Type</option>

<option value="Annual Leave">Annual Leave</option>

<option value="Sick Leave">Sick Leave</option>

<option value="Casual Leave">Casual Leave</option>

<option value="Maternity Leave">Maternity Leave</option>

</select>

</div>

<div class="mb-3">
<label>Start Date</label>
<input type="date" name="start_date" class="form-control" required>
</div>

<div class="mb-3">
<label>End Date</label>
<input type="date" name="end_date" class="form-control" required>
</div>

<div class="mb-3">
<label>Reason</label>
<textarea name="reason" class="form-control" rows="4" required></textarea>
</div>

<button type="submit" name="submit_leave" class="btn btn-primary">
Submit Leave
</button>

</form>

</div>

</div>

<!-- LEAVE HISTORY -->

<div class="card mb-4">

<div class="card-header">
My Leave History
</div>

<div class="card-body">

<table class="table table-bordered">

<thead>

<tr>
<th>Leave Type</th>
<th>Start Date</th>
<th>End Date</th>
<th>Status</th>
</tr>

</thead>

<tbody>

<?php if(mysqli_num_rows($leaveQuery) > 0): ?>

<?php while($leaveData = mysqli_fetch_assoc($leaveQuery)): ?>

<tr>

<td><?php echo $leaveData['leave_type']; ?></td>

<td><?php echo $leaveData['start_date']; ?></td>

<td><?php echo $leaveData['end_date']; ?></td>

<td>
<?php echo ucfirst($leaveData['status']); ?>
</td>

</tr>

<?php endwhile; ?>

<?php else: ?>

<tr>
<td colspan="4" class="text-center text-danger">
No leave requests found
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