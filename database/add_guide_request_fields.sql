-- Migration to add contact number and time fields to tourist_guide_requests table
-- Run this if the table already exists without these fields

-- Check if columns exist and add them if they don't
ALTER TABLE tourist_guide_requests 
ADD COLUMN IF NOT EXISTS contactNumber VARCHAR(20) NOT NULL AFTER customerName,
ADD COLUMN IF NOT EXISTS time TIME NOT NULL AFTER date;

-- Alternative for MySQL versions that don't support IF NOT EXISTS:
-- ALTER TABLE tourist_guide_requests ADD COLUMN contactNumber VARCHAR(20) NOT NULL AFTER customerName;
-- ALTER TABLE tourist_guide_requests ADD COLUMN time TIME NOT NULL AFTER date;
