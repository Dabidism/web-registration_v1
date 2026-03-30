<?php
header('Content-Type: application/json');
require_once '../dbConnection.php';

try {
    $db = new Database();
    $conn = $db->getConnection();

    $collegesResult = $conn->query("SELECT id, code FROM colleges");
    $collegeCourses = [];

    if ($collegesResult && $collegesResult->num_rows > 0) {
        while ($col = $collegesResult->fetch_assoc()) {
            $college_id = $col['id'];
            $code = $col['code'];
            $collegeCourses[$code] = [];
            
            $coursesResult = $conn->query("SELECT name FROM courses WHERE college_id = $college_id");
            if ($coursesResult && $coursesResult->num_rows > 0) {
                while ($crs = $coursesResult->fetch_assoc()) {
                    $collegeCourses[$code][] = $crs['name'];
                }
            }
        }
    }

    echo json_encode($collegeCourses);
    $db->closeConnection();
} catch (Exception $e) {
    echo json_encode([]);
}
?>