<?php
require 'c:/xampp/htdocs/web-registration_v1/php/dbConnection.php';
$db = new Database();
$conn = $db->getConnection();
$hash = password_hash('admin', PASSWORD_DEFAULT);
$conn->query("UPDATE user SET password = '$hash' WHERE username = 'admin'");
echo "Admin password updated to 'admin'.";
