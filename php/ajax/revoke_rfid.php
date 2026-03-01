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

    // Get the stickerID and carpassid before removing them
    $getAccess = $conn->prepare("SELECT stickerID, carpassid FROM vehicle WHERE plateNum = ?");
    $getAccess->bind_param("s", $plateNum);
    $getAccess->execute();
    $accessResult = $getAccess->get_result();
    $accessData = $accessResult->fetch_assoc();
    $stickerID = $accessData['stickerID'] ?? null;
    $carPassID = $accessData['carpassid'] ?? null;

    // Update vehicle to remove RFID and car pass
    $stmt = $conn->prepare("UPDATE vehicle SET stickerID = NULL, carpassid = NULL WHERE plateNum = ?");
    $stmt->bind_param("s", $plateNum);
    $stmt->execute();

    // Set RFID status back to available if it exists
    if ($stickerID) {
        $updateRfid = $conn->prepare("UPDATE rfidtag SET status = 'available' WHERE stickerID = ?");
        $updateRfid->bind_param("s", $stickerID);
        $updateRfid->execute();
    }

    // Set Vehicle Pass status back to available if it exists
    if ($carPassID) {
        $updatePass = $conn->prepare("UPDATE vehiclepass SET status = 'available' WHERE passID = ?");
        $updatePass->bind_param("s", $carPassID);
        $updatePass->execute();
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