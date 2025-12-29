<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    exit;
}

require_once '../dbConnection.php';

$db = new Database();
$conn = $db->getConnection();

// Get parking allocation data
$parkingResult = $conn->query("SELECT * FROM parkingstatus WHERE id = 1");
$parkingData = $parkingResult ? $parkingResult->fetch_assoc() : null;

// Get campus occupancy by role from historical_log and vehicle owner data
$occupancyByRole = [
    'students' => 0,
    'faculty' => 0,
    'staff' => 0,
    'guests' => 0
];

// Get current occupancy by role
$result = $conn->query("
    SELECT 
        CASE 
            WHEN vo.role = 'student' THEN 'students'
            WHEN vo.role = 'faculty' THEN 'faculty'
            WHEN vo.role IN ('non-teaching', 'staff') THEN 'staff'
            WHEN v.visitorID IS NOT NULL THEN 'guests'
            ELSE 'guests'
        END as role_category,
        COUNT(*) as count
    FROM historical_log h
    JOIN vehicle v ON h.plateNum = v.plateNum
    LEFT JOIN vehicleowner vo ON v.OwnerID = vo.OwnerID
    WHERE h.status = 'entered' AND h.exitTime IS NULL
    GROUP BY role_category
");

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $occupancyByRole[$row['role_category']] = $row['count'];
    }
}

// Update parking status with current occupancy
if ($parkingData) {
    $conn->query("UPDATE parkingstatus SET 
        currentOccupiedStudents = {$occupancyByRole['students']},
        currentOccupiedFaculty = {$occupancyByRole['faculty']},
        currentOccupiedStaff = {$occupancyByRole['staff']},
        currentOccupiedGuests = {$occupancyByRole['guests']}
        WHERE id = 1");
}

$totalOccupied = array_sum($occupancyByRole);
$totalCapacity = $parkingData ? $parkingData['totalCapacity'] : 300;

$response = [
    'total_occupied' => $totalOccupied,
    'total_capacity' => $totalCapacity,
    'occupancy_percentage' => ($totalOccupied / $totalCapacity) * 100,
    'occupancy_by_role' => $occupancyByRole,
    'allocations' => [
        'students' => $parkingData['allocatedStudents'] ?? 0,
        'faculty' => $parkingData['allocatedFaculty'] ?? 0,
        'staff' => $parkingData['allocatedStaff'] ?? 0,
        'guests' => $parkingData['allocatedGuests'] ?? 0
    ]
];

$db->closeConnection();

header('Content-Type: application/json');
echo json_encode($response);
?>