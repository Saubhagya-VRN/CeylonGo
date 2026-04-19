-- Run only if you mistakenly dropped `package_reviews.destination`.
-- Normal setup: column exists in main.sql / migrate_package_reviews.sql.

ALTER TABLE `package_reviews`
  ADD COLUMN `destination` varchar(255) DEFAULT NULL COMMENT 'Package title snapshot at submit' AFTER `review_text`;
