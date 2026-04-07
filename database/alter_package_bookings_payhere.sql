-- Run once on existing DB: PayHere paid status + receipt id (phpMyAdmin or mysql CLI).

ALTER TABLE `package_bookings`
  MODIFY COLUMN `status` ENUM('pending', 'approved', 'rejected', 'cancelled', 'paid') NOT NULL DEFAULT 'pending',
  ADD COLUMN `payhere_payment_id` VARCHAR(64) DEFAULT NULL AFTER `status`,
  ADD COLUMN `paid_at` DATETIME DEFAULT NULL AFTER `payhere_payment_id`;
