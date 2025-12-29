<?php
session_start();
header('Content-Type: application/json');
require_once '../dbConnection.php';

// Check if user is admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'SSEDMMO Admin') {
    echo json_encode(['success' => false, 'message' => 'Access denied. Admin privileges required.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';
$role = $_POST['role'] ?? '';

if (empty($username) || empty($password) || empty($role)) {
    echo json_encode(['success' => false, 'message' => 'All fields are required']);
    exit;
}

$db = new Database();
$conn = $db->getConnection();

// Check if username already exists
$checkStmt = $conn->prepare("SELECT userID FROM user WHERE username = ?");
$checkStmt->bind_param("s", $username);
$checkStmt->execute();
$result = $checkStmt->get_result();

if ($result->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'Username already exists']);
    $db->closeConnection();
    exit;
}

// Generate new userID
$result = $conn->query("SELECT MAX(CAST(SUBSTRING(userID, 2) AS UNSIGNED)) as max_id FROM user WHERE userID LIKE 'U%'");
$row = $result->fetch_assoc();
$nextId = ($row['max_id'] ?? 0) + 1;
$userID = 'U' . str_pad($nextId, 3, '0', STR_PAD_LEFT);

// Hash password
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// Insert new user
$stmt = $conn->prepare("INSERT INTO user (userID, username, password, role) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $userID, $username, $hashedPassword, $role);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'User added successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to add user: ' . $stmt->error]);
}

$db->closeConnection();
?>