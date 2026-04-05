-- Run once if `trips` already exists without payment columns (customise-trip Payments step).
ALTER TABLE `trips`
  ADD COLUMN `budget_lkr` decimal(12,2) DEFAULT NULL AFTER `number_of_days`,
  ADD COLUMN `payhere_payment_id` varchar(64) DEFAULT NULL AFTER `status`,
  ADD COLUMN `paid_at` datetime DEFAULT NULL AFTER `payhere_payment_id`,
  ADD COLUMN `bank_transfer_submitted_at` datetime DEFAULT NULL AFTER `paid_at`,
  ADD COLUMN `bank_transfer_slip_path` varchar(255) DEFAULT NULL AFTER `bank_transfer_submitted_at`;
