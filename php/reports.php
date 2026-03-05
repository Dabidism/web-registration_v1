<?php
session_start();

// Check authentication and single session
require_once 'auth_check.php';

$pageTitle = "Reports";
$currentPage = "reports";
$cssFiles = ["reports.css"];
$externalJs = ["https://cdn.jsdelivr.net/npm/chart.js"];
$jsFiles = ["reports.js"];

require_once 'dbConnection.php';

$db = new Database();
$conn = $db->getConnection();

// Get statistics
$totalEntriesLog = $conn->query("SELECT COUNT(*) as count FROM entryexitlog WHERE MONTH(entryTime) = MONTH(CURRENT_DATE()) AND YEAR(entryTime) = YEAR(CURRENT_DATE())")->fetch_assoc()['count'] ?? 0;
$totalEntriesVis = $conn->query("SELECT COUNT(*) as count FROM visitor WHERE MONTH(entryTime) = MONTH(CURRENT_DATE()) AND YEAR(entryTime) = YEAR(CURRENT_DATE())")->fetch_assoc()['count'] ?? 0;
$totalEntries = $totalEntriesLog + $totalEntriesVis;

$totalVisitors = $conn->query("SELECT COUNT(DISTINCT visitorID) as count FROM visitor WHERE MONTH(entryTime) = MONTH(CURRENT_DATE()) AND YEAR(entryTime) = YEAR(CURRENT_DATE())")->fetch_assoc()['count'] ?? 0;
$totalVehicles = $conn->query("SELECT COUNT(*) as count FROM vehicle")->fetch_assoc()['count'] ?? 0;
$totalApplications = $conn->query("SELECT COUNT(DISTINCT OwnerID) as count FROM applications WHERE registrationStatus = 'pending'")->fetch_assoc()['count'] ?? 0;

// Get traffic data for different periods
$dayTraffic = [];
$today = date('Y-m-d');
for ($hour = 6; $hour <= 20; $hour++) {
  $hourFormatted = sprintf('%02d:00:00', $hour);
  $fullHour = $today . ' ' . $hourFormatted;
  $hourLabel = sprintf('%02d:00', $hour);
  $entriesResult = $conn->query("SELECT COUNT(*) as count FROM entryexitlog WHERE DATE_FORMAT(entryTime, '%Y-%m-%d %H:00:00') = '$fullHour'");
  $entries = $entriesResult ? $entriesResult->fetch_assoc()['count'] : 0;
  $dayTraffic[] = ['label' => $hourLabel, 'entries' => $entries];
}

$weekTraffic = [];
for ($i = 6; $i >= 0; $i--) {
  $date = date('Y-m-d', strtotime("-$i days"));
  $dayLabel = date('D', strtotime("-$i days"));
  $entriesResult = $conn->query("SELECT COUNT(*) as count FROM entryexitlog WHERE DATE(entryTime) = '$date'");
  $entries = $entriesResult ? $entriesResult->fetch_assoc()['count'] : 0;
  $weekTraffic[] = ['label' => $dayLabel, 'entries' => $entries];
}

$monthTraffic = [];
for ($i = 29; $i >= 0; $i--) {
  $date = date('Y-m-d', strtotime("-$i days"));
  $dayLabel = date('M j', strtotime("-$i days"));
  $entriesResult = $conn->query("SELECT COUNT(*) as count FROM entryexitlog WHERE DATE(entryTime) = '$date'");
  $entries = $entriesResult ? $entriesResult->fetch_assoc()['count'] : 0;
  $monthTraffic[] = ['label' => $dayLabel, 'entries' => $entries];
}

include_once '../includes/header.php';
?>

