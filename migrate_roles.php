<?php
require_once 'php/dbConnection.php';

$db = new Database();
$conn = $db->getConnection();

echo "Starting migration...\n";

// 1. Modify the column to VARCHAR to allow any string
try {
    $sql = "ALTER TABLE user MODIFY COLUMN role VARCHAR(50) NOT NULL";
    if ($conn->query($sql) === TRUE) {
        echo "Successfully modified 'role' column to VARCHAR(50).\n";
    } else {
        echo "Error modifying column: " . $conn->error . "\n";
    }
} catch (Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
}

// 2. Update existing roles
$updates = [
    'admin' => 'SSEDMMO Admin',
    'staff' => 'SSEDMMO Staff'
];

foreach ($updates as $old => $new) {
    $sql = "UPDATE user SET role = '$new' WHERE role = '$old'";
    if ($conn->query($sql) === TRUE) {
        echo "Updated rows with role '$old' to '$new'. Rows affected: " . $conn->affected_rows . "\n";
    } else {
        echo "Error updating role '$old': " . $conn->error . "\n";
    }
}

echo "Migration completed.\n";
$db->closeConnection();
?>