<?php
require_once 'dbConnection.php';
$db = new Database();
$conn = $db->getConnection();
$res = $conn->query("DESCRIBE entryexitlog");
while($row = $res->fetch_assoc()) {
    print_r($row);
}
echo "\n---VISITOR---\n";
$res = $conn->query("DESCRIBE visitor");
while($row = $res->fetch_assoc()) {
    print_r($row);
}
?>
