-- Add driver assignment columns to transport_requests table
ALTER TABLE `transport_requests`
  ADD COLUMN `assigned_driver_id` VARCHAR(12) DEFAULT NULL AFTER `status`,
  ADD COLUMN `assigned_vehicle_no` VARCHAR(15) DEFAULT NULL AFTER `assigned_driver_id`,
  ADD KEY `idx_assigned_driver` (`assigned_driver_id`);
