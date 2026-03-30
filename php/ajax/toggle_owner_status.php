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

$id = $_POST['id'] ?? '';
$currentStatus = $_POST['current_status'] ?? 1;

if (empty($id)) {
    echo json_encode(['success' => false, 'message' => 'Owner ID is required']);
    exit;
}

try {
    $db = new Database();
    $conn = $db->getConnection();
    
    $newStatus = $currentStatus ? 0 : 1;
    $stmt = $conn->prepare("UPDATE vehicleowner SET is_active = ? WHERE OwnerID = ?");
    $stmt->bind_param("is", $newStatus, $id);
    
    if ($stmt->execute()) {
        // Also update their vehicles
        $stmtV = $conn->prepare("UPDATE vehicle SET is_active = ? WHERE OwnerID = ?");
        $stmtV->bind_param("is", $newStatus, $id);
        $stmtV->execute();
        
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Could not update status']);
    }

    $db->closeConnection();
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
