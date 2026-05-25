<?php
$role = $_SESSION['role'] ?? '';
?>

<div id="layoutSidenav_nav">
    <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">

        <div class="sb-sidenav-menu">
            <div class="nav">

                <!-- CORE -->
                <div class="sb-sidenav-menu-heading">Core</div>

                <?php if($role == 'admin'): ?>
                <a class="nav-link" href="../admin/index.php">
                    <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                    Dashboard
                </a>
                <?php endif; ?>

                <?php if($role == 'hr'): ?>
                    <a class="nav-link" href="../hr/index.php">
                    <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                    Dashboard
                </a>
                <?php endif; ?>

                <?php if($role == 'admin' || $role == 'hr'): ?>

                <!-- HR / ADMIN SHARED -->
                <div class="sb-sidenav-menu-heading">HR Management</div>

                <a class="nav-link" href="../employee/index.php">
                    <div class="sb-nav-link-icon"><i class="fas fa-users"></i></div>
                    Employees
                </a>

                <?php if($role == 'admin'): ?>
                <a class="nav-link" href="../employee/add_employee.php">
                    <div class="sb-nav-link-icon"><i class="fas fa-user-plus"></i></div>
                    Add Employee
                </a>
                <?php endif; ?>

                <a class="nav-link" href="../tasks/user_task.php">
                    <div class="sb-nav-link-icon"><i class="fas fa-tasks"></i></div>
                    My Tasks
                </a>

                <div class="sb-sidenav-menu-heading">Operations</div>

                <a class="nav-link" href="../payroll/index.php">
                    <div class="sb-nav-link-icon"><i class="fas fa-money-check-dollar"></i></div>
                    Payroll
                </a>

                <a class="nav-link" href="../leaves/index.php">
                    <div class="sb-nav-link-icon"><i class="fas fa-calendar-days"></i></div>
                    Leave Management
                </a>

                <a class="nav-link" href="../departments/index.php">
                    <div class="sb-nav-link-icon"><i class="fas fa-building"></i></div>
                    Departments
                </a>

                <a class="nav-link" href="../attendance/index.php">
                    <div class="sb-nav-link-icon"><i class="fas fa-clipboard-list"></i></div>
                    Attendance
                </a>

                <a class="nav-link" href="../reports/index.php">
                    <div class="sb-nav-link-icon"><i class="fas fa-chart-bar"></i></div>
                    Reports     
                </a>

                <?php endif; ?>

          

                <!-- ADMIN ONLY -->
                <?php if($role == 'admin' || $role == 'hr'): ?>
                <a class="nav-link" href="../tasks/index.php">
                    <div class="sb-nav-link-icon"><i class="fas fa-tasks"></i></div>
                    Tasks
                </a>

                <?php endif; ?>

                <?php if($role == 'admin' || $role == 'hr'): ?>
                <a class="nav-link" href="../admin/users_reset.php">
                 <div class="sb-nav-link-icon"><i class="fas fa-key"></i></div>
                 User Password Reset
                </a>
                <?php endif; ?>

                <?php if($role == 'employee'): ?>

                <!-- EMPLOYEE ONLY (CLEAN START) -->
                <div class="sb-sidenav-menu-heading">Self Service</div>


               <a class="nav-link" href="../employee_dashboard/index.php">
                   Dashboard
                </a>    

                <a class="nav-link" href="../tasks/user_task.php">
                    <div class="sb-nav-link-icon"><i class="fas fa-tasks"></i></div>
                    My Tasks
                </a>


                <a class="nav-link" href="../employee_dashboard/profile.php">
                <div class="sb-nav-link-icon">
                <i class="fas fa-user"></i>
                </div>
                 My Profile
                </a>



                <a class="nav-link" href="../employee_dashboard/leave.php">
                <div class="sb-nav-link-icon">
                <i class="fas fa-calendar-days"></i>
                </div>
                My Leave
                </a>

                <a class="nav-link" href="../employee_dashboard/attendance.php">
                <div class="sb-nav-link-icon">
                <i class="fas fa-clipboard-list"></i>
                </div>
                My Attendance
                </a>

                <a class="nav-link" href="../employee_dashboard/payroll.php">
                <div class="sb-nav-link-icon">
                <i class="fas fa-money-check-dollar"></i>
                </div>
                My Payroll
                </a>


                <?php endif; ?>

                

            </div>
        </div>

        <div class="sb-sidenav-footer">
            <div class="small">Logged in as:</div>
            <?php echo $name ?? 'User'; ?> (<?php echo $role ?? 'Role'; ?>)
        </div>

    </nav>
</div>