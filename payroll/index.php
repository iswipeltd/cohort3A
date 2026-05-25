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
| FETCH PAYROLL RECORDS
|--------------------------------------------------------------------------
*/

$search = "";

if(isset($_GET['search'])){
    $search = mysqli_real_escape_string($conn, $_GET['search']);
}

$sql = "SELECT 
            payroll.*,
            users.full_name,
            departments.department_name

        FROM payroll

        LEFT JOIN users
        ON payroll.user_id = users.id

        LEFT JOIN departments
        ON users.department_id = departments.id

        WHERE
            users.full_name LIKE '%$search%'
            OR payroll.pay_month LIKE '%$search%'
            OR departments.department_name LIKE '%$search%'

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

                <h1 class="mt-4">Payroll Management</h1>

                <?php if(isset($_GET['success'])): ?>

                    <div class="alert alert-success">

                        Payroll Added Successfully!

                    </div>

                <?php endif; ?>

                <?php if(isset($_GET['updated'])): ?>

                 <div class="alert alert-success">

                Payroll Updated Successfully!

                </div>

                 <?php endif; ?>


                 <?php if(isset($_GET['deleted'])): ?>

                <div class="alert alert-danger">

                Payroll Deleted Successfully!

                 </div>

                <?php endif; ?>

                <?php if($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'hr'): ?>
                <a href="add_payroll.php"
                   class="btn btn-primary mb-3">

                    Add Payroll

                </a>
                <?php endif; ?>


                <form method="GET" class="mb-3">

<div class="row">

<div class="col-md-4">

<input type="text"
       name="search"
       class="form-control"
       placeholder="Search payroll..."
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

                

                <div class="card mb-4">

                    <div class="card-body">

                        <table class="table table-bordered table-striped">

                            <thead class="table-dark">

                                <tr>

                                    <th>ID</th>

                                    <th>Employee</th>

                                    <th>Basic Salary</th>

                                    <th>Allowance</th>

                                    <th>Deduction</th>

                                    <th>Net Salary</th>

                                    <th>Pay Month</th>

                                    <th>Date Created</th>

                                    <th>Actions</th>

                                </tr>

                            </thead>

                            <tbody>

                            <?php while($row = mysqli_fetch_assoc($result)): ?>

                                <tr>

                                    <td>
                                        <?php echo $row['id']; ?>
                                    </td>

                                    <td>
                                        <?php echo $row['full_name']; ?>
                                    </td>

                                    <td>
                                        ₦<?php echo number_format($row['basic_salary'], 2); ?>
                                    </td>

                                    <td>
                                        ₦<?php echo number_format($row['allowance'], 2); ?>
                                    </td>

                                    <td>
                                        ₦<?php echo number_format($row['deduction'], 2); ?>
                                    </td>

                                    <td>
                                        ₦<?php echo number_format($row['net_salary'], 2); ?>
                                    </td>

                                    <td>
                                        <?php echo $row['pay_month']; ?>
                                    </td>

                                    <td>
                                        <?php echo $row['created_at']; ?>
                                    </td>

                                    <td>

                                    <?php if($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'hr'): ?>
                                    <a href="edit_payroll.php?id=<?php echo $row['id']; ?>"
                                     class="btn btn-warning btn-sm">

                                      Edit

                                    </a>
                                    <?php endif; ?>

                                    <?php if($_SESSION['role'] == 'admin'): ?>
                                    <a href="delete_payroll.php?id=<?php echo $row['id']; ?>"
                                          class="btn btn-danger btn-sm"
                                    onclick="return confirm('Are you sure you want to delete this payroll record?')">

                                        Delete

                                     </a>
                                     <?php endif; ?>


                                     <a href="payslip.php?id=<?php echo $row['id']; ?>"
                                     class="btn btn-info btn-sm">

                                    Payslip

                                      </a>
                                      <a href="payslip_pdf.php?id=<?php echo $row['user_id']; ?>"
                                      class="btn btn-danger btn-sm">

                                         Payslip PDF

                                       </a>
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