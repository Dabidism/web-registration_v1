<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

require_once 'dbConnection.php';

// Set timezone
date_default_timezone_set('Asia/Manila');

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

if ($period === 'custom' && isset($endDate)) {
    $dateConditionLog = "BETWEEN '$startDate' AND '$endDate'";
    $dateConditionVis = "BETWEEN '$startDate' AND '$endDate'";
} else {
    $dateConditionLog = ">= '$startDate'";
    $dateConditionVis = ">= '$startDate'";
}

// 1. Total counts
$entriesResult = $conn->query("
    SELECT SUM(cnt) as count FROM (
        SELECT COUNT(*) as cnt FROM entryexitlog WHERE entryTime $dateConditionLog
        UNION ALL
        SELECT COUNT(*) as cnt FROM visitor WHERE entryTime $dateConditionVis
    ) t
");
$exitsResult = $conn->query("
    SELECT SUM(cnt) as count FROM (
        SELECT COUNT(*) as cnt FROM entryexitlog WHERE exitTime $dateConditionLog
        UNION ALL
        SELECT COUNT(*) as cnt FROM visitor WHERE exitTime $dateConditionVis
    ) t
");
$uniqueVehiclesResult = $conn->query("
    SELECT COUNT(DISTINCT plateNum) as count 
    FROM (
        SELECT plateNum FROM entryexitlog WHERE entryTime $dateConditionLog
        UNION
        SELECT plateNum FROM visitor WHERE entryTime $dateConditionVis
    ) t
");

// 2. Average Entry Time
$avgTimeResult = $conn->query("
    SELECT SEC_TO_TIME(AVG(TIME_TO_SEC(TIME(entryTime)))) as avgTime
    FROM (
        SELECT entryTime FROM entryexitlog WHERE entryTime $dateConditionLog
        UNION ALL
        SELECT entryTime FROM visitor WHERE entryTime $dateConditionVis
    ) t
");

// 3. Site Breakdown
$siteResult = $conn->query("
    SELECT IFNULL(gateLocation, 'Old Site') as gateLocation, COUNT(*) as count
    FROM (
        SELECT gateLocation FROM entryexitlog WHERE entryTime $dateConditionLog
        UNION ALL
        SELECT gateLocation FROM visitor WHERE entryTime $dateConditionVis
    ) t
    GROUP BY gateLocation
");

// 4. Vehicle Type Breakdown
$vehicleTypeResult = $conn->query("
    SELECT IFNULL(v.vehicleType, 'Visitor Vehicle') as vehicleType, COUNT(*) as count 
    FROM (
        SELECT plateNum, entryTime FROM entryexitlog WHERE entryTime $dateConditionLog
        UNION ALL
        SELECT plateNum, entryTime FROM visitor WHERE entryTime $dateConditionVis
    ) combined
    LEFT JOIN vehicle v ON combined.plateNum = v.plateNum 
    GROUP BY vehicleType
");

// 5. Role Breakdown
$roleResult = $conn->query("
    SELECT 
        CASE 
            WHEN vo.role IS NOT NULL THEN vo.role 
            ELSE 'Visitor' 
        END as userRole, 
        COUNT(*) as count 
    FROM (
        SELECT plateNum, entryTime FROM entryexitlog WHERE entryTime $dateConditionLog
        UNION ALL
        SELECT plateNum, entryTime FROM visitor WHERE entryTime $dateConditionVis
    ) combined
    LEFT JOIN vehicle v ON combined.plateNum = v.plateNum 
    LEFT JOIN vehicleowner vo ON v.OwnerID = vo.OwnerID 
    GROUP BY userRole
");


$totalEntries = $entriesResult ? $entriesResult->fetch_assoc()['count'] : 0;
$totalExits = $exitsResult ? $exitsResult->fetch_assoc()['count'] : 0;
$totalUniqueVehicles = $uniqueVehiclesResult ? $uniqueVehiclesResult->fetch_assoc()['count'] : 0;
$avgTime = $avgTimeResult ? $avgTimeResult->fetch_assoc()['avgTime'] : null;

$avgTimeFormatted = 'N/A';
if ($avgTime) {
    $avgTimeFormatted = date('g:i A', strtotime($avgTime));
}

// Generate report content
$content = "
<h4>SUMMARY ($periodLabel)</h4>
<table style='width:100%; border-collapse: collapse; margin-bottom: 20px;'>
    <tr>
        <td style='padding: 8px; border: 1px solid #ddd;'>Total Entries</td>
        <td style='padding: 8px; border: 1px solid #ddd;'><strong>" . number_format((float)$totalEntries) . "</strong></td>
    </tr>
    <tr>
        <td style='padding: 8px; border: 1px solid #ddd;'>Total Exits</td>
        <td style='padding: 8px; border: 1px solid #ddd;'><strong>" . number_format((float)$totalExits) . "</strong></td>
    </tr>
    <tr>
        <td style='padding: 8px; border: 1px solid #ddd;'>Unique Vehicles</td>
        <td style='padding: 8px; border: 1px solid #ddd;'><strong>" . number_format((float)$totalUniqueVehicles) . "</strong></td>
    </tr>
    <tr>
        <td style='padding: 8px; border: 1px solid #ddd;'>Average Entry Time</td>
        <td style='padding: 8px; border: 1px solid #ddd;'><strong>" . $avgTimeFormatted . "</strong></td>
    </tr>
</table>

<h4>SITE BREAKDOWN</h4>
<table style='width:100%; border-collapse: collapse; margin-bottom: 20px;'>
    <tr style='background-color: #f2f2f2;'>
        <th style='padding: 8px; border: 1px solid #ddd; text-align: left;'>Site Location</th>
        <th style='padding: 8px; border: 1px solid #ddd; text-align: left;'>Entries</th>
    </tr>";

if ($siteResult && $siteResult->num_rows > 0) {
    while ($row = $siteResult->fetch_assoc()) {
        $content .= "
    <tr>
        <td style='padding: 8px; border: 1px solid #ddd;'>" . htmlspecialchars($row['gateLocation'] ?: 'Unknown') . "</td>
        <td style='padding: 8px; border: 1px solid #ddd;'>" . number_format((float)$row['count']) . "</td>
    </tr>";
    }
} else {
    $content .= "<tr><td colspan='2' style='padding: 8px; border: 1px solid #ddd; text-align: center;'>No data available</td></tr>";
}
$content .= "</table>";


// Optional: Daily Breakdown if period > 1 day
if ($period === 'week' || $period === 'month') {
    $dailyResult = $conn->query("
        SELECT DATE(entryTime) as rDate, DAYNAME(entryTime) as dayName, COUNT(*) as count
        FROM (
            SELECT entryTime FROM entryexitlog WHERE entryTime $dateConditionLog
            UNION ALL
            SELECT entryTime FROM visitor WHERE entryTime $dateConditionVis
        ) t
        GROUP BY DATE(entryTime), DAYNAME(entryTime)
        ORDER BY rDate ASC
    ");
    
    $content .= "
    <h4>DAILY BREAKDOWN</h4>
    <table style='width:100%; border-collapse: collapse; margin-bottom: 20px;'>
        <tr style='background-color: #f2f2f2;'>
            <th style='padding: 8px; border: 1px solid #ddd; text-align: left;'>Date</th>
            <th style='padding: 8px; border: 1px solid #ddd; text-align: left;'>Count</th>
        </tr>";

    if ($dailyResult && $dailyResult->num_rows > 0) {
        while ($row = $dailyResult->fetch_assoc()) {
            $formattedDate = date('M j (l)', strtotime($row['rDate']));
            $content .= "
        <tr>
            <td style='padding: 8px; border: 1px solid #ddd;'>" . $formattedDate . "</td>
            <td style='padding: 8px; border: 1px solid #ddd;'>" . number_format((float)$row['count']) . "</td>
        </tr>";
        }
    } else {
        $content .= "<tr><td colspan='2' style='padding: 8px; border: 1px solid #ddd; text-align: center;'>No data available</td></tr>";
    }
    $content .= "</table>";
}

$content .= "
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
        <td style='padding: 8px; border: 1px solid #ddd;'>" . number_format((float)$row['count']) . "</td>
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
        <td style='padding: 8px; border: 1px solid #ddd;'>" . number_format((float)$row['count']) . "</td>
    </tr>";
    }
} else {
    $content .= "<tr><td colspan='2' style='padding: 8px; border: 1px solid #ddd; text-align: center;'>No data available</td></tr>";
}

$content .= "</table>";

$db->closeConnection();

echo json_encode(['content' => $content]);
?>