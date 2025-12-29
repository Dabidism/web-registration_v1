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
switch($period) {
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
if ($period === 'custom' && isset($endDate)) {
    $entriesResult = $conn->query("SELECT COUNT(*) as count FROM entryexitlog WHERE entryTime BETWEEN '$startDate' AND '$endDate'");
    $visitorsResult = $conn->query("SELECT COUNT(DISTINCT v.visitorID) as count FROM visitor v JOIN vehicle vh ON v.plateNum = vh.plateNum JOIN entryexitlog e ON vh.plateNum = e.plateNum WHERE e.entryTime BETWEEN '$startDate' AND '$endDate'");
    $whereClause = "WHERE e.entryTime BETWEEN '$startDate' AND '$endDate'";
} else {
    $entriesResult = $conn->query("SELECT COUNT(*) as count FROM entryexitlog WHERE entryTime >= '$startDate'");
    $visitorsResult = $conn->query("SELECT COUNT(DISTINCT v.visitorID) as count FROM visitor v JOIN vehicle vh ON v.plateNum = vh.plateNum JOIN entryexitlog e ON vh.plateNum = e.plateNum WHERE e.entryTime >= '$startDate'");
    $whereClause = "WHERE e.entryTime >= '$startDate'";
}
$totalEntries = $entriesResult ? $entriesResult->fetch_assoc()['count'] : 0;
$totalVisitors = $visitorsResult ? $visitorsResult->fetch_assoc()['count'] : 0;

// Get recent entries for the period
$recentEntriesQuery = "SELECT 
    CASE 
        WHEN vo.fName IS NOT NULL THEN CONCAT(vo.fName, ' ', vo.lName)
        ELSE v.fullName
    END as fullName,
    e.plateNum, e.entryTime, e.gateLocation 
    FROM entryexitlog e 
    LEFT JOIN vehicle vh ON e.plateNum = vh.plateNum
    LEFT JOIN vehicleowner vo ON vh.OwnerID = vo.OwnerID
    LEFT JOIN visitor v ON vh.visitorID = v.visitorID
    $whereClause 
    ORDER BY e.entryTime DESC LIMIT 10";
$recentEntriesResult = $conn->query($recentEntriesQuery);

$recentEntries = '';
if ($recentEntriesResult && $recentEntriesResult->num_rows > 0) {
    while ($row = $recentEntriesResult->fetch_assoc()) {
        $name = $row['fullName'] ?: 'Unknown';
        $plate = $row['plateNum'] ? '(' . $row['plateNum'] . ')' : '(No Vehicle)';
        $time = date('g:i A', strtotime($row['entryTime']));
        $gate = $row['gateLocation'] ?: 'Unknown Gate';
        $recentEntries .= "$name $plate - $gate at $time<br>";
    }
} else {
    $recentEntries = 'No entries found for this period.';
}

// Generate report content
$content = "
<h4>SUMMARY ($periodLabel)</h4>
<p>
    Total Entries: " . number_format($totalEntries) . "<br>
    Unique Visitors: " . number_format($totalVisitors) . "<br>
    Period: $periodLabel<br>
    Report Generated: " . date('n/j/Y g:i:s A') . "
</p>
<h4>RECENT ACTIVITY</h4>
<p>$recentEntries</p>
";

$db->closeConnection();

echo json_encode(['content' => $content]);
?>