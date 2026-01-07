<?php
// Add new car pass to inventory
session_start();
header('Content-Type: application/json');

// Check if request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Check required parameters
if (!isset($_POST['passID'])) {
    echo json_encode(['success' => false, 'message' => 'Missing car pass ID']);
    exit;
}

$passID = trim($_POST['passID']);

// Validate passID is not empty
if (empty($passID)) {
    echo json_encode(['success' => false, 'message' => 'Car pass ID cannot be empty']);
    exit;
}

// Include database connection
require_once '../dbConnection.php';

// Create database instance
$db = new Database();
$conn = $db->getConnection();

try {
    // Check if passID already exists
    $checkQuery = "SELECT passID FROM vehiclepass WHERE passID = ?";
    $checkStmt = $conn->prepare($checkQuery);
    $checkStmt->bind_param("s", $passID);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();

    if ($checkResult->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'This car pass ID already exists']);
        exit;
    }

    // Insert new car pass with 'available' status
    $insertQuery = "INSERT INTO vehiclepass (passID, status, issuedBy) VALUES (?, 'available', NULL)";
    $insertStmt = $conn->prepare($insertQuery);
    $insertStmt->bind_param("s", $passID);

    if ($insertStmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Car pass added successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to add car pass: ' . $conn->error]);
    }

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
} finally {
    $db->closeConnection();
}
?>