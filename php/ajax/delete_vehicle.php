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

    $stmt = $conn->prepare("DELETE FROM vehicle WHERE plateNum = ?");
    $stmt->bind_param("s", $plateNum);

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode(['success' => true, 'message' => 'Vehicle deleted successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Vehicle not found']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete vehicle']);
    }

    $db->closeConnection();

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>