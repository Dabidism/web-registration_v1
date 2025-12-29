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

    // Check if owner has vehicles
    $checkStmt = $conn->prepare("SELECT COUNT(*) as count FROM vehicle WHERE OwnerID = ?");
    $checkStmt->bind_param("s", $ownerID);
    $checkStmt->execute();
    $result = $checkStmt->get_result();
    $count = $result->fetch_assoc()['count'];

    if ($count > 0) {
        echo json_encode(['success' => false, 'message' => 'Cannot delete owner with registered vehicles']);
        exit;
    }

    $stmt = $conn->prepare("DELETE FROM vehicleowner WHERE OwnerID = ?");
    $stmt->bind_param("s", $ownerID);

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode(['success' => true, 'message' => 'Owner deleted successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Owner not found']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete owner']);
    }

    $db->closeConnection();

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>