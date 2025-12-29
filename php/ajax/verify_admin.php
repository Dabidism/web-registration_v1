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

$password = $_POST['password'] ?? '';

if (empty($password)) {
    echo json_encode(['success' => false, 'message' => 'Password is required']);
    exit;
}

try {
    $db = new Database();
    $conn = $db->getConnection();

    // Check password against all admin users
    $stmt = $conn->prepare("SELECT password FROM user WHERE role = 'SSEDMMO Admin'");
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'No admin users found']);
        exit;
    }

    $passwordValid = false;
    while ($user = $result->fetch_assoc()) {
        if (password_verify($password, $user['password'])) {
            $passwordValid = true;
            break;
        }
    }

    if ($passwordValid) {
        echo json_encode(['success' => true, 'message' => 'Password verified']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid admin password']);
    }

    $db->closeConnection();

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>