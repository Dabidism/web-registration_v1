<?php
// Set content type to JSON
header('Content-Type: application/json');

// Include database connection
require_once '../dbConnection.php';

// Check if ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Owner ID is required'
    ]);
    exit;
}

$ownerId = $_GET['id'];

// Create database instance
$db = new Database();
$conn = $db->getConnection();

try {
    // Get all applications for this owner
    $query = "SELECT * FROM applications WHERE OwnerID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $ownerId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Application not found'
        ]);
        exit;
    }
    
    // Get the first row for user information
    $firstApp = $result->fetch_assoc();
    $result->data_seek(0); // Reset pointer for later use
    
    // Build HTML for application details
    $html = '<div class="application-header">
        <h3>Application Details</h3>
        <p>Review the applicant\'s information and documents.</p>
    </div>';
    
    // User information section
    $html .= '<div class="info-section">
        <div class="applicant-details">
            <h5>
                <span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="8" r="5" />
                        <path d="M20 21a8 8 0 0 0-16 0" />
                    </svg>
                </span>
                Personal Information
            </h5>
            <div class="details-container">
                <div class="details-row">
                    <div><strong>Full Name:</strong> ' . htmlspecialchars($firstApp['fName'] . ' ' . $firstApp['mName'] . ' ' . $firstApp['lName']) . '</div>
                    <div><strong>Email:</strong> ' . htmlspecialchars($firstApp['email']) . '</div>
                    <div><strong>Contact Number:</strong> ' . htmlspecialchars($firstApp['contact_num']) . '</div>
                    <div><strong>Office/College:</strong> ' . htmlspecialchars($firstApp['college']) . '</div>
                    <div><strong>Course:</strong> ' . htmlspecialchars($firstApp['course']) . '</div>';
    
    if (!empty($firstApp['year'])) {
        $html .= '<div><strong>Year Level:</strong> ' . htmlspecialchars($firstApp['year']) . '</div>';
    }
    
    if (!empty($firstApp['section'])) {
        $html .= '<div><strong>Section:</strong> ' . htmlspecialchars($firstApp['section']) . '</div>';
    }
    
    $html .= '<div><strong>Academic Year:</strong> ' . htmlspecialchars($firstApp['academicYear']) . '</div>
                </div>
            </div>
        </div>';
    
    // Driver's License
    if (!empty($firstApp['drivers_license'])) {
        $html .= '<div class="document-preview">
            <h5>
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="4" width="18" height="16" rx="2" />
                    <circle cx="9" cy="10" r="2" />
                    <path d="M15 8h2" />
                    <path d="M15 12h2" />
                    <path d="M7 16h10" />
                </svg>
                Driver\'s License
            </h5>
            <div class="document-thumbnail-container">
                <img src="../../' . htmlspecialchars($firstApp['drivers_license']) . '" alt="Driver\'s License" class="document-image" data-src="../../' . htmlspecialchars($firstApp['drivers_license']) . '">
            </div>
            <button class="view-document-btn" data-src="../../' . htmlspecialchars($firstApp['drivers_license']) . '">View Full Document</button>
        </div>';
    }
    
    // Vehicle information - loop through all applications for this owner
    $index = 1;
    while ($app = $result->fetch_assoc()) {
        $html .= '<div class="vehicle-details">
            <h5>
                <span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M19 17h2c.6 0 1-.4 1-1v-3c0-.9-.7-1.7-1.5-1.9C18.7 10.6 16 10 16 10s-1.3-1.4-2.2-2.3c-.5-.4-1.1-.7-1.8-.7H5c-.6 0-1.1.4-1.4.9l-1.4 2.9A3.7 3.7 0 0 0 2 12v4c0 .6.4 1 1 1h2" />
                        <circle cx="7" cy="17" r="2" />
                        <path d="M9 17h6" />
                        <circle cx="17" cy="17" r="2" />
                    </svg>
                </span>
                Vehicle ' . $index . ' Information
            </h5>
            <div class="details-container">
                <div class="details-row">
                    <div><strong>Vehicle Type:</strong> ' . htmlspecialchars($app['vehicleType']) . '</div>
                    <div><strong>Plate Number:</strong> ' . htmlspecialchars($app['plateNum']) . '</div>
                    <div><strong>Model:</strong> ' . htmlspecialchars($app['model']) . '</div>
                    <div><strong>Brand/Manufacturer:</strong> ' . htmlspecialchars($app['manufacturer']) . '</div>
                    <div><strong>Color:</strong> ' . htmlspecialchars($app['color']) . '</div>
                    <div><strong>Number of Wheels:</strong> ' . htmlspecialchars($app['numOfWheels']) . '</div>
                    <div><strong>Fuel Type:</strong> ' . htmlspecialchars($app['fuelType']) . '</div>
                    <div><strong>Cubic Capacity:</strong> ' . htmlspecialchars($app['cubicCapacity']) . '</div>
                </div>
            </div>';
        
        // Vehicle documents
        $html .= '<div class="documents-section">
            <h5>
                <span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z" />
                        <path d="M14 2v4a2 2 0 0 0 2 2h4" />
                        <path d="M10 9H8" />
                        <path d="M16 13H8" />
                        <path d="M16 17H8" />
                    </svg>
                </span>
                Required Documents
            </h5>
            <div class="documents-container">';
        
        if (!empty($app['offical_receipt'])) {
            $html .= '<div class="document-item">
                <span class="document-name">Official Receipt (OR)</span>
                <div class="document-thumbnail-container">
                    <img src="../../' . htmlspecialchars($app['offical_receipt']) . '" alt="Official Receipt" class="document-image" data-src="../../' . htmlspecialchars($app['offical_receipt']) . '">
                </div>
                <button class="view-document-btn" data-src="../../' . htmlspecialchars($app['offical_receipt']) . '">View Full Document</button>
            </div>';
        }
        
        if (!empty($app['cert_of_registration'])) {
            $html .= '<div class="document-item">
                <span class="document-name">Certificate of Registration (CR)</span>
                <div class="document-thumbnail-container">
                    <img src="../../' . htmlspecialchars($app['cert_of_registration']) . '" alt="Certificate of Registration" class="document-image" data-src="../../' . htmlspecialchars($app['cert_of_registration']) . '">
                </div>
                <button class="view-document-btn" data-src="../../' . htmlspecialchars($app['cert_of_registration']) . '">View Full Document</button>
            </div>';
        }
        
        $html .= '</div>
        </div>';
        
        $html .= '</div>'; // End vehicle-details
        $index++;
    }
    
    $html .= '</div>'; // End info-section
    
    echo json_encode([
        'success' => true,
        'html' => $html
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
} finally {
    $db->close();
}