<?php
header('Content-Type: application/json');
require_once '../dbConnection.php';

$db = new Database();
$conn = $db->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $plateNum = $_POST['plateNum'];
    
    if (isset($_POST['stickerID']) || isset($_POST['carpassid'])) {
        // Update RFID/Car Pass info
        $stickerID = $_POST['stickerID'] ?? null;
        $carpassid = $_POST['carpassid'] ?? null;
        
        $query = "UPDATE vehicle SET stickerID = ? WHERE plateNum = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ss", $stickerID, $plateNum);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'RFID record updated successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update RFID record']);
        }
    } else {
        // Update vehicle info
        $vehicleType = $_POST['vehicleType'];
        $model = $_POST['model'];
        $manufacturer = $_POST['manufacturer'];
        $color = $_POST['color'];
        
        $query = "UPDATE vehicle SET vehicleType = ?, model = ?, manufacturer = ?, color = ? WHERE plateNum = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sssss", $vehicleType, $model, $manufacturer, $color, $plateNum);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Vehicle updated successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update vehicle']);
        }
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

$db->closeConnection();
?>