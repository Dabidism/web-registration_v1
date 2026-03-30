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
        // Format violationType from snake_case to Title Case
        if (isset($row['violationType'])) {
            $row['violationType'] = ucwords(str_replace('_', ' ', $row['violationType']));
        }
        
        // Format violationDate
        if (!empty($row['violationDate'])) {
            $row['formatted_date'] = date('M j, Y g:i A', strtotime($row['violationDate']));
        } else {
            $row['formatted_date'] = 'N/A';
        }
        $violations[] = $row;
    }
    
    echo json_encode(['success' => true, 'violations' => $violations]);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>