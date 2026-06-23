# ADEEEEE - Human Resources Management System

A complete PHP/MySQL-based HR management platform with separate Admin and Employee dashboards.

## Features

### Admin Dashboard
- Employee Management (CRUD, documents, departments, roles)
- Recruitment & Onboarding (job postings, candidates, interviews, offers)
- Attendance & Time Tracking (clock-in/out, timesheets, shifts, overtime)
- Leave Management (types, requests, balances, policies, reports)
- Payroll Processing (salary structures, payslips, tax, bonuses)
- Performance Reviews (KPIs, goals, feedback, promotions)
- Training & Development (programs, enrollments, certificates)
- Reports & Analytics (workforce trends, exports)
- User & Role Management (permissions, access control)
- Audit Logs & Compliance

### Employee Dashboard
- Clock In/Out with live status
- Leave Application & Balance Tracking
- Payslip Access & Salary History
- Expense Claims
- Task Management
- Performance Reviews & Self-Assessment
- Training Enrollment
- Document Uploads
- Support Tickets
- Notifications & Announcements

## Requirements

- PHP 8.0+
- MySQL 5.7+ / MariaDB 10.3+
- Apache/Nginx web server
- mod_rewrite enabled (optional but recommended)

## Installation

1. **Extract** the project to your web server root (e.g., `/var/www/html/HRSuite/`).

2. **Create the database** using the provided SQL file:
   ```bash
   mysql -u root -p
   CREATE DATABASE hrsuite CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   exit
   mysql -u root -p hrsuite < hrsuite.sql
   ```

3. **Update database credentials** in `config/database.php`:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'your_mysql_user');
   define('DB_PASS', 'your_mysql_password');
   define('DB_NAME', 'hrsuite');
   ```

4. **Set folder permissions** for uploads:
   ```bash
   chmod -R 755 uploads/
   chown -R www-data:www-data uploads/
   ```

5. **Access the application**:
   - Admin Portal: `http://your-server/HRSuite/admin_dashboard/signin.php`
   - Employee Portal: `http://your-server/HRSuite/user-dashboard/signin.php`

## Default Credentials

| Role      | Email                  | Password       |
|-----------|------------------------|----------------|
| Admin     | admin@hrsuite.com      | Admin@123      |
| Employee  | john.doe@hrsuite.com   | Employee@123   |

**Note:** Change default passwords immediately after first login.

## Database Schema Overview

The system uses 25+ interconnected tables:
- `users` - Authentication & profiles
- `employees` - Employee records & HR data
- `departments`, `roles` - Organizational structure
- `leave_requests`, `leave_types` - Leave management
- `attendance`, `timesheets` - Time tracking
- `payroll_periods`, `payroll_records`, `payslips` - Payroll
- `job_postings`, `candidates` - Recruitment
- `training_programs`, `training_enrollments` - Learning
- `tasks` - Work management
- `expenses` - Reimbursements
- `assets`, `asset_logs` - Equipment tracking
- `announcements`, `messages`, `notifications` - Communication
- `activity_logs` - Audit trail

## Backend API Endpoints (REST-style)

All backend processing files are in `/process/`:
- `login.php` - Authentication
- `logout.php` - Session termination
- `add_employee.php` - Employee creation
- `apply_leave.php` - Leave submission
- `approve_leave.php` - Admin approval/rejection
- `clock_in.php` - Attendance recording
- `submit_expense.php` - Expense claims

## Security Notes

- Passwords are hashed with `password_hash()` (bcrypt)
- Role-based access control via `require_auth()` and `require_admin()`
- All SQL queries use prepared statements (PDO)
- Activity logging tracks all major actions with IP addresses
- Upload directories should be outside web root or protected with `.htaccess`

## Customization

- Update company name in `settings` table
- Modify leave types, departments, and roles directly in the database
- Adjust payroll tax rates in `config/database.php`
- Customize email templates (add SMTP config for production)
