<?php
require_once 'dbConnection.php';

function isCodeValid($ownerID) {
    $db = new Database();
    $conn = $db->getConnection();
    
    $stmt = $conn->prepare("SELECT approvalTimestamp FROM vehicleowner WHERE OwnerID = ? AND registrationStatus = 'approved'");
    $stmt->bind_param("s", $ownerID);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        $db->closeConnection();
        return false; // Code doesn't exist or not approved
    }
    
    $row = $result->fetch_assoc();
    $approvalTime = $row['approvalTimestamp'];
    
    if (!$approvalTime) {
        $db->closeConnection();
        return false; // No approval timestamp
    }
    
    // Check if 48 hours (172800 seconds) have passed
    $currentTime = time();
    $approvalTimestamp = strtotime($approvalTime);
    $timeDifference = $currentTime - $approvalTimestamp;
    
    $db->closeConnection();
    return $timeDifference <= 172800; // 48 hours = 48 * 60 * 60 = 172800 seconds
}

function invalidateExpiredCodes() {
    $db = new Database();
    $conn = $db->getConnection();
    
    // Update codes that are older than 48 hours to expired status
    $stmt = $conn->prepare("UPDATE vehicleowner SET registrationStatus = 'expired' WHERE registrationStatus = 'approved' AND approvalTimestamp < DATE_SUB(NOW(), INTERVAL 48 HOUR)");
    $stmt->execute();
    
    $affectedRows = $stmt->affected_rows;
    $db->closeConnection();
    
    return $affectedRows;
}
?>