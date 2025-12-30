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
    switch ($period) {
        case 'day':
            $startDate = date('Y-m-d H:i:s', strtotime('-24 hours'));
            $filename = 'gate_report_day_' . date('Y-m-d');
            $periodLabel = 'Last 24 Hours';
            break;
        case 'week':
            $startDate = date('Y-m-d H:i:s', strtotime('-7 days'));
            $filename = 'gate_report_week_' . date('Y-m-d');
            $periodLabel = 'Last 7 Days';
            break;
        case 'month':
            $startDate = date('Y-m-d H:i:s', strtotime('-30 days'));
            $filename = 'gate_report_month_' . date('Y-m-d');
            $periodLabel = 'Last 30 Days';
            break;
        case 'custom':
            if ($customDate) {
                $startDate = $customDate . ' 00:00:00';
                $endDate = $customDate . ' 23:59:59';
                $filename = 'gate_report_' . $customDate;
                $periodLabel = 'Date: ' . date('M j, Y', strtotime($customDate));
            } else {
                $startDate = date('Y-m-d H:i:s', strtotime('-24 hours'));
                $filename = 'gate_report_date_' . date('Y-m-d');
                $periodLabel = 'Last 24 Hours';
                $endDate = null;
            }
            break;
        default:
            $startDate = date('Y-m-d H:i:s', strtotime('-24 hours'));
            $filename = 'gate_report_date_' . date('Y-m-d');
            $periodLabel = 'Last 24 Hours';
    }

    // Set headers for Excel download
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment; filename="' . $filename . '.xls"');
    header('Pragma: no-cache');
    header('Expires: 0');

    // Get statistics
    if (isset($endDate)) {
        $entriesResult = $conn->query("SELECT COUNT(*) as count FROM entryexitlog WHERE entryTime BETWEEN '$startDate' AND '$endDate'");
        $exitsResult = $conn->query("SELECT COUNT(*) as count FROM entryexitlog WHERE exitTime BETWEEN '$startDate' AND '$endDate'");
        $uniqueVehiclesResult = $conn->query("SELECT COUNT(DISTINCT plateNum) as count FROM entryexitlog WHERE entryTime BETWEEN '$startDate' AND '$endDate'");

        $vehicleTypeQuery = "SELECT v.vehicleType, COUNT(*) as count 
                             FROM entryexitlog e 
                             JOIN vehicle v ON e.plateNum = v.plateNum 
                             WHERE e.entryTime BETWEEN '$startDate' AND '$endDate' 
                             GROUP BY v.vehicleType";

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

        $vehicleTypeQuery = "SELECT v.vehicleType, COUNT(*) as count 
                             FROM entryexitlog e 
                             JOIN vehicle v ON e.plateNum = v.plateNum 
                             WHERE e.entryTime >= '$startDate' 
                             GROUP BY v.vehicleType";

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

    echo '<html>';
    echo '<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head>';
    echo '<body>';

    echo '<h2 style="font-family: Arial, sans-serif;">GATE ACCESS SUMMARY REPORT</h2>';
    echo '<p style="font-family: Arial, sans-serif;"><strong>Generated on:</strong> ' . date('M j, Y g:i A') . '<br>';
    echo '<strong>Period:</strong> ' . $periodLabel . '</p>';

    echo '<br>';

    // Summary Table
    echo '<h3 style="font-family: Arial, sans-serif; background-color: #444; color: white; padding: 5px;">SUMMARY STATISTICS</h3>';
    echo '<table border="1" cellpadding="5" cellspacing="0" style="border-collapse: collapse; font-family: Arial, sans-serif; width: 100%;">';
    echo '<tr style="background-color: #f2f2f2;"><th>Metric</th><th>Count</th></tr>';
    echo '<tr><td>Total Entries</td><td>' . number_format($totalEntries) . '</td></tr>';
    echo '<tr><td>Total Exits</td><td>' . number_format($totalExits) . '</td></tr>';
    echo '<tr><td>Unique Vehicles</td><td>' . number_format($totalUniqueVehicles) . '</td></tr>';
    echo '</table>';

    echo '<br>';

    // Breakdown tables logic similar to other files
    echo '<h3 style="font-family: Arial, sans-serif; background-color: #444; color: white; padding: 5px;">ENTRIES BY VEHICLE TYPE</h3>';
    echo '<table border="1" cellpadding="5" cellspacing="0" style="border-collapse: collapse; font-family: Arial, sans-serif; width: 100%;">';
    echo '<tr style="background-color: #f2f2f2;"><th>Vehicle Type</th><th>Count</th></tr>';
    if ($vehicleTypeResult && $vehicleTypeResult->num_rows > 0) {
        while ($row = $vehicleTypeResult->fetch_assoc()) {
            echo '<tr><td>' . htmlspecialchars($row['vehicleType'] ?: 'Unknown') . '</td><td>' . number_format($row['count']) . '</td></tr>';
        }
    } else {
        echo '<tr><td colspan="2">No data available</td></tr>';
    }
    echo '</table>';

    echo '<br>';

    echo '<h3 style="font-family: Arial, sans-serif; background-color: #444; color: white; padding: 5px;">ENTRIES BY USER ROLE</h3>';
    echo '<table border="1" cellpadding="5" cellspacing="0" style="border-collapse: collapse; font-family: Arial, sans-serif; width: 100%;">';
    echo '<tr style="background-color: #f2f2f2;"><th>Role</th><th>Count</th></tr>';
    if ($roleResult && $roleResult->num_rows > 0) {
        while ($row = $roleResult->fetch_assoc()) {
            echo '<tr><td>' . htmlspecialchars(ucfirst(strtolower($row['userRole']))) . '</td><td>' . number_format($row['count']) . '</td></tr>';
        }
    } else {
        echo '<tr><td colspan="2">No data available</td></tr>';
    }
    echo '</table>';

    echo '</body></html>';

} else {
    // Export full log (unchanged, but switched to Excel format for consistency if desired, or kept CSV)
    // The user specifically asked for "the report" to be converted.

    // Let's keep the full log as CSV for now as it might be large, unless requested otherwise. 
    // Actually, "make sure it is also the correct format" implies the User wants this specific new format.
    // The full log is a different "type". I will leave type=log as CSV for data analysis, 
    // but the request specifically targeted "generating reports part" (type=report).

    $filename = 'gate_log_full_' . date('Y-m-d');
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $filename . '.csv"');
    header('Pragma: no-cache');
    header('Expires: 0');

    $output = fopen('php://output', 'w');
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

    $result = $conn->query($query);
    if (!$result)
        die('Query failed: ' . $conn->error);

    fputcsv($output, ['Name', 'Plate Number', 'Entry Time', 'Exit Time', 'Gate Location']);
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
    }
    fclose($output);
}
$db->closeConnection();
?>