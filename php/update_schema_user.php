<?php
require_once 'dbConnection.php';

$db = new Database();
$conn = $db->getConnection();

// Add created_at column to user table
$sql = "SHOW COLUMNS FROM user LIKE 'created_at'";
$result = $conn->query($sql);

if ($result->num_rows == 0) {
    $sql = "ALTER TABLE user ADD COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP";
    if ($conn->query($sql) === TRUE) {
        echo "Column 'created_at' added successfully to 'user' table.<br>";
    } else {
        echo "Error adding column: " . $conn->error . "<br>";
    }
} else {
    echo "Column 'created_at' already exists in 'user' table.<br>";
}

$db->closeConnection();
?>