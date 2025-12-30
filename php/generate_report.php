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
        break;
    case 'week':
        $startDate = date('Y-m-d H:i:s', strtotime('-7 days'));
        $periodLabel = 'Last 7 Days';
        break;
    case 'month':
        $startDate = date('Y-m-d H:i:s', strtotime('-30 days'));
        $periodLabel = 'Last 30 Days';
        break;
    case 'custom':
        if ($customDate) {
            $startDate = $customDate . ' 00:00:00';
            $endDate = $customDate . ' 23:59:59';
            $periodLabel = 'Date: ' . date('M j, Y', strtotime($customDate));
        } else {
            $startDate = date('Y-m-d H:i:s', strtotime('-24 hours'));
            $periodLabel = 'Last 24 Hours';
        }
        break;
    default:
        $startDate = date('Y-m-d H:i:s', strtotime('-24 hours'));
        $periodLabel = 'Last 24 Hours';
}

// Get statistics for the period
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

// Generate report content
$content = "
<h4>SUMMARY ($periodLabel)</h4>
<table style='width:100%; border-collapse: collapse; margin-bottom: 20px;'>
    <tr>
        <td style='padding: 8px; border: 1px solid #ddd;'>Total Entries</td>
        <td style='padding: 8px; border: 1px solid #ddd;'><strong>" . number_format($totalEntries) . "</strong></td>
    </tr>
    <tr>
        <td style='padding: 8px; border: 1px solid #ddd;'>Total Exits</td>
        <td style='padding: 8px; border: 1px solid #ddd;'><strong>" . number_format($totalExits) . "</strong></td>
    </tr>
    <tr>
        <td style='padding: 8px; border: 1px solid #ddd;'>Unique Vehicles</td>
        <td style='padding: 8px; border: 1px solid #ddd;'><strong>" . number_format($totalUniqueVehicles) . "</strong></td>
    </tr>
</table>

<h4>ENTRIES BY VEHICLE TYPE</h4>
<table style='width:100%; border-collapse: collapse; margin-bottom: 20px;'>
    <tr style='background-color: #f2f2f2;'>
        <th style='padding: 8px; border: 1px solid #ddd; text-align: left;'>Vehicle Type</th>
        <th style='padding: 8px; border: 1px solid #ddd; text-align: left;'>Count</th>
    </tr>";

if ($vehicleTypeResult && $vehicleTypeResult->num_rows > 0) {
    while ($row = $vehicleTypeResult->fetch_assoc()) {
        $content .= "
    <tr>
        <td style='padding: 8px; border: 1px solid #ddd;'>" . htmlspecialchars($row['vehicleType'] ?: 'Unknown') . "</td>
        <td style='padding: 8px; border: 1px solid #ddd;'>" . number_format($row['count']) . "</td>
    </tr>";
    }
} else {
    $content .= "<tr><td colspan='2' style='padding: 8px; border: 1px solid #ddd; text-align: center;'>No data available</td></tr>";
}

$content .= "</table>

<h4>ENTRIES BY USER ROLE</h4>
<table style='width:100%; border-collapse: collapse; margin-bottom: 20px;'>
    <tr style='background-color: #f2f2f2;'>
        <th style='padding: 8px; border: 1px solid #ddd; text-align: left;'>Role</th>
        <th style='padding: 8px; border: 1px solid #ddd; text-align: left;'>Count</th>
    </tr>";

if ($roleResult && $roleResult->num_rows > 0) {
    while ($row = $roleResult->fetch_assoc()) {
        $roleName = ucfirst(strtolower($row['userRole']));
        $content .= "
    <tr>
        <td style='padding: 8px; border: 1px solid #ddd;'>" . htmlspecialchars($roleName) . "</td>
        <td style='padding: 8px; border: 1px solid #ddd;'>" . number_format($row['count']) . "</td>
    </tr>";
    }
} else {
    $content .= "<tr><td colspan='2' style='padding: 8px; border: 1px solid #ddd; text-align: center;'>No data available</td></tr>";
}

$content .= "</table>
<p style='font-size: 12px; color: #666;'>Report Generated: " . date('n/j/Y g:i:s A') . "</p>
";

$db->closeConnection();

echo json_encode(['content' => $content]);
?>