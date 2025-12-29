<?php
session_start();

// Check if user is already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: php/dashboard.php");
    exit;
}

// Redirect to login page
header("Location: php/login.php");
exit;
?>