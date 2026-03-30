<?php
header('Content-Type: application/json');
require_once '../dbConnection.php';
require_once '../auth_check.php';

$db = new Database();
$conn = $db->getConnection();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Validate contact number: allow digits, +, spaces, hyphens; 10-15 chars
function isValidContact($contact) {
    $contact = trim($contact);
    if (strlen($contact) < 10 || strlen($contact) > 15) return false;
    return (bool) preg_match('/^[0-9+\s\-]+$/', $contact);
}

$fName = trim($_POST['fName'] ?? '');
$lName = trim($_POST['lName'] ?? '');
$mName = trim($_POST['mName'] ?? '');
$schoolID = trim($_POST['schoolID'] ?? '');
$email = trim($_POST['email'] ?? '');
$contact_num = trim($_POST['contact_num'] ?? '');
$role = trim($_POST['role'] ?? '');
$college = trim($_POST['college'] ?? '');
$course = trim($_POST['course'] ?? '');
$employment_type = !empty($_POST['employment_type']) ? trim($_POST['employment_type']) : null;

if (empty($fName) || empty($lName) || empty($email) || empty($contact_num) || empty($role) || empty($college) || empty($schoolID)) {
    echo json_encode(['success' => false, 'message' => 'Required fields: First Name, Last Name, School ID, Email, Contact Number, Role, College']);
    exit;
}

if (!isValidContact($contact_num)) {
    echo json_encode(['success' => false, 'message' => 'Invalid phone/contact format. Use 10-15 digits (e.g. 09XXXXXXXXX).']);
    exit;
}

try {
    $result = $conn->query("SELECT MAX(CAST(SUBSTRING(OwnerID, 2) AS UNSIGNED)) as max_num FROM vehicleowner WHERE OwnerID LIKE 'O%'");
    $maxNum = $result ? $result->fetch_assoc()['max_num'] : 0;
    $ownerID = 'O' . str_pad($maxNum + 1, 3, '0', STR_PAD_LEFT);

    $approvalTime = date('Y-m-d H:i:s');
    $academicYear = '2025-2026';
    $registrationStatus = 'approved';
    $stmt = $conn->prepare("INSERT INTO vehicleowner (OwnerID, fName, lName, mName, schoolID, role, email, contact_num, college, course, academicYear, registrationStatus, approvalTimestamp, employment_type) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssssssssss", $ownerID, $fName, $lName, $mName, $schoolID, $role, $email, $contact_num, $college, $course, $academicYear, $registrationStatus, $approvalTime, $employment_type);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Owner added successfully', 'ownerID' => $ownerID]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to add owner']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}

$db->closeConnection();
