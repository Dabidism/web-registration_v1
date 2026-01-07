<?php
// Check authentication and single session
require_once 'auth_check.php';
// Set page title and current page for navigation highlighting
$pageTitle = "RFID and Car Pass Management";
$currentPage = "rfid_management";
$cssFiles = ["rfid_management.css", "rfid_add_styles.css", "issue_rfid_modal.css"];
$jsFiles = ["rfid_management.js", "admin-auth.js"];

// Include database connection
require_once 'dbConnection.php';

// Create database instance
$db = new Database();
$conn = $db->getConnection();

// Get pending vehicles (without RFID tags)
$pendingQuery = "SELECT v.*, vo.fName, vo.lName, vo.email 
                FROM vehicle v 
                JOIN vehicleowner vo ON v.OwnerID = vo.OwnerID 
                WHERE v.stickerID IS NULL OR v.stickerID = ''
                ORDER BY vo.lName, vo.fName";
$pendingResult = $conn->query($pendingQuery);

// Get registered vehicles (with RFID tags) - simplified query for new table structure
$registeredQuery = "SELECT v.*, vo.fName, vo.lName, vo.email, r.status as rfidStatus, r.issuedBy 
                   FROM vehicle v 
                   JOIN vehicleowner vo ON v.OwnerID = vo.OwnerID 
                   LEFT JOIN rfidtag r ON v.stickerID = r.stickerID
                   WHERE v.stickerID IS NOT NULL AND v.stickerID != ''
                   ORDER BY v.plateNum";
$registeredResult = $conn->query($registeredQuery);

// Include header
include_once '../includes/header.php';
?>

<main class="main">
  <div class="stats">
    <div class="card-flex">
      <div>
        <h3>Pending RFID</h3>
        <p>Awaiting Physical Verification</p>
        <br />
        <strong class="card-num"><?php echo $pendingResult->num_rows; ?></strong>
      </div>
    </div>
    <div class="card-flex">
      <div>
        <h3>Total Approved</h3>
        <p>Fully Registered Vehicles</p>
        <br />
        <strong class="card-num"><?php echo $registeredResult->num_rows; ?></strong>
      </div>
    </div>
    <?php if ($_SESSION['role'] === 'SSEDMMO Admin'): ?>
      <div class="card-flex">
        <div>
          <h3>Add RFID Tag</h3>
          <p>Register New RFID</p>
          <br />
          <button class="btn-add-rfid">Add RFID</button>
        </div>
      </div>
    <?php endif; ?>
  </div>

  <div class="grid">
    <div class="card">
      <div class="card-header">
        <div class="card-title-section">
          <h4 class="card-title">Pending Physical Verification</h4>
          <p>
            Approved applications awaiting document verification and RFID
            tag issuance
          </p>
        </div>
        <div class="search-area">
          <div class="search-box">
            <input type="text" id="searchPending" placeholder="Search by owner name or plate number..." />
            <button class="search-btn" type="submit" title="Search">
              <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="lucide lucide-search-icon lucide-search">
                <path d="m21 21-4.34-4.34" />
                <circle cx="11" cy="11" r="8" />
              </svg>
            </button>
          </div>
        </div>
      </div>
      <table class="rfid-table" id="pendingTable">
        <thead>
          <tr>
            <th>Owner</th>
            <th>Plate Number</th>
            <th>Vehicle Type</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($pendingResult && $pendingResult->num_rows > 0): ?>
            <?php while ($row = $pendingResult->fetch_assoc()): ?>
              <tr>
                <td>
                  <div class="owner-info">
                    <strong><?php echo htmlspecialchars($row['fName'] . ' ' . $row['lName']); ?></strong>
                    <span class="owner-email"><?php echo htmlspecialchars($row['email']); ?></span>
                  </div>
                </td>
                <td><span class="plate-number"><?php echo htmlspecialchars($row['plateNum']); ?></span></td>
                <td><span class="vehicle-type"><?php echo htmlspecialchars($row['vehicleType']); ?></span></td>
                <td>
                  <div class="action-buttons">
                    <button class="btn-issue" data-id="<?php echo $row['plateNum']; ?>">Issue RFID & Car Pass</button>
                  </div>
                </td>
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr>
              <td colspan="4" class="no-data">No pending vehicles found</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    <div class="card">
      <div class="card-header">
        <div class="card-title-section">
          <h4 class="card-title">RFID Records</h4>
          <p>Vehicles with issued RFID tags and complete registration</p>
        </div>
        <div class="search-area">
          <div class="search-box">
            <input type="text" id="searchRegistered" placeholder="Search by owner name or plate number..." />
            <button class="search-btn" type="submit" title="Search">
              <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="lucide lucide-search-icon lucide-search">
                <path d="m21 21-4.34-4.34" />
                <circle cx="11" cy="11" r="8" />
              </svg>
            </button>
          </div>
        </div>
      </div>
      <table class="rfid-table" id="registeredTable">
        <thead>
          <tr>
            <th>Sticker ID</th>
            <th>Owner</th>
            <th>Plate Number</th>
            <th>Vehicle Type</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($registeredResult && $registeredResult->num_rows > 0): ?>
            <?php while ($row = $registeredResult->fetch_assoc()): ?>
              <tr>
                <td><span class="sticker-id"><?php echo htmlspecialchars($row['stickerID'] ?? 'N/A'); ?></span></td>
                <td>
                  <div class="owner-info">
                    <strong><?php echo htmlspecialchars($row['fName'] . ' ' . $row['lName']); ?></strong>
                  </div>
                </td>
                <td><span class="plate-number"><?php echo htmlspecialchars($row['plateNum']); ?></span></td>
                <td><span class="vehicle-type"><?php echo htmlspecialchars($row['vehicleType']); ?></span></td>
                <td>
                  <button class="btn-revoke" data-plate="<?php echo htmlspecialchars($row['plateNum']); ?>"
                    data-sticker="<?php echo htmlspecialchars($row['stickerID'] ?? ''); ?>">
                    Revoke Access
                  </button>
                </td>
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr>
              <td colspan="5" class="no-data">No registered vehicles found</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</main>

