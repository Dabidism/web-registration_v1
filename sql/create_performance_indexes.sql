-- ============================================
-- Performance Indexes for Occupancy Queries
-- Purpose: Speed up queries even if triggers fail
-- ============================================

USE gate_pass_system;

-- Index for historical_log queries (status, date filtering)
CREATE INDEX IF NOT EXISTS idx_historical_status_date 
ON historical_log(status, entryTime, exitTime);

-- Index for vehicle lookups (plate number, relationships)
CREATE INDEX IF NOT EXISTS idx_vehicle_owner_visitor 
ON vehicle(plateNum, OwnerID, visitorID);

-- Index for vehicle owner role lookups
CREATE INDEX IF NOT EXISTS idx_owner_role 
ON vehicleowner(OwnerID, role);

-- Index for parking status lookups
CREATE INDEX IF NOT EXISTS idx_parkingstatus_id 
ON parkingstatus(id);

-- Verify indexes were created
SHOW INDEX FROM historical_log WHERE Key_name LIKE 'idx_%';
SHOW INDEX FROM vehicle WHERE Key_name LIKE 'idx_%';
SHOW INDEX FROM vehicleowner WHERE Key_name LIKE 'idx_%';
