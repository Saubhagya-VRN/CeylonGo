-- Fix guide accounts by syncing ref_id in users table with id from guide_users table
-- This connects the two tables using email as the matching key

-- Step 1: Update ref_id in users table to match the guide_users.id based on email
UPDATE users u
INNER JOIN guide_users g ON u.email = g.email
SET u.ref_id = g.id
WHERE u.role = 'guide';

-- Step 2: Verify the fix - shows all guide users with their linked IDs
SELECT 
    u.id AS users_table_id, 
    u.email, 
    u.role, 
    u.ref_id AS users_ref_id, 
    g.id AS guide_users_id, 
    g.first_name, 
    g.last_name,
    CASE 
        WHEN u.ref_id = g.id THEN 'MATCHED ✓'
        ELSE 'MISMATCH ✗'
    END AS status
FROM users u
LEFT JOIN guide_users g ON u.email = g.email
WHERE u.role = 'guide';
