<?php
header('Content-Type: application/json');

require_once '../dbConnection.php';

try {
    $db = new Database();
    $conn = $db->getConnection();

    // Get all available RFID tags from rfidtag table with full details
    $query = "SELECT stickerID, tagCode FROM rfidtag WHERE status = 'available' ORDER BY stickerID";
    $result = $conn->query($query);

    $availableRfid = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $availableRfid[] = [
                'stickerID' => $row['stickerID'],
                'tagCode' => $row['tagCode'] ?? 'Not scanned'
            ];
        }
    }

    echo json_encode(['success' => true, 'data' => $availableRfid]);
    $db->closeConnection();

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>