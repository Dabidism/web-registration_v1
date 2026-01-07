-- ============================================
-- Simplified RFID Tag Table
-- Purpose: Manage available RFID tags for registered vehicles
-- Similar structure to vehiclepass table
-- ============================================

-- Drop existing table if it exists
DROP TABLE IF EXISTS rfidtag;

-- Create simplified rfidtag table
CREATE TABLE rfidtag (
    stickerID VARCHAR(20) PRIMARY KEY COMMENT 'RFID tag ID (e.g., RFID001)',
    tagCode VARCHAR(100) DEFAULT NULL COMMENT 'Scanned RFID tag code from converter',
    status ENUM('available', 'unavailable') DEFAULT 'available' COMMENT 'Tag availability status',
    issuedBy VARCHAR(50) DEFAULT NULL COMMENT 'Username of admin who issued the RFID tag',
    INDEX idx_status (status),
    INDEX idx_tagCode (tagCode)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='RFID tags inventory for registered vehicles';

-- Insert 20 RFID tags (RFID001 to RFID020)
INSERT INTO rfidtag (stickerID, tagCode, status, issuedBy) VALUES
('RFID001', NULL, 'available', NULL),
('RFID002', NULL, 'available', NULL),
('RFID003', NULL, 'available', NULL),
('RFID004', NULL, 'available', NULL),
('RFID005', NULL, 'available', NULL),
('RFID006', NULL, 'available', NULL),
('RFID007', NULL, 'available', NULL),
('RFID008', NULL, 'available', NULL),
('RFID009', NULL, 'available', NULL),
('RFID010', NULL, 'available', NULL),
('RFID011', NULL, 'available', NULL),
('RFID012', NULL, 'available', NULL),
('RFID013', NULL, 'available', NULL),
('RFID014', NULL, 'available', NULL),
('RFID015', NULL, 'available', NULL),
('RFID016', NULL, 'available', NULL),
('RFID017', NULL, 'available', NULL),
('RFID018', NULL, 'available', NULL),
('RFID019', NULL, 'available', NULL),
('RFID020', NULL, 'available', NULL);

-- Verify RFID tags were created
SELECT * FROM rfidtag ORDER BY stickerID;
