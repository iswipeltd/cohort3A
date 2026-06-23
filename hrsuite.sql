-- ADEEEEE Full Database Schema
-- Run this in MySQL/MariaDB to initialize the database
-- CREATE DATABASE hrsuite DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- USE hrsuite;

SET FOREIGN_KEY_CHECKS = 0;

-- Drop existing tables if re-running
DROP TABLE IF EXISTS notifications;
DROP TABLE IF EXISTS messages;
DROP TABLE IF EXISTS tasks;
DROP TABLE IF EXISTS asset_logs;
DROP TABLE IF EXISTS asset_requests;
DROP TABLE IF EXISTS assets;
DROP TABLE IF EXISTS expenses;
DROP TABLE IF EXISTS payslips;
DROP TABLE IF EXISTS payroll_records;
DROP TABLE IF EXISTS payroll_periods;
DROP TABLE IF EXISTS timesheets;
DROP TABLE IF EXISTS attendance;
DROP TABLE IF EXISTS leave_requests;
DROP TABLE IF EXISTS leave_types;
DROP TABLE IF EXISTS training_enrollments;
DROP TABLE IF EXISTS training_programs;
DROP TABLE IF EXISTS candidate_notes;
DROP TABLE IF EXISTS candidates;
DROP TABLE IF EXISTS job_postings;
DROP TABLE IF EXISTS performance_reviews;
DROP TABLE IF EXISTS kpi_records;
DROP TABLE IF EXISTS documents;
DROP TABLE IF EXISTS faqs;
DROP TABLE IF EXISTS employees;
DROP TABLE IF EXISTS roles;
DROP TABLE IF EXISTS departments;
DROP TABLE IF EXISTS activity_logs;
DROP TABLE IF EXISTS announcements;
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS settings;

SET FOREIGN_KEY_CHECKS = 1;

-- ============================================
-- CORE AUTH & USERS
-- ============================================
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(150) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('admin','hr','manager','employee') DEFAULT 'employee',
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    phone VARCHAR(30),
    avatar VARCHAR(255),
    status ENUM('active','inactive','suspended') DEFAULT 'active',
    two_factor_enabled TINYINT(1) DEFAULT 0,
    two_factor_secret VARCHAR(64),
    last_login DATETIME,
    last_login_ip VARCHAR(45),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_role (role),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- ORGANIZATION
-- ============================================
CREATE TABLE departments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    manager_id INT NULL,
    location VARCHAR(100),
    status ENUM('active','inactive') DEFAULT 'active',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    level INT DEFAULT 1 COMMENT '1=Employee, 2=Manager, 3=HR, 4=Admin',
    description TEXT,
    permissions JSON,
    status ENUM('active','inactive') DEFAULT 'active',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- EMPLOYEES
