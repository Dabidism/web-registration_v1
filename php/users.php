<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit;
}

// Set page title and current page for navigation highlighting
$pageTitle = "Users";
$currentPage = "users";
$cssFiles = ["users.css"];
$jsFiles = ["script.js", "admin-auth.js"];

// Include database connection
require_once 'dbConnection.php';

// Create database instance
$db = new Database();
$conn = $db->getConnection();

// Get all users from user table
$query = "SELECT * FROM user ORDER BY username";
$result = $conn->query($query);

// Include header
include_once '../includes/header.php';
?>

<main class="main">
  <div class="container">
    <div class="header">
      <h2>Users</h2>
      <div class="header-actions">
        <div class="search-container">
          <input type="text" id="searchInput" placeholder="Search users..." />
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
            <option value="SSEDMMO Staff">SSEDMMO Staff</option>
            <option value="guard">Guard</option>
            <option value="SSEDMMO Admin">SSEDMMO Admin</option>
          </select>
        </div>
        <?php if ($_SESSION['role'] === 'SSEDMMO Admin'): ?>
          <button class="add-btn" onclick="showAddUserModal()">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
              stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
              class="lucide lucide-plus">
              <path d="M5 12h14" />
              <path d="M12 5v14" />
            </svg>
            Add User
          </button>
        <?php endif; ?>
      </div>
    </div>

    <div class="card">
      <table class="data-table">
        <thead>
          <tr>
            <th>Username</th>
            <th>Role</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
              <tr>
                <td><?php echo htmlspecialchars($row['username']); ?></td>
                <td>
                  <span class="role-badge role-<?php echo strtolower(str_replace(' ', '-', $row['role'])); ?>">
                    <?php echo htmlspecialchars($row['role']); ?>
                  </span>
                </td>

                <td>
                  <?php if ($_SESSION['role'] === 'SSEDMMO Admin'): ?>
                    <div class="action-buttons">
                      <button class="btn-view" onclick="viewUser('<?php echo $row['userID']; ?>')">View</button>
                      <button class="btn-edit" onclick="editUser('<?php echo $row['userID']; ?>')">Edit</button>
                      <button class="btn-delete" onclick="confirmDeleteUser('<?php echo $row['userID']; ?>')">Delete</button>
                    </div>
                  <?php else: ?>
                    <span class="no-permission">No Access</span>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr>
              <td colspan="3" class="no-data">No users found</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</main>

<!-- Add User Modal -->
<div id="addUserModal" class="modal">
  <div class="modal-content">
    <span class="close-btn" onclick="closeAddUserModal()">&times;</span>
    <h3>Add New User</h3>
    <form id="addUserForm" class="mt-4">
      <div class="mb-4">
        <label class="block mb-2 font-bold">Username:</label>
        <input type="text" name="username" required class="form-input">
      </div>
      <div class="mb-4">
        <label class="block mb-2 font-bold">Password:</label>
        <input type="password" name="password" required class="form-input">
      </div>
      <div class="mb-4">
        <label class="block mb-2 font-bold">Role:</label>
        <select name="role" required class="form-input">
          <option value="">Select Role</option>
          <option value="SSEDMMO Admin">SSEDMMO Admin</option>
          <option value="SSEDMMO Staff">SSEDMMO Staff</option>
          <option value="guard">Guard</option>
        </select>
      </div>
      <hr class="mb-4">
      <p class="text-red font-bold mb-2">Admin Confirmation Required</p>
      <div class="mb-4">
        <label class="block mb-2">Admin Password:</label>
        <input type="password" id="addUserAdminPassword" required class="form-input"
          placeholder="Enter your admin password to confirm">
      </div>
      <div class="modal-footer">
        <button type="button" onclick="addUser()" class="btn btn-primary">Add User</button>
        <button type="button" onclick="closeAddUserModal()" class="btn btn-secondary">Cancel</button>
      </div>
    </form>
  </div>
</div>

<!-- View User Modal -->
<div id="viewUserModal" class="modal">
  <div class="modal-content">
    <span class="close-btn" onclick="closeViewUserModal()">&times;</span>
    <h3>User Details</h3>
    <div id="userDetails" class="mb-4"></div>
    <div class="modal-footer">
      <button type="button" onclick="closeViewUserModal()" class="btn btn-secondary">Close</button>
    </div>
  </div>
</div>

