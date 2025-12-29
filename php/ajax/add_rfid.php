<?php
session_start();
header('Content-Type: application/json');
require_once '../dbConnection.php';

// Check if user is admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'SSEDMMO Admin') {
    echo json_encode(['success' => false, 'message' => 'Access denied. Admin privileges required.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$rfidCode = $_POST['rfidCode'] ?? '';

if (empty($rfidCode)) {
    echo json_encode(['success' => false, 'message' => 'RFID Code is required']);
    exit;
}

$db = new Database();
$conn = $db->getConnection();

// Check if RFID code already exists
$checkStmt = $conn->prepare("SELECT stickerID FROM rfidtag WHERE rfidCode = ?");
$checkStmt->bind_param("s", $rfidCode);
$checkStmt->execute();
$result = $checkStmt->get_result();

if ($result->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'RFID code already taken']);
    $db->closeConnection();
    exit;
}

// Generate new stickerID
$result = $conn->query("SELECT MAX(CAST(SUBSTRING(stickerID, 2) AS UNSIGNED)) as max_id FROM rfidtag WHERE stickerID LIKE 'S%'");
$row = $result->fetch_assoc();
$nextId = ($row['max_id'] ?? 0) + 1;
$stickerID = 'S' . str_pad($nextId, 3, '0', STR_PAD_LEFT);

// Insert new RFID tag
$stmt = $conn->prepare("INSERT INTO rfidtag (stickerID, rfidCode, issuedAt, status, expirationDate) VALUES (?, ?, NOW(), 'inactive', DATE_ADD(NOW(), INTERVAL 1 YEAR))");
$stmt->bind_param("ss", $stickerID, $rfidCode);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'RFID tag added successfully']);
} else {
    // Check if error is due to duplicate entry
    if (strpos($stmt->error, 'Duplicate entry') !== false) {
        echo json_encode(['success' => false, 'message' => 'RFID tag already exists in the system']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to add RFID tag: ' . $stmt->error]);
    }
}

$db->closeConnection();
?>