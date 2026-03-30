<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'SSEDMMO Admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    require_once '../dbConnection.php';
    
    $college_id = $_POST['college_id'] ?? '';
    $name = trim($_POST['name'] ?? '');

    if (empty($college_id) || empty($name)) {
        echo json_encode(['success' => false, 'message' => 'College ID and Name are required']);
        exit;
    }

    try {
        $db = new Database();
        $conn = $db->getConnection();

        $stmt = $conn->prepare("INSERT INTO courses (college_id, name) VALUES (?, ?)");
        $stmt->bind_param("is", $college_id, $name);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'id' => $conn->insert_id]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error adding course']);
        }
        $db->closeConnection();
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Database exception']);
    }
}
?>
