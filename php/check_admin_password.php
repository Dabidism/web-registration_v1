<?php
session_start();
require_once 'dbConnection.php';

// Only allow this for debugging - remove in production
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'SSEDMMO Admin') {
    die('Access denied');
}

$db = new Database();
$conn = $db->getConnection();

// Get admin user info
$stmt = $conn->prepare("SELECT userID, username, password FROM user WHERE role = 'SSEDMMO Admin'");
$stmt->execute();
$result = $stmt->get_result();

echo "<h2>Admin Users Debug Info</h2>";
while ($user = $result->fetch_assoc()) {
    echo "<p><strong>User ID:</strong> " . htmlspecialchars($user['userID']) . "</p>";
    echo "<p><strong>Username:</strong> " . htmlspecialchars($user['username']) . "</p>";
    echo "<p><strong>Password Hash:</strong> " . htmlspecialchars($user['password']) . "</p>";

    // Test password verification with common passwords
    $testPasswords = ['admin', 'password', '123456', 'admin123'];
    echo "<p><strong>Password Tests:</strong></p>";
    foreach ($testPasswords as $testPass) {
        $isValid = password_verify($testPass, $user['password']);
        echo "<p>- '$testPass': " . ($isValid ? 'VALID' : 'INVALID') . "</p>";
    }
    echo "<hr>";
}

$db->closeConnection();
?>