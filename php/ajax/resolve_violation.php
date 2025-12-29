<?php
require_once '../dbConnection.php';

header('Content-Type: application/json');

if (!isset($_POST['violationID'])) {
    echo json_encode(['success' => false, 'message' => 'Violation ID is required']);
    exit;
}

$violationID = $_POST['violationID'];

try {
    $db = new Database();
    $conn = $db->getConnection();
    
    $query = "UPDATE violations SET status = 'resolved', resolvedDate = NOW() WHERE violationID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $violationID);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Violation resolved successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to resolve violation']);
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>