<?php
session_start();

// Check authentication and single session
require_once 'auth_check.php';

// Set page title and current page for navigation highlighting
$pageTitle = "Colleges";
$currentPage = "colleges";
$cssFiles = ["vehicles.css"]; // Reusing table/modal styles
$jsFiles = ["script.js", "colleges.js"];

// Include database connection
require_once 'dbConnection.php';

// Create database instance
$db = new Database();
$conn = $db->getConnection();

// Get all colleges with courses
$query = "
  SELECT c.id, c.code, c.name, c.is_active, GROUP_CONCAT(crs.name SEPARATOR ', ') as courses
  FROM colleges c
  LEFT JOIN courses crs ON c.id = crs.college_id
  GROUP BY c.id, c.code, c.name, c.is_active
  ORDER BY c.code
";
$result = $conn->query($query);

// Include header
include_once '../includes/header.php';
?>

<main class="main">
  <div class="container">
    <div class="header">
      <h2>Colleges Management</h2>
      <div class="header-actions">
        <div class="search-container">
          <input type="text" id="searchInput" placeholder="Search colleges..." />
          <button class="search-btn">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
              stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
              class="lucide lucide-search">
              <circle cx="11" cy="11" r="8" />
              <path d="m21 21-4.3-4.3" />
            </svg>
          </button>
        </div>
        <?php if ($_SESSION['role'] === 'SSEDMMO Admin'): ?>
          <button class="add-btn" id="addCollegeBtn">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
              stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
              class="lucide lucide-plus">
              <path d="M5 12h14" />
              <path d="M12 5v14" />
            </svg>
            Add College
          </button>
        <?php endif; ?>
      </div>
    </div>

    <div class="card">
      <table class="data-table">
        <thead>
          <tr>
            <th>College Code</th>
            <th>Name</th>
            <th>Courses</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
              <tr>
                <td><strong><?php echo htmlspecialchars($row['code']); ?></strong></td>
                <td><?php echo htmlspecialchars($row['name']); ?></td>
                <td style="max-width: 300px; font-size: 0.9em; color: #4b5563;">
                  <?php echo $row['courses'] ? htmlspecialchars($row['courses']) : '<em style="color:#9ca3af">No courses</em>'; ?>
                </td>
                <td>
                  <?php if (isset($row['is_active']) && $row['is_active']): ?>
                    <span style="color: green; font-weight: bold;">Active</span>
                  <?php else: ?>
                    <span style="color: gray; font-weight: bold;">Inactive</span>
                  <?php endif; ?>
                </td>
                <td>
                  <div class="action-buttons">
                    <?php if ($_SESSION['role'] === 'SSEDMMO Admin'): ?>
                      <button class="btn-courses" data-id="<?php echo $row['id']; ?>" data-name="<?php echo htmlspecialchars($row['name']); ?>" style="background-color: #3b82f6; color: white; padding: 5px 10px; border: none; border-radius: 4px; cursor: pointer; margin-right: 5px;">Manage Courses</button>
                      <button class="btn-toggle-status <?php echo (isset($row['is_active']) && $row['is_active']) ? 'btn-deactivate' : 'btn-activate'; ?>" data-id="<?php echo $row['id']; ?>" data-status="<?php echo isset($row['is_active']) ? $row['is_active'] : 1; ?>">
                        <?php echo (isset($row['is_active']) && $row['is_active']) ? 'Deactivate' : 'Activate'; ?>
                      </button>
                    <?php endif; ?>
                  </div>
                </td>
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr>
              <td colspan="3" class="no-data">No colleges found</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</main>

<!-- Add College Modal -->
<div id="addCollegeModal" class="modal">
  <div class="modal-content">
    <span class="close">&times;</span>
    <h3>Add New College</h3>
    <div id="addCollegeMessage" class="form-message hidden" role="alert"></div>
    <form id="addCollegeForm">
      <div class="form-group">
        <label>College Code (e.g. CAS):</label>
        <input type="text" id="addCollegeCode" name="code" required>
      </div>
      <div class="form-group">
        <label>College Name:</label>
        <input type="text" id="addCollegeName" name="name" required>
      </div>

      <hr style="margin:15px 0;">
      <h4 style="margin-bottom: 10px; color: #374151;">Courses</h4>
      <div id="dynamicCoursesContainer">
        <div class="form-group course-input-group" style="display: flex; gap: 10px; margin-bottom: 10px;">
          <input type="text" name="courses[]" placeholder="Course Name (e.g. BS in IT)" class="course-input" required style="flex: 1;">
          <button type="button" class="btn-remove-course hidden" style="background-color: #ef4444; color: white; border: none; padding: 0 10px; border-radius: 4px; cursor: pointer;">&times;</button>
        </div>
      </div>
      <button type="button" id="addAnotherCourseBtn" style="background-color: #10b981; color: white; border: none; padding: 8px 15px; border-radius: 4px; cursor: pointer; margin-bottom: 15px; font-size: 13px;">+ Add another course</button>

      <hr style="margin:15px 0;">
      <p style="color:#dc2626;font-weight:600;">Admin Confirmation Required</p>

      <div class="form-group">
        <label>Admin Password:</label>
        <input type="password" id="addCollegeAdminPassword" required placeholder="Enter your admin password to confirm">
      </div>

      <button type="button" id="saveAddCollege" class="btn-save">Add College</button>
    </form>
  </div>
</div>

<!-- Manage Courses Modal -->
<div id="manageCoursesModal" class="modal">
  <div class="modal-content">
    <span class="close">&times;</span>
    <h3 id="manageCoursesTitle">Manage Courses</h3>
    <input type="hidden" id="manageCollegeID">
    <div id="manageCoursesMessage" class="form-message hidden" role="alert"></div>
    
    <div class="form-group">
      <label>Add New Course:</label>
      <div style="display: flex; gap: 10px;">
        <input type="text" id="newCourseName" placeholder="Course Name (e.g. BS in IT)" style="flex: 1; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
        <button type="button" id="addNewCourseBtn" style="background-color: #10b981; color: white; border: none; padding: 0 15px; border-radius: 4px; cursor: pointer;">Add</button>
      </div>
    </div>
    
    <hr style="margin: 15px 0; border: 1px solid #e5e7eb;">
    <h4 style="margin-bottom: 10px; color: #374151;">Existing Courses</h4>
    <div id="existingCoursesList" style="max-height: 250px; overflow-y: auto; text-align: left; padding: 10px; background-color: #f9fafb; border: 1px solid #e5e7eb; border-radius: 4px;">
      <!-- Loaded dynamically -->
    </div>
  </div>
</div>

<!-- Toggle Status College Modal -->
<div id="toggleCollegeModal" class="modal">
  <div class="modal-content small">
    <h3 class="text-warning">Confirm Status Change</h3>
    <p>Are you sure you want to change the active status of this college?</p>
    <p style="color: #d97706; font-size: 0.9em; margin-bottom: 15px;"><strong>Note:</strong> Inactive colleges may not appear as options during new registrations.</p>
    <input type="hidden" id="toggleCollegeID">
    <input type="hidden" id="toggleCollegeCurrent">
    <div class="form-group">
      <label>Admin Password:</label>
      <input type="password" id="toggleCollegeAdminPassword" required placeholder="Enter your admin password to confirm">
    </div>
    <div class="modal-footer">
      <button type="button" class="btn-cancel" id="cancelToggleCollegeBtn">Cancel</button>
      <button type="button" id="confirmToggleCollegeBtn" class="btn-toggle-status">Confirm</button>
    </div>
  </div>
</div>

<?php
$db->closeConnection();
include_once '../includes/footer.php';
?>
