<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'SSEDMMO Admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

require_once '../dbConnection.php';

if (!isset($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'User ID required']);
    exit;
}

$db = new Database();
$conn = $db->getConnection();

$userID = $_GET['id'];
$query = "SELECT u.userID, u.username, u.role, u.created_at, 
          (SELECT MAX(timestamp) FROM accesslog WHERE userID = u.userID AND action = 'login') as last_login 
          FROM user u 
          WHERE u.userID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $userID);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    echo json_encode(['success' => true, 'user' => $user]);
} else {
    echo json_encode(['success' => false, 'message' => 'User not found']);
}

$stmt->close();
$db->closeConnection();
?>