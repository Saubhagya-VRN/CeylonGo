-- Allow general (non–package-specific) reviews: package_id may be NULL.
-- Run on existing databases after backup: mysql -u root ceylon_go < database/update_package_reviews_nullable_package_id.sql

ALTER TABLE `package_reviews`
  MODIFY `package_id` int(10) UNSIGNED DEFAULT NULL COMMENT 'NULL = general / service review; set for legacy package-specific rows';
