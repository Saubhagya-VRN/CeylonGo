-- Run once (phpMyAdmin SQL tab or mysql CLI).
-- Stores relative path under public/uploads/ for the tourist's bank slip image.

ALTER TABLE `package_bookings`
  ADD COLUMN `bank_transfer_slip_path` VARCHAR(500) DEFAULT NULL
  COMMENT 'Relative path under public/uploads (e.g. bank_slips/booking_1_....jpg)'
  AFTER `bank_transfer_submitted_at`;
