<?php
session_start();
require_once '../dbConnection.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'SSEDMMO Admin') {
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

    // First get the owner ID for this vehicle
    $ownerQuery = $conn->prepare("SELECT OwnerID FROM vehicle WHERE plateNum = ?");
    $ownerQuery->bind_param("s", $plateNum);
    $ownerQuery->execute();
    $ownerResult = $ownerQuery->get_result();
    
    $ownerID = null;
    if ($ownerRow = $ownerResult->fetch_assoc()) {
        $ownerID = $ownerRow['OwnerID'];
    }

    $conn->begin_transaction();

    $stmt = $conn->prepare("DELETE FROM vehicle WHERE plateNum = ?");
    $stmt->bind_param("s", $plateNum);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        $message = 'Vehicle deleted successfully';
        
        // If we found an owner, see if they have any vehicles left
        if ($ownerID !== null) {
            $countQuery = $conn->prepare("SELECT COUNT(*) as count FROM vehicle WHERE OwnerID = ?");
            $countQuery->bind_param("s", $ownerID);
            $countQuery->execute();
            $countResult = $countQuery->get_result();
            $count = $countResult->fetch_assoc()['count'];
            
            // If the owner has 0 vehicles left, delete the owner
            if ($count == 0) {
                $delOwnerStmt = $conn->prepare("DELETE FROM vehicleowner WHERE OwnerID = ?");
                $delOwnerStmt->bind_param("s", $ownerID);
                $delOwnerStmt->execute();
                $message .= ' and the associated vehicle owner was also removed.';
            }
        }
        $conn->commit();
        echo json_encode(['success' => true, 'message' => $message]);
    } else {
        $conn->rollback();
        echo json_encode(['success' => false, 'message' => 'Vehicle not found']);
    }

    $db->closeConnection();

} catch (Exception $e) {
    if (isset($conn)) $conn->rollback();
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>