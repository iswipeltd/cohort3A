<?php
session_start();
include("../includes/auth_check.php");
checkRole(['admin', 'hr']);
include("../config/db.php");

/*
|--------------------------------------------------------------------------
| ACCESS CONTROL (ADMIN ONLY)
|--------------------------------------------------------------------------
*/

if(!isset($_SESSION['user_id']) || !in_array    ($_SESSION['role'], ['admin', 'hr'])){
    header("Location: ../auth/login.php");
    exit();
}

/*
|--------------------------------------------------------------------------
| FETCH ALL TASKS (ADMIN VIEW)
|--------------------------------------------------------------------------
*/

$sql = "SELECT tasks.*, 
        u1.full_name AS assigned_to_name,
        u2.full_name AS assigned_by_name

        FROM tasks

        LEFT JOIN users u1 ON tasks.assigned_to = u1.id
        LEFT JOIN users u2 ON tasks.assigned_by = u2.id

        ORDER BY tasks.id DESC";

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

<h1 class="mt-4">Task Management (Admin)</h1>

<?php if($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'hr'): ?>
<a href="create_task.php" class="btn btn-primary mb-3">
    Assign Task
</a>
<?php endif; ?>

<div class="card mb-4">

<div class="card-body">

<table class="table table-bordered table-striped">

<thead class="table-dark">

<tr>
    <th>ID</th>
    <th>Title</th>
    <th>Assigned To</th>
    <th>Assigned By</th>
    <th>Status</th>
    <th>Action</th>
</tr>

</thead>

<tbody>

<?php if(mysqli_num_rows($result) > 0): ?>

    <?php while($row = mysqli_fetch_assoc($result)): ?>

    <tr>

        <td><?php echo $row['id']; ?></td>
        <td><?php echo $row['title']; ?></td>
        <td><?php echo $row['assigned_to_name']; ?></td>
        <td><?php echo $row['assigned_by_name']; ?></td>

        <td>
            <?php echo ucfirst($row['status']); ?>
        </td>

        <td>

            <!-- EDIT -->
             <?php if($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'hr'): ?>
            <a href="edit_task.php?id=<?php echo $row['id']; ?>" 
               class="btn btn-primary btn-sm">
                Edit
            </a>
            <?php endif; ?>

            <!-- DELETE -->
             <?php if($_SESSION['role'] == 'admin'): ?>
            <a href="delete_task.php?id=<?php echo $row['id']; ?>" 
               class="btn btn-danger btn-sm"
               onclick="return confirm('Delete this task?')">
                Delete
            </a>
            <?php endif; ?>

        </td>

    </tr>

    <?php endwhile; ?>

<?php else: ?>

    <tr>
        <td colspan="6" class="text-center text-danger">
            No tasks available
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