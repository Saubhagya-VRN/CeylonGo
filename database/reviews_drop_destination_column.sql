-- Live DB only: remove `destination` from `reviews` (tourist feedback table).
-- Fresh installs: `main.sql` already has no `destination` on `reviews`.
-- If MySQL says "Unknown column", the column is already gone — skip.

ALTER TABLE `reviews` DROP COLUMN `destination`;
