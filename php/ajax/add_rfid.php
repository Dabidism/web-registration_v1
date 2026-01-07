<?php
// Add new RFID tag to inventory
session_start();
header('Content-Type: application/json');

// Check if request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Check required parameters - now expecting tagCode instead of stickerID
if (!isset($_POST['tagCode'])) {
    echo json_encode(['success' => false, 'message' => 'Missing RFID tag code']);
    exit;
}

$tagCode = trim($_POST['tagCode']);

// Validate tagCode is not empty
if (empty($tagCode)) {
    echo json_encode(['success' => false, 'message' => 'RFID tag code cannot be empty']);
    exit;
}

// Include database connection
require_once '../dbConnection.php';

// Create database instance
$db = new Database();
$conn = $db->getConnection();

try {
    // Check if tagCode already exists
    $checkCodeQuery = "SELECT stickerID FROM rfidtag WHERE tagCode = ?";
    $checkCodeStmt = $conn->prepare($checkCodeQuery);
    $checkCodeStmt->bind_param("s", $tagCode);
    $checkCodeStmt->execute();
    $checkCodeResult = $checkCodeStmt->get_result();

    if ($checkCodeResult->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'This tag code is already registered']);
        exit;
    }

    // Auto-generate next stickerID
    $result = $conn->query("SELECT MAX(CAST(SUBSTRING(stickerID, 5) AS UNSIGNED)) as max_id FROM rfidtag WHERE stickerID LIKE 'RFID%'");
    $row = $result->fetch_assoc();
    $nextId = ($row['max_id'] ?? 0) + 1;
    $stickerID = 'RFID' . str_pad($nextId, 3, '0', STR_PAD_LEFT);

    // Insert new RFID tag with 'available' status
    $insertQuery = "INSERT INTO rfidtag (stickerID, tagCode, status, issuedBy) VALUES (?, ?, 'available', NULL)";
    $insertStmt = $conn->prepare($insertQuery);
    $insertStmt->bind_param("ss", $stickerID, $tagCode);

    if ($insertStmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'RFID tag added successfully',
            'stickerID' => $stickerID
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to add RFID tag: ' . $conn->error]);
    }

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
} finally {
    $db->closeConnection();
}
?>