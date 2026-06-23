-- ADEEEEE Database Schema
-- Run this to create the complete database

CREATE DATABASE IF NOT EXISTS hrsuite CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE hrsuite;

-- Users & Authentication
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('admin','hr','manager','employee') DEFAULT 'employee',
    status ENUM('active','inactive','suspended') DEFAULT 'active',
    two_factor_enabled TINYINT DEFAULT 0,
    two_factor_secret VARCHAR(32) NULL,
    last_login DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Employees
CREATE TABLE employees (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    employee_id VARCHAR(50) NOT NULL UNIQUE,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    date_of_birth DATE NULL,
    gender ENUM('male','female','other','prefer_not_to_say') NULL,
    address TEXT,
    emergency_contact_name VARCHAR(100),
    emergency_contact_phone VARCHAR(20),
    emergency_contact_relation VARCHAR(50),
    department_id INT NULL,
    role_id INT NULL,
    manager_id INT NULL,
    salary DECIMAL(12,2) DEFAULT 0.00,
    currency VARCHAR(3) DEFAULT 'NGN',
    hire_date DATE NOT NULL,
    termination_date DATE NULL,
    status ENUM('active','inactive','terminated','on_leave') DEFAULT 'active',
    profile_picture VARCHAR(255),
    bank_name VARCHAR(100),
    bank_account VARCHAR(50),
    bank_code VARCHAR(20),
    bank_name VARCHAR(100),
    bank_account VARCHAR(50),
    bank_code VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (manager_id) REFERENCES employees(id) ON DELETE SET NULL
);

-- Departments
CREATE TABLE departments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    code VARCHAR(20),
    description TEXT,
    head_id INT NULL,
    parent_id INT NULL,
    location VARCHAR(255),
    status ENUM('active','inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (head_id) REFERENCES employees(id) ON DELETE SET NULL,
    FOREIGN KEY (parent_id) REFERENCES departments(id) ON DELETE SET NULL
);

-- Roles
CREATE TABLE roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    level ENUM('executive','manager','senior','mid','junior','intern') DEFAULT 'mid',
    permissions JSON,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Employee Documents
CREATE TABLE employee_documents (
    id INT AUTO_INCREMENT PRIMARY KEY,
    employee_id INT NOT NULL,
    document_type ENUM('cv','contract','id','certificate','other') DEFAULT 'other',
    file_name VARCHAR(255) NOT NULL,
    file_path VARCHAR(500) NOT NULL,
    file_size INT,
    mime_type VARCHAR(100),
    uploaded_by INT,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE,
    FOREIGN KEY (uploaded_by) REFERENCES users(id) ON DELETE SET NULL
);

-- Job Postings
CREATE TABLE job_postings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    department_id INT NULL,
    location VARCHAR(255),
    job_type ENUM('full-time','part-time','contract','internship','remote') DEFAULT 'full-time',
    salary_min DECIMAL(12,2),
    salary_max DECIMAL(12,2),
    currency VARCHAR(3) DEFAULT 'NGN',
    description TEXT,
    requirements TEXT,
    posted_by INT,
    status ENUM('open','closed','paused','draft') DEFAULT 'draft',
    posted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    closes_at DATE NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE SET NULL,
    FOREIGN KEY (posted_by) REFERENCES users(id) ON DELETE SET NULL
);

-- Job Applications
CREATE TABLE job_applications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    job_posting_id INT NOT NULL,
    applicant_name VARCHAR(255) NOT NULL,
    applicant_email VARCHAR(255) NOT NULL,
    applicant_phone VARCHAR(20),
    resume_path VARCHAR(500),
    cover_letter TEXT,
    status ENUM('new','shortlisted','interview','rejected','hired') DEFAULT 'new',
    current_stage VARCHAR(50),
    applied_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (job_posting_id) REFERENCES job_postings(id) ON DELETE CASCADE
);

-- Interviews
CREATE TABLE interviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    application_id INT NOT NULL,
    interviewer_id INT NOT NULL,
    scheduled_at DATETIME NOT NULL,
    location VARCHAR(255),
    interview_type ENUM('phone','video','in-person') DEFAULT 'in-person',
    status ENUM('scheduled','completed','cancelled','no_show') DEFAULT 'scheduled',
    feedback TEXT,
    rating INT CHECK (rating BETWEEN 1 AND 5),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (application_id) REFERENCES job_applications(id) ON DELETE CASCADE,
    FOREIGN KEY (interviewer_id) REFERENCES employees(id) ON DELETE CASCADE
);

