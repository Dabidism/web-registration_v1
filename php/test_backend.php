<?php
// Mock POST request array for testing process_registration.php
$_SERVER['REQUEST_METHOD'] = 'POST';

$_POST = [
    'userType' => 'faculty',
    'firstName' => 'Bob',
    'lastName' => 'Builder',
    'middleName' => '',
    'schoolID' => 'FB-1002',
    'email' => 'bob@builder.com',
    'contactNum' => '09998887777',
    'employment_type' => 'permanent',
    'college' => 'CAS',
    'course' => '',
    'academicYear' => '',
    'yearLevel' => '',
    'section' => '',
    'additionalDriverName' => 'Wendy Builder',
    'additionalDriverRelationship' => 'Spouse',
    'vehicleType' => ['Car', 'Motorcycle'],
    'manufacturer' => ['Ford', 'Honda'],
    'model' => ['Ranger', 'Click'],
    'color' => ['White', 'Black'],
    'plateNumber' => ['BBY-001', 'MC-002'],
    'fuelType' => ['Diesel', 'Gasoline'],
    'cubicCapacity' => ['', '150'],
    'numWheels' => ['4', '2'],
    'termsAccepted' => '1'
];

// Mock $_FILES for dummy files (to bypass isset checks)
$_FILES = [
    'driversLicense' => [
        'name' => 'fake_license.jpg',
        'type' => 'image/jpeg',
        'tmp_name' => 'C:/Windows/Temp/php123.tmp',
        'error' => 0,
        'size' => 1024
    ],
    'officialReceipt' => [
        'name' => ['fake_or1.jpg', 'fake_or2.jpg'],
        'type' => ['image/jpeg', 'image/jpeg'],
        'tmp_name' => ['C:/Windows/Temp/php124.tmp', 'C:/Windows/Temp/php125.tmp'],
        'error' => [0, 0],
        'size' => [1024, 1024]
    ],
    'certRegistration' => [
        'name' => ['fake_cr1.jpg', 'fake_cr2.jpg'],
        'type' => ['image/jpeg', 'image/jpeg'],
        'tmp_name' => ['C:/Windows/Temp/php126.tmp', 'C:/Windows/Temp/php127.tmp'],
        'error' => [0, 0],
        'size' => [1024, 1024]
    ]
];

// Temporarily redefine move_uploaded_file to always return true for testing, 
// since we don't have real temp files
ob_start();
require 'c:\xampp\htdocs\web-registration_v1\php\process_registration.php';
$output = ob_get_clean();

echo "Backend Response:\n";
echo $output;
?>
