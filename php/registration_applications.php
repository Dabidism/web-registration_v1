<?php
// Check authentication and single session
require_once 'auth_check.php';
// Set timezone
date_default_timezone_set('Asia/Manila');

// Get statistics
$pendingCount = 0;
// ... (omitted statistics logic if no change needed, but I must preserve file structure)
// Wait, I am replacing the top part? 
// The file starts with auth_check. 
// Set page title and current page for navigation highlighting
$pageTitle = "Registration Applications";
$currentPage = "registration_applications";
$cssFiles = ["registration_applications.css"];
$jsFiles = ["registration_applications.js"];

// Include database connection
require_once 'dbConnection.php';

// Create database instance
$db = new Database();
$conn = $db->getConnection();

// Get statistics
$pendingCount = 0;
$approvedCount = 0;
$rejectedCount = 0;
$totalVehicles = 0;

// Count pending applications
$query = "SELECT COUNT(*) as count FROM applications WHERE registrationStatus = 'pending'";
$result = $conn->query($query);
if ($result && $row = $result->fetch_assoc()) {
  $pendingCount = $row['count'];
}

// Count approved applications
$query = "SELECT COUNT(*) as count FROM applications WHERE registrationStatus = 'approved'";
$result = $conn->query($query);
if ($result && $row = $result->fetch_assoc()) {
  $approvedCount = $row['count'];
}

// Count rejected applications
$query = "SELECT COUNT(*) as count FROM applications WHERE registrationStatus = 'rejected'";
$result = $conn->query($query);
if ($result && $row = $result->fetch_assoc()) {
  $rejectedCount = $row['count'];
}

// Count total registered vehicles
$query = "SELECT COUNT(*) as count FROM vehicle";
$result = $conn->query($query);
if ($result && $row = $result->fetch_assoc()) {
  $totalVehicles = $row['count'];
}

// Get applications based on filter
$status = isset($_GET['status']) ? $_GET['status'] : 'all';
$search = isset($_GET['search']) ? $_GET['search'] : '';

$whereConditions = [];
if ($status != 'all') {
  $whereConditions[] = "registrationStatus = '$status'";
}
if (!empty($search)) {
  $whereConditions[] = "(applicationID LIKE '%$search%' OR OwnerID LIKE '%$search%')";
}
$whereClause = !empty($whereConditions) ? "WHERE " . implode(" AND ", $whereConditions) : "";

// Get unique applications (group by user info to avoid duplicates for multiple vehicles)
$query = "SELECT *, MIN(applicationID) as firstAppId, 
         CASE 
           WHEN registrationStatus = 'approved' THEN 
             (SELECT approvalTimestamp FROM vehicleowner WHERE fName = applications.fName AND lName = applications.lName AND email = applications.email LIMIT 1)
           WHEN registrationStatus = 'rejected' THEN 
             (SELECT MAX(applicationDate) FROM applications a2 WHERE a2.fName = applications.fName AND a2.lName = applications.lName AND a2.email = applications.email AND a2.registrationStatus = 'rejected')
           ELSE NULL
         END as statusTimestamp
         FROM applications $whereClause GROUP BY fName, lName, email ORDER BY applicationDate DESC";
$applications = $conn->query($query);

// Include header
include_once '../includes/header.php';
?>

