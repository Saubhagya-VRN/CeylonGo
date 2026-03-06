-- Fix whitespace issues in transport_vehicle user_id column
-- This script trims leading and trailing whitespace from user_id values

UPDATE transport_vehicle 
SET user_id = TRIM(user_id) 
WHERE user_id != TRIM(user_id);

-- Verify the update
SELECT vehicle_no, CONCAT('[', user_id, ']') as user_id_with_brackets, vehicle_type, psg_capacity 
FROM transport_vehicle;
