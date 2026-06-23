<?php require_once __DIR__ . '/../../config/session.php'; $u = current_user(); ?>
<!-- Sidebar -->
<aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <a href="dashboard.php" class="sidebar-logo" style="display:inline-flex;align-items:center;gap:8px;"><span style="width:28px;height:28px;display:inline-block;vertical-align:middle;"><svg viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg"><defs><linearGradient id="ag" x1="0%" y1="0%" x2="100%" y2="100%"><stop offset="0%" style="stop-color:#f59e0b;stop-opacity:1"/><stop offset="50%" style="stop-color:#fbbf24;stop-opacity:1"/><stop offset="100%" style="stop-color:#f59e0b;stop-opacity:1"/></linearGradient></defs><polygon points="50,5 90,25 90,75 50,95 10,75 10,25" fill="url(#ag)" stroke="#fbbf24" stroke-width="2"/><text x="50" y="62" text-anchor="middle" font-family="Poppins,sans-serif" font-weight="800" font-size="42" fill="#fff">A</text></svg></span> ADEEEEE</a>
    </div>
    <div class="sidebar-user">
        <div class="d-flex align-items-center gap-3">
            <img src="<?php echo $u['avatar'] ? htmlspecialchars($u['avatar']) : 'https://ui-avatars.com/api/?name=' . urlencode($u['first_name'] . '+' . $u['last_name']) . '&background=6366f1&color=fff&size=120'; ?>" alt="" style="width:42px;height:42px;border-radius:12px;object-fit:cover;border:2px solid var(--sidebar-border);">
            <div>
                <div class="name fw-bold" style="font-size:0.88rem;"><?php echo htmlspecialchars($u['first_name'] . ' ' . $u['last_name']); ?></div>
                <div class="d-flex align-items-center gap-1 mt-1">
                    <span style="font-size:0.68rem;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;background:var(--primary);color:#fff;padding:2px 8px;border-radius:4px;"><?php echo strtoupper($u['role'] ?? 'ADMIN'); ?></span>
                </div>
            </div>
        </div>
    </div>
    <ul class="sidebar-menu">
        <li><a href="welcome.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'welcome.php' ? 'active' : ''; ?>"><i class="fa-solid fa-house"></i> Home</a></li>
        <li><a href="dashboard.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>"><i class="fa-solid fa-chart-pie"></i> Dashboard</a></li>
    </ul>
    <div class="sidebar-section">People</div>
    <ul class="sidebar-menu">
        <li><a href="employees.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'employees.php' ? 'active' : ''; ?>"><i class="fa-solid fa-users"></i> Employees</a></li>
        <li><a href="employee_add.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'employee_add.php' ? 'active' : ''; ?>"><i class="fa-solid fa-user-plus"></i> Add Employee</a></li>
        <li><a href="departments.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'departments.php' ? 'active' : ''; ?>"><i class="fa-solid fa-building"></i> Departments</a></li>
        <li><a href="roles.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'roles.php' ? 'active' : ''; ?>"><i class="fa-solid fa-id-badge"></i> Roles</a></li>
    </ul>
    <div class="sidebar-section">HR Operations</div>
    <ul class="sidebar-menu">
        <li><a href="leave_requests.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'leave_requests.php' ? 'active' : ''; ?>"><i class="fa-solid fa-calendar-check"></i> Leave Requests</a></li>
        <li><a href="attendance.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'attendance.php' ? 'active' : ''; ?>"><i class="fa-solid fa-clock"></i> Attendance</a></li>
        <li><a href="payroll.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'payroll.php' ? 'active' : ''; ?>"><i class="fa-solid fa-money-bill-wave"></i> Payroll</a></li>
        <li><a href="expenses.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'expenses.php' ? 'active' : ''; ?>"><i class="fa-solid fa-wallet"></i> Expenses</a></li>
        <li><a href="tasks.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'tasks.php' ? 'active' : ''; ?>"><i class="fa-solid fa-list-check"></i> Tasks</a></li>
        <li><a href="asset_requests.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'asset_requests.php' ? 'active' : ''; ?>"><i class="fa-solid fa-box-open"></i> Asset Requests</a></li>
    </ul>
    <div class="sidebar-section">Organization</div>
    <ul class="sidebar-menu">
        <li><a href="job_postings.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'job_postings.php' ? 'active' : ''; ?>"><i class="fa-solid fa-briefcase"></i> Jobs</a></li>
        <li><a href="training_programs.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'training_programs.php' ? 'active' : ''; ?>"><i class="fa-solid fa-graduation-cap"></i> Training</a></li>
        <li><a href="training_enrollments.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'training_enrollments.php' ? 'active' : ''; ?>"><i class="fa-solid fa-user-check"></i> Enrollments</a></li>
        <li><a href="announcements.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'announcements.php' ? 'active' : ''; ?>"><i class="fa-solid fa-bullhorn"></i> Announcements</a></li>
        <li><a href="messages.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'messages.php' ? 'active' : ''; ?>"><i class="fa-solid fa-envelope"></i> Messages</a></li>
        <li><a href="faqs.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'faqs.php' ? 'active' : ''; ?>"><i class="fa-solid fa-circle-question"></i> FAQs</a></li>
    </ul>
    <div class="sidebar-section">Settings</div>
    <ul class="sidebar-menu">
        <li><a href="settings.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'settings.php' ? 'active' : ''; ?>"><i class="fa-solid fa-gear"></i> Settings</a></li>
        <li><a href="payment_settings.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'payment_settings.php' ? 'active' : ''; ?>"><i class="fa-solid fa-credit-card"></i> Payment Settings</a></li>
        <li><a href="/HRSuite/process/logout.php"><i class="fa-solid fa-right-from-bracket"></i> Log Out</a></li>
    </ul>
</aside>