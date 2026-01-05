<?php
require_once 'dbConnection.php';

$db = new Database();
$conn = $db->getConnection();

// Add reviewed_by column to applications table
$sql = "SHOW COLUMNS FROM applications LIKE 'reviewed_by'";
$result = $conn->query($sql);

if ($result->num_rows == 0) {
    $sql = "ALTER TABLE applications ADD COLUMN reviewed_by VARCHAR(100) DEFAULT NULL AFTER registrationStatus";
    if ($conn->query($sql) === TRUE) {
        echo "Column 'reviewed_by' added successfully to 'applications' table.<br>";
    } else {
        echo "Error adding column: " . $conn->error . "<br>";
    }
} else {
    echo "Column 'reviewed_by' already exists in 'applications' table.<br>";
}

// Also add to vehicleowner just in case? 
// The user asks for "approved table of registration", which likely refers to registration_applications.php lists.
// But maybe they mean the vehicleowners table? 
// No, "approved table of registration" likely means the "Approved" tab/filter in registration_applications.php.
// So reviewed_by in applications is sufficient.

$db->closeConnection();
?>