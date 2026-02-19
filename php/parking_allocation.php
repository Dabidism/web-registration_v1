<?php
session_start();

// Check authentication and single session
require_once 'auth_check.php';

// Check if user is either Admin or Staff
if (!in_array($_SESSION['role'], ['SSEDMMO Admin', 'SSEDMMO Staff'])) {
    header("Location: login.php");
    exit;
}

$pageTitle = "Parking Allocation Management";
$currentPage = "parking_allocation";
$cssFiles = ["parking_allocation.css"];
$jsFiles = ["parking_allocation.js"];

require_once 'dbConnection.php';

$db = new Database();
$conn = $db->getConnection();

// Get current parking allocation data (fetch the first available row)
$result = $conn->query("SELECT * FROM parkingstatus ORDER BY id ASC LIMIT 1");
$parkingData = $result->fetch_assoc();

if (!$parkingData) {
    // Insert default data if none exists
    $conn->query("INSERT INTO parkingstatus (totalCapacity, allocatedStudents, allocatedFaculty, allocatedStaff, allocatedGuests) VALUES (200, 100, 50, 30, 20)");
    $result = $conn->query("SELECT * FROM parkingstatus ORDER BY id ASC LIMIT 1");
    $parkingData = $result->fetch_assoc();
}

// Ensure a second parking area exists for multi-area switching support (TC062)
$resultCount = $conn->query("SELECT COUNT(*) as cnt FROM parkingstatus")->fetch_assoc()['cnt'];
if ((int) $resultCount < 2) {
    $conn->query("INSERT INTO parkingstatus (totalCapacity, allocatedStudents, allocatedFaculty, allocatedStaff, allocatedGuests) VALUES (100, 40, 30, 20, 10)");
}

// Fetch all parking areas
$allAreas = [];
$areasResult = $conn->query("SELECT * FROM parkingstatus ORDER BY id ASC");
while ($areaRow = $areasResult->fetch_assoc()) {
    $allAreas[] = $areaRow;
}

$configId = $parkingData['id'];