-- ============================================
CREATE TABLE employees (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNIQUE,
    employee_code VARCHAR(50) UNIQUE,
    department_id INT,
    role_id INT,
    manager_id INT NULL,
    salary DECIMAL(12,2) DEFAULT 0.00,
    currency VARCHAR(3) DEFAULT 'NGN',
    employment_type ENUM('full-time','part-time','contract','intern') DEFAULT 'full-time',
    start_date DATE,
    end_date DATE NULL,
    work_location VARCHAR(100),
    status ENUM('active','inactive','terminated','on_leave','probation') DEFAULT 'active',
    address TEXT,
    city VARCHAR(100),
    country VARCHAR(100),
    emergency_name VARCHAR(100),
    emergency_phone VARCHAR(30),
    emergency_relationship VARCHAR(50),
    bank_name VARCHAR(100),
    bank_account VARCHAR(50),
    bank_code VARCHAR(20) NULL,
    tax_id VARCHAR(50),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE SET NULL,
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE SET NULL,
    FOREIGN KEY (manager_id) REFERENCES employees(id) ON DELETE SET NULL,
    INDEX idx_status (status),
    INDEX idx_dept (department_id),
    INDEX idx_manager (manager_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Link departments manager FK after employees exists
ALTER TABLE departments ADD CONSTRAINT fk_dept_manager FOREIGN KEY (manager_id) REFERENCES employees(id) ON DELETE SET NULL;

-- ============================================
-- DOCUMENTS
-- ============================================
CREATE TABLE documents (
    id INT AUTO_INCREMENT PRIMARY KEY,
    employee_id INT,
    type ENUM('cv','contract','id_card','certificate','tax_form','other') DEFAULT 'other',
    title VARCHAR(255),
    filename VARCHAR(255),
    file_path VARCHAR(500),
    uploaded_by INT,
    uploaded_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE,
    FOREIGN KEY (uploaded_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_emp_doc (employee_id, type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- LEAVE
-- ============================================
CREATE TABLE leave_types (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    default_days INT DEFAULT 0,
    carry_forward TINYINT(1) DEFAULT 0,
    max_carry_forward INT DEFAULT 0,
    requires_approval TINYINT(1) DEFAULT 1,
    paid TINYINT(1) DEFAULT 1,
    color VARCHAR(7) DEFAULT '#0d6efd',
    status ENUM('active','inactive') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE leave_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    employee_id INT NOT NULL,
    leave_type_id INT NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    days INT NOT NULL,
    reason TEXT,
    attachment VARCHAR(255),
    status ENUM('pending','approved','rejected','cancelled') DEFAULT 'pending',
    approved_by INT NULL,
    approved_at DATETIME NULL,
    rejection_reason TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE,
    FOREIGN KEY (leave_type_id) REFERENCES leave_types(id) ON DELETE RESTRICT,
    FOREIGN KEY (approved_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_status (status),
    INDEX idx_employee (employee_id),
    INDEX idx_dates (start_date, end_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- ATTENDANCE & TIME
-- ============================================
CREATE TABLE attendance (
    id INT AUTO_INCREMENT PRIMARY KEY,
    employee_id INT NOT NULL,
    record_date DATE NOT NULL,
    clock_in TIME NULL,
    clock_out TIME NULL,
    break_start TIME NULL,
    break_end TIME NULL,
    status ENUM('present','absent','late','half_day','on_leave','holiday','remote') DEFAULT 'present',
    hours_worked DECIMAL(4,2) DEFAULT 0.00,
    overtime DECIMAL(4,2) DEFAULT 0.00,
    notes TEXT,
    device VARCHAR(50) COMMENT 'web, biometric, mobile',
    geo_latitude DECIMAL(10,8) NULL,
    geo_longitude DECIMAL(11,8) NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE,
    UNIQUE KEY uk_attendance_date (employee_id, record_date),
    INDEX idx_date (record_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE timesheets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    employee_id INT NOT NULL,
    week_start DATE NOT NULL,
    mon DECIMAL(4,2) DEFAULT 0,
    tue DECIMAL(4,2) DEFAULT 0,
    wed DECIMAL(4,2) DEFAULT 0,
    thu DECIMAL(4,2) DEFAULT 0,
    fri DECIMAL(4,2) DEFAULT 0,
    sat DECIMAL(4,2) DEFAULT 0,
    sun DECIMAL(4,2) DEFAULT 0,
    total DECIMAL(5,2) DEFAULT 0,
    status ENUM('draft','submitted','approved','rejected') DEFAULT 'draft',
    approved_by INT NULL,
    approved_at DATETIME NULL,
    submitted_at DATETIME NULL,
    FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE,
    FOREIGN KEY (approved_by) REFERENCES users(id) ON DELETE SET NULL,
    UNIQUE KEY uk_timesheet_week (employee_id, week_start)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- PAYROLL
-- ============================================
CREATE TABLE payroll_periods (
    id INT AUTO_INCREMENT PRIMARY KEY,
    month INT NOT NULL,
    year INT NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    status ENUM('open','processing','closed','paid') DEFAULT 'open',
    processed_by INT NULL,
    processed_at DATETIME NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uk_period (month, year),
    FOREIGN KEY (processed_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE payroll_records (
    id INT AUTO_INCREMENT PRIMARY KEY,
    period_id INT NOT NULL,
    employee_id INT NOT NULL,
    base_salary DECIMAL(12,2) DEFAULT 0.00,
    bonus DECIMAL(12,2) DEFAULT 0.00,
    overtime_pay DECIMAL(12,2) DEFAULT 0.00,
    allowances DECIMAL(12,2) DEFAULT 0.00,
    gross_pay DECIMAL(12,2) DEFAULT 0.00,
    deductions DECIMAL(12,2) DEFAULT 0.00,
    tax DECIMAL(12,2) DEFAULT 0.00,
    insurance DECIMAL(12,2) DEFAULT 0.00,
    pension DECIMAL(12,2) DEFAULT 0.00,
    net_pay DECIMAL(12,2) DEFAULT 0.00,
    status ENUM('draft','generated','paid') DEFAULT 'draft',
    paid_at DATETIME DEFAULT NULL,
    paid_by INT DEFAULT NULL,
    FOREIGN KEY (period_id) REFERENCES payroll_periods(id) ON DELETE CASCADE,
    FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE,
    UNIQUE KEY uk_payroll (period_id, employee_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE payslips (
    id INT AUTO_INCREMENT PRIMARY KEY,
    payroll_record_id INT NOT NULL,
    generated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    pdf_path VARCHAR(500),
    sent_email TINYINT(1) DEFAULT 0,
    FOREIGN KEY (payroll_record_id) REFERENCES payroll_records(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- RECRUITMENT
-- ============================================
CREATE TABLE job_postings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    department_id INT,
    description TEXT,
    requirements TEXT,
    location VARCHAR(100),
    type ENUM('full-time','part-time','contract','intern','remote') DEFAULT 'full-time',
    salary_min DECIMAL(12,2),
    salary_max DECIMAL(12,2),
    status ENUM('open','paused','closed') DEFAULT 'open',
    posted_by INT,
    posted_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    closes_at DATE NULL,
    FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE SET NULL,
    FOREIGN KEY (posted_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE candidates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    job_posting_id INT,
    first_name VARCHAR(100),
    last_name VARCHAR(100),
    email VARCHAR(150),
    phone VARCHAR(30),
    resume_path VARCHAR(500),
    cover_letter TEXT,
    stage ENUM('new','screening','interview','offer','hired','rejected') DEFAULT 'new',
    status ENUM('active','withdrawn','archived') DEFAULT 'active',
    source VARCHAR(50),
    applied_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    interview_date DATETIME NULL,
    offer_sent_at DATETIME NULL,
    hired_at DATETIME NULL,
    FOREIGN KEY (job_posting_id) REFERENCES job_postings(id) ON DELETE CASCADE,
    INDEX idx_stage (stage),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE candidate_notes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    candidate_id INT NOT NULL,
    user_id INT,
    note TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (candidate_id) REFERENCES candidates(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- PERFORMANCE
-- ============================================
CREATE TABLE kpi_records (
    id INT AUTO_INCREMENT PRIMARY KEY,
    employee_id INT NOT NULL,
    title VARCHAR(200),
    description TEXT,
    target_value DECIMAL(10,2),
    actual_value DECIMAL(10,2),
    unit VARCHAR(20),
    period_start DATE,
    period_end DATE,
    status ENUM('active','completed','missed') DEFAULT 'active',
    created_by INT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE performance_reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    employee_id INT NOT NULL,
    reviewer_id INT,
    review_period_start DATE,
    review_period_end DATE,
    overall_rating DECIMAL(3,2),
    strengths TEXT,
    improvements TEXT,
    goals JSON,
    status ENUM('draft','submitted','acknowledged') DEFAULT 'draft',
    submitted_at DATETIME NULL,
    acknowledged_at DATETIME NULL,
    FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE,
    FOREIGN KEY (reviewer_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- TRAINING
-- ============================================
CREATE TABLE training_programs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    duration_hours INT,
    mode ENUM('online','in-person','hybrid') DEFAULT 'online',
    status ENUM('active','upcoming','completed','archived') DEFAULT 'upcoming',
    created_by INT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE training_enrollments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    program_id INT NOT NULL,
    employee_id INT NOT NULL,
    enrolled_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    started_at DATETIME NULL,
    completed_at DATETIME NULL,
    progress_percent INT DEFAULT 0,
    score DECIMAL(5,2) NULL,
    certificate_path VARCHAR(500),
    status ENUM('pending','enrolled','in_progress','completed','dropped') DEFAULT 'pending',
    FOREIGN KEY (program_id) REFERENCES training_programs(id) ON DELETE CASCADE,
    FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE,
    UNIQUE KEY uk_enrollment (program_id, employee_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- COMMUNICATION
-- ============================================
CREATE TABLE announcements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    target_audience ENUM('all','department','role','custom') DEFAULT 'all',
    target_department_id INT NULL,
    pinned TINYINT(1) DEFAULT 0,
    posted_by INT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    expires_at DATETIME NULL,
    FOREIGN KEY (posted_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_audience (target_audience)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    from_user_id INT NOT NULL,
    to_user_id INT NULL,
    to_department_id INT NULL COMMENT 'Broadcast to department',
    subject VARCHAR(255),
    body TEXT,
    read_at DATETIME NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (from_user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (to_user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (to_department_id) REFERENCES departments(id) ON DELETE CASCADE,
    INDEX idx_to_user (to_user_id, read_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    type VARCHAR(50),
    message VARCHAR(500),
    link VARCHAR(255),
    read_at DATETIME NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_read (user_id, read_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- EXPENSES & ASSETS
-- ============================================
CREATE TABLE expenses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    employee_id INT NOT NULL,
    type VARCHAR(50),
    amount DECIMAL(10,2) NOT NULL,
    currency VARCHAR(3) DEFAULT 'NGN',
    expense_date DATE,
    description TEXT,
    receipt_path VARCHAR(500),
    status ENUM('pending','approved','rejected','reimbursed') DEFAULT 'pending',
    approved_by INT NULL,
    approved_at DATETIME NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE,
    FOREIGN KEY (approved_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE assets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    asset_code VARCHAR(50) UNIQUE,
    name VARCHAR(200) NOT NULL,
    type VARCHAR(50),
    brand VARCHAR(100),
    model VARCHAR(100),
    serial_number VARCHAR(100),
    purchase_date DATE,
    purchase_cost DECIMAL(12,2),
    assigned_to INT NULL,
    assigned_at DATETIME NULL,
    condition_rating ENUM('excellent','good','fair','poor','damaged','lost') DEFAULT 'excellent',
    status ENUM('available','assigned','maintenance','retired','lost') DEFAULT 'available',
    notes TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (assigned_to) REFERENCES employees(id) ON DELETE SET NULL,
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE asset_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    employee_id INT NOT NULL,
    asset_type VARCHAR(50) NOT NULL,
    description TEXT,
    justification TEXT,
    urgency ENUM('low','medium','high') DEFAULT 'medium',
    status ENUM('pending','approved','rejected','fulfilled','cancelled') DEFAULT 'pending',
    requested_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    processed_at DATETIME NULL,
    processed_by INT NULL,
    notes TEXT,
    FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE,
    FOREIGN KEY (processed_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_status (status),
    INDEX idx_employee (employee_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE asset_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    asset_id INT NOT NULL,
    employee_id INT,
    action ENUM('assigned','returned','reported','repaired','retired') NOT NULL,
    notes TEXT,
    performed_by INT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (asset_id) REFERENCES assets(id) ON DELETE CASCADE,
    FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE SET NULL,
    FOREIGN KEY (performed_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- TASKS
-- ============================================
CREATE TABLE tasks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    project VARCHAR(100),
    assigned_to INT,
    assigned_by INT,
    due_date DATE,
    priority ENUM('low','medium','high','urgent') DEFAULT 'medium',
    progress INT DEFAULT 0,
    status ENUM('open','in_progress','review','completed','cancelled') DEFAULT 'open',
    completed_at DATETIME NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (assigned_to) REFERENCES employees(id) ON DELETE SET NULL,
    FOREIGN KEY (assigned_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_assigned (assigned_to, status),
    INDEX idx_due (due_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- FAQS
-- ============================================
CREATE TABLE faqs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    question VARCHAR(500) NOT NULL,
    answer TEXT NOT NULL,
    category VARCHAR(100) DEFAULT 'General',
    sort_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_by INT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_category (category),
    INDEX idx_active (is_active, sort_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- AUDIT & SECURITY
-- ============================================
CREATE TABLE activity_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    action VARCHAR(50) NOT NULL,
    module VARCHAR(50),
    record_id INT,
    details TEXT,
    ip_address VARCHAR(45),
    user_agent VARCHAR(255),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_user (user_id),
    INDEX idx_module (module),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- SETTINGS
-- ============================================
CREATE TABLE settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT,
    setting_group VARCHAR(50) DEFAULT 'general',
    updated_by INT,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (updated_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- SEED DATA
-- ============================================

-- Default leave types
INSERT INTO leave_types (name, default_days, carry_forward, max_carry_forward, requires_approval, paid, color) VALUES
('Annual Leave', 20, 1, 5, 1, 1, '#0d6efd'),
('Sick Leave', 10, 0, 0, 1, 1, '#198754'),
('Maternity Leave', 90, 0, 0, 1, 1, '#dc3545'),
('Paternity Leave', 14, 0, 0, 1, 1, '#fd7e14'),
('Unpaid Leave', 0, 0, 0, 1, 0, '#6c757d'),
('Compassionate Leave', 5, 0, 0, 1, 1, '#6610f2'),
('Study Leave', 10, 0, 0, 1, 1, '#20c997');

-- Default departments
INSERT INTO departments (name, description, location, status) VALUES
('Engineering', 'Software development and technical operations', 'New York', 'active'),
('Sales', 'Revenue generation and client relationships', 'Chicago', 'active'),
('Human Resources', 'People operations and talent management', 'New York', 'active'),
('Marketing', 'Brand, communications, and growth', 'Los Angeles', 'active'),
('Finance', 'Accounting, payroll, and financial planning', 'New York', 'active'),
('Operations', 'Business operations and logistics', 'Chicago', 'active');

-- Default roles
INSERT INTO roles (name, level, description, status) VALUES
('Admin', 4, 'Full system access', 'active'),
('HR Manager', 3, 'HR operations and people management', 'active'),
('Manager', 2, 'Team lead with reporting access', 'active'),
('Senior Employee', 1, 'Experienced individual contributor', 'active'),
('Employee', 1, 'Standard individual contributor', 'active'),
('Intern', 1, 'Temporary trainee', 'active');

-- Default admin user (password: Admin@123) - bcrypt hashed
INSERT INTO users (email, password_hash, role, first_name, last_name, phone, status) VALUES
('admin@hrsuite.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'System', 'Administrator', '+1-555-0100', 'active');

-- Default employee user (password: Employee@123)
INSERT INTO users (email, password_hash, role, first_name, last_name, phone, status) VALUES
('john.doe@hrsuite.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'employee', 'John', 'Doe', '+1-555-0101', 'active');

-- Link employees
INSERT INTO employees (user_id, employee_code, department_id, role_id, manager_id, salary, employment_type, start_date, work_location, status, address, city, country, emergency_name, emergency_phone, emergency_relationship, bank_name, bank_account, bank_code, tax_id) VALUES
(1, 'EMP-ADM-001', 3, 1, NULL, 8000.00, 'full-time', '2020-01-15', 'New York', 'active', '100 Admin Blvd', 'New York', 'USA', 'Jane Admin', '+1-555-0200', 'Spouse', 'Access Bank PLC', '000123456789', '044', 'TAX-001'),
(2, 'EMP-001', 1, 4, 1, 5800.00, 'full-time', '2022-03-10', 'New York', 'active', '200 Oak Street', 'New York', 'USA', 'Mary Doe', '+1-555-0201', 'Mother', 'Access Bank PLC', '000987654321', '044', 'TAX-002');

-- Sample tasks
INSERT INTO tasks (title, description, project, assigned_to, assigned_by, due_date, priority, progress, status) VALUES
('Prepare Q2 Report', 'Compile quarterly analytics for leadership review', 'Annual Review', 2, 1, '2026-05-20', 'high', 40, 'in_progress'),
('Client Presentation', 'Prepare slides for the Acme Corp pitch', 'Sales Pitch', 2, 1, '2026-05-15', 'medium', 75, 'in_progress'),
('Update Emergency Contacts', 'Verify and update personal emergency contact details', 'HR Initiative', 2, 1, '2026-05-10', 'low', 100, 'completed');

-- Sample attendance
INSERT INTO attendance (employee_id, record_date, clock_in, clock_out, status, hours_worked, device) VALUES
(2, '2026-05-05', '08:30:00', '17:00:00', 'present', 8.50, 'web'),
(2, '2026-05-06', '08:45:00', '17:00:00', 'late', 8.25, 'web'),
(2, '2026-05-07', '08:30:00', '17:00:00', 'present', 8.50, 'web'),
(2, '2026-05-08', '08:30:00', NULL, 'present', 0.00, 'web');

-- Sample leave request
INSERT INTO leave_requests (employee_id, leave_type_id, start_date, end_date, days, reason, status) VALUES
(2, 1, '2026-05-15', '2026-05-19', 5, 'Family trip to Florida', 'pending');

-- Sample expense
INSERT INTO expenses (employee_id, type, amount, expense_date, description, status) VALUES
(2, 'Travel', 120.00, '2026-05-05', 'Taxi and mileage to client site', 'approved');

-- Sample job posting
INSERT INTO job_postings (title, department_id, description, requirements, location, type, status, posted_by) VALUES
('Senior PHP Developer', 1, 'We are looking for an experienced backend engineer.', '5+ years PHP, MySQL, Bootstrap', 'New York', 'full-time', 'open', 1);

-- Sample training program
INSERT INTO training_programs (title, description, duration_hours, mode, status, created_by) VALUES
('Leadership Q2 2026', 'Management and leadership skills for emerging leaders.', 20, 'hybrid', 'active', 1);

-- Sample announcement
INSERT INTO announcements (title, message, target_audience, pinned, posted_by) VALUES
('Company Offsite 2026', 'Annual team building event scheduled for May 20 at the lakeside resort.', 'all', 1, 1);

-- Default settings
INSERT INTO settings (setting_key, setting_value, setting_group) VALUES
('company_name', 'ADEEEEE Demo Corp', 'general'),
('timezone', 'America/New_York', 'general'),
('default_currency', 'NGN', 'payroll'),
('tax_rate', '10', 'payroll'),
('enable_2fa', '0', 'security'),
('password_policy', 'strong', 'security');


CREATE TABLE payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    payroll_record_id INT NOT NULL,
    employee_id INT NOT NULL,
    payment_method VARCHAR(50) DEFAULT 'novac_payout',
    reference_number VARCHAR(100),
    amount DECIMAL(12,2) DEFAULT 0.00,
    payment_date DATE,
    status ENUM('pending','completed','failed','refunded') DEFAULT 'pending',
    notes TEXT,
    created_by INT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (payroll_record_id) REFERENCES payroll_records(id) ON DELETE CASCADE,
    FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Default FAQ seed data
INSERT INTO faqs (question, answer, category, sort_order, is_active) VALUES
('How do I apply for leave?', 'Go to the Employee Portal > Leave > Apply Leave. Select your leave type, enter start and end dates, add a reason, and submit. Your manager will be notified for approval.', 'Leave', 1, 1),
('How can I check my leave balance?', 'Navigate to Employee Portal > Leave > Leave Balance. You will see your remaining days for annual, sick, casual, and other leave types.', 'Leave', 2, 1),
('When will I receive my payslip?', 'Payslips are generated after payroll is processed each month. You can download yours from Employee Portal > Payroll > My Payslips.', 'Payroll', 1, 1),
('How do I update my personal details?', 'Go to Employee Portal > Profile > Edit Profile. Update your phone, address, emergency contacts, and save the changes.', 'Profile', 1, 1),
('How do I clock in/out for work?', 'Visit Employee Portal > Attendance > Clock In/Out. Click the button to record your attendance. Ensure your device location is enabled if required.', 'Attendance', 1, 1),
('How do I submit an expense claim?', 'Go to Employee Portal > Expenses > Submit Expense. Upload your receipt, enter the amount and description, then submit for approval.', 'Expenses', 1, 1),
('Who do I contact for HR issues?', 'Use Employee Portal > Support > Contact HR to send a message, or open a support ticket for faster response.', 'Support', 1, 1),
('How do I enroll in a training course?', 'Visit Employee Portal > Training > Enroll Courses. Browse available programs and click Enroll on the course you want.', 'Training', 1, 1),
('What should I do if I forget my password?', 'Click Forgot Password on the login page and enter your email. A reset link will be sent to your registered email address.', 'Account', 1, 1),
('How do I request an asset (laptop, phone, etc.)?', 'Go to Employee Portal > Assets > Request Asset. Select the item type, provide justification, and submit. HR will review your request.', 'Assets', 1, 1);

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
