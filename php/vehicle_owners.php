<?php
session_start();

// Check authentication and single session
require_once 'auth_check.php';

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
        <?php if ($_SESSION['role'] === 'SSEDMMO Admin'): ?>
          <button class="add-btn" id="addOwnerBtn" type="button">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
              stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M5 12h14" />
              <path d="M12 5v14" />
            </svg>
            Add Owner
          </button>
        <?php endif; ?>
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
                    <?php if ($_SESSION['role'] === 'SSEDMMO Admin'): ?>
                      <button class="btn-edit" data-id="<?php echo $row['OwnerID']; ?>">Edit</button>
                      <button class="btn-delete" data-id="<?php echo $row['OwnerID']; ?>">Delete</button>
                    <?php endif; ?>
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
    <p style="color: #d97706; font-size: 0.9em; margin-bottom: 15px;"><strong>Warning:</strong> Deleting this owner will also permanently delete all vehicles registered under their name.</p>
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
        <input type="text" id="editContact" name="contact_num" required placeholder="e.g. 09XXXXXXXXX">
        <span id="editContactError" class="field-error hidden" role="alert">Invalid phone/contact format. Use 10-15
          digits (e.g. 09XXXXXXXXX).</span>
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

<!-- Add Owner Modal -->
<div id="addOwnerModal" class="modal">
  <div class="modal-content">
    <span class="close">&times;</span>
    <h3>Add New Vehicle Owner</h3>
    <div id="addOwnerMessage" class="form-message hidden" role="alert"></div>
    <form id="addOwnerForm">
      <div class="form-group">
        <label>First Name:</label>
        <input type="text" id="addFName" name="fName" required>
      </div>
      <div class="form-group">
        <label>Last Name:</label>
        <input type="text" id="addLName" name="lName" required>
      </div>
      <div class="form-group">
        <label>Middle Name:</label>
        <input type="text" id="addMName" name="mName">
      </div>
      <div class="form-group">
        <label>Email:</label>
        <input type="email" id="addEmail" name="email" required>
      </div>
      <div class="form-group">
        <label>Contact Number:</label>
        <input type="text" id="addContact" name="contact_num" required placeholder="e.g. 09XXXXXXXXX"
          pattern="[0-9+\s\-]{10,15}" title="Enter a valid phone number (10-15 digits)">
        <span id="addContactError" class="field-error hidden">Invalid phone format. Use 10-15 digits (e.g.
          09XXXXXXXXX).</span>
      </div>
      <div class="form-group">
        <label>Role:</label>
        <select id="addRole" name="role" required>
          <option value="">Select Role</option>
          <option value="student">Student</option>
          <option value="faculty">Faculty</option>
          <option value="non-teaching">Non-Teaching Personnel</option>
        </select>
      </div>
      <div class="form-group">
        <label>College:</label>
        <input type="text" id="addCollege" name="college" required>
      </div>
      <div class="form-group">
        <label>Course:</label>
        <input type="text" id="addCourse" name="course">
      </div>
      <div class="form-group hidden" id="addEmploymentTypeField">
        <label>Employment Type:</label>
        <select id="addEmploymentType" name="employment_type">
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
        <input type="password" id="addOwnerAdminPassword" required placeholder="Enter your admin password to confirm">
      </div>
      <button type="button" id="saveAddOwner" class="btn-save">Add Owner</button>
    </form>
  </div>
</div>

<?php
// Close database connection
$db->closeConnection();

// Include footer
include_once '../includes/footer.php';
?>