<!-- Delete User Modal -->
<div id="deleteUserModal" class="modal">
  <div class="modal-content small">
    <h3 class="text-red mb-2">Confirm User Deletion</h3>
    <p class="mb-4">Are you sure you want to delete this user? This action cannot be undone.</p>
    <input type="hidden" id="deleteUserID">
    <div class="mb-4">
      <label class="block mb-2">Admin Password:</label>
      <input type="password" id="deleteUserAdminPassword" required class="form-input"
        placeholder="Enter your admin password to confirm">
    </div>
    <div class="modal-footer">
      <button type="button" onclick="deleteUser()" class="btn btn-danger">Delete User</button>
      <button type="button"
        onclick="document.getElementById('deleteUserModal').style.display='none';document.getElementById('deleteUserAdminPassword').value='';"
        class="btn btn-secondary">Cancel</button>
    </div>
  </div>
</div>

<!-- Edit User Modal -->
<div id="editUserModal" class="modal">
  <div class="modal-content">
    <span class="close-btn" onclick="closeEditUserModal()">&times;</span>
    <h3>Edit User</h3>
    <form id="editUserForm" class="mt-4">
      <input type="hidden" name="userID" id="editUserID">
      <div class="mb-4">
        <label class="block mb-2 font-bold">Username:</label>
        <input type="text" name="username" id="editUsername" required class="form-input">
      </div>
      <div class="mb-4">
        <label class="block mb-2 font-bold">New Password (leave blank to keep current):</label>
        <input type="password" name="password" id="editPassword" class="form-input">
      </div>
      <div class="mb-4">
        <label class="block mb-2 font-bold">Role:</label>
        <select name="role" id="editRole" required class="form-input">
          <option value="SSEDMMO Admin">SSEDMMO Admin</option>
          <option value="SSEDMMO Staff">SSEDMMO Staff</option>
          <option value="guard">Guard</option>
        </select>
      </div>
      <hr class="mb-4">
      <p class="text-red font-bold mb-2">Admin Confirmation Required</p>
      <div class="mb-4">
        <label class="block mb-2">Admin Password:</label>
        <input type="password" id="editUserAdminPassword" required class="form-input"
          placeholder="Enter your admin password to confirm">
      </div>
      <div class="modal-footer">
        <button type="button" onclick="updateUser()" class="btn btn-primary">Update</button>
        <button type="button" onclick="closeEditUserModal()" class="btn btn-secondary">Cancel</button>
      </div>
    </form>
  </div>
</div>

