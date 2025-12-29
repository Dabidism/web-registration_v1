<?php
require_once 'dbConnection.php';

function deleteExpiredVisitorData() {
    $db = new Database();
    $conn = $db->getConnection();
    
    $conn->begin_transaction();
    
    try {
        // Delete visitor logs older than 1 month
        $stmt = $conn->prepare("DELETE FROM visitorlog WHERE entryTime < DATE_SUB(NOW(), INTERVAL 1 MONTH)");
        $stmt->execute();
        $deletedLogs = $stmt->affected_rows;
        
        // Delete visitor records older than 1 month
        $stmt = $conn->prepare("DELETE FROM visitor WHERE createdAt < DATE_SUB(NOW(), INTERVAL 1 MONTH)");
        $stmt->execute();
        $deletedVisitors = $stmt->affected_rows;
        
        // Delete vehicles associated with deleted visitors
        $stmt = $conn->prepare("DELETE FROM vehicle WHERE visitorID IS NOT NULL AND visitorID NOT IN (SELECT visitorID FROM visitor)");
        $stmt->execute();
        $deletedVehicles = $stmt->affected_rows;
        
        $conn->commit();
        $db->closeConnection();
        
        return [
            'success' => true,
            'deleted_logs' => $deletedLogs,
            'deleted_visitors' => $deletedVisitors,
            'deleted_vehicles' => $deletedVehicles
        ];
        
    } catch (Exception $e) {
        $conn->rollback();
        $db->closeConnection();
        return ['success' => false, 'error' => $e->getMessage()];
    }
}

// Run cleanup if called directly
if (basename(__FILE__) == basename($_SERVER['SCRIPT_NAME'])) {
    $result = deleteExpiredVisitorData();
    echo json_encode($result);
}
?>