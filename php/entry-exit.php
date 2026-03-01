<?php
session_start();

// Check authentication and single session
require_once 'auth_check.php';

$pageTitle = "Entry/Exit Logs";
$currentPage = "entry-exit";
$cssFiles = ["entry-exit.css"];
$jsFiles = ["entry-exit.js"];

require_once 'dbConnection.php';

$db = new Database();
$conn = $db->getConnection();

// Date filter (optional)
$fromDate = isset($_GET['fromDate']) ? $_GET['fromDate'] : '';
$toDate = isset($_GET['toDate']) ? $_GET['toDate'] : '';
$whereEel = '';
$whereVis = '';
$paramsEel = [];
$paramsVis = [];
$types = '';

if ($fromDate !== '' && $toDate !== '') {
    $whereEel = " WHERE DATE(eel.entryTime) BETWEEN ? AND ?";
    $whereVis = " WHERE DATE(entryTime) BETWEEN ? AND ?";
    $paramsEel = [$fromDate, $toDate];
    $paramsVis = [$fromDate, $toDate];
    $types = 'ss';
} elseif ($fromDate !== '') {
    $whereEel = " WHERE DATE(eel.entryTime) >= ?";
    $whereVis = " WHERE DATE(entryTime) >= ?";
    $paramsEel = [$fromDate];
    $paramsVis = [$fromDate];
    $types = 's';
} elseif ($toDate !== '') {
    $whereEel = " WHERE DATE(eel.entryTime) <= ?";
    $whereVis = " WHERE DATE(entryTime) <= ?";
    $paramsEel = [$toDate];
    $paramsVis = [$toDate];
    $types = 's';
}

$allLogs = [];

// 1. Get Registered Vehicle Logs
$queryEel = "SELECT CONCAT(vo.fName, ' ', vo.lName) AS fullName, vo.role, eel.gateLocation, eel.plateNum, eel.entryTime, eel.exitTime, eel.status, 'registered' AS logType 
          FROM entryexitlog eel 
          LEFT JOIN vehicle v ON eel.plateNum = v.plateNum 
          LEFT JOIN vehicleowner vo ON v.OwnerID = vo.OwnerID 
          $whereEel";

if ($paramsEel) {
    $stmt1 = $conn->prepare($queryEel);
    $stmt1->bind_param($types, ...$paramsEel);
    $stmt1->execute();
    $res1 = $stmt1->get_result();
} else {
    $res1 = $conn->query($queryEel);
}

if ($res1 && $res1->num_rows > 0) {
    while($row = $res1->fetch_assoc()) {
        $allLogs[] = $row;
    }
}

// 2. Get Visitor Logs
$queryVis = "SELECT fullName, 'Visitor' AS role, gateLocation, plateNum, entryTime, exitTime, status, 'visitor' AS logType
          FROM visitor 
          $whereVis";
          
if ($paramsVis) {
    $stmt2 = $conn->prepare($queryVis);
    $stmt2->bind_param($types, ...$paramsVis);
    $stmt2->execute();
    $res2 = $stmt2->get_result();
} else {
    $res2 = $conn->query($queryVis);
}

if ($res2 && $res2->num_rows > 0) {
    while($row = $res2->fetch_assoc()) {
        $allLogs[] = $row;
    }
}

// Sort the combined array by entryTime DESC
usort($allLogs, function($a, $b) {
    return strtotime($b['entryTime'] ?? 0) - strtotime($a['entryTime'] ?? 0);
});

include_once '../includes/header.php';
?>

