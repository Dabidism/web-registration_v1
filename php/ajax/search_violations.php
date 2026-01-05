<?php
// ajax/search_violations.php
session_start();
header('Content-Type: application/json');

require_once '../dbConnection.php';
require_once '../auth_check.php'; // Ensure user is logged in

// Check if user has permission (Admin or Staff)
// Assuming auth_check.php handles basic login check.

$db = new Database();
$conn = $db->getConnection();

$response = [
    'success' => false,
    'data' => [],
    'message' => ''
];

if (isset($_GET['query'])) {
    $search = '%' . $_GET['query'] . '%';

    try {
        $query = "SELECT 
                    v.violationID, 
                    v.violationType, 
                    v.status, 
                    v.violationDate,
                    veh.plateNum, 
                    veh.model, 
                    veh.manufacturer, 
                    veh.vehicleType,
                    vo.fName, 
                    vo.lName, 
                    vo.contact_num, 
                    vo.email
                  FROM vehicle veh
                  JOIN vehicleowner vo ON veh.OwnerID = vo.OwnerID
                  LEFT JOIN violations v ON veh.plateNum = v.plateNum
                  WHERE 
                    veh.plateNum LIKE ? OR 
                    CONCAT(vo.fName, ' ', vo.lName) LIKE ? OR
                    veh.model LIKE ? OR
                    veh.manufacturer LIKE ? OR
                    veh.vehicleType LIKE ?
                  ORDER BY v.violationDate DESC, veh.plateNum ASC
                  LIMIT 50";

        $stmt = $conn->prepare($query);
        $stmt->bind_param("sssss", $search, $search, $search, $search, $search);
        $stmt->execute();
        $result = $stmt->get_result();

        $data = [];
        while ($row = $result->fetch_assoc()) {
            // Format timestamp if it exists, else N/A
            if (!empty($row['violationDate'])) {
                $row['formatted_date'] = date('M j, Y g:i A', strtotime($row['violationDate']));
            } else {
                $row['formatted_date'] = 'N/A';
            }
            $data[] = $row;
        }

        $response['success'] = true;
        $response['data'] = $data;

    } catch (Exception $e) {
        $response['message'] = "Database error: " . $e->getMessage();
    }
} else {
    $response['message'] = "No query provided";
}

echo json_encode($response);
$db->closeConnection();
?>