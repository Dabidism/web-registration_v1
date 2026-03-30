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
$status = $_POST['status'] ?? ''; // New status to be set: 1 for active, 0 for inactive

if (empty($id) || $status === '') {
    echo json_encode(['success' => false, 'message' => 'College ID and status are required']);
    exit;
}

try {
    $db = new Database();
    $conn = $db->getConnection();

    $stmt = $conn->prepare("UPDATE colleges SET is_active = ? WHERE id = ?");
    $stmt->bind_param("ii", $status, $id);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Could not update college status']);
    }

    $db->closeConnection();
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
