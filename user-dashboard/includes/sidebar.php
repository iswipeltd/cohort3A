<?php require_once __DIR__ . '/../../config/session.php'; $user = current_user(); ?>
<!-- Sidebar -->
<aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <a href="index.php" class="sidebar-logo" style="display:inline-flex;align-items:center;gap:8px;"><span style="width:28px;height:28px;display:inline-block;vertical-align:middle;"><svg viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg"><defs><linearGradient id="ag" x1="0%" y1="0%" x2="100%" y2="100%"><stop offset="0%" style="stop-color:#f59e0b;stop-opacity:1"/><stop offset="50%" style="stop-color:#fbbf24;stop-opacity:1"/><stop offset="100%" style="stop-color:#f59e0b;stop-opacity:1"/></linearGradient></defs><polygon points="50,5 90,25 90,75 50,95 10,75 10,25" fill="url(#ag)" stroke="#fbbf24" stroke-width="2"/><text x="50" y="62" text-anchor="middle" font-family="Poppins,sans-serif" font-weight="800" font-size="42" fill="#fff">A</text></svg></span> ADEEEEE</a>
    </div>
    <div class="sidebar-user">
        <div class="d-flex align-items-center gap-3">
            <img src="<?php echo !empty($user['avatar'])?htmlspecialchars($user['avatar']):'https://ui-avatars.com/api/?name='.urlencode(($user['first_name']??'U').'+'.($user['last_name']??'')).'&background=2563eb&color=fff&size=120'; ?>" style="width:42px;height:42px;border-radius:14px;object-fit:cover;border:2px solid var(--border);" alt="">
            <div>
                <div class="fw-bold" style="font-size:0.88rem;color:var(--text);"><?php echo htmlspecialchars(($user['first_name']??'').' '.($user['last_name']??'')); ?></div>
                <div class="d-flex align-items-center gap-1 mt-1">
                    <span style="font-size:0.68rem;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;background:var(--primary);color:#fff;padding:2px 8px;border-radius:4px;">Employee</span>
                </div>
            </div>
        </div>
    </div>
    <ul class="sidebar-menu">
        <li><a href="index.php" class="<?php echo basename($_SERVER['PHP_SELF'])=='index.php'?'active':''; ?>"><i class="fa-solid fa-house"></i> Home</a></li>
        <li><a href="my_profile.php" class="<?php echo basename($_SERVER['PHP_SELF'])=='my_profile.php'?'active':''; ?>"><i class="fa-solid fa-user"></i> My Profile</a></li>
    </ul>
    <div class="sidebar-section">Work</div>
    <ul class="sidebar-menu">
        <li><a href="clock_in_out.php" class="<?php echo basename($_SERVER['PHP_SELF'])=='clock_in_out.php'?'active':''; ?>"><i class="fa-solid fa-clock"></i> Attendance</a></li>
        <li><a href="apply_leave.php" class="<?php echo basename($_SERVER['PHP_SELF'])=='apply_leave.php'?'active':''; ?>"><i class="fa-solid fa-calendar-day"></i> Apply Leave</a></li>
        <li><a href="leave_status.php" class="<?php echo basename($_SERVER['PHP_SELF'])=='leave_status.php'?'active':''; ?>"><i class="fa-solid fa-calendar-check"></i> Leave Status</a></li>
        <li><a href="my_tasks.php" class="<?php echo basename($_SERVER['PHP_SELF'])=='my_tasks.php'?'active':''; ?>"><i class="fa-solid fa-list-check"></i> My Tasks</a></li>
    </ul>
    <div class="sidebar-section">Pay & Claims</div>
    <ul class="sidebar-menu">
        <li><a href="payroll.php" class="<?php echo basename($_SERVER['PHP_SELF'])=='payroll.php'?'active':''; ?>"><i class="fa-solid fa-money-bill-wave"></i> Payroll</a></li>
        <li><a href="my_payslips.php" class="<?php echo basename($_SERVER['PHP_SELF'])=='my_payslips.php'?'active':''; ?>"><i class="fa-solid fa-file-invoice-dollar"></i> Payslips</a></li>
        <li><a href="submit_expense.php" class="<?php echo basename($_SERVER['PHP_SELF'])=='submit_expense.php'?'active':''; ?>"><i class="fa-solid fa-wallet"></i> Submit Expense</a></li>
        <li><a href="expense_status.php" class="<?php echo basename($_SERVER['PHP_SELF'])=='expense_status.php'?'active':''; ?>"><i class="fa-solid fa-receipt"></i> Expense Status</a></li>
    </ul>
    <div class="sidebar-section">Training</div>
    <ul class="sidebar-menu">
        <li><a href="enroll_courses.php" class="<?php echo basename($_SERVER['PHP_SELF'])=='enroll_courses.php'?'active':''; ?>"><i class="fa-solid fa-graduation-cap"></i> Enroll Courses</a></li>
        <li><a href="my_training.php" class="<?php echo basename($_SERVER['PHP_SELF'])=='my_training.php'?'active':''; ?>"><i class="fa-solid fa-book-open"></i> My Training</a></li>
    </ul>
    <div class="sidebar-section">Assets</div>
    <ul class="sidebar-menu">
        <li><a href="request_asset.php" class="<?php echo basename($_SERVER['PHP_SELF'])=='request_asset.php'?'active':''; ?>"><i class="fa-solid fa-box-open"></i> Request Asset</a></li>
        <li><a href="my_assets.php" class="<?php echo basename($_SERVER['PHP_SELF'])=='my_assets.php'?'active':''; ?>"><i class="fa-solid fa-boxes-stacked"></i> My Assets</a></li>
    </ul>
    <div class="sidebar-section">Support</div>
    <ul class="sidebar-menu">
        <li><a href="contact_hr.php" class="<?php echo basename($_SERVER['PHP_SELF'])=='contact_hr.php'?'active':''; ?>"><i class="fa-solid fa-headset"></i> Contact HR</a></li>
        <li><a href="faqs.php" class="<?php echo basename($_SERVER['PHP_SELF'])=='faqs.php'?'active':''; ?>"><i class="fa-solid fa-circle-question"></i> FAQs</a></li>
    </ul>
    <div class="sidebar-section">Account</div>
    <ul class="sidebar-menu">
        <li><a href="change_password.php" class="<?php echo basename($_SERVER['PHP_SELF'])=='change_password.php'?'active':''; ?>"><i class="fa-solid fa-lock"></i> Password</a></li>
        <li><a href="/HRSuite/process/logout.php"><i class="fa-solid fa-right-from-bracket"></i> Log Out</a></li>
    </ul>
</aside>