-- Custom trip refund requests (run once if columns missing).
ALTER TABLE `trips`
  ADD COLUMN `refund_requested_at` DATETIME DEFAULT NULL COMMENT 'When tourist submitted refund request' AFTER `paid_at`,
  ADD COLUMN `refund_reason` VARCHAR(2000) DEFAULT NULL COMMENT 'Optional reason from tourist' AFTER `refund_requested_at`;
