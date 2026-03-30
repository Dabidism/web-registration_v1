<?php
session_start();
require_once '../dbConnection.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'SSEDMMO Admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

$code = $_POST['code'] ?? '';
$name = $_POST['name'] ?? '';
$courses = $_POST['courses'] ?? '[]';
$coursesArray = json_decode($courses, true);

if (empty($code) || empty($name)) {
    echo json_encode(['success' => false, 'message' => 'Code and Name are required']);
    exit;
}

try {
    $db = new Database();
    $conn = $db->getConnection();
    
    // Start transaction
    $conn->begin_transaction();

    $stmt = $conn->prepare("INSERT INTO colleges (code, name) VALUES (?, ?)");
    $stmt->bind_param("ss", $code, $name);
    
    if ($stmt->execute()) {
        $college_id = $conn->insert_id;
        
        // Insert courses if any exist
        if (!empty($coursesArray) && is_array($coursesArray)) {
            $courseStmt = $conn->prepare("INSERT INTO courses (college_id, name) VALUES (?, ?)");
            foreach ($coursesArray as $courseName) {
                $cName = trim($courseName);
                if (!empty($cName)) {
                    $courseStmt->bind_param("is", $college_id, $cName);
                    $courseStmt->execute();
                }
            }
        }
        
        $conn->commit();
        echo json_encode(['success' => true]);
    } else {
        $conn->rollback();
        echo json_encode(['success' => false, 'message' => 'Could not add college, code might exist']);
    }

    $db->closeConnection();
} catch (Exception $e) {
    if (isset($conn)) $conn->rollback();
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
