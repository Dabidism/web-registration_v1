<?php
header('Content-Type: application/json');
require_once '../dbConnection.php';

$db = new Database();
$conn = $db->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ownerID = $_POST['ownerID'];
    $fName = $_POST['fName'];
    $lName = $_POST['lName'];
    $mName = $_POST['mName'];
    $email = $_POST['email'];
    $contact_num = $_POST['contact_num'];
    $college = $_POST['college'];
    $course = $_POST['course'];
    $employment_type = $_POST['employment_type'] ?? null;
    
    $query = "UPDATE vehicleowner SET fName = ?, lName = ?, mName = ?, email = ?, contact_num = ?, college = ?, course = ?, employment_type = ? WHERE OwnerID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssssssss", $fName, $lName, $mName, $email, $contact_num, $college, $course, $employment_type, $ownerID);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Owner updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update owner']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

$db->closeConnection();
?>