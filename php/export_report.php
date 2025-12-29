<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require_once 'dbConnection.php';

$type = $_GET['type'] ?? 'log';
$period = $_GET['period'] ?? 'day';
$customDate = $_GET['customDate'] ?? '';

$db = new Database();
$conn = $db->getConnection();

if ($type === 'report') {
    // Generate report export
    switch($period) {
        case 'day':
            $startDate = date('Y-m-d H:i:s', strtotime('-24 hours'));
            $filename = 'gate_report_day_' . date('Y-m-d');
            $whereClause = "WHERE e.entryTime >= '$startDate'";
            break;
        case 'week':
            $startDate = date('Y-m-d H:i:s', strtotime('-7 days'));
            $filename = 'gate_report_week_' . date('Y-m-d');
            $whereClause = "WHERE e.entryTime >= '$startDate'";
            break;
        case 'month':
            $startDate = date('Y-m-d H:i:s', strtotime('-30 days'));
            $filename = 'gate_report_month_' . date('Y-m-d');
            $whereClause = "WHERE e.entryTime >= '$startDate'";
            break;
        case 'custom':
            if ($customDate) {
                $startDate = $customDate . ' 00:00:00';
                $endDate = $customDate . ' 23:59:59';
                $filename = 'gate_report_' . $customDate;
                $whereClause = "WHERE e.entryTime BETWEEN '$startDate' AND '$endDate'";
            } else {
                $startDate = date('Y-m-d H:i:s', strtotime('-24 hours'));
                $filename = 'gate_report_date_' . date('Y-m-d');
                $whereClause = "WHERE e.entryTime >= '$startDate'";
            }
            break;
        default:
            $startDate = date('Y-m-d H:i:s', strtotime('-24 hours'));
            $filename = 'gate_report_date_' . date('Y-m-d');
            $whereClause = "WHERE e.entryTime >= '$startDate'";
    }
    
    $query = "SELECT 
        CASE 
            WHEN vo.fName IS NOT NULL THEN CONCAT(vo.fName, ' ', vo.lName)
            ELSE v.fullName
        END as fullName,
        e.plateNum, e.entryTime, e.exitTime, e.gateLocation 
        FROM entryexitlog e 
        LEFT JOIN vehicle vh ON e.plateNum = vh.plateNum
        LEFT JOIN vehicleowner vo ON vh.OwnerID = vo.OwnerID
        LEFT JOIN visitor v ON vh.visitorID = v.visitorID
        $whereClause 
        ORDER BY e.entryTime DESC";
} else {
    // Export full log
    $filename = 'gate_log_full_' . date('Y-m-d');
    $query = "SELECT 
        CASE 
            WHEN vo.fName IS NOT NULL THEN CONCAT(vo.fName, ' ', vo.lName)
            ELSE v.fullName
        END as fullName,
        e.plateNum, e.entryTime, e.exitTime, e.gateLocation 
        FROM entryexitlog e 
        LEFT JOIN vehicle vh ON e.plateNum = vh.plateNum
        LEFT JOIN vehicleowner vo ON vh.OwnerID = vo.OwnerID
        LEFT JOIN visitor v ON vh.visitorID = v.visitorID
        ORDER BY e.entryTime DESC";
}

$result = $conn->query($query);

if (!$result) {
    die('Query failed: ' . $conn->error);
}

// Set headers for CSV download
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="' . $filename . '.csv"');
header('Pragma: no-cache');
header('Expires: 0');

$output = fopen('php://output', 'w');

// CSV headers
fputcsv($output, ['Name', 'Plate Number', 'Entry Time', 'Exit Time', 'Gate Location']);

// CSV data
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        fputcsv($output, [
            $row['fullName'] ?: 'Unknown',
            $row['plateNum'] ?: 'N/A',
            $row['entryTime'] ?: 'N/A',
            $row['exitTime'] ?: 'Still Inside',
            $row['gateLocation'] ?: 'Unknown'
        ]);
    }
} else {
    // Add a row indicating no data found
    fputcsv($output, ['No data found for the selected period', '', '', '', '']);
}

fclose($output);
$db->closeConnection();
?>