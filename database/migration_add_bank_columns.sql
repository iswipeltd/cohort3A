-- ============================================
-- MIGRATION: Add bank columns to employees table
-- Run this if you get "Unknown column 'e.bank_code'" error
-- ============================================

ALTER TABLE employees
    ADD COLUMN IF NOT EXISTS bank_name VARCHAR(100) AFTER profile_picture,
    ADD COLUMN IF NOT EXISTS bank_account VARCHAR(50) AFTER bank_name,
    ADD COLUMN IF NOT EXISTS bank_code VARCHAR(20) AFTER bank_account;

-- If your MariaDB version doesn't support IF NOT EXISTS, use this instead:
-- ALTER TABLE employees ADD COLUMN bank_name VARCHAR(100) AFTER profile_picture;
-- ALTER TABLE employees ADD COLUMN bank_account VARCHAR(50) AFTER bank_name;
-- ALTER TABLE employees ADD COLUMN bank_code VARCHAR(20) AFTER bank_account;
