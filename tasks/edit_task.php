<?php
session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: ../auth/login.php");
    exit();
}

include("../config/db.php");

$id = $_GET['id'];

/*
|------------------------------------------------------------
| FETCH TASK
|------------------------------------------------------------
*/

$task = mysqli_query($conn, "
    SELECT * FROM tasks WHERE id='$id'
");

$task_data = mysqli_fetch_assoc($task);

/*
|------------------------------------------------------------
| FETCH USERS
|------------------------------------------------------------
*/

$users = mysqli_query($conn, "
    SELECT * FROM users ORDER BY full_name ASC
");
?>

<?php include("../includes/header.php"); ?>

<div id="layoutSidenav">

<?php include("../includes/sidebar.php"); ?>

<div id="layoutSidenav_content">

<main>

<div class="container-fluid px-4">

<h1 class="mt-4">Edit Task</h1>

<div class="card mb-4">

<div class="card-body">

<form action="update_edit_task.php" method="POST">

<input type="hidden" name="id"
value="<?php echo $task_data['id']; ?>">

<!-- TITLE -->
<div class="mb-3">
<label>Task Title</label>

<input type="text"
name="title"
class="form-control"
value="<?php echo $task_data['title']; ?>"
required>
</div>

<!-- DESCRIPTION -->
<div class="mb-3">
<label>Description</label>

<textarea name="description"
class="form-control"
rows="4"><?php echo $task_data['description']; ?></textarea>
</div>

<!-- ASSIGN TO -->
<div class="mb-3">
<label>Assign To</label>

<select name="assigned_to" class="form-control">

<?php while($user = mysqli_fetch_assoc($users)): ?>

<option value="<?php echo $user['id']; ?>"

<?php
if($task_data['assigned_to'] == $user['id']){
    echo "selected";
}
?>>

<?php echo $user['full_name']; ?>

</option>

<?php endwhile; ?>

</select>

</div>

<!-- STATUS -->
<div class="mb-3">
<label>Status</label>

<select name="status" class="form-control">

<option value="pending"
<?php if($task_data['status']=="pending") echo "selected"; ?>>
Pending
</option>

<option value="in_progress"
<?php if($task_data['status']=="in_progress") echo "selected"; ?>>
In Progress
</option>

<option value="completed"
<?php if($task_data['status']=="completed") echo "selected"; ?>>
Completed
</option>

</select>

</div>

<button type="submit" class="btn btn-primary">
Update Task
</button>

</form>

</div>

</div>

</div>

</main>

<?php include("../includes/footer.php"); ?>

</div>
</div>