<main class="main">
  <h2>Reports</h2>
  <div class="right-actions">
    <button class="generateBtn" id="generateBtn" type="button">
      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
        class="lucide lucide-calendar-icon lucide-calendar">
        <path d="M8 2v4" />
        <path d="M16 2v4" />
        <rect width="18" height="18" x="3" y="4" rx="2" />
        <path d="M3 10h18" />
      </svg>
      Generate Report
    </button>

  </div>

  <div class="stats">
    <div class="card-flex">
      <div>
        <h3>Total Entries</h3>
        <p>This month</p>
        <br />
        <strong class="card-num"><?php echo number_format($totalEntries); ?></strong>
      </div>
    </div>
    <div class="card-flex">
      <div>
        <h3>Total Visitors</h3>
        <p>This month</p>
        <br />
        <strong class="card-num"><?php echo number_format($totalVisitors); ?></strong>
      </div>
    </div>
    <div class="card-flex">
      <div>
        <h3>Total Vehicles</h3>
        <p>Registered</p>
        <br />
        <strong class="card-num"><?php echo number_format($totalVehicles); ?></strong>
      </div>
    </div>
    <div class="card-flex">
      <div>
        <h3>Pending Applications</h3>
        <p>Awaiting Approval</p>
        <br />
        <strong class="card-num"><?php echo number_format($totalApplications); ?></strong>
      </div>
    </div>
  </div>
  <div class="grid">
    <!-- Violation Search Section -->
    <div class="card" id="violation-search">
      <h3>Search Violations</h3>
      <div class="search-container-reports">
        <input type="text" id="violationSearchInput" class="search-input"
          placeholder="Enter Plate Number, Owner Name, or Vehicle Info (Model/Type)">
        <button id="searchViolationBtn" class="add-btn">Search</button>
      </div>
      <div id="violationResults" class="results-table-container hidden">
        <table class="data-table">
          <thead>
            <tr>
              <th>Plate Number</th>
              <th>Owner</th>
              <th>Contact Details</th>
              <th>Vehicle</th>
              <th>Violation</th>
              <th>Status</th>
              <th>Date</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody id="violationResultsBody">
          </tbody>
        </table>
      </div>
      <div id="noResultsMsg" class="no-data hidden">No violations found matching your criteria.</div>
    </div>

    <div class="card" id="gate-traffic">
      <div class="flex-space-between-center">
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

      <div class="chart-container">
        <canvas id="trafficChart"></canvas>
      </div>
    </div>
  </div>
  <div class="overlay" id="overlay"></div>

  <!-- Period Selection Modal -->
  <div class="pop-up hidden" id="periodModal">
    <span class="close-btn" id="closePeriodModal">&times;</span>
    <h4>Generate Report</h4>
    <div class="period-selection">
      <label>Select Time Period:</label>
      <select id="reportPeriod" required>
        <option value="">-- Select Report Period --</option>
        <option value="day">Last 24 Hours</option>
        <option value="week">Last 7 Days</option>
        <option value="month">Last 30 Days</option>
        <option value="custom">Custom Date</option>
      </select>
      <div id="customDateContainer" class="custom-date-container">
        <input type="date" id="customDate" class="custom-date-input">
      </div>
      <button id="generateReportBtn" class="btn-generate">Generate</button>
    </div>
  </div>

  <!-- Report Preview Modal -->
  <div class="pop-up hidden" id="reportPopup">
    <span class="close-btn" id="closePopup">&times;</span>
    <h4>Report Preview</h4>
    <h3>GATE ACCESS SYSTEM - COMPREHENSIVE REPORT</h3>
    <p class="generated-time font-size-12">
      Generated on: <span id="reportDate"></span>
    </p>

    <div class="report-content" id="reportContent">
      <!-- Dynamic content will be loaded here -->
    </div>

    <div class="btn-container">
      <button class="btn-back" id="backToSelectionBtn">Back</button>
      <button class="btn-download" id="downloadBtn">Download</button>
    </div>
  </div>
</main>

<script>
  // Pass PHP data to JavaScript
  window.reportsData = {
    dayTraffic: <?php echo json_encode($dayTraffic); ?>,
    weekTraffic: <?php echo json_encode($weekTraffic); ?>,
    monthTraffic: <?php echo json_encode($monthTraffic); ?>
  };
</script>

<?php
$db->closeConnection();
include_once '../includes/footer.php';
?>