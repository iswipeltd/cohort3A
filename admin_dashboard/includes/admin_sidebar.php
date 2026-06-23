<?php require_once __DIR__ . '/../../config/session.php'; $u = current_user(); $emp = current_employee(); ?>
        <!-- Sidebar Start -->
        <div class="sidebar pe-4 pb-3">
            <nav class="navbar bg-secondary navbar-dark">
                <a href="index.php" class="navbar-brand mx-4 mb-3">
                    <h3 class="text-primary" style="display:flex;align-items:center;gap:8px;font-family:'Poppins',sans-serif;"><span style="width:32px;height:32px;display:inline-block;"><svg viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg"><defs><linearGradient id="ag" x1="0%" y1="0%" x2="100%" y2="100%"><stop offset="0%" style="stop-color:#f59e0b;stop-opacity:1"/><stop offset="50%" style="stop-color:#fbbf24;stop-opacity:1"/><stop offset="100%" style="stop-color:#f59e0b;stop-opacity:1"/></linearGradient></defs><polygon points="50,5 90,25 90,75 50,95 10,75 10,25" fill="url(#ag)" stroke="#fbbf24" stroke-width="2"/><text x="50" y="62" text-anchor="middle" font-family="Poppins,sans-serif" font-weight="800" font-size="42" fill="#fff">A</text></svg></span>ADEEEEE</h3>
                </a>
                <div class="d-flex align-items-center ms-4 mb-4">
                    <div class="position-relative">
                        <img class="rounded-circle" src="<?php echo $u['avatar'] ? htmlspecialchars($u['avatar']) : 'https://ui-avatars.com/api/?name=' . urlencode(($u['first_name'] ?? 'A') . '+' . ($u['last_name'] ?? 'dmin')) . '&background=6366f1&color=fff&size=80'; ?>" alt="" style="width: 40px; height: 40px; object-fit:cover;">
                        <div class="bg-success rounded-circle border border-2 border-white position-absolute end-0 bottom-0 p-1"></div>
                    </div>
                    <div class="ms-3">
                        <h6 class="mb-0"><?php echo htmlspecialchars(($u['first_name'] ?? 'Admin') . ' ' . ($u['last_name'] ?? '')); ?></h6>
                        <span><?php echo ucfirst($u['role'] ?? 'Admin'); ?></span>
                    </div>
                </div>
                <div class="navbar-nav w-100">
                    <a href="welcome.php" class="nav-item nav-link"><i class="fa fa-star me-2"></i>Welcome</a>
                    <a href="index.php" class="nav-item nav-link active"><i class="fa fa-tachometer-alt me-2"></i>Dashboard</a>
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><i class="fa fa-users me-2"></i>Employee Mgmt</a>
                        <div class="dropdown-menu bg-transparent border-0">
                            <a href="employees.php" class="dropdown-item">All Employees</a>
                            <a href="employee_add.php" class="dropdown-item">Add Employee</a>
                            <a href="employee_docs.php" class="dropdown-item">Documents</a>
                            <a href="departments.php" class="dropdown-item">Departments</a>
                            <a href="roles.php" class="dropdown-item">Roles & Managers</a>
                            <a href="employee_status.php" class="dropdown-item">Status Tracking</a>
                            <a href="delete_employee.php" class="dropdown-item">Delete Employee</a>
                        </div>
                    </div>
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><i class="fa fa-briefcase me-2"></i>Recruitment</a>
                        <div class="dropdown-menu bg-transparent border-0">
                            <a href="job_postings.php" class="dropdown-item">Job Postings</a>
                            <a href="applications.php" class="dropdown-item">Applications</a>
                            <a href="interviews.php" class="dropdown-item">Interviews</a>
                            <a href="candidates.php" class="dropdown-item">Candidate Tracker</a>
                            <a href="offer_letters.php" class="dropdown-item">Offer Letters</a>
                            <a href="onboarding.php" class="dropdown-item">Onboarding</a>
                        </div>
                    </div>
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><i class="fa fa-clock me-2"></i>Attendance</a>
                        <div class="dropdown-menu bg-transparent border-0">
                            <a href="attendance_monitor.php" class="dropdown-item">Monitor Attendance</a>
                            <a href="timesheets.php" class="dropdown-item">Timesheets</a>
                            <a href="shift_schedules.php" class="dropdown-item">Shift Schedules</a>
                            <a href="overtime.php" class="dropdown-item">Overtime & Lateness</a>
                            <a href="biometric.php" class="dropdown-item">Biometric Integration</a>
                        </div>
                    </div>
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><i class="fa fa-list-check me-2"></i>Tasks</a>
                        <div class="dropdown-menu bg-transparent border-0">
                            <a href="tasks.php" class="dropdown-item">All Tasks</a>
                            <a href="edit_task.php" class="dropdown-item">Edit Task</a>
                        </div>
                    </div>
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><i class="fa fa-calendar-alt me-2"></i>Leave Mgmt</a>
                        <div class="dropdown-menu bg-transparent border-0">
                            <a href="leave_types.php" class="dropdown-item">Leave Types</a>
                            <a href="leave_requests.php" class="dropdown-item">Leave Requests</a>
                            <a href="leave_balances.php" class="dropdown-item">Leave Balances</a>
                            <a href="leave_policies.php" class="dropdown-item">Leave Policies</a>
                            <a href="leave_reports.php" class="dropdown-item">Leave Reports</a>
                        </div>
                    </div>
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><i class="fa fa-money-bill-wave me-2"></i>Payroll</a>
                        <div class="dropdown-menu bg-transparent border-0">
                            <a href="salary_structures.php" class="dropdown-item">Salary Structures</a>
                            <a href="process_payroll.php" class="dropdown-item">Process Payroll</a>
                            <a href="bonuses_deductions.php" class="dropdown-item">Bonuses & Deductions</a>
                            <a href="payslips.php" class="dropdown-item">Payslips</a>
                            <a href="tax_compliance.php" class="dropdown-item">Tax & Compliance</a>
                            <a href="payroll_reports.php" class="dropdown-item">Payroll Reports</a>
                        </div>
                    </div>
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><i class="fa fa-chart-line me-2"></i>Performance</a>
                        <div class="dropdown-menu bg-transparent border-0">
                            <a href="kpis.php" class="dropdown-item">KPIs & Goals</a>
                            <a href="performance_reviews.php" class="dropdown-item">Performance Reviews</a>
                            <a href="feedback.php" class="dropdown-item">Feedback & Ratings</a>
                            <a href="progress_tracking.php" class="dropdown-item">Progress Tracking</a>
                            <a href="promotions.php" class="dropdown-item">Promotions & Disciplinary</a>
                        </div>
                    </div>
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><i class="fa fa-graduation-cap me-2"></i>Training</a>
                        <div class="dropdown-menu bg-transparent border-0">
                            <a href="training_programs.php" class="dropdown-item">Training Programs</a>
                            <a href="training_progress.php" class="dropdown-item">Progress Tracking</a>
                            <a href="learning_materials.php" class="dropdown-item">Learning Materials</a>
                            <a href="workshops.php" class="dropdown-item">Workshops & Seminars</a>
                            <a href="training_eval.php" class="dropdown-item">Training Evaluation</a>
                            <a href="training_enrollments.php" class="dropdown-item">Enrollments</a>
                        </div>
                    </div>
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><i class="fa fa-chart-pie me-2"></i>Reports</a>
                        <div class="dropdown-menu bg-transparent border-0">
                            <a href="hr_reports.php" class="dropdown-item">HR Reports</a>
                            <a href="dashboard_metrics.php" class="dropdown-item">Dashboard Metrics</a>
                            <a href="export_data.php" class="dropdown-item">Export Data</a>
                            <a href="workforce_trends.php" class="dropdown-item">Workforce Trends</a>
                        </div>
                    </div>
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><i class="fa fa-user-shield me-2"></i>Users & Roles</a>
                        <div class="dropdown-menu bg-transparent border-0">
                            <a href="user_accounts.php" class="dropdown-item">User Accounts</a>
                            <a href="roles_permissions.php" class="dropdown-item">Roles & Permissions</a>
                            <a href="access_control.php" class="dropdown-item">Access Control</a>
                            <a href="password_reset.php" class="dropdown-item">Password Reset</a>
                        </div>
                    </div>
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><i class="fa fa-file-alt me-2"></i>Policies</a>
                        <div class="dropdown-menu bg-transparent border-0">
                            <a href="company_policies.php" class="dropdown-item">Company Policies</a>
                            <a href="compliance_docs.php" class="dropdown-item">Compliance Docs</a>
                            <a href="policy_updates.php" class="dropdown-item">Policy Updates</a>
                            <a href="acknowledgments.php" class="dropdown-item">Acknowledgments</a>
                            <a href="faqs.php" class="dropdown-item">FAQs</a>
                        </div>
                    </div>
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><i class="fa fa-bell me-2"></i>Communication</a>
                        <div class="dropdown-menu bg-transparent border-0">
                            <a href="announcements.php" class="dropdown-item">Announcements</a>
                            <a href="notifications.php" class="dropdown-item">Notifications</a>
                            <a href="messaging.php" class="dropdown-item">Messaging</a>
                            <a href="faqs.php" class="dropdown-item">FAQs</a>
                        </div>
                    </div>
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><i class="fa fa-cogs me-2"></i>System</a>
                        <div class="dropdown-menu bg-transparent border-0">
                            <a href="portal_settings.php" class="dropdown-item">Portal Settings</a>
                            <a href="org_structure.php" class="dropdown-item">Org Structure</a>
                            <a href="integrations.php" class="dropdown-item">Integrations</a>
                            <a href="backups.php" class="dropdown-item">Backups & Security</a>
                            <a href="asset_requests.php" class="dropdown-item">Asset Requests</a>
                        </div>
                    </div>
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><i class="fa fa-shield-alt me-2"></i>Audit</a>
                        <div class="dropdown-menu bg-transparent border-0">
                            <a href="activity_logs.php" class="dropdown-item">Activity Logs</a>
                            <a href="user_changes.php" class="dropdown-item">User Changes</a>
                            <a href="compliance_checks.php" class="dropdown-item">Compliance Checks</a>
                            <a href="audit_reports.php" class="dropdown-item">Audit Reports</a>
                        </div>
                    </div>
                    <a href="/HRSuite/process/logout.php" class="nav-item nav-link"><i class="fa fa-sign-out-alt me-2"></i>Log Out</a>
                </div>
            </nav>
        </div>
        <!-- Sidebar End -->
