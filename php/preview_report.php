<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

require_once 'dbConnection.php';

$input = json_decode(file_get_contents('php://input'), true);
$period = $input['period'] ?? 'day';
$customDate = $input['customDate'] ?? '';

$db = new Database();
$conn = $db->getConnection();

// Calculate date range based on period
switch ($period) {
    case 'day':
        $startDate = date('Y-m-d H:i:s', strtotime('-24 hours'));
        $periodLabel = 'Last 24 Hours';
        $whereClause = "WHERE e.entryTime >= '$startDate'";
        break;
    case 'week':
        $startDate = date('Y-m-d H:i:s', strtotime('-7 days'));
        $periodLabel = 'Last 7 Days';
        $whereClause = "WHERE e.entryTime >= '$startDate'";
        break;
    case 'month':
        $startDate = date('Y-m-d H:i:s', strtotime('-30 days'));
        $periodLabel = 'Last 30 Days';
        $whereClause = "WHERE e.entryTime >= '$startDate'";
        break;
    case 'custom':
        if ($customDate) {
            $startDate = $customDate . ' 00:00:00';
            $endDate = $customDate . ' 23:59:59';
            $periodLabel = 'Date: ' . date('M j, Y', strtotime($customDate));
            $whereClause = "WHERE e.entryTime BETWEEN '$startDate' AND '$endDate'";
        } else {
            $startDate = date('Y-m-d H:i:s', strtotime('-24 hours'));
            $periodLabel = 'Last 24 Hours';
            $whereClause = "WHERE e.entryTime >= '$startDate'";
        }
        break;
    default:
        $startDate = date('Y-m-d H:i:s', strtotime('-24 hours'));
        $periodLabel = 'Last 24 Hours';
        $whereClause = "WHERE e.entryTime >= '$startDate'";
}

// Get statistics for the period
if ($period === 'custom' && isset($endDate)) {
    $entriesResult = $conn->query("SELECT COUNT(*) as count FROM entryexitlog WHERE entryTime BETWEEN '$startDate' AND '$endDate'");
    $exitsResult = $conn->query("SELECT COUNT(*) as count FROM entryexitlog WHERE exitTime BETWEEN '$startDate' AND '$endDate'");
    $uniqueVehiclesResult = $conn->query("SELECT COUNT(DISTINCT plateNum) as count FROM entryexitlog WHERE entryTime BETWEEN '$startDate' AND '$endDate'");

    // Breakdown by Vehicle Type
    $vehicleTypeQuery = "SELECT v.vehicleType, COUNT(*) as count 
                         FROM entryexitlog e 
                         JOIN vehicle v ON e.plateNum = v.plateNum 
                         WHERE e.entryTime BETWEEN '$startDate' AND '$endDate' 
                         GROUP BY v.vehicleType";

    // Breakdown by Role
    $roleQuery = "SELECT 
                    CASE 
                        WHEN vo.role IS NOT NULL THEN vo.role 
                        ELSE 'Visitor' 
                    END as userRole, 
                    COUNT(*) as count 
                  FROM entryexitlog e 
                  LEFT JOIN vehicle v ON e.plateNum = v.plateNum 
                  LEFT JOIN vehicleowner vo ON v.OwnerID = vo.OwnerID 
                  WHERE e.entryTime BETWEEN '$startDate' AND '$endDate' 
                  GROUP BY userRole";
} else {
    $entriesResult = $conn->query("SELECT COUNT(*) as count FROM entryexitlog WHERE entryTime >= '$startDate'");
    $exitsResult = $conn->query("SELECT COUNT(*) as count FROM entryexitlog WHERE exitTime >= '$startDate'");
    $uniqueVehiclesResult = $conn->query("SELECT COUNT(DISTINCT plateNum) as count FROM entryexitlog WHERE entryTime >= '$startDate'");

    // Breakdown by Vehicle Type
    $vehicleTypeQuery = "SELECT v.vehicleType, COUNT(*) as count 
                         FROM entryexitlog e 
                         JOIN vehicle v ON e.plateNum = v.plateNum 
                         WHERE e.entryTime >= '$startDate' 
                         GROUP BY v.vehicleType";

    // Breakdown by Role
    $roleQuery = "SELECT 
                    CASE 
                        WHEN vo.role IS NOT NULL THEN vo.role 
                        ELSE 'Visitor' 
                    END as userRole, 
                    COUNT(*) as count 
                  FROM entryexitlog e 
                  LEFT JOIN vehicle v ON e.plateNum = v.plateNum 
                  LEFT JOIN vehicleowner vo ON v.OwnerID = vo.OwnerID 
                  WHERE e.entryTime >= '$startDate' 
                  GROUP BY userRole";
}

