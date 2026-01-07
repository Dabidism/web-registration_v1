<?php
// Process RFID and Car Pass issuance via AJAX
session_start();
header('Content-Type: application/json');

// Check if request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Check authorization
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['SSEDMMO Admin', 'SSEDMMO Staff'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

// Check required parameters
if (!isset($_POST['plateNum'])) {
    echo json_encode(['success' => false, 'message' => 'Missing plate number']);
    exit;
}

$plateNum = $_POST['plateNum'];
$stickerID = $_POST['stickerID'] ?? '';
$carpassId = $_POST['carpassId'] ?? '';

// Require both RFID and carpass
if (empty($stickerID) || empty($carpassId)) {
    echo json_encode(['success' => false, 'message' => 'Both RFID Tag and Car Pass ID must be selected']);
    exit;
}

// Include database connection
require_once '../dbConnection.php';

// Create database instance
$db = new Database();
$conn = $db->getConnection();

try {
    $conn->begin_transaction();

    // Handle RFID if provided
    if (!empty($stickerID)) {
        // Check if RFID tag is available in rfidtag table
        $checkRfidQuery = "SELECT status FROM rfidtag WHERE stickerID = ?";
        $checkRfidStmt = $conn->prepare($checkRfidQuery);
        $checkRfidStmt->bind_param("s", $stickerID);
        $checkRfidStmt->execute();
        $rfidResult = $checkRfidStmt->get_result();

        if ($rfidResult->num_rows === 0) {
            throw new Exception("Invalid RFID tag ID");
        }

        $rfidData = $rfidResult->fetch_assoc();
        if ($rfidData['status'] === 'unavailable') {
            throw new Exception("RFID tag $stickerID is already assigned");
        }

        // Check if sticker ID already assigned to another vehicle
        $checkQuery = "SELECT plateNum FROM vehicle WHERE stickerID = ?";
        $checkStmt = $conn->prepare($checkQuery);
        $checkStmt->bind_param("s", $stickerID);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();

        if ($checkResult->num_rows > 0) {
            throw new Exception('This sticker ID is already assigned to another vehicle');
        }

        // Update vehicle with sticker ID
        $updateQuery = "UPDATE vehicle SET stickerID = ? WHERE plateNum = ?";
        $updateStmt = $conn->prepare($updateQuery);
        $updateStmt->bind_param("ss", $stickerID, $plateNum);
        $updateStmt->execute();

        // Update rfidtag status to unavailable
        $username = $_SESSION['username'] ?? 'admin';
        $updateRfidQuery = "UPDATE rfidtag SET status = 'unavailable', issuedBy = ? WHERE stickerID = ?";
        $updateRfidStmt = $conn->prepare($updateRfidQuery);
        $updateRfidStmt->bind_param("ss", $username, $stickerID);

        if (!$updateRfidStmt->execute()) {
            throw new Exception("Failed to update RFID tag status");
        }
    }

    // Handle Car Pass if provided
    if (!empty($carpassId)) {
        // Check if car pass is available in vehiclepass table
        $checkPassQuery = "SELECT status FROM vehiclepass WHERE passID = ?";
        $checkPassStmt = $conn->prepare($checkPassQuery);
        $checkPassStmt->bind_param("s", $carpassId);
        $checkPassStmt->execute();
        $passResult = $checkPassStmt->get_result();

        if ($passResult->num_rows === 0) {
            throw new Exception("Invalid car pass ID");
        }

        $passData = $passResult->fetch_assoc();
        if ($passData['status'] === 'unavailable') {
            throw new Exception("Car pass $carpassId is already assigned");
        }

        // Check if car pass ID already assigned to another vehicle
        $checkQuery = "SELECT plateNum FROM vehicle WHERE carpassid = ?";
        $checkStmt = $conn->prepare($checkQuery);
        $checkStmt->bind_param("s", $carpassId);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();

        if ($checkResult->num_rows > 0) {
            throw new Exception("Car pass ID is already assigned to another vehicle");
        }

        // Update vehicle with car pass ID
        $updateQuery = "UPDATE vehicle SET carpassid = ? WHERE plateNum = ?";
        $updateStmt = $conn->prepare($updateQuery);
        $updateStmt->bind_param("ss", $carpassId, $plateNum);
        $updateStmt->execute();

        // Update vehiclepass status to unavailable
        $username = $_SESSION['username'] ?? 'admin';
        $updatePassQuery = "UPDATE vehiclepass SET status = 'unavailable', issuedBy = ? WHERE passID = ?";
        $updatePassStmt = $conn->prepare($updatePassQuery);
        $updatePassStmt->bind_param("ss", $username, $carpassId);

        if (!$updatePassStmt->execute()) {
            throw new Exception("Failed to update car pass status");
        }
    }

    $conn->commit();
    echo json_encode(['success' => true, 'message' => 'Successfully issued']);

} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
} finally {
    $db->closeConnection();
}