<main class="main">
  <h1>Vehicle Entry/Exit Logs</h1>
  <p>Track and monitor entries and exits</p>

  <div class="top-bar">
    <div class="top-bar-left">
      <div class="search-area">
        <div class="search-icon"></div>
        <div class="search-box">
          <input type="text" id="searchInput" placeholder="Search by name, plate number, or location" />
          <button class="search-btn" type="submit" title="Search">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
              stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="m21 21-4.34-4.34" />
              <circle cx="11" cy="11" r="8" />
            </svg>
          </button>
        </div>
      </div>
      <div class="date-filter-area" style="display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
        <label for="fromDate">From</label>
        <input type="date" id="fromDate" name="fromDate" value="<?php echo htmlspecialchars($fromDate); ?>" class="date-input">
        <label for="toDate">To</label>
        <input type="date" id="toDate" name="toDate" value="<?php echo htmlspecialchars($toDate); ?>" class="date-input">
        <button type="button" id="dateFilterBtn" class="filter-btn">Filter</button>
      </div>
      <div class="traffic-btn-group">
        <div class="traffic-btn-box">
          <div class="traffic-btn-slider"></div>
          <button class="traffic-btn active">All</button>
          <button class="traffic-btn">Registered Vehicle</button>
          <button class="traffic-btn">Visitor</button>
        </div>
      </div>
      
    </div>
  </div>

  <table class="data-table">
    <thead>
      <tr>
        <th>Full Name</th>
        <th>Role</th>
        <th>Gate Location</th>
        <th>Plate Number</th>
        <th>Entry Time</th>
        <th>Exit Time</th>
        <th>Status</th>
      </tr>
    </thead>
    <tbody id="logsTableBody">
      <?php if (!empty($allLogs)): ?>
        <?php foreach ($allLogs as $row): ?>
          <tr data-type="<?php echo $row['logType']; ?>">
            <td><?php echo htmlspecialchars($row['fullName'] ?? ''); ?></td>
            <td>
              <?php if (!empty($row['role']) && $row['role'] !== 'Visitor'): ?>
                <span class="role-badge role-<?php echo strtolower($row['role']); ?>">
                  <?php echo htmlspecialchars(ucfirst($row['role'])); ?>
                </span>
              <?php else: ?>
                <span class="role-badge role-visitor">Visitor</span>
              <?php endif; ?>
            </td>
            <td><?php echo htmlspecialchars($row['gateLocation'] ?? 'N/A'); ?></td>
            <td><?php echo htmlspecialchars($row['plateNum'] ?? ''); ?></td>
            <td><?php echo !empty($row['entryTime']) ? date('Y-m-d h:i A', strtotime($row['entryTime'])) : '-'; ?></td>
            <td><?php echo !empty($row['exitTime']) ? date('Y-m-d h:i A', strtotime($row['exitTime'])) : '-'; ?></td>
            <td><?php echo $row['status'] == 'exited' ? 'OUT' : ($row['status'] == 'entered' ? 'IN' : 'Denied'); ?></td>
          </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <tr>
          <td colspan="7">No entry/exit logs found</td>
        </tr>
      <?php endif; ?>
    </tbody>
  </table>
</main>

<!-- Record Entry Modal -->
<div id="recordEntryModal" class="modal" style="display:none;">
  <div class="modal-content small">
    <span class="close">&times;</span>
    <h3>Record Entry</h3>
    <p>Record a vehicle entry. (Backend integration can be added here.)</p>
    <div class="form-group">
      <label>Plate Number / Identifier:</label>
      <input type="text" id="entryPlateNum" placeholder="Plate number or visitor ID">
    </div>
    <button type="button" id="submitRecordEntry" class="btn-save">Record Entry</button>
  </div>
</div>

<!-- Record Exit Modal -->
<div id="recordExitModal" class="modal" style="display:none;">
  <div class="modal-content small">
    <span class="close">&times;</span>
    <h3>Record Exit</h3>
    <p>Record a vehicle exit. (Backend integration can be added here.)</p>
    <div class="form-group">
      <label>Plate Number / Identifier:</label>
      <input type="text" id="exitPlateNum" placeholder="Plate number or visitor ID">
    </div>
    <button type="button" id="submitRecordExit" class="btn-save">Record Exit</button>
  </div>
</div>

<?php
$db->closeConnection();
include_once '../includes/footer.php';
?>