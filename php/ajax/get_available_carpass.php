<?php
header('Content-Type: application/json');

require_once '../dbConnection.php';

try {
    $db = new Database();
    $conn = $db->getConnection();

    // Get all available car passes from vehiclepass table
    $query = "SELECT passID FROM vehiclepass WHERE status = 'available' ORDER BY passID";
    $result = $conn->query($query);

    $availableCarpass = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $availableCarpass[] = $row['passID'];
        }
    }

    echo json_encode(['success' => true, 'data' => $availableCarpass]);
    $db->closeConnection();

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>