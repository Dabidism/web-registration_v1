<?php
header('Content-Type: application/json');
require_once '../dbConnection.php';

$db = new Database();
$conn = $db->getConnection();

function isValidContact($contact) {
    $contact = trim($contact);
    if (strlen($contact) < 10 || strlen($contact) > 15) return false;
    return (bool) preg_match('/^[0-9+\s\-]+$/', $contact);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ownerID = $_POST['ownerID'];
    $fName = $_POST['fName'];
    $lName = $_POST['lName'];
    $mName = $_POST['mName'];
    $email = $_POST['email'];
    $contact_num = trim($_POST['contact_num'] ?? '');
    $college = $_POST['college'];
    $course = $_POST['course'];
    $employment_type = $_POST['employment_type'] ?? null;

    if (!isValidContact($contact_num)) {
        echo json_encode(['success' => false, 'message' => 'Invalid phone/contact format. Use 10-15 digits (e.g. 09XXXXXXXXX).']);
        $db->closeConnection();
        exit;
    }
    
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