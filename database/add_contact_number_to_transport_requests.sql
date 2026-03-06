-- Add contact_number field to tourist_transport_requests table
ALTER TABLE `tourist_transport_requests` 
ADD COLUMN `contactNumber` VARCHAR(15) NOT NULL AFTER `customerName`;
