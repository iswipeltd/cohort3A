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
| CHECK PAYROLL ID
|--------------------------------------------------------------------------
*/

if(!isset($_GET['id'])){
    die("Payroll ID Missing");
}

$id = intval($_GET['id']);

/*
|--------------------------------------------------------------------------
| FETCH PAYROLL
|--------------------------------------------------------------------------
*/

$sql = "SELECT * FROM payroll WHERE id='$id'";

$result = mysqli_query($conn, $sql);

if(!$result){
    die("Query Failed: " . mysqli_error($conn));
}

$payroll = mysqli_fetch_assoc($result);

/*
|--------------------------------------------------------------------------
| FETCH EMPLOYEES
|--------------------------------------------------------------------------
*/

$employee_query = "SELECT * FROM users ORDER BY full_name ASC";

$employee_result = mysqli_query($conn, $employee_query);
?>

<?php include("../includes/header.php"); ?>

<div id="layoutSidenav">

    <?php include("../includes/sidebar.php"); ?>

    <div id="layoutSidenav_content">

        <main>

            <div class="container-fluid px-4">

                <h1 class="mt-4">Edit Payroll</h1>

                <div class="card mb-4">

                    <div class="card-body">

                        <form action="update_payroll.php"
                              method="POST">

                            <input type="hidden"
                                   name="id"
                                   value="<?php echo $payroll['id']; ?>">

                            <!-- EMPLOYEE -->
                            <div class="mb-3">

                                <label>Employee</label>

                                <select name="user_id"
                                        class="form-control"
                                        required>

                                    <?php while($employee = mysqli_fetch_assoc($employee_result)): ?>

                                        <option value="<?php echo $employee['id']; ?>"

                                        <?php
                                        if($payroll['user_id'] == $employee['id']){
                                            echo "selected";
                                        }
                                        ?>>

                                            <?php echo $employee['full_name']; ?>

                                        </option>

                                    <?php endwhile; ?>

                                </select>

                            </div>

                            <!-- BASIC SALARY -->
                            <div class="mb-3">

                                <label>Basic Salary</label>

                                <input type="number"
                                       name="basic_salary"
                                       class="form-control"
                                       value="<?php echo $payroll['basic_salary']; ?>"
                                       required>

                            </div>

                            <!-- ALLOWANCE -->
                            <div class="mb-3">

                                <label>Allowance</label>

                                <input type="number"
                                       name="allowance"
                                       class="form-control"
                                       value="<?php echo $payroll['allowance']; ?>">

                            </div>

                            <!-- DEDUCTION -->
                            <div class="mb-3">

                                <label>Deduction</label>

                                <input type="number"
                                       name="deduction"
                                       class="form-control"
                                       value="<?php echo $payroll['deduction']; ?>">

                            </div>

                            <!-- PAY MONTH -->
                            <div class="mb-3">

                                <label>Pay Month</label>

                                <input type="text"
                                       name="pay_month"
                                       class="form-control"
                                       value="<?php echo $payroll['pay_month']; ?>"
                                       required>

                            </div>

                            <button type="submit"
                                    class="btn btn-primary">

                                Update Payroll

                            </button>

                        </form>

                    </div>

                </div>

            </div>

        </main>

<?php include("../includes/footer.php"); ?>