<?php
session_start();
include("../includes/auth_check.php");
checkRole(['admin','hr']);
if(!isset($_SESSION['user_id'])){
    header("Location: ../auth/login.php");
    exit();
}

include("../config/db.php");

// Fetch departments
$sql = "SELECT * FROM departments ORDER BY id DESC";
$result = mysqli_query($conn, $sql);
?>

<?php include("../includes/header.php"); ?>

<div id="layoutSidenav">

    <?php include("../includes/sidebar.php"); ?>

    <div id="layoutSidenav_content">

        <main>
            <div class="container-fluid px-4">

                <h1 class="mt-4">Departments</h1>

                <?php if($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'hr'): ?>
                <a href="add_department.php" class="btn btn-primary mb-3">
                    Add Department
                </a>
                <?php endif; ?>

                <div class="card mb-4">
                    <div class="card-body">
                        <?php if(isset($_GET['deleted'])): ?>
                        <div class="alert alert-success">
                        Department deleted successfully!
                    </div>
                    <?php endif; ?>

                        <table class="table table-bordered">

                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Department Name</th>
                                    <th>Date Created</th>
                                    <th>Action</th>
                                </tr>
                            </thead>

                            <tbody>

                                <?php if(mysqli_num_rows($result) > 0): ?>

                                    <?php while($row = mysqli_fetch_assoc($result)): ?>

                                        <tr>
                                            <td><?php echo $row['id']; ?></td>
                                            <td><?php echo $row['department_name']; ?></td>
                                            <td><?php echo $row['created_at']; ?></td>

                                            <td>

                                            <?php if($_SESSION['role'] == 'admin'): ?>
                                                <a href="delete_department.php?id=<?php echo $row['id']; ?>"
                                                   class="btn btn-danger btn-sm"
                                                   onclick="return confirm('Delete this department?')">
                                                    Delete
                                                </a>
                                            <?php endif; ?>
                                            </td>
                                        </tr>

                                    <?php endwhile; ?>

                                <?php else: ?>

                                    <tr>
                                        <td colspan="4" class="text-center">
                                            No departments found
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