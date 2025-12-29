<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "thesis_test";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  try {
    // Get form data
    $userType = $_POST['userType'];
    $lastName = $_POST['lastName'];
    $firstName = $_POST['firstName'];
    $middleName = $_POST['middleName'] ?? '';
    $email = $_POST['email'];
    $contactNum = intval($_POST['contactNum']);
    $college = $_POST['college'];
    $course = $_POST['course'];
    $academicYear = $_POST['academicYear'];
    $yearLevel = $_POST['yearLevel'] ?? '';
    $section = $_POST['section'] ?? '';

    // Generate unique OwnerID
    $result = $conn->query("SELECT MAX(CAST(SUBSTRING(OwnerID, 2) AS UNSIGNED)) as max_id FROM vehicleowner WHERE OwnerID LIKE 'O%'");
    $row = $result->fetch_assoc();
    $nextId = ($row['max_id'] ?? 0) + 1;
    $ownerID = 'O' . str_pad($nextId, 3, '0', STR_PAD_LEFT);

    // Handle driver's license upload
    $driversLicense = null;
    if (isset($_FILES['driversLicense']) && $_FILES['driversLicense']['error'] == 0) {
      $uploadDir = 'DL_upload/';
      if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
      }
      $fileName = $ownerID . '_license.' . pathinfo($_FILES['driversLicense']['name'], PATHINFO_EXTENSION);
      $uploadPath = $uploadDir . $fileName;
      if (move_uploaded_file($_FILES['driversLicense']['tmp_name'], $uploadPath)) {
        $driversLicense = $uploadPath;
      }
    }

    // Insert vehicle owner
    $stmt = $conn->prepare("INSERT INTO vehicleowner (OwnerID, fName, lName, mName, role, department, email, contact_num, college, course, year, section, academicYear, registrationStatus, drivers_license) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', ?)");
    $stmt->bind_param("sssssssissssss", $ownerID, $firstName, $lastName, $middleName, $userType, $college, $email, $contactNum, $college, $course, $yearLevel, $section, $academicYear, $driversLicense);
    $stmt->execute();

    // Handle vehicles
    if (isset($_POST['vehicleType']) && is_array($_POST['vehicleType'])) {
      for ($i = 0; $i < count($_POST['vehicleType']); $i++) {
        $vehicleType = $_POST['vehicleType'][$i];
        $manufacturer = $_POST['manufacturer'][$i];
        $model = $_POST['model'][$i];
        $color = $_POST['color'][$i];
        $plateNumber = $_POST['plateNumber'][$i];
        $numWheels = intval($_POST['numWheels'][$i]);
        $fuelType = $_POST['fuelType'][$i];
        $cubicCapacity = intval($_POST['cubicCapacity'][$i]);

        // Handle OR upload
        $orPath = null;
        if (isset($_FILES['officialReceipt']['tmp_name'][$i]) && $_FILES['officialReceipt']['error'][$i] == 0) {
          $uploadDir = 'OR_upload/';
          if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
          }
          $fileName = $ownerID . '_' . $plateNumber . '_OR.' . pathinfo($_FILES['officialReceipt']['name'][$i], PATHINFO_EXTENSION);
          $uploadPath = $uploadDir . $fileName;
          if (move_uploaded_file($_FILES['officialReceipt']['tmp_name'][$i], $uploadPath)) {
            $orPath = $uploadPath;
          }
        }

        // Handle CR upload
        $crPath = null;
        if (isset($_FILES['certRegistration']['tmp_name'][$i]) && $_FILES['certRegistration']['error'][$i] == 0) {
          $uploadDir = 'CR_upload/';
          if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
          }
          $fileName = $ownerID . '_' . $plateNumber . '_CR.' . pathinfo($_FILES['certRegistration']['name'][$i], PATHINFO_EXTENSION);
          $uploadPath = $uploadDir . $fileName;
          if (move_uploaded_file($_FILES['certRegistration']['tmp_name'][$i], $uploadPath)) {
            $crPath = $uploadPath;
          }
        }

        // Insert vehicle
        $stmt = $conn->prepare("INSERT INTO vehicle (plateNum, OwnerID, vehicleType, model, manufacturer, color, cubicCapacity, numOfWheels, fuelType, offical_receipt, cert_of_registration) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssssisss", $plateNumber, $ownerID, $vehicleType, $model, $manufacturer, $color, $cubicCapacity, $numWheels, $fuelType, $orPath, $crPath);
        $stmt->execute();
      }
    }

    echo "<script>alert('Registration submitted successfully!'); window.location.href='registration.php';</script>";
  } catch (Exception $e) {
    echo "<script>alert('Error: " . $e->getMessage() . "');</script>";
  }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
  <title>Registration Page</title>
  <link rel="stylesheet" href="../css/registration.css" />
  <link rel="stylesheet" href="../css/responsive.css" />
  <script src="../js/responsive.js"></script>
  <script src="../js/registration.js" defer></script>
</head>

