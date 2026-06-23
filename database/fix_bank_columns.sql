-- ============================================
-- MANUAL FIX: Run this in phpMyAdmin SQL tab
-- If you see "Unknown column 'bank_code'" error
-- ============================================

-- Step 1: Add the missing columns to employees table
ALTER TABLE employees 
ADD COLUMN bank_name VARCHAR(100) NULL AFTER profile_picture,
ADD COLUMN bank_account VARCHAR(50) NULL AFTER bank_name,
ADD COLUMN bank_code VARCHAR(20) NULL AFTER bank_account;

-- Step 2: Verify columns were added (optional check)
-- SHOW COLUMNS FROM employees;
