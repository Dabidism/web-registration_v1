<?php
header('Content-Type: application/json');
require_once '../dbConnection.php';

$db = new Database();
$conn = $db->getConnection();

// Get available RFID tags (not assigned to any vehicle)
$query = "SELECT r.stickerID, r.rfidCode 
          FROM rfidtag r 
          LEFT JOIN vehicle v ON r.stickerID = v.stickerID 
          WHERE v.stickerID IS NULL AND r.status = 'inactive'
          ORDER BY r.stickerID";

$result = $conn->query($query);
$rfidTags = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $rfidTags[] = $row;
    }
}

echo json_encode(['success' => true, 'data' => $rfidTags]);

$db->closeConnection();
?>