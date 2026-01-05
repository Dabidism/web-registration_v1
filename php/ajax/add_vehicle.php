<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

require_once '../dbConnection.php';

$db = new Database();
$conn = $db->getConnection();

try {
    $plateNum = trim($_POST['plateNum'] ?? '');
    $ownerID = $_POST['ownerID'] ?? '';
    $vehicleType = $_POST['vehicleType'] ?? '';

    if ($vehicleType === 'Other' && !empty($_POST['otherVehicleType'])) {
        $vehicleType = trim($_POST['otherVehicleType']);
    }

    $model = trim($_POST['model'] ?? '');
    $manufacturer = trim($_POST['manufacturer'] ?? '');
    $color = trim($_POST['color'] ?? '');
    $cubicCapacity = ($vehicleType === 'Motorcycle' || (!empty($_POST['cubicCapacity']) && is_numeric($_POST['cubicCapacity']))) ? intval($_POST['cubicCapacity']) : null;
    $numOfWheels = intval($_POST['numOfWheels'] ?? 0);
    $fuelType = $_POST['fuelType'] ?? '';

    if (empty($plateNum) || empty($ownerID) || empty($vehicleType) || empty($model) || empty($manufacturer) || empty($color) || empty($fuelType) || $numOfWheels <= 0) {
        echo json_encode(['success' => false, 'message' => 'All fields are required']);
        exit;
    }

    // Check if plate number already exists
    $checkStmt = $conn->prepare("SELECT plateNum FROM vehicle WHERE plateNum = ?");
    $checkStmt->bind_param("s", $plateNum);
    $checkStmt->execute();
    if ($checkStmt->get_result()->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'Plate number ' . htmlspecialchars($plateNum) . ' already exists in the system.']);
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO vehicle (plateNum, OwnerID, vehicleType, model, manufacturer, color, cubicCapacity, numOfWheels, fuelType) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssssis", $plateNum, $ownerID, $vehicleType, $model, $manufacturer, $color, $cubicCapacity, $numOfWheels, $fuelType);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Vehicle added successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to add vehicle']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
} finally {
    $db->closeConnection();
}
?>