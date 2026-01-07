-- Add issuedBy column to rfidtag table if it doesn't exist
-- This tracks which admin issued the RFID tag

ALTER TABLE rfidtag 
ADD COLUMN IF NOT EXISTS issuedBy VARCHAR(50) DEFAULT NULL COMMENT 'Username of admin who issued the RFID tag';

-- Verify column was added
DESCRIBE rfidtag;
