-- Add status column to guide_requests table
-- This allows tracking of pending/approved/rejected requests

ALTER TABLE `guide_requests` 
ADD COLUMN `status` ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending' AFTER `notes`,
ADD COLUMN `guide_id` INT NULL AFTER `status`,
ADD COLUMN `approved_at` TIMESTAMP NULL AFTER `guide_id`,
ADD INDEX `idx_status` (`status`),
ADD INDEX `idx_guide_id` (`guide_id`);

-- Update existing records to have pending status
UPDATE `guide_requests` SET `status` = 'pending' WHERE `status` IS NULL OR `status` = '';
