<?php
session_start();
include("../includes/auth_check.php");
checkRole(['admin', 'hr']);

if(!isset($_SESSION['user_id'])){
    header("Location: ../auth/login.php");
    exit();
}

include("../config/db.php");

/*
|--------------------------------------------------------------------------
| FETCH ALL USERS (HR + STAFF)
|--------------------------------------------------------------------------
*/

$users = mysqli_query($conn, "
    SELECT id, full_name, role 
    FROM users 
    ORDER BY full_name ASC
");
?>

<?php include("../includes/header.php"); ?>

<div id="layoutSidenav">

<?php include("../includes/sidebar.php"); ?>

<div id="layoutSidenav_content">

<main>

<div class="container-fluid px-4">

<h1 class="mt-4">Assign Task</h1>

<div class="card mb-4">

<div class="card-body">

<form action="store_task.php" method="POST">

<!-- TASK TITLE -->
<div class="mb-3">
<label>Task Title</label>
<input type="text" name="title" class="form-control" required>
</div>

<!-- DESCRIPTION -->
<div class="mb-3">
<label>Task Description</label>
<textarea name="description" class="form-control" rows="4"></textarea>
</div>

<!-- ASSIGN TO USER -->
<div class="mb-3">
<label>Assign To</label>

<select name="assigned_to" class="form-control" required>

<option value="">Select User</option>

<?php while($u = mysqli_fetch_assoc($users)): ?>

<option value="<?php echo $u['id']; ?>">
    <?php echo $u['full_name']; ?> (<?php echo $u['role']; ?>)
</option>

<?php endwhile; ?>

</select>

</div>

<!-- ASSIGNED BY (ADMIN SESSION) -->
<input type="hidden" name="assigned_by" value="<?php echo $_SESSION['user_id']; ?>">

<!-- SUBMIT -->
<button type="submit" class="btn btn-primary">
    Assign Task
</button>

</form>

</div>

</div>

</div>

</main>

<?php include("../includes/footer.php"); ?>

</div>
</div>