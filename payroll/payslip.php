<?php
session_start();
include("../includes/auth_check.php");
checkRole(['admin','hr']);


if(!isset($_SESSION['user_id'])){
    header("Location: ../auth/login.php");
    exit();
}

include("../config/db.php");

if(!isset($_GET['id'])){
    die("Payroll ID missing");
}

$id = intval($_GET['id']);

/*
|--------------------------------------------------------------------------
| FETCH PAYSLIP DATA
|--------------------------------------------------------------------------
*/

$sql = "SELECT 
            payroll.*,
            users.full_name,
            users.email,
            departments.department_name

        FROM payroll

        LEFT JOIN users 
        ON payroll.user_id = users.id

        LEFT JOIN departments 
        ON users.department_id = departments.id

        WHERE payroll.id = $id";

$result = mysqli_query($conn, $sql);

if(!$result){
    die("Query Error: " . mysqli_error($conn));
}

$data = mysqli_fetch_assoc($result);

if(!$data){
    die("Payroll record not found");
}
?>

<?php include("../includes/header.php"); ?>

<div id="layoutSidenav">

    <?php include("../includes/sidebar.php"); ?>

    <div id="layoutSidenav_content">

        <main>

            <div class="container-fluid px-4">

                <h1 class="mt-4">Payroll Payslip</h1>

                <div class="card mb-4">

                    <div class="card-body" id="payslipArea">

                        <h3 class="text-center">HR SYSTEM PAYSLIP</h3>
                        <hr>

                        <p><strong>Employee:</strong> <?php echo $data['full_name']; ?></p>
                        <p><strong>Email:</strong> <?php echo $data['email']; ?></p>
                        <p><strong>Department:</strong> <?php echo $data['department_name'] ?? 'N/A'; ?></p>
                        <p><strong>Month:</strong> <?php echo $data['pay_month']; ?></p>

                        <hr>

                        <table class="table table-bordered">

                            <tr>
                                <td>Basic Salary</td>
                                <td>₦<?php echo number_format($data['basic_salary'],2); ?></td>
                            </tr>

                            <tr>
                                <td>Allowance</td>
                                <td>₦<?php echo number_format($data['allowance'],2); ?></td>
                            </tr>

                            <tr>
                                <td>Deduction</td>
                                <td>₦<?php echo number_format($data['deduction'],2); ?></td>
                            </tr>

                            <tr>
                                <th>Net Salary</th>
                                <th>₦<?php echo number_format($data['net_salary'],2); ?></th>
                            </tr>

                        </table>

                        <button class="btn btn-primary" onclick="window.print()">
                            Print Payslip
                        </button>

                    </div>

                </div>

            </div>

        </main>

<?php include("../includes/footer.php"); ?>

    </div>
</div>

<!-- PRINT STYLE -->
<style>
@media print {

    .sb-topnav,
    #layoutSidenav_nav,
    footer,
    button {
        display: none !important;
    }

    #layoutSidenav_content {
        margin: 0 !important;
        padding: 0 !important;
    }

    #payslipArea {
        box-shadow: none !important;
        border: none !important;
    }
}
</style>