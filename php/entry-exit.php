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
$whereClause = '';
$params = [];
$types = '';
if ($fromDate !== '' && $toDate !== '') {
    $whereClause = " WHERE DATE(eel.entryTime) BETWEEN ? AND ?";
    $params = [$fromDate, $toDate];
    $types = 'ss';
} elseif ($fromDate !== '') {
    $whereClause = " WHERE DATE(eel.entryTime) >= ?";
    $params = [$fromDate];
    $types = 's';
} elseif ($toDate !== '') {
    $whereClause = " WHERE DATE(eel.entryTime) <= ?";
    $params = [$toDate];
    $types = 's';
}

$query = "SELECT eel.*, v.plateNum, vo.fName, vo.lName, vo.role, v.stickerID
          FROM entryexitlog eel 
          LEFT JOIN vehicle v ON eel.plateNum = v.plateNum 
          LEFT JOIN vehicleowner vo ON v.OwnerID = vo.OwnerID 
          $whereClause
          ORDER BY eel.entryTime DESC";
if ($params) {
    $stmt = $conn->prepare($query);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $conn->query($query);
}
if (!$result)
  $result = (object) ['num_rows' => 0];

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
          <input type="text" id="searchInput" placeholder="Search by name, plate number, or sticker ID" />
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
        <input type="date" id="fromDate" name="fromDate" value="<?php echo htmlspecialchars($fromDate); ?>">
        <label for="toDate">To</label>
        <input type="date" id="toDate" name="toDate" value="<?php echo htmlspecialchars($toDate); ?>">
        <button type="button" id="dateFilterBtn" class="search-btn">Filter</button>
      </div>
      <div class="traffic-btn-group">
        <div class="traffic-btn-box">
          <div class="traffic-btn-slider"></div>
          <button class="traffic-btn active">Registered Vehicle</button>
          <button class="traffic-btn">Visitor</button>
        </div>
      </div>
      <div class="record-actions" style="display:flex; gap:10px;">
        <button type="button" id="recordEntryBtn" class="add-btn">Record Entry</button>
        <button type="button" id="recordExitBtn" class="add-btn">Record Exit</button>
      </div>
    </div>
  </div>

  <table class="data-table">
    <thead>
      <tr>
        <th>Full Name</th>
        <th>Role</th>
        <th>Sticker ID</th>
        <th>Plate Number</th>
        <th>Entry Time</th>
        <th>Exit Time</th>
        <th>Status</th>
      </tr>
    </thead>
    <tbody id="logsTableBody">
      <?php if ($result && $result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
          <tr data-type="<?php echo ($row['stickerID'] && $row['stickerID'] !== 'N/A') ? 'registered' : 'visitor'; ?>">
            <td><?php echo htmlspecialchars(($row['fName'] ?? '') . ' ' . ($row['lName'] ?? '')); ?></td>
            <td>
              <?php if (!empty($row['role'])): ?>
                <span class="role-badge role-<?php echo strtolower($row['role']); ?>">
                  <?php echo htmlspecialchars(ucfirst($row['role'])); ?>
                </span>
              <?php else: ?>
                <span class="role-badge role-visitor">Visitor</span>
              <?php endif; ?>
            </td>
            <td><?php echo htmlspecialchars($row['stickerID'] ?? 'N/A'); ?></td>
            <td><?php echo htmlspecialchars($row['plateNum']); ?></td>
            <td><?php echo $row['entryTime'] ? date('Y-m-d h:i A', strtotime($row['entryTime'])) : '-'; ?></td>
            <td><?php echo $row['exitTime'] ? date('Y-m-d h:i A', strtotime($row['exitTime'])) : '-'; ?></td>
            <td><?php echo $row['status'] == 'exited' ? 'OUT' : ($row['status'] == 'entered' ? 'IN' : 'Denied'); ?></td>
          </tr>
        <?php endwhile; ?>
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