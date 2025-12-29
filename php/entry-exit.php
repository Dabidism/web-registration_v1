<?php
session_start();

if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit;
}

$pageTitle = "Entry/Exit Logs";
$currentPage = "entry-exit";
$cssFiles = ["entry-exit.css"];
$jsFiles = ["entry-exit.js"];

require_once 'dbConnection.php';

$db = new Database();
$conn = $db->getConnection();

// Get entry/exit logs
$query = "SELECT eel.*, v.plateNum, vo.fName, vo.lName, vo.role, v.stickerID
          FROM entryexitlog eel 
          LEFT JOIN vehicle v ON eel.plateNum = v.plateNum 
          LEFT JOIN vehicleowner vo ON v.OwnerID = vo.OwnerID 
          ORDER BY eel.entryTime DESC";
$result = $conn->query($query);
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
      <div class="traffic-btn-group">
        <div class="traffic-btn-box">
          <div class="traffic-btn-slider"></div>
          <button class="traffic-btn active">Registered Vehicle</button>
          <button class="traffic-btn">Visitor</button>
        </div>
      </div>
    </div>
  </div>

  <table>
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
            <td><?php echo htmlspecialchars($row['role'] ?? 'N/A'); ?></td>
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

<?php
$db->closeConnection();
include_once '../includes/footer.php';
?>