$totalEntries = $entriesResult ? $entriesResult->fetch_assoc()['count'] : 0;
$totalExits = $exitsResult ? $exitsResult->fetch_assoc()['count'] : 0;
$totalUniqueVehicles = $uniqueVehiclesResult ? $uniqueVehiclesResult->fetch_assoc()['count'] : 0;

$vehicleTypeResult = $conn->query($vehicleTypeQuery);
$roleResult = $conn->query($roleQuery);

// Construct HTML tables
$summaryTable = "
<div class='section'>
    <h2>SUMMARY STATISTICS</h2>
    <table class='data-table' style='width: 100%; border-collapse: collapse; margin-bottom: 20px;'>
        <tr>
            <th style='border: 1px solid #ddd; padding: 12px; text-align: left; background-color: #f8f9fa;'>Metric</th>
            <th style='border: 1px solid #ddd; padding: 12px; text-align: left; background-color: #f8f9fa;'>Count</th>
        </tr>
        <tr>
            <td style='border: 1px solid #ddd; padding: 12px;'>Total Entries</td>
            <td style='border: 1px solid #ddd; padding: 12px; font-weight: bold;'>" . number_format($totalEntries) . "</td>
        </tr>
        <tr>
            <td style='border: 1px solid #ddd; padding: 12px;'>Total Exits</td>
            <td style='border: 1px solid #ddd; padding: 12px; font-weight: bold;'>" . number_format($totalExits) . "</td>
        </tr>
        <tr>
            <td style='border: 1px solid #ddd; padding: 12px;'>Unique Vehicles</td>
            <td style='border: 1px solid #ddd; padding: 12px; font-weight: bold;'>" . number_format($totalUniqueVehicles) . "</td>
        </tr>
    </table>
</div>

<div class='section'>
    <h2>ENTRIES BY VEHICLE TYPE</h2>
    <table class='data-table' style='width: 100%; border-collapse: collapse; margin-bottom: 20px;'>
        <thead>
            <tr>
                <th style='border: 1px solid #ddd; padding: 12px; text-align: left; background-color: #f8f9fa;'>Vehicle Type</th>
                <th style='border: 1px solid #ddd; padding: 12px; text-align: left; background-color: #f8f9fa;'>Count</th>
            </tr>
        </thead>
        <tbody>";

if ($vehicleTypeResult && $vehicleTypeResult->num_rows > 0) {
    while ($row = $vehicleTypeResult->fetch_assoc()) {
        $summaryTable .= "
            <tr>
                <td style='border: 1px solid #ddd; padding: 12px;'>" . htmlspecialchars($row['vehicleType'] ?: 'Unknown') . "</td>
                <td style='border: 1px solid #ddd; padding: 12px;'>" . number_format($row['count']) . "</td>
            </tr>";
    }
} else {
    $summaryTable .= "<tr><td colspan='2' style='border: 1px solid #ddd; padding: 12px; text-align: center;'>No data available</td></tr>";
}
$summaryTable .= "</tbody></table></div>";

$summaryTable .= "
<div class='section'>
    <h2>ENTRIES BY USER ROLE</h2>
    <table class='data-table' style='width: 100%; border-collapse: collapse; margin-bottom: 20px;'>
        <thead>
            <tr>
                <th style='border: 1px solid #ddd; padding: 12px; text-align: left; background-color: #f8f9fa;'>Role</th>
                <th style='border: 1px solid #ddd; padding: 12px; text-align: left; background-color: #f8f9fa;'>Count</th>
            </tr>
        </thead>
        <tbody>";

if ($roleResult && $roleResult->num_rows > 0) {
    while ($row = $roleResult->fetch_assoc()) {
        $roleName = ucfirst(strtolower($row['userRole']));
        $summaryTable .= "
            <tr>
                <td style='border: 1px solid #ddd; padding: 12px;'>" . htmlspecialchars($roleName) . "</td>
                <td style='border: 1px solid #ddd; padding: 12px;'>" . number_format($row['count']) . "</td>
            </tr>";
    }
} else {
    $summaryTable .= "<tr><td colspan='2' style='border: 1px solid #ddd; padding: 12px; text-align: center;'>No data available</td></tr>";
}
$summaryTable .= "</tbody></table></div>";


$html = "
<!DOCTYPE html>
<html>
<head>
    <title>Gate Access Report Preview</title>
    <link rel='stylesheet' href='../css/report_preview.css'>
</head>
<body>
    <div class='container'>
        <div class='header'>
            <h1>GATE ACCESS SYSTEM</h1>
            <h2 style='color: #666; margin: 5px 0;'>COMPREHENSIVE SUMMARY REPORT</h2>
            <p class='date'>Generated on: " . date('M j, Y g:i:s A') . "</p>
            <p class='date'>Period: $periodLabel</p>
        </div>
        
        $summaryTable
    </div>
</body>
</html>";

$db->closeConnection();

echo json_encode(['html' => $html]);
?>