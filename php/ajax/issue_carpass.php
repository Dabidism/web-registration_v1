<?php
// Process Car Pass issuance via AJAX
session_start();
header('Content-Type: application/json');

// Check if request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Check required parameters
if (!isset($_POST['plateNum']) || !isset($_POST['carpassId'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
    exit;
}

$plateNum = $_POST['plateNum'];
$carpassId = $_POST['carpassId'];

// Include database connection
require_once '../dbConnection.php';

// Create database instance
$db = new Database();
$conn = $db->getConnection();

try {
    // Check if car pass is available in vehiclepass table
    $checkPassQuery = "SELECT status FROM vehiclepass WHERE passID = ?";
    $checkPassStmt = $conn->prepare($checkPassQuery);
    $checkPassStmt->bind_param("s", $carpassId);
    $checkPassStmt->execute();
    $passResult = $checkPassStmt->get_result();

    if ($passResult->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid car pass ID']);
        exit;
    }

    $passData = $passResult->fetch_assoc();
    if ($passData['status'] === 'unavailable') {
        echo json_encode(['success' => false, 'message' => 'Car pass is already assigned']);
        exit;
    }

    // Check if vehicle already has a car pass
    $checkQuery = "SELECT carpassid FROM vehicle WHERE plateNum = ?";
    $checkStmt = $conn->prepare($checkQuery);
    $checkStmt->bind_param("s", $plateNum);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();

    if ($checkResult->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Vehicle not found']);
        exit;
    }

    $vehicle = $checkResult->fetch_assoc();
    if (!empty($vehicle['carpassid'])) {
        echo json_encode(['success' => false, 'message' => 'Vehicle already has a car pass assigned']);
        exit;
    }

    // Update vehicle with car pass ID
    $updateQuery = "UPDATE vehicle SET carpassid = ? WHERE plateNum = ?";
    $updateStmt = $conn->prepare($updateQuery);
    $updateStmt->bind_param("ss", $carpassId, $plateNum);

    if ($updateStmt->execute()) {
        // Update vehiclepass status to unavailable
        $username = $_SESSION['username'] ?? 'admin';
        $updatePassQuery = "UPDATE vehiclepass SET status = 'unavailable', issuedBy = ? WHERE passID = ?";
        $updatePassStmt = $conn->prepare($updatePassQuery);
        $updatePassStmt->bind_param("ss", $username, $carpassId);

        if ($updatePassStmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Car pass issued successfully']);
        } else {
            // Rollback vehicle update if vehiclepass update fails
            $rollbackQuery = "UPDATE vehicle SET carpassid = NULL WHERE plateNum = ?";
            $rollbackStmt = $conn->prepare($rollbackQuery);
            $rollbackStmt->bind_param("s", $plateNum);
            $rollbackStmt->execute();
            echo json_encode(['success' => false, 'message' => 'Failed to update car pass status']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to issue car pass: ' . $conn->error]);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
} finally {
    $db->closeConnection();
}
?>