-- Align `reviews` with tourist review form + admin: drop destination; add reply fields.
-- Backup first. If a statement errors (column already changed), skip that line and continue.

ALTER TABLE `reviews` DROP COLUMN `destination`;

ALTER TABLE `reviews`
  ADD COLUMN `admin_reply` text DEFAULT NULL AFTER `review_text`,
  ADD COLUMN `replied_at` datetime DEFAULT NULL AFTER `admin_reply`;
