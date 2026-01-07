-- ============================================
-- Occupancy Tracking Triggers
-- Purpose: Automatically update parkingstatus when vehicles enter/exit
-- ============================================

USE gate_pass_system;

-- Drop existing triggers if they exist
DROP TRIGGER IF EXISTS after_vehicle_entry;
DROP TRIGGER IF EXISTS after_vehicle_exit;

DELIMITER $$

-- ============================================
-- Trigger 1: Increment occupancy on vehicle entry
-- ============================================
CREATE TRIGGER after_vehicle_entry
AFTER INSERT ON historical_log
FOR EACH ROW
BEGIN
    DECLARE user_role VARCHAR(50);
    DECLARE is_visitor BOOLEAN DEFAULT FALSE;
    
    -- Only process if vehicle entered today
    IF NEW.status = 'entered' AND DATE(NEW.entryTime) = CURDATE() THEN
        
        -- Determine role category
        SELECT 
            CASE 
                WHEN vo.role = 'student' THEN 'students'
                WHEN vo.role = 'faculty' THEN 'faculty'
                WHEN vo.role IN ('non-teaching', 'staff') THEN 'staff'
                WHEN v.visitorID IS NOT NULL THEN 'guests'
                ELSE 'guests'
            END,
            v.visitorID IS NOT NULL
        INTO user_role, is_visitor
        FROM vehicle v
        LEFT JOIN vehicleowner vo ON v.OwnerID = vo.OwnerID
        WHERE v.plateNum = NEW.plateNum;
        
        -- Increment appropriate counter
        IF user_role = 'students' THEN
            UPDATE parkingstatus 
            SET currentOccupiedStudents = currentOccupiedStudents + 1 
            WHERE id = 1;
        ELSEIF user_role = 'faculty' THEN
            UPDATE parkingstatus 
            SET currentOccupiedFaculty = currentOccupiedFaculty + 1 
            WHERE id = 1;
        ELSEIF user_role = 'staff' THEN
            UPDATE parkingstatus 
            SET currentOccupiedStaff = currentOccupiedStaff + 1 
            WHERE id = 1;
        ELSE
            UPDATE parkingstatus 
            SET currentOccupiedGuests = currentOccupiedGuests + 1 
            WHERE id = 1;
        END IF;
        
    END IF;
END$$

-- ============================================
-- Trigger 2: Decrement occupancy on vehicle exit
-- ============================================
CREATE TRIGGER after_vehicle_exit
AFTER UPDATE ON historical_log
FOR EACH ROW
BEGIN
    DECLARE user_role VARCHAR(50);
    
    -- Only process if status changed from entered to exited
    IF OLD.status = 'entered' AND NEW.status = 'exited' AND DATE(OLD.entryTime) = CURDATE() THEN
        
        -- Determine role category
        SELECT 
            CASE 
                WHEN vo.role = 'student' THEN 'students'
                WHEN vo.role = 'faculty' THEN 'faculty'
                WHEN vo.role IN ('non-teaching', 'staff') THEN 'staff'
                WHEN v.visitorID IS NOT NULL THEN 'guests'
                ELSE 'guests'
            END
        INTO user_role
        FROM vehicle v
        LEFT JOIN vehicleowner vo ON v.OwnerID = vo.OwnerID
        WHERE v.plateNum = NEW.plateNum;
        
        -- Decrement appropriate counter (prevent negative values)
        IF user_role = 'students' THEN
            UPDATE parkingstatus 
            SET currentOccupiedStudents = GREATEST(currentOccupiedStudents - 1, 0) 
            WHERE id = 1;
        ELSEIF user_role = 'faculty' THEN
            UPDATE parkingstatus 
            SET currentOccupiedFaculty = GREATEST(currentOccupiedFaculty - 1, 0) 
            WHERE id = 1;
        ELSEIF user_role = 'staff' THEN
            UPDATE parkingstatus 
            SET currentOccupiedStaff = GREATEST(currentOccupiedStaff - 1, 0) 
            WHERE id = 1;
        ELSE
            UPDATE parkingstatus 
            SET currentOccupiedGuests = GREATEST(currentOccupiedGuests - 1, 0) 
            WHERE id = 1;
        END IF;
        
    END IF;
END$$

DELIMITER ;

-- ============================================
-- Initialize current occupancy counts
-- Run this ONCE after creating triggers
-- ============================================
UPDATE parkingstatus SET
    currentOccupiedStudents = (
        SELECT COUNT(*) FROM historical_log h
        JOIN vehicle v ON h.plateNum = v.plateNum
        LEFT JOIN vehicleowner vo ON v.OwnerID = vo.OwnerID
        WHERE h.status = 'entered' 
            AND h.exitTime IS NULL
            AND DATE(h.entryTime) = CURDATE()
            AND vo.role = 'student'
    ),
    currentOccupiedFaculty = (
        SELECT COUNT(*) FROM historical_log h
        JOIN vehicle v ON h.plateNum = v.plateNum
        LEFT JOIN vehicleowner vo ON v.OwnerID = vo.OwnerID
        WHERE h.status = 'entered' 
            AND h.exitTime IS NULL
            AND DATE(h.entryTime) = CURDATE()
            AND vo.role = 'faculty'
    ),
    currentOccupiedStaff = (
        SELECT COUNT(*) FROM historical_log h
        JOIN vehicle v ON h.plateNum = v.plateNum
        LEFT JOIN vehicleowner vo ON v.OwnerID = vo.OwnerID
        WHERE h.status = 'entered' 
            AND h.exitTime IS NULL
            AND DATE(h.entryTime) = CURDATE()
            AND vo.role IN ('non-teaching', 'staff')
    ),
    currentOccupiedGuests = (
        SELECT COUNT(*) FROM historical_log h
        JOIN vehicle v ON h.plateNum = v.plateNum
        WHERE h.status = 'entered' 
            AND h.exitTime IS NULL
            AND DATE(h.entryTime) = CURDATE()
            AND v.visitorID IS NOT NULL
    )
WHERE id = 1;