-- Onboarding
CREATE TABLE onboarding (
    id INT AUTO_INCREMENT PRIMARY KEY,
    employee_id INT NOT NULL,
    checklist JSON,
    status ENUM('not_started','in_progress','completed') DEFAULT 'not_started',
    started_at TIMESTAMP NULL,
    completed_at TIMESTAMP NULL,
    FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE
);

-- Attendance
CREATE TABLE attendance (
    id INT AUTO_INCREMENT PRIMARY KEY,
    employee_id INT NOT NULL,
    date DATE NOT NULL,
    clock_in TIME NULL,
    clock_out TIME NULL,
    break_start TIME NULL,
    break_end TIME NULL,
    total_hours DECIMAL(5,2),
    status ENUM('present','absent','late','half_day','on_leave','holiday') DEFAULT 'present',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE,
    UNIQUE KEY unique_attendance (employee_id, date)
);

-- Shift Schedules
CREATE TABLE shift_schedules (
    id INT AUTO_INCREMENT PRIMARY KEY,
    employee_id INT NOT NULL,
    week_start DATE NOT NULL,
    mon_start TIME, mon_end TIME,
    tue_start TIME, tue_end TIME,
    wed_start TIME, wed_end TIME,
    thu_start TIME, thu_end TIME,
    fri_start TIME, fri_end TIME,
    sat_start TIME, sat_end TIME,
    sun_start TIME, sun_end TIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE
);

-- Timesheets
CREATE TABLE timesheets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    employee_id INT NOT NULL,
    week_starting DATE NOT NULL,
    mon_hours DECIMAL(4,2) DEFAULT 0,
    tue_hours DECIMAL(4,2) DEFAULT 0,
    wed_hours DECIMAL(4,2) DEFAULT 0,
    thu_hours DECIMAL(4,2) DEFAULT 0,
    fri_hours DECIMAL(4,2) DEFAULT 0,
    sat_hours DECIMAL(4,2) DEFAULT 0,
    sun_hours DECIMAL(4,2) DEFAULT 0,
    total_hours DECIMAL(5,2) DEFAULT 0,
    status ENUM('draft','submitted','approved','rejected') DEFAULT 'draft',
    submitted_at TIMESTAMP NULL,
    approved_by INT NULL,
    approved_at TIMESTAMP NULL,
    notes TEXT,
    FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE,
    FOREIGN KEY (approved_by) REFERENCES employees(id) ON DELETE SET NULL
);

-- Leave Types
CREATE TABLE leave_types (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    code VARCHAR(20),
    days_allowed INT DEFAULT 0,
    paid TINYINT DEFAULT 1,
    carry_forward TINYINT DEFAULT 0,
    description TEXT,
    status ENUM('active','inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Leave Requests
CREATE TABLE leave_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    employee_id INT NOT NULL,
    leave_type_id INT NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    total_days INT NOT NULL,
    reason TEXT,
    supporting_doc VARCHAR(500),
    status ENUM('pending','approved','rejected','cancelled') DEFAULT 'pending',
    approved_by INT NULL,
    approved_at TIMESTAMP NULL,
    rejection_reason TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE,
    FOREIGN KEY (leave_type_id) REFERENCES leave_types(id) ON DELETE CASCADE,
    FOREIGN KEY (approved_by) REFERENCES employees(id) ON DELETE SET NULL
);

-- Leave Balances
CREATE TABLE leave_balances (
    id INT AUTO_INCREMENT PRIMARY KEY,
    employee_id INT NOT NULL,
    leave_type_id INT NOT NULL,
    year INT NOT NULL,
    total_days INT DEFAULT 0,
    used_days INT DEFAULT 0,
    pending_days INT DEFAULT 0,
    balance_days INT GENERATED ALWAYS AS (total_days - used_days - pending_days) STORED,
    FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE,
    FOREIGN KEY (leave_type_id) REFERENCES leave_types(id) ON DELETE CASCADE,
    UNIQUE KEY unique_balance (employee_id, leave_type_id, year)
);

-- Payroll Periods
CREATE TABLE payroll_periods (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    pay_date DATE NOT NULL,
    status ENUM('draft','processing','completed','closed') DEFAULT 'draft',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Payroll Entries
CREATE TABLE payroll_entries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    payroll_period_id INT NOT NULL,
    employee_id INT NOT NULL,
    base_salary DECIMAL(12,2) DEFAULT 0.00,
    bonus DECIMAL(12,2) DEFAULT 0.00,
    overtime_pay DECIMAL(12,2) DEFAULT 0.00,
    allowances DECIMAL(12,2) DEFAULT 0.00,
    gross_pay DECIMAL(12,2) DEFAULT 0.00,
    tax_deduction DECIMAL(12,2) DEFAULT 0.00,
    other_deductions DECIMAL(12,2) DEFAULT 0.00,
    net_pay DECIMAL(12,2) DEFAULT 0.00,
    status ENUM('draft','processed','paid') DEFAULT 'draft',
    processed_at TIMESTAMP NULL,
    FOREIGN KEY (payroll_period_id) REFERENCES payroll_periods(id) ON DELETE CASCADE,
    FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE
);

-- Payslips
CREATE TABLE payslips (
    id INT AUTO_INCREMENT PRIMARY KEY,
    payroll_entry_id INT NOT NULL,
    employee_id INT NOT NULL,
    payslip_path VARCHAR(500),
    generated_at TIMESTAMP NULL,
    downloaded_at TIMESTAMP NULL,
    FOREIGN KEY (payroll_entry_id) REFERENCES payroll_entries(id) ON DELETE CASCADE,
    FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE
);

-- Performance Reviews
CREATE TABLE performance_reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    employee_id INT NOT NULL,
    reviewer_id INT NOT NULL,
    review_period VARCHAR(50) NOT NULL,
    review_date DATE NOT NULL,
    goals JSON,
    achievements TEXT,
    strengths TEXT,
    improvements TEXT,
    overall_rating INT CHECK (overall_rating BETWEEN 1 AND 5),
    status ENUM('draft','submitted','acknowledged') DEFAULT 'draft',
    employee_comments TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE,
    FOREIGN KEY (reviewer_id) REFERENCES employees(id) ON DELETE CASCADE
);

