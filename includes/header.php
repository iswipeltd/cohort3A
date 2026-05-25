<?php


if(!isset($_SESSION['user_id'])){
    header("Location: ../auth/login.php");
    exit();
}

include("../config/db.php");

$name = $_SESSION['name'];
$role = $_SESSION['role'];

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8" />

    <meta http-equiv="X-UA-Compatible" content="IE=edge" />

    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />

    <meta name="description" content="" />

    <meta name="author" content="" />

    <title>Dashboard - HR_SUITE</title>

    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />

    <link href="../assets/css/styles.css" rel="stylesheet">

    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>

</head>

<body class="sb-nav-fixed">

<nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">

    <!-- Navbar Brand -->

    <a class="navbar-brand ps-3" href="index.php">
        HR_Management
    </a>

    <!-- Sidebar Toggle -->

    <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0"
            id="sidebarToggle"
            href="#!">

        <i class="fas fa-bars"></i>

    </button>

    <!-- Navbar Search -->

    <?php if($role == 'admin' || $role == 'hr'): ?>

    <form class="d-none d-md-inline-block form-inline ms-auto me-0 me-md-3 my-2 my-md-0"
          action="../employee/search.php"
          method="GET">

        <div class="input-group">

            <input class="form-control"
                   type="text"
                   name="search"
                   placeholder="Search employees..."
                   required>

            <button class="btn btn-primary" type="submit">

                <i class="fas fa-search"></i>

            </button>

        </div>

    </form>

    <?php endif; ?>

    <!-- Navbar -->

    <ul class="navbar-nav ms-auto ms-md-0 me-3 me-lg-4">

        <li class="nav-item dropdown">

            <a class="nav-link dropdown-toggle"
               id="navbarDropdown"
               href="#"
               role="button"
               data-bs-toggle="dropdown"
               aria-expanded="false">

                <i class="fas fa-user fa-fw"></i>

                <span class="ms-2 d-none d-lg-inline">

                    <?php echo "Welcome, " . $name; ?>

                </span>

            </a>

            <ul class="dropdown-menu dropdown-menu-end"
                aria-labelledby="navbarDropdown">

                <li>
                    <a class="dropdown-item" href="#!">
                        Settings
                    </a>
                </li>

                <li>
                    <a class="dropdown-item" href="#!">
                        Activity Log
                    </a>
                </li>

                <li>
                    <hr class="dropdown-divider" />
                </li>

                <li>

                    <a class="dropdown-item"
                       href="../auth/logout.php"
                       onclick="return confirm('Are you sure you want to logout?')">

                        Logout

                    </a>

                </li>

            </ul>

        </li>

    </ul>

</nav>