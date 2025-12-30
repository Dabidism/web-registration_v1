<?php
session_start();
require_once '../dbConnection.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['SSEDMMO Admin', 'SSEDMMO Staff'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

$plateNum = $_POST['plateNum'] ?? '';

if (empty($plateNum)) {
    echo json_encode(['success' => false, 'message' => 'Plate number is required']);
    exit;
}

try {
    $db = new Database();
    $conn = $db->getConnection();

    $conn->begin_transaction();

    // Get the stickerID before removing it
    $getSticker = $conn->prepare("SELECT stickerID FROM vehicle WHERE plateNum = ?");
    $getSticker->bind_param("s", $plateNum);
    $getSticker->execute();
    $stickerResult = $getSticker->get_result();
    $stickerID = $stickerResult->fetch_assoc()['stickerID'] ?? null;

    // Update vehicle to remove RFID and car pass
    $stmt = $conn->prepare("UPDATE vehicle SET stickerID = NULL, carpassid = NULL WHERE plateNum = ?");
    $stmt->bind_param("s", $plateNum);
    $stmt->execute();

    // Set RFID status back to inactive if it exists
    if ($stickerID) {
        $updateRfid = $conn->prepare("UPDATE rfidtag SET status = 'inactive' WHERE stickerID = ?");
        $updateRfid->bind_param("s", $stickerID);
        $updateRfid->execute();
    }

    $conn->commit();
    echo json_encode(['success' => true, 'message' => 'RFID and Car Pass revoked successfully']);

} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
} finally {
    $db->closeConnection();
}
?>