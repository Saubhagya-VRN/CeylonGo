-- Add more vehicle types to transport_vehicle_types table

INSERT INTO `transport_vehicle_types` (`type_name`) VALUES
('CAR'),
('SUV'),
('MINI VAN'),
('BUS'),
('MINI BUS'),
('LUXURY CAR'),
('MOTORCYCLE'),
('SCOOTER');

-- Display all vehicle types
SELECT * FROM transport_vehicle_types ORDER BY type_id;