<main class="main">
  <h2>Manage Vehicle Registration Applications</h2>

  <div class="stats">
    <div class="card-flex">
      <div>
        <h3>Pending Applications</h3>
        <p>Awaiting Review</p>
        <br />
        <strong class="card-num"><?php echo $pendingCount; ?></strong>
      </div>
    </div>
    <div class="card-flex">
      <div>
        <h3>Approved Applications</h3>
        <p>Document review approved</p>
        <br />
        <strong class="card-num"><?php echo $approvedCount; ?></strong>
      </div>
    </div>
    <div class="card-flex">
      <div>
        <h3>Declined Applications</h3>
        <p>Declined Request</p>
        <br />
        <strong class="card-num"><?php echo $rejectedCount; ?></strong>
      </div>
    </div>
    <div class="card-flex">
      <div>
        <h3>Total Registered Vehicles</h3>
        <p>Vechicles that can enter</p>
        <br />
        <strong class="card-num"><?php echo $totalVehicles; ?></strong>
      </div>
    </div>
  </div>
  <div class="grid">
    <div class="search-area">
      <div class="search-icon"></div>
      <div class="search-box">
        <input type="text" id="searchInput" placeholder="Search by unique code or application ID..."
          value="<?php echo htmlspecialchars($search); ?>" />
        <button class="search-btn" type="submit" title="Search">
          <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
            class="lucide lucide-search-icon lucide-search">
            <path d="m21 21-4.34-4.34" />
            <circle cx="11" cy="11" r="8" />
          </svg>
        </button>
        <select class="status-dropdown" id="statusFilter">
          <option value="all" <?php echo ($status == 'all') ? 'selected' : ''; ?>>All statuses</option>
          <option value="pending" <?php echo ($status == 'pending') ? 'selected' : ''; ?>>Pending</option>
          <option value="approved" <?php echo ($status == 'approved') ? 'selected' : ''; ?>>Approved</option>
          <option value="rejected" <?php echo ($status == 'rejected') ? 'selected' : ''; ?>>Rejected</option>
        </select>
        <select class="role-dropdown" id="roleFilter">
          <option value="all">All Roles</option>
          <option value="student">Student</option>
          <option value="faculty">Faculty</option>
          <option value="non-teaching">Non-Teaching Personnel</option>
        </select>
      </div>
    </div>
    <div class="card">
      <div class="card-content">
        <div>
          <strong>Registration Applications</strong>
          <p>Review and Manage Applications</p>
        </div>
        <div class="applications-table">
          <?php if ($applications && $applications->num_rows > 0): ?>
            <?php while ($app = $applications->fetch_assoc()): ?>
              <div class="application-row" data-role="<?php echo htmlspecialchars($app['role']); ?>">
                <!-- Center: Name & Department -->
                <div class="application-info">
                  <span
                    class="applicant-name"><?php echo htmlspecialchars($app['OwnerID'] . ' - ' . $app['fName'] . ' ' . $app['lName']); ?></span>
                  <span class="applicant-dept"><?php echo htmlspecialchars($app['college']); ?></span>
                </div>
                <!-- Right: Status & Review Button -->
                <div class="application-actions">
                  <div class="application-date">
                    <div>Applied:
                      <?php echo isset($app['applicationDate']) && !empty($app['applicationDate']) ? date('M j, Y g:i A', strtotime($app['applicationDate'])) : 'Date not available'; ?>
                    </div>
                    <?php if ($app['registrationStatus'] != 'pending' && !empty($app['statusTimestamp'])): ?>
                      <div class="status-date"><?php echo ucfirst($app['registrationStatus']); ?>:
                        <?php echo date('M j, Y g:i A', strtotime($app['statusTimestamp'])); ?>
                      </div>
                      <?php if (!empty($app['reviewed_by'])): ?>
                        <div class="reviewer-name" style="font-size: 0.85em; color: #6b7280; margin-top: 2px;">
                          by <?php echo htmlspecialchars($app['reviewed_by']); ?>
                        </div>
                      <?php endif; ?>
                    <?php endif; ?>
                  </div>
                  <span class="status-badge <?php echo strtolower($app['registrationStatus']); ?>">
                    <span>
                      <?php if ($app['registrationStatus'] == 'pending'): ?>
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                          stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                          class="lucide lucide-clock-icon lucide-clock">
                          <path d="M12 6v6l4 2" />
                          <circle cx="12" cy="12" r="10" />
                        </svg>
                      <?php elseif ($app['registrationStatus'] == 'approved'): ?>
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                          stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                          class="lucide lucide-circle-check-big-icon lucide-circle-check-big">
                          <path d="M21.801 10A10 10 0 1 1 17 3.335" />
                          <path d="m9 11 3 3L22 4" />
                        </svg>
                      <?php else: ?>
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                          stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                          class="lucide lucide-circle-x-icon lucide-circle-x">
                          <circle cx="12" cy="12" r="10" />
                          <path d="m15 9-6 6" />
                          <path d="m9 9 6 6" />
                        </svg>
                      <?php endif; ?>
                    </span>
                    <?php echo ucfirst($app['registrationStatus']); ?>
                  </span>
                  <a href="review_application.php?id=<?php echo $app['firstAppId']; ?>" class="review-btn">
                    <span>
                      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="lucide lucide-eye-icon lucide-eye">
                        <path
                          d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0a1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0" />
                        <circle cx="12" cy="12" r="3" />
                      </svg>
                    </span>
                    Review
                  </a>
                </div>
              </div>
            <?php endwhile; ?>
          <?php else: ?>
            <div class="no-applications">No applications found</div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</main>

<?php include_once '../includes/footer.php'; ?>