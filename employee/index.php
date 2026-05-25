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

$search = "";

if(isset($_GET['search'])){
    $search = mysqli_real_escape_string($conn, $_GET['search']);
}


$sql = "SELECT 
            users.*,
            departments.department_name

        FROM users

        LEFT JOIN departments
        ON users.department_id = departments.id

        WHERE 
            users.full_name LIKE '%$search%'
            OR users.email LIKE '%$search%'
            OR users.role LIKE '%$search%'

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

                <h1 class="mt-4">Employee Management</h1>

                <?php if($_SESSION['role'] == 'admin'): ?>
                <a href="add_employee.php" class="btn btn-primary mb-3">
                    Add Employee
                </a>
                <?php endif; ?>

                <form method="GET" class="mb-3">

<div class="row">

<div class="col-md-4">

<input type="text"
       name="search"
       class="form-control"
       placeholder="Search employee..."
       value="<?php echo $search; ?>">

</div>

<div class="col-md-2">

<button type="submit"
        class="btn btn-primary">

    Search

</button>

</div>

</div>

</form>

                <table class="table table-bordered table-striped">

                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Full Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Date Created</th>
                            <th>Department</th>
                            <th>Actions</th>
                        </tr>
                    </thead>

                    <tbody>

                    <?php while($row = mysqli_fetch_assoc($result)): ?>

                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo $row['full_name']; ?></td>
                            <td><?php echo $row['email']; ?></td>
                            <td><?php echo ucfirst($row['role']); ?></td>
                            <td><?php echo $row['created_at']; ?></td>
                            <td><?php echo $row['department_name'] ?? 'N/A'; ?></td>

                            <td>
                                <?php if($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'hr'): ?>
                                <a href="edit_employee.php?id=<?php echo $row['id']; ?>"
                                   class="btn btn-warning btn-sm">
                                   Edit
                                </a>
                                <?php endif; ?>

                                <?php if($_SESSION['role'] == 'admin'): ?>
                                <a href="delete_employee.php?id=<?php echo $row['id']; ?>"
                                   class="btn btn-danger btn-sm"
                                   onclick="return confirm('Are you sure you want to delete this employee?')">
                                   Delete
                                </a>
                                <?php endif; ?>

                            </td>
                        </tr>

                    <?php endwhile; ?>

                    </tbody>

                </table>
                <nav>

<ul class="pagination">

<?php for($i = 1; $i <= $totalPages; $i++): ?>

<li class="page-item">

<a class="page-link"
   href="?page=<?php echo $i; ?>">

   <?php echo $i; ?>

</a>

</li>

<?php endfor; ?>

</ul>

</nav>

            </div>

        </main>

    </div>

</div>

<?php include("../includes/footer.php"); ?>