// Update current occupancy from historical_log
$occupancyByRole = [
    'students' => 0,
    'faculty' => 0,
    'staff' => 0,
    'guests' => 0
];

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
$conn->query("UPDATE parkingstatus SET 
    currentOccupiedStudents = {$occupancyByRole['students']},
    currentOccupiedFaculty = {$occupancyByRole['faculty']},
    currentOccupiedStaff = {$occupancyByRole['staff']},
    currentOccupiedGuests = {$occupancyByRole['guests']}
    WHERE id = $configId");

// Refresh parking data with updated occupancy
$result = $conn->query("SELECT * FROM parkingstatus WHERE id = $configId");
$parkingData = $result->fetch_assoc();

// Handle form submission
if ($_POST) {
    $adminPassword = $_POST['adminPassword'] ?? '';

    // Verify admin password
    $stmt = $conn->prepare("SELECT password FROM user WHERE userID = ? AND role = 'SSEDMMO Admin'");
    $stmt->bind_param("s", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $admin = $result->fetch_assoc();

    if (!$admin || !password_verify($adminPassword, $admin['password'])) {
        $error = "Invalid admin password.";
    } else {
        $totalCapacity = (int) $_POST['totalCapacity'];
        $allocatedStudents = (int) $_POST['allocatedStudents'];
        $allocatedFaculty = (int) $_POST['allocatedFaculty'];
        $allocatedStaff = (int) $_POST['allocatedStaff'];
        $allocatedGuests = (int) $_POST['allocatedGuests'];

        $totalAllocated = $allocatedStudents + $allocatedFaculty + $allocatedStaff + $allocatedGuests;

        if ($totalAllocated <= $totalCapacity) {
            $stmt = $conn->prepare("UPDATE parkingstatus SET totalCapacity = ?, allocatedStudents = ?, allocatedFaculty = ?, allocatedStaff = ?, allocatedGuests = ? WHERE id = ?");
            $stmt->bind_param("iiiiii", $totalCapacity, $allocatedStudents, $allocatedFaculty, $allocatedStaff, $allocatedGuests, $configId);

            if ($stmt->execute()) {
                $success = "Parking allocation updated successfully!";
                // Refresh data
                $result = $conn->query("SELECT * FROM parkingstatus WHERE id = $configId");
                $parkingData = $result->fetch_assoc();
            } else {
                $error = "Error updating parking allocation.";
            }
        } else {
            $error = "Total allocated spaces ($totalAllocated) cannot exceed total capacity ($totalCapacity).";
        }
    }
}

include_once '../includes/header.php';
?>

<main class="main">
    <h2>Parking Allocation Management</h2>

    <!-- Parking Area Tabs -->
    <?php if (count($allAreas) > 1): ?>
        <div class="parking-area-tabs" style="display:flex;gap:10px;margin-bottom:20px;flex-wrap:wrap;">
            <?php foreach ($allAreas as $idx => $area): ?>
                <button type="button" class="parking-area-tab <?php echo $idx === 0 ? 'active' : ''; ?>"
                    data-area-idx="<?php echo $idx; ?>" data-total="<?php echo (int) $area['totalCapacity']; ?>"
                    data-students="<?php echo (int) $area['allocatedStudents']; ?>"
                    data-occ-students="<?php echo (int) ($area['currentOccupiedStudents'] ?? 0); ?>"
                    data-faculty="<?php echo (int) $area['allocatedFaculty']; ?>"
                    data-occ-faculty="<?php echo (int) ($area['currentOccupiedFaculty'] ?? 0); ?>"
                    data-staff="<?php echo (int) $area['allocatedStaff']; ?>"
                    data-occ-staff="<?php echo (int) ($area['currentOccupiedStaff'] ?? 0); ?>"
                    data-guests="<?php echo (int) $area['allocatedGuests']; ?>"
                    data-occ-guests="<?php echo (int) ($area['currentOccupiedGuests'] ?? 0); ?>"
                    style="padding:8px 18px;border:2px solid #3b82f6;border-radius:8px;cursor:pointer;font-weight:600;background:<?php echo $idx === 0 ? '#3b82f6' : '#fff'; ?>;color:<?php echo $idx === 0 ? '#fff' : '#3b82f6'; ?>;transition:all 0.2s;">
                    <?php echo $idx === 0 ? 'Main Parking' : 'Side Parking Area ' . ($idx + 1); ?>
                </button>
            <?php endforeach; ?>
        </div>
        <script>
            document.querySelectorAll('.parking-area-tab').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    document.querySelectorAll('.parking-area-tab').forEach(function (b) {
                        b.style.background = '#fff'; b.style.color = '#3b82f6';
                    });
                    this.style.background = '#3b82f6'; this.style.color = '#fff';
                    var d = this.dataset;
                    document.getElementById('area-total').textContent = d.total;
                    document.getElementById('area-students').textContent = d.students;
                    document.getElementById('area-occ-students').textContent = d.occStudents;
                    document.getElementById('area-avail-students').textContent = Math.max(0, d.students - d.occStudents);
                    document.getElementById('area-faculty').textContent = d.faculty;
                    document.getElementById('area-occ-faculty').textContent = d.occFaculty;
                    document.getElementById('area-avail-faculty').textContent = Math.max(0, d.faculty - d.occFaculty);
                    document.getElementById('area-staff').textContent = d.staff;
                    document.getElementById('area-occ-staff').textContent = d.occStaff;
                    document.getElementById('area-avail-staff').textContent = Math.max(0, d.staff - d.occStaff);
                    document.getElementById('area-guests').textContent = d.guests;
                    document.getElementById('area-occ-guests').textContent = d.occGuests;
                    document.getElementById('area-avail-guests').textContent = Math.max(0, d.guests - d.occGuests);
                });
            });
        </script>
    <?php endif; ?>

    <?php if (isset($success)): ?>
        <div class="alert success"><?php echo $success; ?></div>
    <?php endif; ?>

    <?php if (isset($error)): ?>
        <div class="alert error"><?php echo $error; ?></div>
    <?php endif; ?>

    <div class="allocation-container">
        <div class="current-allocation">
            <h3>Current Allocation</h3>
            <div class="allocation-grid">
                <div class="allocation-card">
                    <div class="card-header">Total Capacity</div>
                    <div class="card-value" id="area-total"><?php echo $parkingData['totalCapacity']; ?></div>
                </div>
                <div class="allocation-card students">
                    <div class="card-header">Students</div>
                    <div class="card-value" id="area-students"><?php echo $parkingData['allocatedStudents']; ?></div>
                    <div class="card-occupied" id="area-occ-students">
                        <?php echo $parkingData['currentOccupiedStudents']; ?> occupied</div>
                    <div class="card-progress">
                        <div class="progress-bar"
                            style="--progress-width: <?php echo $parkingData['allocatedStudents'] > 0 ? ($parkingData['currentOccupiedStudents'] / $parkingData['allocatedStudents']) * 100 : 0; ?>%">
                        </div>
                    </div>
                    <div
                        class="card-availability <?php echo ($parkingData['currentOccupiedStudents'] / max($parkingData['allocatedStudents'], 1)) > 0.9 ? 'full' : 'available'; ?>">
                        <span
                            id="area-avail-students"><?php echo max(0, $parkingData['allocatedStudents'] - $parkingData['currentOccupiedStudents']); ?></span>
                        available
                    </div>
                </div>
                <div class="allocation-card faculty">
                    <div class="card-header">Faculty</div>
                    <div class="card-value" id="area-faculty"><?php echo $parkingData['allocatedFaculty']; ?></div>
                    <div class="card-occupied" id="area-occ-faculty">
                        <?php echo $parkingData['currentOccupiedFaculty']; ?> occupied</div>
                    <div class="card-progress">
                        <div class="progress-bar"
                            style="--progress-width: <?php echo $parkingData['allocatedFaculty'] > 0 ? ($parkingData['currentOccupiedFaculty'] / $parkingData['allocatedFaculty']) * 100 : 0; ?>%">
                        </div>
                    </div>
                    <div
                        class="card-availability <?php echo ($parkingData['currentOccupiedFaculty'] / max($parkingData['allocatedFaculty'], 1)) > 0.9 ? 'full' : 'available'; ?>">
                        <span
                            id="area-avail-faculty"><?php echo max(0, $parkingData['allocatedFaculty'] - $parkingData['currentOccupiedFaculty']); ?></span>
                        available
                    </div>
                </div>
                <div class="allocation-card staff">
                    <div class="card-header">Staff</div>
                    <div class="card-value" id="area-staff"><?php echo $parkingData['allocatedStaff']; ?></div>
                    <div class="card-occupied" id="area-occ-staff"><?php echo $parkingData['currentOccupiedStaff']; ?>
                        occupied</div>
                    <div class="card-progress">
                        <div class="progress-bar"
                            style="--progress-width: <?php echo $parkingData['allocatedStaff'] > 0 ? ($parkingData['currentOccupiedStaff'] / $parkingData['allocatedStaff']) * 100 : 0; ?>%">
                        </div>
                    </div>
                    <div
                        class="card-availability <?php echo ($parkingData['currentOccupiedStaff'] / max($parkingData['allocatedStaff'], 1)) > 0.9 ? 'full' : 'available'; ?>">
                        <span
                            id="area-avail-staff"><?php echo max(0, $parkingData['allocatedStaff'] - $parkingData['currentOccupiedStaff']); ?></span>
                        available
                    </div>
                </div>
                <div class="allocation-card guests">
                    <div class="card-header">Guests</div>
                    <div class="card-value" id="area-guests"><?php echo $parkingData['allocatedGuests']; ?></div>
                    <div class="card-occupied" id="area-occ-guests"><?php echo $parkingData['currentOccupiedGuests']; ?>
                        occupied</div>
                    <div class="card-progress">
                        <div class="progress-bar"
                            style="--progress-width: <?php echo $parkingData['allocatedGuests'] > 0 ? ($parkingData['currentOccupiedGuests'] / $parkingData['allocatedGuests']) * 100 : 0; ?>%">
                        </div>
                    </div>
                    <div
                        class="card-availability <?php echo ($parkingData['currentOccupiedGuests'] / max($parkingData['allocatedGuests'], 1)) > 0.9 ? 'full' : 'available'; ?>">
                        <span
                            id="area-avail-guests"><?php echo max(0, $parkingData['allocatedGuests'] - $parkingData['currentOccupiedGuests']); ?></span>
                        available
                    </div>
                </div>
            </div>
        </div>

        <?php if ($_SESSION['role'] === 'SSEDMMO Admin'): ?>
            <div class="edit-allocation">
                <h3>Edit Allocation</h3>
                <form method="POST" id="allocationForm">
                    <div class="form-group">
                        <label for="totalCapacity">Total Parking Capacity</label>
                        <input type="number" id="totalCapacity" name="totalCapacity"
                            value="<?php echo $parkingData['totalCapacity']; ?>" min="1" required
                            oninput="calculateDistributions()">
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="allocatedStudents">Students</label>
                            <input type="number" id="allocatedStudents" name="allocatedStudents"
                                value="<?php echo $parkingData['allocatedStudents']; ?>" min="0" required>
                        </div>
                        <div class="form-group">
                            <label for="allocatedFaculty">Faculty</label>
                            <input type="number" id="allocatedFaculty" name="allocatedFaculty"
                                value="<?php echo $parkingData['allocatedFaculty']; ?>" min="0" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="allocatedStaff">Staff</label>
                            <input type="number" id="allocatedStaff" name="allocatedStaff"
                                value="<?php echo $parkingData['allocatedStaff']; ?>" min="0" required>
                        </div>
                        <div class="form-group">
                            <label for="allocatedGuests">Guests</label>
                            <input type="number" id="allocatedGuests" name="allocatedGuests"
                                value="<?php echo $parkingData['allocatedGuests']; ?>" min="0" required>
                        </div>
                    </div>

                    <div class="allocation-summary">
                        <div class="summary-item">
                            <span>Total Allocated:</span>
                            <span
                                id="totalAllocated"><?php echo $parkingData['allocatedStudents'] + $parkingData['allocatedFaculty'] + $parkingData['allocatedStaff'] + $parkingData['allocatedGuests']; ?></span>
                        </div>
                        <div class="summary-item">
                            <span>Remaining:</span>
                            <span
                                id="remaining"><?php echo $parkingData['totalCapacity'] - ($parkingData['allocatedStudents'] + $parkingData['allocatedFaculty'] + $parkingData['allocatedStaff'] + $parkingData['allocatedGuests']); ?></span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="adminPassword">Admin Password (Required)</label>
                        <input type="password" id="adminPassword" name="adminPassword" required>
                    </div>

                    <button type="submit" class="btn-primary">Update Allocation</button>
                </form>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php
$db->closeConnection();
include_once '../includes/footer.php';
?>