-- Training Programs
CREATE TABLE training_programs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    category VARCHAR(100),
    duration_hours INT,
    mode ENUM('online','in-person','hybrid') DEFAULT 'online',
    start_date DATE,
    end_date DATE,
    status ENUM('upcoming','active','completed','cancelled') DEFAULT 'upcoming',
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
);

-- Training Enrollments
CREATE TABLE training_enrollments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    employee_id INT NOT NULL,
    training_program_id INT NOT NULL,
    enrollment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    completion_date TIMESTAMP NULL,
    progress_percent INT DEFAULT 0,
    score INT,
    certificate_path VARCHAR(500),
    status ENUM('enrolled','in_progress','completed','dropped') DEFAULT 'enrolled',
    FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE,
    FOREIGN KEY (training_program_id) REFERENCES training_programs(id) ON DELETE CASCADE
);

-- Announcements
CREATE TABLE announcements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    target_audience ENUM('all','department','role','specific') DEFAULT 'all',
    target_ids JSON,
    priority ENUM('low','normal','high','urgent') DEFAULT 'normal',
    posted_by INT NOT NULL,
    posted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expires_at DATE NULL,
    status ENUM('active','expired','draft') DEFAULT 'draft',
    FOREIGN KEY (posted_by) REFERENCES users(id) ON DELETE CASCADE
);

-- Messages
CREATE TABLE messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sender_id INT NOT NULL,
    receiver_id INT NULL,
    subject VARCHAR(255),
    body TEXT,
    is_broadcast TINYINT DEFAULT 0,
    read_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (receiver_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Notifications
CREATE TABLE notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    type VARCHAR(50) NOT NULL,
    title VARCHAR(255),
    message TEXT,
    link VARCHAR(255),
    is_read TINYINT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Expense Claims
CREATE TABLE expense_claims (
    id INT AUTO_INCREMENT PRIMARY KEY,
    employee_id INT NOT NULL,
    expense_type VARCHAR(50),
    amount DECIMAL(10,2) NOT NULL,
    currency VARCHAR(3) DEFAULT 'NGN',
    expense_date DATE NOT NULL,
    description TEXT,
    receipt_path VARCHAR(500),
    status ENUM('pending','approved','rejected','reimbursed') DEFAULT 'pending',
    approved_by INT NULL,
    approved_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE,
    FOREIGN KEY (approved_by) REFERENCES employees(id) ON DELETE SET NULL
);

-- Assets
CREATE TABLE assets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    asset_tag VARCHAR(50) NOT NULL UNIQUE,
    name VARCHAR(255) NOT NULL,
    category VARCHAR(100),
    model VARCHAR(100),
    serial_number VARCHAR(100),
    purchase_date DATE,
    purchase_cost DECIMAL(10,2),
    status ENUM('available','assigned','maintenance','retired') DEFAULT 'available',
    current_employee_id INT NULL,
    condition_notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (current_employee_id) REFERENCES employees(id) ON DELETE SET NULL
);

