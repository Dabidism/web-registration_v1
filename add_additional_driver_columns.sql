-- SQL script to add additional driver columns to vehicleowner and applications tables
-- This allows vehicle owners to register an additional driver

-- Add columns to vehicleowner table
ALTER TABLE `vehicleowner` 
ADD COLUMN `additional_driver_name` VARCHAR(100) NULL AFTER `drivers_license`,
ADD COLUMN `additional_driver_relationship` VARCHAR(50) NULL AFTER `additional_driver_name`;

-- Add columns to applications table (for pending applications)
ALTER TABLE `applications` 
ADD COLUMN `additional_driver_name` VARCHAR(100) NULL AFTER `drivers_license`,
ADD COLUMN `additional_driver_relationship` VARCHAR(50) NULL AFTER `additional_driver_name`;

-- Add comments to document the new columns
ALTER TABLE `vehicleowner` 
COMMENT = 'Vehicle owner information with optional additional driver details';

ALTER TABLE `applications` 
COMMENT = 'Pending vehicle registration applications with optional additional driver details';