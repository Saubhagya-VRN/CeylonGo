-- If you already created `inquiries`, run this to support guest inquiries.
ALTER TABLE `inquiries`
  MODIFY COLUMN `user_id` INT(11) NULL,
  ADD COLUMN `guest_name` VARCHAR(120) NULL AFTER `user_id`,
  ADD COLUMN `guest_email` VARCHAR(190) NULL AFTER `guest_name`;

CREATE INDEX `idx_inquiries_guest_email` ON `inquiries` (`guest_email`);

