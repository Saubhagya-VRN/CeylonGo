-- Fix guide accounts with NULL ref_id in users table
-- This updates the ref_id to match the guide_users.id based on email

UPDATE users u
INNER JOIN guide_users g ON u.email = g.email
SET u.ref_id = g.id
WHERE u.role = 'guide' AND (u.ref_id IS NULL OR u.ref_id = '');

-- Verify the fix
SELECT u.id, u.email, u.role, u.ref_id, g.id as guide_id, g.first_name, g.last_name
FROM users u
LEFT JOIN guide_users g ON u.email = g.email AND u.role = 'guide'
WHERE u.role = 'guide';
