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

// Get statistics
if ($period === 'custom' && isset($endDate)) {
    $entriesResult = $conn->query("SELECT COUNT(*) as count FROM entryexitlog WHERE entryTime BETWEEN '$startDate' AND '$endDate'");
} else {
    $entriesResult = $conn->query("SELECT COUNT(*) as count FROM entryexitlog WHERE entryTime >= '$startDate'");
}
$totalEntries = $entriesResult ? $entriesResult->fetch_assoc()['count'] : 0;

// Get all entries for the period
$allEntriesQuery = "SELECT 
    CASE 
        WHEN vo.fName IS NOT NULL THEN CONCAT(vo.fName, ' ', vo.lName)
        ELSE v.fullName
    END as fullName,
    e.plateNum, e.entryTime, e.exitTime, e.gateLocation, e.status
    FROM entryexitlog e 
    LEFT JOIN vehicle vh ON e.plateNum = vh.plateNum
    LEFT JOIN vehicleowner vo ON vh.OwnerID = vo.OwnerID
    LEFT JOIN visitor v ON vh.visitorID = v.visitorID
    $whereClause 
    ORDER BY e.entryTime DESC";

$allEntriesResult = $conn->query($allEntriesQuery);

$entriesTable = '';
if ($allEntriesResult && $allEntriesResult->num_rows > 0) {
    $entriesTable = '<table class="data-table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Plate Number</th>
                <th>Entry Time</th>
                <th>Exit Time</th>
                <th>Gate Location</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>';

    while ($row = $allEntriesResult->fetch_assoc()) {
        $name = $row['fullName'] ?: 'Unknown';
        $plate = $row['plateNum'] ?: 'N/A';
        $entryTime = $row['entryTime'] ? date('M j, Y g:i A', strtotime($row['entryTime'])) : 'N/A';
        $exitTime = $row['exitTime'] ? date('M j, Y g:i A', strtotime($row['exitTime'])) : 'Still Inside';
        $gate = $row['gateLocation'] ?: 'New Site';
        $status = ucfirst($row['status'] ?: 'entered');

        $entriesTable .= "<tr>
            <td>$name</td>
            <td>$plate</td>
            <td>$entryTime</td>
            <td>$exitTime</td>
            <td>$gate</td>
            <td>$status</td>
        </tr>";
    }
    $entriesTable .= '</tbody></table>';
} else {
    $entriesTable = '<p class="no-data">No entries found for this period.</p>';
}

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
            <h2 style='color: #666; margin: 5px 0;'>COMPREHENSIVE REPORT</h2>
            <p class='date'>Generated on: " . date('M j, Y g:i:s A') . "</p>
            <p class='date'>Period: $periodLabel</p>
        </div>
        
        <div class='section'>
            <h2>SUMMARY STATISTICS</h2>
            <div class='summary-grid'>
                <div class='summary-card'>
                    <h3>Total Entries</h3>
                    <p class='number'>" . number_format($totalEntries) . "</p>
                </div>
            </div>
        </div>
        
        <div class='section'>
            <h2>DETAILED ENTRY/EXIT LOG</h2>
            $entriesTable
        </div>
    </div>
</body>
</html>";

$db->closeConnection();

echo json_encode(['html' => $html]);
?>