<?php
require_once 'dbConnection.php';

$db = new Database();
$conn = $db->getConnection();

// Add is_active column to vehicleowner and vehicle tables
$sql1 = "ALTER TABLE vehicleowner ADD COLUMN is_active BOOLEAN DEFAULT 1";
$sql2 = "ALTER TABLE vehicle ADD COLUMN is_active BOOLEAN DEFAULT 1";

$conn->query($sql1);
$conn->query($sql2);

echo "Migration successful\n";
?>