<script>
  // Client-side search functionality
  document.getElementById('searchInput').addEventListener('keyup', function () {
    const searchValue = this.value.toLowerCase();
    const rows = document.querySelectorAll('.data-table tbody tr');

    rows.forEach(row => {
      const text = row.textContent.toLowerCase();
      row.style.display = text.includes(searchValue) ? '' : 'none';
    });
  });

  // Filter by role
  document.getElementById('filterRole').addEventListener('change', function () {
    const filterValue = this.value.toLowerCase();
    const rows = document.querySelectorAll('.data-table tbody tr');

    if (filterValue === '') {
      rows.forEach(row => row.style.display = '');
      return;
    }

    rows.forEach(row => {
      const roleCell = row.querySelector('.role-badge');
      if (roleCell) {
        const role = roleCell.textContent.toLowerCase().trim();
        row.style.display = role === filterValue ? '' : 'none';
      }
    });
  });

  // Add User functionality
  function showAddUserModal() {
    document.getElementById('addUserModal').style.display = 'block';
  }

  function closeAddUserModal() {
    document.getElementById('addUserModal').style.display = 'none';
    document.getElementById('addUserForm').reset();
    document.getElementById('addUserAdminPassword').value = '';
  }

  function addUser() {
    const adminPassword = document.getElementById('addUserAdminPassword').value;

    if (!adminPassword) {
      alert('Please enter your admin password');
      return;
    }

    // Verify admin password first
    fetch('ajax/verify_admin.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: `password=${encodeURIComponent(adminPassword)}`
    })
      .then(response => response.json())
      .then(result => {
        if (result.success) {
          // Admin verified, proceed with adding user
          const formData = new FormData(document.getElementById('addUserForm'));

          fetch('ajax/add_user.php', {
            method: 'POST',
            body: formData
          })
            .then(response => response.json())
            .then(data => {
              if (data.success) {
                alert('User added successfully!');
                closeAddUserModal();
                location.reload();
              } else {
                alert('Error: ' + data.message);
              }
            })
            .catch(error => {
              console.error('Error:', error);
              alert('Failed to add user');
            });
        } else {
          alert('Invalid admin password');
          document.getElementById('addUserAdminPassword').value = '';
          document.getElementById('addUserAdminPassword').focus();
        }
      })
      .catch(error => {
        console.error('Error:', error);
        alert('Failed to verify admin password');
      });
  }

  // View User functionality
  function viewUser(userID) {
    fetch(`ajax/get_user.php?id=${userID}`)
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          document.getElementById('userDetails').innerHTML = `
          <p><strong>User ID:</strong> ${data.user.userID}</p>
          <p><strong>Username:</strong> ${data.user.username}</p>
          <p><strong>Role:</strong> ${data.user.role}</p>
          <p><strong>Created:</strong> ${data.user.created_at || 'N/A'}</p>
          <p><strong>Last Login:</strong> ${data.user.last_login || 'N/A'}</p>
        `;
          document.getElementById('viewUserModal').style.display = 'block';
        } else {
          alert('Error loading user details');
        }
      })
      .catch(error => {
        console.error('Error:', error);
        alert('Failed to load user details');
      });
  }

  function closeViewUserModal() {
    document.getElementById('viewUserModal').style.display = 'none';
  }

  // Edit User functionality
  function editUser(userID) {
    fetch(`ajax/get_user.php?id=${userID}`)
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          document.getElementById('editUserID').value = data.user.userID;
          document.getElementById('editUsername').value = data.user.username;
          document.getElementById('editRole').value = data.user.role;
          document.getElementById('editPassword').value = '';
          document.getElementById('editUserModal').style.display = 'block';
        } else {
          alert('Error loading user details');
        }
      })
      .catch(error => {
        console.error('Error:', error);
        alert('Failed to load user details');
      });
  }

  function closeEditUserModal() {
    document.getElementById('editUserModal').style.display = 'none';
    document.getElementById('editUserForm').reset();
    document.getElementById('editUserAdminPassword').value = '';
  }

  function updateUser() {
    const adminPassword = document.getElementById('editUserAdminPassword').value;

    if (!adminPassword) {
      alert('Please enter your admin password');
      return;
    }

    // Verify admin password first
    fetch('ajax/verify_admin.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: `password=${encodeURIComponent(adminPassword)}`
    })
      .then(response => response.json())
      .then(result => {
        if (result.success) {
          // Admin verified, proceed with updating user
          const formData = new FormData(document.getElementById('editUserForm'));

          fetch('ajax/update_user.php', {
            method: 'POST',
            body: formData
          })
            .then(response => response.json())
            .then(data => {
              if (data.success) {
                alert('User updated successfully!');
                closeEditUserModal();
                location.reload();
              } else {
                alert('Error: ' + data.message);
              }
            })
            .catch(error => {
              console.error('Error:', error);
              alert('Failed to update user');
            });
        } else {
          alert('Invalid admin password');
          document.getElementById('editUserAdminPassword').value = '';
          document.getElementById('editUserAdminPassword').focus();
        }
      })
      .catch(error => {
        console.error('Error:', error);
        alert('Failed to verify admin password');
      });
  }

  // Delete User functionality
  function confirmDeleteUser(userID) {
    document.getElementById('deleteUserID').value = userID;
    document.getElementById('deleteUserModal').style.display = 'block';
  }

  function deleteUser() {
    const adminPassword = document.getElementById('deleteUserAdminPassword').value;
    const userID = document.getElementById('deleteUserID').value;

    if (!adminPassword) {
      alert('Please enter your admin password');
      return;
    }

    // Verify admin password first
    fetch('ajax/verify_admin.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: `password=${encodeURIComponent(adminPassword)}`
    })
      .then(response => response.json())
      .then(result => {
        if (result.success) {
          // Admin verified, proceed with deletion
          const formData = new FormData();
          formData.append('userID', userID);

          fetch('ajax/delete_user.php', {
            method: 'POST',
            body: formData
          })
            .then(response => response.json())
            .then(data => {
              if (data.success) {
                alert('User deleted successfully!');
                document.getElementById('deleteUserModal').style.display = 'none';
                location.reload();
              } else {
                alert('Error: ' + data.message);
              }
            })
            .catch(error => {
              console.error('Error:', error);
              alert('Failed to delete user');
            });
        } else {
          alert('Invalid admin password');
          document.getElementById('deleteUserAdminPassword').value = '';
          document.getElementById('deleteUserAdminPassword').focus();
        }
      })
      .catch(error => {
        console.error('Error:', error);
        alert('Failed to verify admin password');
      });
  }


  // Close modal when clicking X
  document.addEventListener('click', function (e) {
    if (e.target.classList.contains('close')) {
      e.target.closest('.modal').style.display = 'none';
    }
  });
</script>

<?php
// Close database connection
$db->closeConnection();

// Include footer
include_once '../includes/footer.php';
?>