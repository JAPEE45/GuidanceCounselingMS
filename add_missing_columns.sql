-- Add missing columns to students table
-- Run this script to add middle_name and department fields

USE gcms_db;

-- Add middle_name column (optional field)
ALTER TABLE students 
ADD COLUMN middle_name VARCHAR(100) NULL AFTER first_name;

-- Add department column
ALTER TABLE students 
ADD COLUMN department VARCHAR(100) NULL AFTER year_level;

-- Verify the changes
DESCRIBE students;
