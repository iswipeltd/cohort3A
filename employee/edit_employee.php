<?php
session_start();
include("../includes/auth_check.php");
checkRole(['admin','hr']);

if(!isset($_SESSION['user_id'])){
    header("Location: ../auth/login.php");
    exit();
}

include("../config/db.php");

$name = $_SESSION['name'];
$role = $_SESSION['role'];

// GET ID
$id = intval($_GET['id'] ?? 0);

// FETCH USER
$sql = "SELECT * FROM users WHERE id = $id";
$result = mysqli_query($conn, $sql);

if(!$result){
    die("SQL ERROR: " . mysqli_error($conn));
}

$user = mysqli_fetch_assoc($result);

if(!$user){
    die("Employee not found");
}

// FETCH DEPARTMENTS
$dept_sql = "SELECT * FROM departments";
$dept_result = mysqli_query($conn, $dept_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>Edit Employee</title>

    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
    <link href="../assets/css/styles.css" rel="stylesheet">
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js"></script>
</head>

<body class="sb-nav-fixed">

<!-- TOP HEADER -->
<nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
            <!-- Navbar Brand-->
            <a class="navbar-brand ps-3" href="index.php">HR_System</a>
            <!-- Sidebar Toggle-->
            <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" href="#!"><i class="fas fa-bars"></i></button>
            <!-- Navbar Search-->
            <form class="d-none d-md-inline-block form-inline ms-auto me-0 me-md-3 my-2 my-md-0">
                <div class="input-group">
                    <input class="form-control" type="text" placeholder="Search for..." aria-label="Search for..." aria-describedby="btnNavbarSearch" />
                    <button class="btn btn-primary" id="btnNavbarSearch" type="button"><i class="fas fa-search"></i></button>
                </div>
            </form>
            <!-- Navbar-->
            <ul class="navbar-nav ms-auto ms-md-0 me-3 me-lg-4">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="fas fa-user fa-fw"></i>
                    <span class="ms-2 d-none d-lg-inline">
                    <?php echo "Welcome, " . $name; ?>
                    </span></a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="#!">Settings</a></li>
                        <li><a class="dropdown-item" href="#!">Activity Log</a></li>
                        <li><hr class="dropdown-divider" /></li>
                        <li><a class="dropdown-item"
                            href="../auth/logout.php"
                            onclick="return confirm('Are you sure you want to logout?')">
                            Logout
                        </a></li>
                    </ul>
                </li>
            </ul>
        </nav>
<div id="layoutSidenav">

<!-- SIDEBAR -->
<div id="layoutSidenav_nav">
    <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">

        <div class="sb-sidenav-menu">
            <div class="nav">

                <div class="sb-sidenav-menu-heading">Core</div>

                <a class="nav-link" href="../admin/index.php">
                    <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                    Dashboard
                </a>

                <div class="sb-sidenav-menu-heading">HR Management</div>

                <a class="nav-link" href="../employee/index.php">
                    <div class="sb-nav-link-icon"><i class="fas fa-users"></i></div>
                    Employees
                </a>

                <a class="nav-link" href="../employee/add_employee.php">
                    <div class="sb-nav-link-icon"><i class="fas fa-user-plus"></i></div>
                    Add Employee
                </a>

                <div class="sb-sidenav-menu-heading">Operations</div>

                <a class="nav-link" href="">
                    <div class="sb-nav-link-icon"><i class="fas fa-money-check-dollar"></i></div>
                    Payroll
                </a>

                <a class="nav-link" href="#">
                    <div class="sb-nav-link-icon"><i class="fas fa-calendar-days"></i></div>
                    Leave Management
                </a>

                <a class="nav-link" href="../departments/index.php">
                    <div class="sb-nav-link-icon"><i class="fas fa-building"></i></div>
                    Departments
                </a>

            </div>
        </div>

        <div class="sb-sidenav-footer">
            <div class="small">Logged in as:</div>
            <?php echo $name ?? 'User'; ?> (<?php echo $role ?? 'Role'; ?>)
        </div>

    </nav>
</div>

<!-- MAIN CONTENT -->
<div id="layoutSidenav_content">

<main>
<div class="container-fluid px-4">

    <h1 class="mt-4">Edit Employee</h1>

    <div class="card mb-4">
        <div class="card-body">

            <form action="update_employee.php" method="POST">

                <input type="hidden" name="id" value="<?php echo $user['id']; ?>">

                <div class="mb-3">
                    <label>Full Name</label>
                    <input type="text" name="full_name"
                           class="form-control"
                           value="<?php echo $user['full_name']; ?>" required>
                </div>

                <div class="mb-3">
                    <label>Email</label>
                    <input type="email" name="email"
                           class="form-control"
                           value="<?php echo $user['email']; ?>" required>
                </div>

                <div class="mb-3">
                    <label>Department</label>
                    <select name="department_id" class="form-control">

                        <option value="">Select Department</option>

                        <?php while($dept = mysqli_fetch_assoc($dept_result)): ?>
                            <option value="<?php echo $dept['id']; ?>"
                                <?php if($user['department_id'] == $dept['id']) echo "selected"; ?>>
                                <?php echo $dept['department_name']; ?>
                            </option>
                        <?php endwhile; ?>

                    </select>
                </div>

                <div class="mb-3">
                    <label>Role</label>
                    <select name="role" class="form-control">

                        <option value="employee" <?php if($user['role']=="employee") echo "selected"; ?>>Employee</option>
                        <option value="hr" <?php if($user['role']=="hr") echo "selected"; ?>>HR</option>
                        <option value="admin" <?php if($user['role']=="admin") echo "selected"; ?>>Admin</option>

                    </select>
                </div>

                <button type="submit" class="btn btn-primary">
                    Update Employee
                </button>

            </form>

        </div>
    </div>

</div>
</main>

<!-- FOOTER -->
<footer class="py-4 bg-light mt-auto">
    <div class="container-fluid px-4">
        <div class="d-flex justify-content-between small">
            <div>Copyright © HR System 2026</div>
        </div>
    </div>
</footer>

</div> <!-- layoutSidenav_content -->
</div> <!-- layoutSidenav -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>