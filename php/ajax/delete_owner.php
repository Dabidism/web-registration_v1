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

$ownerID = $_POST['ownerID'] ?? '';

if (empty($ownerID)) {
    echo json_encode(['success' => false, 'message' => 'Owner ID is required']);
    exit;
}

try {
    $db = new Database();
    $conn = $db->getConnection();

    $conn->begin_transaction();

    // Delete all vehicles associated with this owner
    $delVehiclesStmt = $conn->prepare("DELETE FROM vehicle WHERE OwnerID = ?");
    $delVehiclesStmt->bind_param("s", $ownerID);
    $delVehiclesStmt->execute();
    
    $vehiclesDeleted = $delVehiclesStmt->affected_rows;

    // Delete the owner
    $stmt = $conn->prepare("DELETE FROM vehicleowner WHERE OwnerID = ?");
    $stmt->bind_param("s", $ownerID);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        $conn->commit();
        $msg = "Owner deleted successfully.";
        if ($vehiclesDeleted > 0) {
            $msg .= " $vehiclesDeleted associated vehicle(s) were also removed.";
        }
        echo json_encode(['success' => true, 'message' => $msg]);
    } else {
        $conn->rollback();
        echo json_encode(['success' => false, 'message' => 'Owner not found']);
    }

    $db->closeConnection();

} catch (Exception $e) {
    if (isset($conn)) $conn->rollback();
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>