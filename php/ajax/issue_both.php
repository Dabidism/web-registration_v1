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

        // Update RFID tag status to active
        $updateRfidQuery = "UPDATE rfidtag SET status = 'active', issuedAt = NOW() WHERE stickerID = ?";
        $updateRfidStmt = $conn->prepare($updateRfidQuery);
        $updateRfidStmt->bind_param("s", $stickerID);
        $updateRfidStmt->execute();

        // Update vehicle with sticker ID
        $updateQuery = "UPDATE vehicle SET stickerID = ? WHERE plateNum = ?";
        $updateStmt = $conn->prepare($updateQuery);
        $updateStmt->bind_param("ss", $stickerID, $plateNum);
        $updateStmt->execute();
    }

    // Handle Car Pass if provided
    if (!empty($carpassId)) {
        // Check if car pass ID already exists
        $checkQuery = "SELECT plateNum FROM vehicle WHERE carpassid = ?";
        $checkStmt = $conn->prepare($checkQuery);
        $checkStmt->bind_param("s", $carpassId);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();

        if ($checkResult->num_rows > 0) {
            echo json_encode(['success' => false, 'message' => 'This car pass ID is already assigned to another vehicle']);
            exit;
        }

        // Update vehicle with car pass ID
        $updateQuery = "UPDATE vehicle SET carpassid = ? WHERE plateNum = ?";
        $updateStmt = $conn->prepare($updateQuery);
        $updateStmt->bind_param("ss", $carpassId, $plateNum);
        $updateStmt->execute();
    }

    $conn->commit();
    echo json_encode(['success' => true, 'message' => 'Successfully issued']);

} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
} finally {
    $db->closeConnection();
}