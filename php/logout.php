<?php
session_start();

// Log logout before destroying session
if (isset($_SESSION['user_id'])) {
    require_once 'dbConnection.php';
    $db = new Database();
    $conn = $db->getConnection();
    
    $logStmt = $conn->prepare("INSERT INTO accesslog (userID, action, description) VALUES (?, 'logout', 'User logged out')");
    $logStmt->bind_param("s", $_SESSION['user_id']);
    $logStmt->execute();
    
    $db->closeConnection();
}

session_destroy();
header("Location: login.php");
exit;
?>