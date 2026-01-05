<?php
session_start();

// Check authentication and single session
require_once 'auth_check.php';

// Set page title and current page for navigation highlighting
$pageTitle = "Access Logs";
$currentPage = "access_logs";
$cssFiles = ["users.css"];
$jsFiles = ["access_logs.js"];

// Include database connection
require_once 'dbConnection.php';

// Create database instance
$db = new Database();
$conn = $db->getConnection();

// Get access logs with user information (exclude failed login attempts)
$query = "SELECT al.*, u.username, u.role 
          FROM accesslog al 
          LEFT JOIN user u ON al.userID = u.userID 
          WHERE al.action != 'failed_login'
          ORDER BY al.timestamp DESC";
$result = $conn->query($query);

// Include header
include_once '../includes/header.php';
?>

<main class="main">
  <div class="container">
    <div class="header">
      <h2>Access Logs</h2>
      <div class="header-actions">
        <div class="search-container">
          <input type="text" id="searchInput" placeholder="Search logs..." />
          <button class="search-btn">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
              stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
              class="lucide lucide-search">
              <circle cx="11" cy="11" r="8" />
              <path d="m21 21-4.3-4.3" />
            </svg>
          </button>
        </div>
        <div class="filter-container">
          <select id="filterAction">
            <option value="">All Actions</option>
            <option value="login">Login</option>
            <option value="logout">Logout</option>
          </select>
        </div>

      </div>
    </div>

    <div class="card">
      <table class="data-table">
        <thead>
          <tr>
            <th>Date & Time</th>
            <th>User</th>
            <th>Role</th>
            <th>Action</th>
            <th>Description</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($result && $result->num_rows > 0): ?>
            <?php
            $count = 0;
            $result->data_seek(0); // Reset result pointer
            while ($row = $result->fetch_assoc()):
              $count++;
              $class = $count > 10 ? 'class="hidden-log"' : '';
              ?>
              <tr <?php echo $class; ?>>
                <td><?php echo date('M d, Y H:i:s', strtotime($row['timestamp'])); ?></td>
                <td><?php echo htmlspecialchars($row['username'] ?? 'Unknown'); ?></td>
                <td>
                  <?php if ($row['role']): ?>
                    <span class="role-badge role-<?php echo strtolower(str_replace(' ', '-', $row['role'])); ?>">
                      <?php echo htmlspecialchars(ucfirst($row['role'])); ?>
                    </span>
                  <?php else: ?>
                    <span class="role-badge">N/A</span>
                  <?php endif; ?>
                </td>
                <td>
                  <span class="action-badge action-<?php echo strtolower($row['action']); ?>">
                    <?php echo htmlspecialchars(ucfirst($row['action'])); ?>
                  </span>
                </td>
                <td><?php echo htmlspecialchars($row['description']); ?></td>
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr>
              <td colspan="5" class="no-data">No access logs found</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    <?php if ($result && $result->num_rows > 10): ?>
      <div class="text-center-margin-top-20">
        <button id="viewAllBtn" class="add-btn" onclick="toggleLogs()">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M6 9l6 6 6-6" />
          </svg>
          <span id="btnText">View All</span>
        </button>
      </div>
    <?php endif; ?>
  </div>
</main>

<?php
// Close database connection
$db->closeConnection();

// Include footer
include_once '../includes/footer.php';
?>