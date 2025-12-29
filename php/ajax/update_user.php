<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'SSEDMMO Admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

require_once '../dbConnection.php';

if (!isset($_POST['userID']) || !isset($_POST['username']) || !isset($_POST['role'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

$db = new Database();
$conn = $db->getConnection();

$userID = $_POST['userID'];
$username = $_POST['username'];
$role = $_POST['role'];
$password = $_POST['password'];

try {
    if (!empty($password)) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE user SET username = ?, password = ?, role = ? WHERE userID = ?");
        $stmt->bind_param("ssss", $username, $hashedPassword, $role, $userID);
    } else {
        $stmt = $conn->prepare("UPDATE user SET username = ?, role = ? WHERE userID = ?");
        $stmt->bind_param("sss", $username, $role, $userID);
    }

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'User updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update user']);
    }

    $stmt->close();
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}

$db->closeConnection();
?>