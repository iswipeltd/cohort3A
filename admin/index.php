<?php
session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: ../auth/login.php");
    exit();
}

include("../config/db.php");

// get user
$name = $_SESSION['name'] ?? '';
$role = $_SESSION['role'] ?? '';

// counts
$totalEmployees = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT COUNT(*) as total FROM users"))['total'];

$totalAdmins = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT COUNT(*) as total FROM users WHERE role='admin'"))['total'];

$totalHR = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT COUNT(*) as total FROM users WHERE role='hr'"))['total'];

$totalStaff = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT COUNT(*) as total FROM users WHERE role='employee'"))['total'];


/*
|--------------------------------------------------------------------------
| TOTAL DEPARTMENTS
|--------------------------------------------------------------------------
*/

$department_query = "SELECT COUNT(*) AS total_departments FROM departments";

$department_result = mysqli_query($conn, $department_query);

$department_data = mysqli_fetch_assoc($department_result);

$total_departments = $department_data['total_departments'];

/*
|--------------------------------------------------------------------------
| TOTAL PAYROLL RECORDS
|--------------------------------------------------------------------------
*/

$payroll_query = "SELECT COUNT(*) AS total_payroll FROM payroll";

$payroll_result = mysqli_query($conn, $payroll_query);

$payroll_data = mysqli_fetch_assoc($payroll_result);

$total_payroll = $payroll_data['total_payroll'];

/*
|--------------------------------------------------------------------------
| TOTAL SALARY PAYOUT
|--------------------------------------------------------------------------
*/

$salary_query = "SELECT SUM(net_salary) AS total_salary FROM payroll";

$salary_result = mysqli_query($conn, $salary_query);

$salary_data = mysqli_fetch_assoc($salary_result);

$total_salary = $salary_data['total_salary'] ?? 0;



// recent employees (ONLY ONE QUERY)
$recent_result = mysqli_query($conn,
    "SELECT * FROM users ORDER BY id DESC LIMIT 5");
?>

<?php include("../includes/header.php"); ?>

<div id="layoutSidenav">

<?php include("../includes/sidebar.php"); ?>

<div id="layoutSidenav_content">

<main>
<div class="container-fluid px-4">

    <h1 class="mt-4">Dashboard</h1>

    <!-- CARDS -->
    <div class="row">

        <div class="col-xl-3 col-md-6">
            <div class="card bg-primary text-white mb-4">
                <div class="card-body">Total Employees: <?php echo $totalEmployees; ?></div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card bg-warning text-white mb-4">
                <div class="card-body">HR Users: <?php echo $totalHR; ?></div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card bg-success text-white mb-4">
                <div class="card-body">Admins: <?php echo $totalAdmins; ?></div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card bg-danger text-white mb-4">
                <div class="card-body">Staff: <?php echo $totalStaff; ?></div>
            </div>
        </div>


        <!-- TOTAL DEPARTMENTS -->
              <div class="col-xl-3 col-md-6">

               <div class="card bg-primary text-white mb-4">

        <div class="card-body">

            Total Departments:
            <?php echo $total_departments; ?>

        </div>

      </div>

       </div> 


       <!-- TOTAL PAYROLL -->
<div class="col-xl-3 col-md-6">

    <div class="card bg-success text-white mb-4">

        <div class="card-body">

            Payroll Records:
            <?php echo $total_payroll; ?>

        </div>

    </div>

</div>




<!-- TOTAL SALARY -->
<div class="col-xl-3 col-md-6">

    <div class="card bg-dark text-white mb-4">

        <div class="card-body">

            Total Salary Payout:
            ₦<?php echo number_format($total_salary, 2); ?>

        </div>

    </div>

</div>







    </div>

    <!-- RECENT EMPLOYEES -->
    <div class="card mb-4">

        <div class="card-header">
            <i class="fas fa-users me-1"></i>
            Recent Employees
        </div>

        <div class="card-body">

            <table class="table table-bordered">

                <thead class="table-dark">
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Date Joined</th>
                    </tr>
                </thead>

                <tbody>

                <?php if(mysqli_num_rows($recent_result) > 0): ?>

                    <?php while($row = mysqli_fetch_assoc($recent_result)): ?>
                        <tr>
                            <td><?php echo $row['full_name']; ?></td>
                            <td><?php echo $row['email']; ?></td>
                            <td><?php echo ucfirst($row['role']); ?></td>
                            <td><?php echo $row['created_at']; ?></td>
                        </tr>
                    <?php endwhile; ?>

                <?php else: ?>

                    <tr>
                        <td colspan="4" class="text-center">No employees found</td>
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