<body>
  <header class="header">
    <div class="logo-title">
      <img src="logo.png" alt="Logo" class="site-logo" />
      <span class="site-name">
        <h3>
          <span class="highlight-yellow">ISATU </span><span class="highlight-blue">Vehicle Registration System</span>
        </h3>
      </span>
    </div>
    <div class="header-right">
      <button class="login-btn">Login</button>
      <button class="register-btn">Register Vehicle</button>
    </div>
  </header>

  <div class="container">
    <h2><span class="highlight">Vehicle</span> <span>Registration</span></h2>
    <p>
      Please fill out the form completely and upload the required documents.
    </p>

    <form method="POST" enctype="multipart/form-data">
      <section>
        <h3>User Information</h3>
        <div class="checkbox-group">
          <label>
            <input type="radio" name="userType" value="student" required />
            Student
          </label>
          <label>
            <input type="radio" name="userType" value="employee" required />
            Faculty
          </label>
          <label>
            <input type="radio" name="userType" value="employee" required />
            Non-Teaching Personnel
          </label>
        </div>

        <div class="grid-3">
          <input type="text" name="lastName" placeholder="Lastname" required />
          <input type="text" name="firstName" placeholder="Firstname" required />
          <input type="text" name="middleName" placeholder="Middlename" />
        </div>

        <div class="grid-2">
          <input type="email" name="email" placeholder="Email Address" required />
          <input type="text" name="contactNum" placeholder="Contact Number" required />
        </div>

        <div class="grid-3">
          <select name="college" required>
            <option value="CAS">(CAS) College of Arts and Sciences</option>
            <option value="CEA">(CEA) College of Engineering and Architecture</option>
            <option value="CCI">(CCI) College of Information and Informatics</option>
            <option value="COE">(COE) College of Education</option>
            <option value="CIT">(CIT) College of Industrial Technology</option>
          </select>
          <input type="text" name="course" placeholder="Course" required />
          <select name="academicYear" required>
            <option value="">Select Academic Year</option>
            <option value="2025-2026">2025-2026</option>
            <option value="2026-2027">2026-2027</option>
          </select>
        </div>

        <div class="grid-3">
          <select name="yearLevel">
            <option value="">Year Level (For Students)</option>
            <option value="1st">1st</option>
            <option value="2nd">2nd</option>
            <option value="3rd">3rd</option>
            <option value="4th">4th</option>
          </select>
          <input type="text" name="section" placeholder="Section" />
        </div>

        <div class="upload-box">
          <label>Upload Scanned Copy of Driver's License</label>
          <div class="upload-area">
            Drag and drop files here or
            <span class="browse">click to browse</span>
            <input type="file" name="driversLicense" accept="image/*,application/pdf" class="hidden" required />
          </div>
        </div>
      </section>

      <div id="vehicle-sections">
        <section class="vehicle-section">
          <div class="section-header">
            <h3>Vehicle Information</h3>
            <button type="button" class="btn-delete-vehicle">Remove</button>
          </div>

          <div class="grid-3">
            <select name="vehicleType[]" required>
              <option value="">Select Vehicle Type</option>
              <option value="Car">Car</option>
              <option value="Motorcycle">Motorcycle</option>
            </select>
            <input type="text" name="manufacturer[]" placeholder="Manufacturer" required />
            <input type="text" name="model[]" placeholder="Model" required />
          </div>

          <div class="grid-3">
            <input type="text" name="color[]" placeholder="Color" required />
            <input type="text" name="plateNumber[]" placeholder="Plate Number" required />
            <select name="numWheels[]" required>
              <option value="">Select Number of Wheels</option>
              <option value="2">2</option>
              <option value="4">4</option>
            </select>
          </div>

          <div class="grid-3">
            <select name="fuelType[]" required>
              <option value="">Select Fuel Type</option>
              <option value="Gasoline">Gasoline</option>
              <option value="Diesel">Diesel</option>
              <option value="Electric">Electric</option>
              <option value="Hybrid">Hybrid</option>
            </select>
            <input type="text" name="cubicCapacity[]" placeholder="Cubic Capacity" required />
          </div>

          <div class="upload-box">
            <label>Upload Scanned Copy of Official Receipt (OR)</label>
            <div class="upload-area">
              Drag and drop files here or
              <span class="browse">click to browse</span>
              <input type="file" name="officialReceipt[]" accept="image/*,application/pdf" class="hidden" required />
            </div>
          </div>

          <div class="upload-box">
            <label>Upload Scanned Copy of Certificate of Registration (CR)</label>
            <div class="upload-area">
              Drag and drop files here or
              <span class="browse">click to browse</span>
              <input type="file" name="certRegistration[]" accept="image/*,application/pdf" class="hidden" required />
            </div>
          </div>
        </section>
      </div>
      <button type="button" class="add-btn" id="addVehicleBtn">+ Add More Vehicle</button>

      <div class="agreement">
        <input type="checkbox" required /> I agree to the university's traffic regulations, security policies, and
        penalties for violations
      </div>
      <div class="button-row">
        <button type="submit" class="submit-btn">Submit Application</button>
      </div>
    </form>
  </div>
</body>

</html>