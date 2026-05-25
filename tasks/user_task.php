<?php
session_start();
include("../includes/auth_check.php");
checkRole(['admin','hr', 'employee']);
if(!isset($_SESSION['user_id'])){
    header("Location: ../auth/login.php");
    exit();
}

include("../config/db.php");

$user_id = $_SESSION['user_id'];
echo "Logged in user ID: " . $_SESSION['user_id'];

/*
|--------------------------------------------------------------------------
| FETCH TASKS ASSIGNED TO THIS USER
|--------------------------------------------------------------------------
*/

$sql = "SELECT tasks.*, 
        u.full_name AS assigned_by_name

        FROM tasks

        LEFT JOIN users u 
        ON tasks.assigned_by = u.id

        WHERE tasks.assigned_to = '$user_id'

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

<h1 class="mt-4">My Tasks</h1>

<div class="card mb-4">

<div class="card-body">

<table class="table table-bordered table-striped">

<thead class="table-dark">

<tr>
    <th>ID</th>
    <th>Title</th>
    <th>Description</th>
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
        <td><?php echo $row['description']; ?></td>
        <td><?php echo $row['assigned_by_name']; ?></td>

        <td>
            <?php echo ucfirst($row['status']); ?>
        </td>

        <td>

            <form action="update_task.php" method="POST">

                <input type="hidden" name="task_id" value="<?php echo $row['id']; ?>">

                <select name="status" class="form-control mb-2">

                    <option value="pending" <?php if($row['status']=="pending") echo "selected"; ?>>
                        Pending
                    </option>

                    <option value="in_progress" <?php if($row['status']=="in_progress") echo "selected"; ?>>
                        In Progress
                    </option>

                    <option value="completed" <?php if($row['status']=="completed") echo "selected"; ?>>
                        Completed
                    </option>

                </select>

                <button type="submit" class="btn btn-primary btn-sm">
                    Update
                </button>

            </form>

        </td>

    </tr>

    <?php endwhile; ?>

<?php else: ?>

    <tr>
        <td colspan="6" class="text-center text-danger">
            No tasks assigned to you yet
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