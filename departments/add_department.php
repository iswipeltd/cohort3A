<?php
session_start();
include("../includes/auth_check.php");
checkRole(['admin', 'hr']);

if(!isset($_SESSION['user_id'])){
    header("Location: ../auth/login.php");
    exit();
}

include("../config/db.php");

// Handle form submission
if(isset($_POST['save'])){

    $department_name = $_POST['department_name'];

    if(!empty($department_name)){

        $sql = "INSERT INTO departments (department_name)
                VALUES ('$department_name')";

        if(mysqli_query($conn, $sql)){
            header("Location: index.php?success=1");
            exit();
        } else {
            $error = "Error: " . mysqli_error($conn);
        }

    } else {
        $error = "Department name cannot be empty";
    }
}
?>

<?php include("../includes/header.php"); ?>

<div id="layoutSidenav">

    <?php include("../includes/sidebar.php"); ?>

    <div id="layoutSidenav_content">

        <main>
            <div class="container-fluid px-4">

                <h1 class="mt-4">Add Department</h1>

                <?php if(isset($error)): ?>
                    <div class="alert alert-danger">
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>

                <?php if(isset($_GET['success'])): ?>
                    <div class="alert alert-success">
                        Department added successfully!
                    </div>
                <?php endif; ?>

                <div class="card mb-4">
                    <div class="card-body">

                        <form method="POST">

                            <div class="mb-3">
                                <label>Department Name</label>
                                <input type="text"
                                       name="department_name"
                                       class="form-control"
                                       placeholder="Enter department name"
                                       required>
                            </div>

                            <button type="submit"
                                    name="save"
                                    class="btn btn-primary">
                                Save Department
                            </button>

                        </form>

                    </div>
                </div>

            </div>
        </main>

<?php include("../includes/footer.php"); ?>

    </div>
</div>