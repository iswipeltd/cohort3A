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

                <h1 class="mt-4">Add Payroll</h1>

                <div class="card mb-4">

                    <div class="card-body">

                        <form action="save_payroll.php" method="POST">

                            <!-- EMPLOYEE -->
                            <div class="mb-3">

                                <label>Select Employee</label>

                                <select name="user_id"
                                        class="form-control"
                                        required>

                                    <option value="">
                                        -- Select Employee --
                                    </option>

                                    <?php while($employee = mysqli_fetch_assoc($employee_result)): ?>

                                        <option value="<?php echo $employee['id']; ?>">

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
                                       required>

                            </div>

                            <!-- ALLOWANCE -->
                            <div class="mb-3">

                                <label>Allowance</label>

                                <input type="number"
                                       name="allowance"
                                       class="form-control"
                                       value="0">

                            </div>

                            <!-- DEDUCTION -->
                            <div class="mb-3">

                                <label>Deduction</label>

                                <input type="number"
                                       name="deduction"
                                       class="form-control"
                                       value="0">

                            </div>

                            <!-- MONTH -->
                            <div class="mb-3">

                                <label>Pay Month</label>

                                <input type="text"
                                       name="pay_month"
                                       class="form-control"
                                       placeholder="May 2026"
                                       required>

                            </div>

                            <button type="submit"
                                    class="btn btn-primary">

                                Save Payroll

                            </button>

                        </form>

                    </div>

                </div>

            </div>

        </main>

<?php include("../includes/footer.php"); ?>