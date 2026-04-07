-- Run once on existing databases (phpMyAdmin SQL tab or mysql CLI).
-- Adds column used when tourist completes bank-transfer flow (Continue → My Bookings).

ALTER TABLE `package_bookings`
  ADD COLUMN `bank_transfer_submitted_at` DATETIME DEFAULT NULL
  COMMENT 'Tourist clicked Continue after bank transfer; awaiting manual verification'
  AFTER `paid_at`;
