<?php
require_once 'dbConnection.php';

$db = new Database();
$conn = $db->getConnection();

echo "<h2>Database Schema Update</h2>";

// Add session_token column to user table
$sql = "SHOW COLUMNS FROM user LIKE 'session_token'";
$result = $conn->query($sql);

if ($result->num_rows == 0) {
    // Column doesn't exist, add it
    $alterSql = "ALTER TABLE user ADD COLUMN session_token VARCHAR(255) DEFAULT NULL";
    if ($conn->query($alterSql) === TRUE) {
        echo "<div style='color: green'>Successfully added 'session_token' column to 'user' table.</div>";
    } else {
        echo "<div style='color: red'>Error adding column: " . $conn->error . "</div>";
    }
} else {
    echo "<div style='color: orange'>Column 'session_token' already exists. No changes made.</div>";
}

$db->closeConnection();
?>
<br>
<a href="login.php">Go to Login</a>