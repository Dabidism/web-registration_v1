<?php
require_once '../dbConnection.php';

header('Content-Type: application/json');

if (!isset($_GET['plateNum'])) {
    echo json_encode(['success' => false, 'message' => 'Plate number is required']);
    exit;
}

$plateNum = $_GET['plateNum'];

try {
    $db = new Database();
    $conn = $db->getConnection();
    
    $query = "SELECT * FROM violations WHERE plateNum = ? AND status = 'pending' ORDER BY violationDate DESC";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $plateNum);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $violations = [];
    while ($row = $result->fetch_assoc()) {
        $violations[] = $row;
    }
    
    echo json_encode(['success' => true, 'violations' => $violations]);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>