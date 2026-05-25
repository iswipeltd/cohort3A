<?php
session_start();
include("../includes/auth_check.php");
checkRole(['admin', 'hr']);

if(!isset($_SESSION['user_id'])){
    header("Location: ../auth/login.php");
    exit();
}

include("../config/db.php");
include("../includes/notification_helper.php");
sendNotification(
    $conn,
    $hr_id,
    "New leave request submitted by Employee ID: $user_id"
);

// fetch employees
$users = mysqli_query($conn, "SELECT * FROM users");
?>

<?php include("../includes/header.php"); ?>

<div id="layoutSidenav">

    <?php include("../includes/sidebar.php"); ?>

    <div id="layoutSidenav_content">

        <main>

            <div class="container-fluid px-4">

                <h1 class="mt-4">Apply Leave</h1>

                <div class="card mb-4">
                    <div class="card-body">

                        <form action="store_leave.php" method="POST">

                            <div class="mb-3">
                                <label>Employee</label>
                                <select name="user_id" class="form-control" required>
                                    <option value="">Select Employee</option>

                                    <?php while($u = mysqli_fetch_assoc($users)): ?>
                                        <option value="<?php echo $u['id']; ?>">
                                            <?php echo $u['full_name']; ?>
                                        </option>
                                    <?php endwhile; ?>

                                </select>
                            </div>

                            <div class="mb-3">
                                <label>Leave Type</label>
                                <select name="leave_type" class="form-control">
                                    <option>Annual</option>
                                    <option>Sick</option>
                                    <option>Casual</option>
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
                                <textarea name="reason" class="form-control"></textarea>
                            </div>

                            <button class="btn btn-primary" type="submit">
                                Submit Leave
                            </button>

                        </form>

                    </div>
                </div>

            </div>

        </main>

<?php include("../includes/footer.php"); ?>

    </div>
</div>