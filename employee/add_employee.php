<?php
session_start();
include("../includes/auth_check.php");
checkRole(['admin']);

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

include("../config/db.php");

if (isset($_POST['save'])) {

    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $role = $_POST['role'];
    $department_id = $_POST['department_id'];

    $password = password_hash("12345678", PASSWORD_DEFAULT);
    
    $sql = "INSERT INTO users (full_name, email, password, role, department_id)
        VALUES ('$full_name', '$email', '$password', '$role', '$department_id')";
    if (mysqli_query($conn, $sql)) {
        header("Location: add_employee.php?success=1");
        exit();
    }
}

$dept_query = "SELECT * FROM departments";
$dept_result = mysqli_query($conn, $dept_query);
?>

<?php include("../includes/header.php"); ?>

<div id="layoutSidenav">

    <?php include("../includes/sidebar.php"); ?>

    <div id="layoutSidenav_content">

        <main>
            <div class="container-fluid px-4">

                <h1 class="mt-4">Add Employee</h1>

                <?php if (isset($_GET['success'])): ?>
                    <div class="alert alert-success">
                        Employee Added Successfully!
                    </div>
                <?php endif; ?>

                <div class="card mb-4">
                    <div class="card-body">

                        <form method="POST">

                            <div class="mb-3">
                                <label>Full Name</label>
                                <input type="text" name="full_name" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label>Email</label>
                                <input type="email" name="email" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label>Department</label>
                                <select name="department_id" class="form-control" required>
                                    <option value="">Select Department</option>
                                    <?php while ($dept_row = mysqli_fetch_assoc($dept_result)): ?>
                                        <option value="<?php echo $dept_row['id']; ?>">
                                            <?php echo $dept_row['department_name']; ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>

                            <div class="mb-3">                  
                                <label>Role</label>
                                <select name="role" class="form-control" required>
                                    <option value="employee">Employee</option>
                                    <option value="hr">HR</option>
                                    <option value="admin">Admin</option>
                                </select>
                            </div>

                            <button type="submit" name="save" class="btn btn-primary">
                                Save Employee
                            </button>

                        </form>

                    </div>
                </div>

            </div>
        </main>

<?php include("../includes/footer.php"); ?>

    </div>
</div>