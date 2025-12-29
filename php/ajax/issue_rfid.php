<?php
// Process RFID tag issuance via AJAX
header('Content-Type: application/json');

// Check if request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Check required parameters
if (!isset($_POST['plateNum']) || !isset($_POST['stickerID'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
    exit;
}

$plateNum = $_POST['plateNum'];
$stickerID = $_POST['stickerID'];

// Include database connection
require_once '../dbConnection.php';

// Create database instance
$db = new Database();
$conn = $db->getConnection();

try {
    // Check if sticker ID already exists
    $checkQuery = "SELECT plateNum FROM vehicle WHERE stickerID = ?";
    $checkStmt = $conn->prepare($checkQuery);
    $checkStmt->bind_param("s", $stickerID);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();
    
    if ($checkResult->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'This sticker ID is already assigned to another vehicle']);
        exit;
    }
    
    // Insert RFID tag record and update vehicle
    $conn->begin_transaction();
    

    
    // Update RFID tag status to active
    $updateRfidQuery = "UPDATE rfidtag SET status = 'active', issuedAt = NOW() WHERE stickerID = ?";
    $updateRfidStmt = $conn->prepare($updateRfidQuery);
    $updateRfidStmt->bind_param("s", $stickerID);
    $updateRfidStmt->execute();
    
    // Update vehicle with sticker ID
    $updateQuery = "UPDATE vehicle SET stickerID = ? WHERE plateNum = ?";
    $updateStmt = $conn->prepare($updateQuery);
    $updateStmt->bind_param("ss", $stickerID, $plateNum);
    
    if ($updateStmt->execute()) {
        $conn->commit();
        echo json_encode(['success' => true, 'message' => 'RFID tag issued successfully']);
    } else {
        $conn->rollback();
        echo json_encode(['success' => false, 'message' => 'Failed to issue RFID tag: ' . $conn->error]);
    }
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
} finally {
    $db->closeConnection();
}