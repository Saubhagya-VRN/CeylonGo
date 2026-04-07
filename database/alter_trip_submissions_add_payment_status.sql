-- Add payment_status column to trip_submissions for easy filtering/reporting.
-- Values: pending, payment_submitted, completed, refunded
ALTER TABLE `trip_submissions`
  ADD COLUMN `payment_status` ENUM('pending','payment_submitted','completed','refunded') NOT NULL DEFAULT 'pending' AFTER `trip_json`,
  ADD KEY `payment_status` (`payment_status`);

