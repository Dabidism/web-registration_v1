<?php
// Process RFID tag issuance via AJAX
session_start();
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
    // Check if RFID tag is available in rfidtag table
    $checkRfidQuery = "SELECT status FROM rfidtag WHERE stickerID = ?";
    $checkRfidStmt = $conn->prepare($checkRfidQuery);
    $checkRfidStmt->bind_param("s", $stickerID);
    $checkRfidStmt->execute();
    $rfidResult = $checkRfidStmt->get_result();

    if ($rfidResult->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid RFID tag ID']);
        exit;
    }

    $rfidData = $rfidResult->fetch_assoc();
    if ($rfidData['status'] === 'unavailable') {
        echo json_encode(['success' => false, 'message' => 'RFID tag is already assigned']);
        exit;
    }

    // Check if vehicle already has an RFID tag
    $checkQuery = "SELECT stickerID FROM vehicle WHERE plateNum = ?";
    $checkStmt = $conn->prepare($checkQuery);
    $checkStmt->bind_param("s", $plateNum);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();

    if ($checkResult->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Vehicle not found']);
        exit;
    }

    $vehicle = $checkResult->fetch_assoc();
    if (!empty($vehicle['stickerID'])) {
        echo json_encode(['success' => false, 'message' => 'Vehicle already has an RFID tag assigned']);
        exit;
    }

    // Start transaction
    $conn->begin_transaction();

    // Update vehicle with sticker ID
    $updateQuery = "UPDATE vehicle SET stickerID = ? WHERE plateNum = ?";
    $updateStmt = $conn->prepare($updateQuery);
    $updateStmt->bind_param("ss", $stickerID, $plateNum);

    if ($updateStmt->execute()) {
        // Update rfidtag status to unavailable
        $username = $_SESSION['username'] ?? 'admin';
        $updateRfidQuery = "UPDATE rfidtag SET status = 'unavailable', issuedBy = ? WHERE stickerID = ?";
        $updateRfidStmt = $conn->prepare($updateRfidQuery);
        $updateRfidStmt->bind_param("ss", $username, $stickerID);

        if ($updateRfidStmt->execute()) {
            $conn->commit();
            echo json_encode(['success' => true, 'message' => 'RFID tag issued successfully']);
        } else {
            // Rollback vehicle update if rfidtag update fails
            $conn->rollback();
            echo json_encode(['success' => false, 'message' => 'Failed to update RFID tag status']);
        }
    } else {
        $conn->rollback();
        echo json_encode(['success' => false, 'message' => 'Failed to issue RFID tag: ' . $conn->error]);
    }
} catch (Exception $e) {
    if ($conn)
        $conn->rollback();
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
} finally {
    $db->closeConnection();
}
?>