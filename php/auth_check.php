<?php
// Ensure session is started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Basic Session Check
if (!isset($_SESSION['user_id'])) {
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        // AJAX request
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Session expired', 'redirect' => 'login.php']);
        exit;
    } else {
        // Normal request
        header("Location: login.php");
        exit;
    }
}
?>