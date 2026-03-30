<?php
require_once 'dbConnection.php';
$db = new Database();
$conn = $db->getConnection();

$sql = "CREATE TABLE IF NOT EXISTS courses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    college_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    FOREIGN KEY (college_id) REFERENCES colleges(id) ON DELETE CASCADE
)";

if ($conn->query($sql) === TRUE) {
    echo "Courses table created successfully or already exists.";
} else {
    echo "Error creating table: " . $conn->error;
}
$db->closeConnection();
?>
