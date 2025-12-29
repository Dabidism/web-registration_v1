<?php
// Process Car Pass issuance via AJAX
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
    
    if ($updateStmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Car pass issued successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to issue car pass: ' . $conn->error]);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
} finally {
    $db->closeConnection();
}