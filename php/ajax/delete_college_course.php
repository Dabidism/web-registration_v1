<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'SSEDMMO Admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    require_once '../dbConnection.php';
    
    $id = $_POST['id'] ?? '';

    if (empty($id)) {
        echo json_encode(['success' => false, 'message' => 'Course ID is required']);
        exit;
    }

    try {
        $db = new Database();
        $conn = $db->getConnection();

        $stmt = $conn->prepare("DELETE FROM courses WHERE id = ?");
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error deleting course']);
        }
        $db->closeConnection();
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Database exception']);
    }
}
?>
