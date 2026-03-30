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
$currentStatus = $_POST['current_status'] ?? 1;

if (empty($plateNum)) {
    echo json_encode(['success' => false, 'message' => 'Plate Number is required']);
    exit;
}

try {
    $db = new Database();
    $conn = $db->getConnection();
    
    $newStatus = $currentStatus ? 0 : 1;
    $stmt = $conn->prepare("UPDATE vehicle SET is_active = ? WHERE plateNum = ?");
    $stmt->bind_param("is", $newStatus, $plateNum);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Could not update vehicle status']);
    }

    $db->closeConnection();
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