-- Asset Assignments
CREATE TABLE asset_assignments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    asset_id INT NOT NULL,
    employee_id INT NOT NULL,
    assigned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    returned_at TIMESTAMP NULL,
    condition_on_return TEXT,
    FOREIGN KEY (asset_id) REFERENCES assets(id) ON DELETE CASCADE,
    FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE
);

-- Support Tickets
CREATE TABLE support_tickets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    employee_id INT NOT NULL,
    category VARCHAR(50),
    subject VARCHAR(255) NOT NULL,
    description TEXT,
    priority ENUM('low','medium','high','urgent') DEFAULT 'medium',
    status ENUM('open','in_progress','resolved','closed') DEFAULT 'open',
    assigned_to INT NULL,
    resolved_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE,
    FOREIGN KEY (assigned_to) REFERENCES users(id) ON DELETE SET NULL
);

-- Audit Logs
CREATE TABLE audit_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    action VARCHAR(50) NOT NULL,
    module VARCHAR(50),
    entity_type VARCHAR(50),
    entity_id INT,
    old_values JSON,
    new_values JSON,
    ip_address VARCHAR(45),
    user_agent VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Company Policies
CREATE TABLE company_policies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    category VARCHAR(100),
    content TEXT,
    file_path VARCHAR(500),
    version VARCHAR(20),
    effective_date DATE,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
);

-- Policy Acknowledgments
CREATE TABLE policy_acknowledgments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    policy_id INT NOT NULL,
    employee_id INT NOT NULL,
    acknowledged_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ip_address VARCHAR(45),
    FOREIGN KEY (policy_id) REFERENCES company_policies(id) ON DELETE CASCADE,
    FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE,
    UNIQUE KEY unique_ack (policy_id, employee_id)
);

-- System Settings
CREATE TABLE system_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) NOT NULL UNIQUE,
    setting_value TEXT,
    setting_type ENUM('string','number','boolean','json') DEFAULT 'string',
    description TEXT,
    updated_by INT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (updated_by) REFERENCES users(id) ON DELETE SET NULL
);

-- Insert default data
INSERT INTO users (email, password_hash, role, status) VALUES 
('admin@hrsuite.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'active'),
('hr@hrsuite.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'hr', 'active'),
('manager@hrsuite.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'manager', 'active'),
('employee@hrsuite.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'employee', 'active');

INSERT INTO departments (name, code, description, location, status) VALUES
('Engineering', 'ENG', 'Software development and engineering', 'New York', 'active'),
('Human Resources', 'HR', 'HR and recruitment', 'New York', 'active'),
('Sales', 'SAL', 'Sales and business development', 'Chicago', 'active'),
('Marketing', 'MKT', 'Marketing and communications', 'New York', 'active'),
('Finance', 'FIN', 'Finance and accounting', 'New York', 'active'),
('Operations', 'OPS', 'Operations and logistics', 'Dallas', 'active');

INSERT INTO roles (name, level, description) VALUES
('CEO', 'executive', 'Chief Executive Officer'),
('Director', 'executive', 'Department Director'),
('Manager', 'manager', 'Team Manager'),
('Senior Engineer', 'senior', 'Senior level engineer'),
('HR Manager', 'manager', 'Human Resources Manager'),
('Sales Representative', 'mid', 'Sales team member'),
('Marketing Lead', 'senior', 'Marketing team lead'),
('Junior Developer', 'junior', 'Junior level developer'),
('Intern', 'intern', 'Intern/trainee');

INSERT INTO leave_types (name, code, days_allowed, paid, description) VALUES
('Annual Leave', 'ANNUAL', 20, 1, 'Standard annual paid leave'),
('Sick Leave', 'SICK', 10, 1, 'Paid sick leave'),
('Maternity Leave', 'MATERNITY', 90, 1, 'Paid maternity leave'),
('Paternity Leave', 'PATERNITY', 14, 1, 'Paid paternity leave'),
('Unpaid Leave', 'UNPAID', 0, 0, 'Unpaid leave for personal reasons'),
('Compassionate Leave', 'COMPASSIONATE', 5, 1, 'Bereavement and compassionate leave');

INSERT INTO system_settings (setting_key, setting_value, setting_type, description) VALUES
('company_name', 'ADEEEEE Corporation', 'string', 'Company display name'),
('timezone', 'America/New_York', 'string', 'System timezone'),
('currency', 'NGN', 'string', 'Default currency'),
('payroll_day', '15', 'number', 'Day of month for payroll processing');


CREATE TABLE password_resets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    token VARCHAR(255) NOT NULL,
    expires_at DATETIME NOT NULL,
    used TINYINT(1) DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_token (token),
    INDEX idx_expires (expires_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
