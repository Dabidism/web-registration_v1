-- ============================================
-- Clear All RFID Tags and Reset Counter
-- Purpose: Delete all existing RFID tags from the system
-- ============================================

-- Delete all RFID tags
DELETE FROM rfidtag;

-- Verify table is empty
SELECT COUNT(*) as remaining_tags FROM rfidtag;

-- The next RFID tag added will start from RFID001
-- because the auto-generation logic finds the MAX ID and adds 1
