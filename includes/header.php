<?php
// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
  session_start();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
  <title><?php echo $pageTitle ?? 'ISATU Vehicle Gate Pass'; ?></title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="../css/global.css" />
  <link rel="stylesheet" href="../css/sidebar.css" />
  <?php if (isset($cssFiles) && is_array($cssFiles)): ?>
    <?php foreach ($cssFiles as $css): ?>
      <link rel="stylesheet" href="../css/<?php echo $css; ?>" />
    <?php endforeach; ?>
  <?php endif; ?>
  <?php if (isset($externalJs) && is_array($externalJs)): ?>
    <?php foreach ($externalJs as $js): ?>
      <script src="<?php echo $js; ?>"></script>
    <?php endforeach; ?>
  <?php endif; ?>
</head>

<body>
  <aside class="sidebar">
    <div>
      <div class="logo-title">
        <h2 class="sidebar-title login-title">
          <span class="highlight-blue">Vehicle Gate Pass</span>
        </h2>
      </div>
      <div class="nav-links">
        <a href="dashboard.php" <?php echo ($currentPage === 'dashboard') ? 'class="active"' : ''; ?>>
          <span class="sidebar-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
              stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
              class="lucide lucide-blocks-icon lucide-blocks">
              <path d="M10 22V7a1 1 0 0 0-1-1H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-5a1 1 0 0 0-1-1H2" />
              <rect x="14" y="2" width="8" height="8" rx="1" />
            </svg>
          </span>
          Dashboard
        </a>
        <a href="vehicles.php" <?php echo ($currentPage === 'vehicles') ? 'class="active"' : ''; ?>>
          <span class="sidebar-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
              stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
              class="lucide lucide-car-icon lucide-car">
              <path
                d="M19 17h2c.6 0 1-.4 1-1v-3c0-.9-.7-1.7-1.5-1.9C18.7 10.6 16 10 16 10s-1.3-1.4-2.2-2.3c-.5-.4-1.1-.7-1.8-.7H5c-.6 0-1.1.4-1.4.9l-1.4 2.9A3.7 3.7 0 0 0 2 12v4c0 .6.4 1 1 1h2" />
              <circle cx="7" cy="17" r="2" />
              <path d="M9 17h6" />
              <circle cx="17" cy="17" r="2" />
            </svg>
          </span>
          Vehicles
        </a>
        <a href="vehicle_owners.php" <?php echo ($currentPage === 'vehicle_owners') ? 'class="active"' : ''; ?>>
          <span class="sidebar-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
              stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
              class="lucide lucide-user-check">
              <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
              <circle cx="9" cy="7" r="4" />
              <polyline points="16 11 18 13 22 9" />
            </svg>
          </span>
          Vehicle Owners
        </a>
        <a href="entry-exit.php" <?php echo ($currentPage === 'entry-exit') ? 'class="active"' : ''; ?>>
          <span class="sidebar-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
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
          Entry/Exit Logs
        </a>
        <a href="users.php" <?php echo ($currentPage === 'users') ? 'class="active"' : ''; ?>>
          <span class="sidebar-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
              stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
              class="lucide lucide-users-icon lucide-users">
              <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
              <path d="M16 3.128a4 4 0 0 1 0 7.744" />
              <path d="M22 21v-2a4 4 0 0 0-3-3.87" />
              <circle cx="9" cy="7" r="4" />
            </svg>
          </span>
          Users
        </a>
        <a href="access_logs.php" <?php echo ($currentPage === 'access_logs') ? 'class="active"' : ''; ?>>
          <span class="sidebar-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
              stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
              class="lucide lucide-clock3-icon lucide-clock-3">
              <circle cx="12" cy="12" r="10" />
              <polyline points="12 6 12 12 16.5 12" />
            </svg>
          </span>
          Access Logs
        </a>
        <a href="reports.php" <?php echo ($currentPage === 'reports') ? 'class="active"' : ''; ?>>
          <span class="sidebar-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
              stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
              class="lucide lucide-notebook-text-icon lucide-notebook-text">
              <path d="M2 6h4" />
              <path d="M2 10h4" />
              <path d="M2 14h4" />
              <path d="M2 18h4" />
              <rect width="16" height="20" x="4" y="2" rx="2" />
              <path d="M9.5 8h5" />
              <path d="M9.5 12H16" />
              <path d="M9.5 16H14" />
            </svg>
          </span>
          Reports
        </a>

        <a href="registration_applications.php" <?php echo ($currentPage === 'registration_applications') ? 'class="active"' : ''; ?>>
          <span class="sidebar-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
              stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
              class="lucide lucide-notepad-text-icon lucide-notepad-text">
              <path d="M8 2v4" />
              <path d="M12 2v4" />
              <path d="M16 2v4" />
              <rect width="16" height="18" x="4" y="4" rx="2" />
              <path d="M8 10h6" />
              <path d="M8 14h8" />
              <path d="M8 18h5" />
            </svg>
          </span>
          Registration Applications
        </a>
        <a href="rfid_management.php" <?php echo ($currentPage === 'rfid_management') ? 'class="active"' : ''; ?>>
          <span class="sidebar-icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
              fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
              class="lucide lucide-id-card-icon lucide-id-card">
              <path d="M16 10h2" />
              <path d="M16 14h2" />
              <path d="M6.17 15a3 3 0 0 1 5.66 0" />
              <circle cx="9" cy="11" r="2" />
              <rect x="2" y="5" width="20" height="14" rx="2" />
            </svg></span>
          RFID and Car Pass Management
        </a>
        <a href="parking_allocation.php" <?php echo ($currentPage === 'parking_allocation') ? 'class="active"' : ''; ?>>
          <span class="sidebar-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
              stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <rect x="3" y="3" width="18" height="18" rx="2" />
              <path d="M9 9h6v6H9z" />
              <path d="M9 3v6" />
              <path d="M15 3v6" />
              <path d="M9 15v6" />
              <path d="M15 15v6" />
              <path d="M3 9h6" />
              <path d="M15 9h6" />
            </svg>
          </span>
          Parking Allocation
        </a>

      </div>
    </div>
    <?php if (isset($_SESSION['username'])): ?>
      <div class="user-info-box">
        <h3>Logged in as:</h3>
        <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong>
        <span class="user-info-role"><?php echo ucfirst($_SESSION['role']); ?></span>
      </div>
    <?php endif; ?>
    <a href="logout.php" class="logout" onclick="return confirm('Are you sure you want to logout?')"><span
        class="sidebar-icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
          fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
          class="lucide lucide-log-out-icon lucide-log-out">
          <path d="m16 17 5-5-5-5" />
          <path d="M21 12H9" />
          <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
        </svg></span>Logout</a>
  </aside>