-- ============================================
-- Simplified Vehicle Pass Table
-- Purpose: Manage available car passes for registered vehicles
-- ============================================

-- Drop existing table if it exists
DROP TABLE IF EXISTS vehiclepass;

-- Create simplified vehiclepass table
CREATE TABLE vehiclepass (
    passID VARCHAR(20) PRIMARY KEY COMMENT 'Car pass ID (e.g., CP01)',
    status ENUM('available', 'unavailable') DEFAULT 'available' COMMENT 'Pass availability status',
    issuedBy VARCHAR(50) DEFAULT NULL COMMENT 'Username of admin who issued the pass',
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Vehicle passes inventory for registered vehicles';

-- Insert 20 car passes (CP01 to CP20)
INSERT INTO vehiclepass (passID, status, issuedBy) VALUES
('CP01', 'available', NULL),
('CP02', 'available', NULL),
('CP03', 'available', NULL),
('CP04', 'available', NULL),
('CP05', 'available', NULL),
('CP06', 'available', NULL),
('CP07', 'available', NULL),
('CP08', 'available', NULL),
('CP09', 'available', NULL),
('CP10', 'available', NULL),
('CP11', 'available', NULL),
('CP12', 'available', NULL),
('CP13', 'available', NULL),
('CP14', 'available', NULL),
('CP15', 'available', NULL),
('CP16', 'available', NULL),
('CP17', 'available', NULL),
('CP18', 'available', NULL),
('CP19', 'available', NULL),
('CP20', 'available', NULL);

-- Verify car passes were created
SELECT * FROM vehiclepass ORDER BY passID;
