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
| FETCH LEAVES WITH USER DATA
|--------------------------------------------------------------------------
*/

$search = "";

if(isset($_GET['search'])){
    $search = mysqli_real_escape_string($conn, $_GET['search']);
}

$sql = "SELECT 
            leaves.*,
            users.full_name,
            departments.department_name

        FROM leaves

        LEFT JOIN users
        ON leaves.user_id = users.id

        LEFT JOIN departments
        ON users.department_id = departments.id

        WHERE
            users.full_name LIKE '%$search%'
            OR leaves.leave_type LIKE '%$search%'
            OR leaves.status LIKE '%$search%'
            OR leaves.start_date LIKE '%$search%'
            OR leaves.end_date LIKE '%$search%'

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

                <h1 class="mt-4">Leave Management</h1>

                <?php if(isset($_GET['success'])): ?>
                    <div class="alert alert-success">
                        Leave submitted successfully
                    </div>
                <?php endif; ?>

                <?php if(isset($_GET['approved'])): ?>
                    <div class="alert alert-success">
                        Leave Approved Successfully
                    </div>
                <?php endif; ?>

                <?php if(isset($_GET['rejected'])): ?>
                    <div class="alert alert-danger">
                        Leave Rejected
                    </div>
                <?php endif; ?>

                <?php if($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'hr'): ?>

                <a href="add_leave.php" class="btn btn-primary mb-3">
                    Apply Leave
                </a>
                <?php endif; ?>
                <form method="GET" class="mb-3">

                    <div class="row">

                        <div class="col-md-4">

                            <input type="text"
                                   name="search"
                                   class="form-control"
                                   placeholder="Search leaves..."
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
                                    <th>Type</th>
                                    <th>Reason</th>
                                    <th>Start Date</th>
                                    <th>End Date</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>

                            <tbody>

                            <?php while($row = mysqli_fetch_assoc($result)): ?>

                                <tr>
                                    <td><?php echo $row['id']; ?></td>

                                    <td><?php echo $row['full_name'] ?? 'Unknown'; ?></td>

                                    <td><?php echo $row['department_name'] ?? 'N/A'; ?></td>

                                    <td><?php echo $row['leave_type']; ?></td>

                                    <td><?php echo $row['reason']; ?></td>

                                    <td><?php echo $row['start_date']; ?></td>

                                    <td><?php echo $row['end_date']; ?></td>

                                    <td>
                                        <?php if(strtolower($row['status']) == 'pending'): ?>
                                            <span class="badge bg-warning">Pending</span>

                                        <?php elseif(strtolower($row['status']) == 'approved'): ?>
                                            <span class="badge bg-success">Approved</span>

                                        <?php else: ?>
                                            <span class="badge bg-danger">Rejected</span>

                                        <?php endif; ?>
                                    </td>

                                    <td style="white-space: nowrap;">

                                        <?php if(strtolower($row['status']) == 'pending'): ?>

                                            <a href="approve_leave.php?id=<?php echo $row['id']; ?>"
                                               class="btn btn-success btn-sm me-1"
                                               onclick="return confirm('Approve this leave?')">
                                                Approve
                                            </a>

                                            <a href="reject_leave.php?id=<?php echo $row['id']; ?>"
                                               class="btn btn-warning btn-sm me-1"
                                               onclick="return confirm('Reject this leave?')">
                                                Reject
                                            </a>

                                        <?php endif; ?>
                                        
                                        <?php if($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'hr'): ?>

                                        <a href="edit_leave.php?id=<?php echo $row['id']; ?>"
                                           class="btn btn-primary btn-sm me-1">
                                            Edit
                                        </a>
                                        <?php endif; ?>


                                        <?php if($_SESSION['role'] == 'admin'): ?>
                                        <a href="delete_leave.php?id=<?php echo $row['id']; ?>"
                                           class="btn btn-danger btn-sm"
                                           onclick="return confirm('Delete this leave?')">
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