<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit;
}

// Set page title and current page for navigation highlighting
$pageTitle = "Vehicle Owners";
$currentPage = "vehicle_owners";
$cssFiles = ["vehicles.css"]; // vehicles.css now contains modal styles
$jsFiles = ["script.js", "admin-auth.js", "vehicle_owners.js"];

// Include database connection
require_once 'dbConnection.php';

// Create database instance
$db = new Database();
$conn = $db->getConnection();

// Get all vehicle owners from vehicleowner table
$query = "SELECT * FROM vehicleowner ORDER BY lName, fName";
$result = $conn->query($query);

// Include header
include_once '../includes/header.php';
?>

<main class="main">
  <div class="container">
    <div class="header">
      <h2>Vehicle Owners</h2>
      <div class="header-actions">
        <div class="search-container">
          <input type="text" id="searchInput" placeholder="Search owners..." />
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
          <select id="filterRole">
            <option value="">All Roles</option>
            <option value="student">Student</option>
            <option value="faculty">Faculty</option>
            <option value="non-teaching">Non-Teaching Personnel</option>
          </select>
        </div>
      </div>
    </div>

    <div class="card">
      <table class="data-table">
        <thead>
          <tr>
            <th>Name</th>
            <th>Role</th>
            <th>Email</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
              <tr>
                <td>
                  <div class="owner-info">
                    <strong><?php echo htmlspecialchars($row['fName'] . ' ' . $row['lName']); ?></strong>
                  </div>
                </td>
                <td>
                  <span class="role-badge role-<?php echo strtolower($row['role']); ?>">
                    <?php echo htmlspecialchars(ucfirst($row['role'])); ?>
                  </span>
                </td>
                <td><?php echo htmlspecialchars($row['email']); ?></td>
                <td>
                  <div class="action-buttons">
                    <button class="btn-view" data-id="<?php echo $row['OwnerID']; ?>">View</button>
                    <button class="btn-edit" data-id="<?php echo $row['OwnerID']; ?>">Edit</button>
                    <button class="btn-delete" data-id="<?php echo $row['OwnerID']; ?>">Delete</button>
                  </div>
                </td>
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr>
              <td colspan="4" class="no-data">No vehicle owners found</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</main>

<!-- View Owner Modal -->
<div id="viewOwnerModal" class="modal">
  <div class="modal-content">
    <span class="close">&times;</span>
    <h3>Owner Details</h3>
    <div id="viewOwnerContent"></div>
  </div>
</div>

<!-- Delete Owner Modal -->
<div id="deleteOwnerModal" class="modal">
  <div class="modal-content small">
    <h3 class="text-danger">Confirm Owner Deletion</h3>
    <p>Are you sure you want to delete this owner? This action cannot be undone.</p>
    <input type="hidden" id="deleteOwnerID">
    <div class="form-group">
      <label>Admin Password:</label>
      <input type="password" id="deleteOwnerAdminPassword" required placeholder="Enter your admin password to confirm">
    </div>
    <div class="modal-footer">
      <button type="button" class="btn-cancel">Cancel</button>
      <button type="button" id="confirmDeleteOwnerBtn" class="btn-danger">Delete Owner</button>
    </div>
  </div>
</div>

<!-- Edit Owner Modal -->
<div id="editOwnerModal" class="modal">
  <div class="modal-content">
    <span class="close">&times;</span>
    <h3>Edit Owner</h3>
    <form id="editOwnerForm">
      <input type="hidden" id="editOwnerID" name="ownerID">

      <div class="form-group">
        <label>First Name:</label>
        <input type="text" id="editFName" name="fName" required>
      </div>

      <div class="form-group">
        <label>Last Name:</label>
        <input type="text" id="editLName" name="lName" required>
      </div>

      <div class="form-group">
        <label>Middle Name:</label>
        <input type="text" id="editMName" name="mName">
      </div>

      <div class="form-group">
        <label>Email:</label>
        <input type="email" id="editEmail" name="email" required>
      </div>

      <div class="form-group">
        <label>Contact:</label>
        <input type="text" id="editContact" name="contact_num" required>
      </div>

      <div class="form-group">
        <label>College:</label>
        <input type="text" id="editCollege" name="college" required>
      </div>

      <div class="form-group">
        <label>Course:</label>
        <input type="text" id="editCourse" name="course">
      </div>

      <div class="form-group hidden" id="editEmploymentTypeField">
        <label>Employment Type:</label>
        <select id="editEmploymentType" name="employment_type">
          <option value="">Select Employment Type</option>
          <option value="permanent">Permanent</option>
          <option value="job_hire">Job Hire</option>
          <option value="part_time">Part-time</option>
        </select>
      </div>

      <hr style="margin:15px 0;">
      <p style="color:#dc2626;font-weight:600;">Admin Confirmation Required</p>

      <div class="form-group">
        <label>Admin Password:</label>
        <input type="password" id="editOwnerAdminPassword" required placeholder="Enter your admin password to confirm">
      </div>

      <button type="button" id="saveOwnerEdit" class="btn-save">Save</button>
    </form>
  </div>
</div>

<?php
// Close database connection
$db->closeConnection();

// Include footer
include_once '../includes/footer.php';
?>