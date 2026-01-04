<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit;
}

// Set page title and current page for navigation highlighting
$pageTitle = "Dashboard";
$currentPage = "dashboard";
$cssFiles = ["dashboard.css"];
$jsFiles = ["dashboard.js"];
$externalJs = ["https://cdn.jsdelivr.net/npm/chart.js"];

// Include database connection
require_once 'dbConnection.php';

// Create database instance
$db = new Database();
$conn = $db->getConnection();

// Get dashboard statistics
$stats = [
  'total_vehicles' => 0,
  'active_vehicles' => 0,
  'pending_vehicles' => 0,
  'total_users' => 0
];

// Get total vehicles
$result = $conn->query("SELECT COUNT(*) as count FROM vehicle");
if ($result) {
  $stats['total_vehicles'] = $result->fetch_assoc()['count'];
}

// Get parking allocation data
$parkingResult = $conn->query("SELECT * FROM parkingstatus ORDER BY id ASC LIMIT 1");
$parkingData = $parkingResult ? $parkingResult->fetch_assoc() : null;

// Get campus occupancy by role from historical_log and vehicle owner data
$occupancyByRole = [
  'students' => 0,
  'faculty' => 0,
  'staff' => 0,
  'guests' => 0
];

// Get current occupancy by role
$result = $conn->query("
    SELECT 
        CASE 
            WHEN vo.role = 'student' THEN 'students'
            WHEN vo.role = 'faculty' THEN 'faculty'
            WHEN vo.role IN ('non-teaching', 'staff') THEN 'staff'
            WHEN v.visitorID IS NOT NULL THEN 'guests'
            ELSE 'guests'
        END as role_category,
        COUNT(*) as count
    FROM historical_log h
    JOIN vehicle v ON h.plateNum = v.plateNum
    LEFT JOIN vehicleowner vo ON v.OwnerID = vo.OwnerID
    WHERE h.status = 'entered' AND h.exitTime IS NULL
    GROUP BY role_category
");

if ($result) {
  while ($row = $result->fetch_assoc()) {
    $occupancyByRole[$row['role_category']] = $row['count'];
  }
}

// Update parking status with current occupancy
if ($parkingData) {
  $configId = $parkingData['id'];
  $conn->query("UPDATE parkingstatus SET 
        currentOccupiedStudents = {$occupancyByRole['students']},
        currentOccupiedFaculty = {$occupancyByRole['faculty']},
        currentOccupiedStaff = {$occupancyByRole['staff']},
        currentOccupiedGuests = {$occupancyByRole['guests']}
        WHERE id = $configId");
}

$stats['active_vehicles'] = array_sum($occupancyByRole);
$stats['total_capacity'] = $parkingData ? $parkingData['totalCapacity'] : 300;
$stats['occupancy_by_role'] = $occupancyByRole;

// Get pending vehicles (vehicles without RFID tags)
$result = $conn->query("SELECT COUNT(*) as count FROM vehicle WHERE stickerID IS NULL");
if ($result) {
  $stats['pending_vehicles'] = $result->fetch_assoc()['count'];
}

// Get total users
$result = $conn->query("SELECT COUNT(*) as count FROM vehicleowner");
if ($result) {
  $stats['total_users'] = $result->fetch_assoc()['count'];
}

// Get recent access logs (last 5) with usernames
$recentLogs = [];
$result = $conn->query("SELECT u.username, a.action, a.description, a.timestamp FROM accesslog a LEFT JOIN user u ON a.userID = u.userID ORDER BY a.timestamp DESC LIMIT 5");
if ($result) {
  while ($row = $result->fetch_assoc()) {
    $recentLogs[] = $row;
  }
}

// Get vehicle types data from database
$vehicleTypes = [];
$result = $conn->query("SELECT vehicleType, COUNT(*) as count FROM vehicle WHERE vehicleType IS NOT NULL AND vehicleType != '' GROUP BY vehicleType");
if ($result) {
  while ($row = $result->fetch_assoc()) {
    $vehicleTypes[] = $row;
  }
}

// Get today's entries count from historical_log
$today = date('Y-m-d');
$result = $conn->query("SELECT COUNT(*) as count FROM historical_log WHERE DATE(entryTime) = '$today' AND status = 'entered'");
if ($result) {
  $stats['todays_entries'] = $result->fetch_assoc()['count'];
} else {
  $stats['todays_entries'] = 0;
}

// Get daily entries for the past week from historical_log
$dailyEntries = [];
for ($i = 6; $i >= 0; $i--) {
  $date = date('Y-m-d', strtotime("-$i days"));
  $result = $conn->query("SELECT COUNT(*) as count FROM historical_log WHERE DATE(entryTime) = '$date' AND status = 'entered'");
  $count = $result ? $result->fetch_assoc()['count'] : 0;
  $dailyEntries[] = ['date' => $date, 'count' => $count];
}

// Get traffic data for different periods (registered vehicles only)
$dayTraffic = [];
$today = date('Y-m-d');
for ($hour = 6; $hour <= 20; $hour++) {
  $hourFormatted = sprintf('%02d:00:00', $hour);
  $fullHour = $today . ' ' . $hourFormatted;
  $hourLabel = sprintf('%02d:00', $hour);
  $entriesResult = $conn->query("SELECT COUNT(*) as count FROM historical_log h JOIN vehicle v ON h.plateNum = v.plateNum WHERE DATE_FORMAT(h.entryTime, '%Y-%m-%d %H:00:00') = '$fullHour' AND h.status = 'entered' AND v.OwnerID IS NOT NULL");
  $entries = $entriesResult ? $entriesResult->fetch_assoc()['count'] : 0;
  $dayTraffic[] = ['label' => $hourLabel, 'entries' => $entries];
}

$weekTraffic = [];
for ($i = 6; $i >= 0; $i--) {
  $date = date('Y-m-d', strtotime("-$i days"));
  $dayLabel = date('D', strtotime("-$i days"));
  $entriesResult = $conn->query("SELECT COUNT(*) as count FROM historical_log h JOIN vehicle v ON h.plateNum = v.plateNum WHERE DATE(h.entryTime) = '$date' AND h.status = 'entered' AND v.OwnerID IS NOT NULL");
  $entries = $entriesResult ? $entriesResult->fetch_assoc()['count'] : 0;
  $weekTraffic[] = ['label' => $dayLabel, 'entries' => $entries];
}

$monthTraffic = [];
for ($i = 29; $i >= 0; $i--) {
  $date = date('Y-m-d', strtotime("-$i days"));
  $dayLabel = date('M j', strtotime("-$i days"));
  $entriesResult = $conn->query("SELECT COUNT(*) as count FROM historical_log h JOIN vehicle v ON h.plateNum = v.plateNum WHERE DATE(h.entryTime) = '$date' AND h.status = 'entered' AND v.OwnerID IS NOT NULL");
  $entries = $entriesResult ? $entriesResult->fetch_assoc()['count'] : 0;
  $monthTraffic[] = ['label' => $dayLabel, 'entries' => $entries];
}

// Get visitor traffic data for different periods (visitor vehicles only)
$dayVisitorTraffic = [];
$today = date('Y-m-d');
for ($hour = 6; $hour <= 20; $hour++) {
  $hourFormatted = sprintf('%02d:00:00', $hour);
  $fullHour = $today . ' ' . $hourFormatted;
  $hourLabel = sprintf('%02d:00', $hour);
  $entriesResult = $conn->query("SELECT COUNT(*) as count FROM historical_log h JOIN vehicle v ON h.plateNum = v.plateNum WHERE DATE_FORMAT(h.entryTime, '%Y-%m-%d %H:00:00') = '$fullHour' AND h.status = 'entered' AND v.visitorID IS NOT NULL");
  $entries = $entriesResult ? $entriesResult->fetch_assoc()['count'] : 0;
  $dayVisitorTraffic[] = ['label' => $hourLabel, 'entries' => $entries];
}

$weekVisitorTraffic = [];
for ($i = 6; $i >= 0; $i--) {
  $date = date('Y-m-d', strtotime("-$i days"));
  $dayLabel = date('D', strtotime("-$i days"));
  $entriesResult = $conn->query("SELECT COUNT(*) as count FROM historical_log h JOIN vehicle v ON h.plateNum = v.plateNum WHERE DATE(h.entryTime) = '$date' AND h.status = 'entered' AND v.visitorID IS NOT NULL");
  $entries = $entriesResult ? $entriesResult->fetch_assoc()['count'] : 0;
  $weekVisitorTraffic[] = ['label' => $dayLabel, 'entries' => $entries];
}

$monthVisitorTraffic = [];
for ($i = 29; $i >= 0; $i--) {
  $date = date('Y-m-d', strtotime("-$i days"));
  $dayLabel = date('M j', strtotime("-$i days"));
  $entriesResult = $conn->query("SELECT COUNT(*) as count FROM historical_log h JOIN vehicle v ON h.plateNum = v.plateNum WHERE DATE(h.entryTime) = '$date' AND h.status = 'entered' AND v.visitorID IS NOT NULL");
  $entries = $entriesResult ? $entriesResult->fetch_assoc()['count'] : 0;
  $monthVisitorTraffic[] = ['label' => $dayLabel, 'entries' => $entries];
}

// Include header
include_once '../includes/header.php';
?>

<main class="main">
  <h2>Dashboard</h2>

  <div class="stats">
    <div class="card-flex">
      <div class="w-full">
        Campus Occupancy <br />
        <strong class="card-num-occ"><?php echo $stats['active_vehicles']; ?> /
          <?php echo $stats['total_capacity']; ?></strong>
        <div class="occupancy-progress-bar">
          <div class="occupancy-progress"
            style="--occupancy: <?php echo ($stats['active_vehicles'] / $stats['total_capacity']) * 100; ?>%"></div>
        </div>
        <div
          class="occupancy-status <?php echo ($stats['active_vehicles'] / $stats['total_capacity']) > 0.9 ? 'full' : 'available'; ?>">
          <?php echo ($stats['active_vehicles'] / $stats['total_capacity']) > 0.9 ? 'Nearly Full' : 'Parking Available'; ?>
        </div>
      </div>
      <span class="card-svg">
        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none"
          stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
          class="lucide lucide-car-front-icon lucide-car-front">
          <path d="m21 8-2 2-1.5-3.7A2 2 0 0 0 15.646 5H8.4a2 2 0 0 0-1.903 1.257L5 10 3 8" />
          <path d="M7 14h.01" />
          <path d="M17 14h.01" />
          <rect width="18" height="8" x="3" y="10" rx="2" />
          <path d="M5 18v2" />
          <path d="M19 18v2" />
        </svg>
      </span>
    </div>
    <div class="card-flex">
      <div>
        Today's Entries <br />
        <strong class="card-num"><?php echo $stats['todays_entries']; ?></strong>
      </div>
      <span class="card-svg">
        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none"
          stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
          class="lucide lucide-car-icon lucide-car">
          <path
            d="M19 17h2c.6 0 1-.4 1-1v-3c0-.9-.7-1.7-1.5-1.9C18.7 10.6 16 10 16 10s-1.3-1.4-2.2-2.3c-.5-.4-1.1-.7-1.8-.7H5c-.6 0-1.1.4-1.4.9l-1.4 2.9A3.7 3.7 0 0 0 2 12v4c0 .6.4 1 1 1h2" />
          <circle cx="7" cy="17" r="2" />
          <path d="M9 17h6" />
          <circle cx="17" cy="17" r="2" />
        </svg>
      </span>
    </div>
    <div class="card-flex">
      <div>
        Registered Vehicles <br />
        <strong class="card-num"><?php echo $stats['total_vehicles']; ?></strong>
      </div>
      <span class="card-svg" id="car-icon">
        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none"
          stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
          class="lucide lucide-car-icon lucide-car">
          <path
            d="M19 17h2c.6 0 1-.4 1-1v-3c0-.9-.7-1.7-1.5-1.9C18.7 10.6 16 10 16 10s-1.3-1.4-2.2-2.3c-.5-.4-1.1-.7-1.8-.7H5c-.6 0-1.1.4-1.4.9l-1.4 2.9A3.7 3.7 0 0 0 2 12v4c0 .6.4 1 1 1h2" />
          <circle cx="7" cy="17" r="2" />
          <path d="M9 17h6" />
          <circle cx="17" cy="17" r="2" />
        </svg>
      </span>
    </div>
    <div class="card-flex">
      <div>
        Owners <br />
        <strong class="card-num"><?php echo $stats['total_users']; ?></strong>
      </div>
      <span class="card-svg">
        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none"
          stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
          class="lucide lucide-users-icon lucide-users">
          <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
          <path d="M16 3.128a4 4 0 0 1 0 7.744" />
          <path d="M22 21v-2a4 4 0 0 0-3-3.87" />
          <circle cx="9" cy="7" r="4" />
        </svg>
      </span>
    </div>
  </div>
  <div class="grid">
    <div class="grid-top">
      <div class="card" id="gate-traffic">
        <div class="card-header-row">
          <strong>Gate Traffic Analysis</strong>
          <div class="traffic-btn-group">
            <div class="traffic-btn-box">
              <div class="traffic-btn-slider"></div>
              <button class="traffic-btn active">Day</button>
              <button class="traffic-btn">Week</button>
              <button class="traffic-btn">Month</button>
            </div>
          </div>
        </div>
        <p>Entry and exit patterns over time</p>
        <div class="chart-container-lg">
          <canvas id="trafficChart"></canvas>
        </div>
      </div>
      <div class="card" id="recent-activity">
        <strong>Recent Activity</strong>
        <div class="activity-list">
          <?php foreach ($recentLogs as $log): ?>
            <div class="activity-item">
              <div class="activity-info">
                <span class="activity-user"><?php echo htmlspecialchars($log['username'] ?? 'Unknown'); ?></span>
                <span class="activity-action"><?php echo htmlspecialchars($log['action']); ?></span>
              </div>
              <div class="activity-time"><?php echo date('M j, g:i A', strtotime($log['timestamp'])); ?></div>
            </div>
          <?php endforeach; ?>
        </div>
        <a class="view-btn" href="access_logs.php">View All Logs</a>
      </div>
    </div>
    <div class="grid-bottom">
      <div class="card" id="vehicle-types">
        <strong>Vehicle Types</strong>
        <p>Distribution of registered vehicles</p>
        <div class="chart-container-xl">
          <canvas id="vehicleTypesChart"></canvas>
        </div>
      </div>
      <div class="card" id="daily-visitor-trend">
        <div class="card-header-row">
          <strong>Daily Visitor Trend</strong>
          <div class="traffic-btn-group">
            <div class="traffic-btn-box">
              <div class="traffic-btn-slider"></div>
              <button class="traffic-btn active" onclick="updateVisitorChart('day')">Day</button>
              <button class="traffic-btn" onclick="updateVisitorChart('week')">Week</button>
              <button class="traffic-btn" onclick="updateVisitorChart('month')">Month</button>
            </div>
          </div>
        </div>
        <p>Visitor traffic over time</p>
        <div class="chart-container-lg">
          <canvas id="visitorTrendChart"></canvas>
        </div>
      </div>
    </div>
  </div>
</main>

<script>
  // Pass PHP data to JavaScript
  window.dashboardData = {
    vehicleTypes: <?php echo json_encode($vehicleTypes); ?>,
    dailyEntries: <?php echo json_encode($dailyEntries); ?>,
    dayTraffic: <?php echo json_encode($dayTraffic); ?>,
    weekTraffic: <?php echo json_encode($weekTraffic); ?>,
    monthTraffic: <?php echo json_encode($monthTraffic); ?>,
    dayVisitorTraffic: <?php echo json_encode($dayVisitorTraffic); ?>,
    weekVisitorTraffic: <?php echo json_encode($weekVisitorTraffic); ?>,
    monthVisitorTraffic: <?php echo json_encode($monthVisitorTraffic); ?>,
    stats: <?php echo json_encode($stats); ?>
  };
  console.log('Dashboard data:', window.dashboardData);
</script>

<?php
// Close database connection
$db->closeConnection();

// Include footer
include_once '../includes/footer.php';
?>