<!-- Issue RFID & Car Pass Popup -->
<div id="issueRfidPopup" class="popup-overlay">
  <div class="popup-content minimal-modal">
    <h3>Issue RFID & Car Pass to <span id="vehiclePlateDisplay"></span></h3>
    <select id="rfidTagSelect">
      <option value="">Select RFID tag...</option>
    </select>
    <select id="carpassIdInput">
      <option value="">Select Car Pass ID...</option>
    </select>
    <input type="hidden" id="plateNumInput" value="" />
    <div class="modal-actions">
      <button class="btn-cancel">Cancel</button>
      <button class="btn-confirm">Issue Both</button>
    </div>
  </div>
</div>

<!-- Add RFID Modal -->
<div id="addRfidModal" class="popup-overlay">
  <div class="popup-content">
    <h3>Add New RFID Tag</h3>
    <p>Scan the RFID tag and enter the code below. The system will automatically assign an ID.</p>
    <input type="text" id="newRfidCode" placeholder="Enter scanned RFID tag code (e.g., E0F8FEFE009806...)" />
    <div class="popup-buttons">
      <button class="btn-cancel">Cancel</button>
      <button class="btn-add-confirm">Add RFID Tag</button>
    </div>
  </div>
</div>



<!-- Success Popup -->
<div id="successPopup" class="popup-overlay">
  <div class="popup-content success-popup">
    <div class="success-icon">✓</div>
    <h3 id="successMessage">RFID Tag Issued Successfully!</h3>
    <p id="successDescription">The RFID tag has been assigned to the vehicle and is now active.</p>
    <div class="popup-buttons">
      <button class="btn-success-ok">OK</button>
    </div>
  </div>
</div>

<?php
// Close database connection
$db->closeConnection();

// Include footer
include_once '../includes/footer.php';
?>