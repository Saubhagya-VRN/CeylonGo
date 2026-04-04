ALTER TABLE `package_bookings`
  ADD COLUMN `refund_requested_at` DATETIME DEFAULT NULL COMMENT 'When tourist submitted refund request' AFTER `bank_transfer_submitted_at`,
  ADD COLUMN `refund_reason` VARCHAR(2000) DEFAULT NULL COMMENT 'Optional reason from tourist' AFTER `refund_requested_at`;
