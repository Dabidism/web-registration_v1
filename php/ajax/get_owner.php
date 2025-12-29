<?php
header('Content-Type: application/json');
require_once '../dbConnection.php';

$db = new Database();
$conn = $db->getConnection();

if (isset($_GET['ownerID'])) {
    $ownerID = $_GET['ownerID'];
    
    $query = "SELECT * FROM vehicleowner WHERE OwnerID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $ownerID);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $owner = $result->fetch_assoc();
        echo json_encode(['success' => true, 'owner' => $owner]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Owner not found']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Owner ID required']);
}

$db->closeConnection();
?>