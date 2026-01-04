-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 04, 2026 at 07:54 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `gate_pass_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `accesslog`
--

CREATE TABLE `accesslog` (
  `logID` int(11) NOT NULL,
  `userID` varchar(10) DEFAULT NULL,
  `action` enum('login','logout','failed_login','update','delete','view') NOT NULL,
  `description` text DEFAULT NULL,
  `timestamp` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `accesslog`
--

INSERT INTO `accesslog` (`logID`, `userID`, `action`, `description`, `timestamp`) VALUES
(120, 'U000', 'login', 'User logged in successfully', '2026-01-03 01:38:01'),
(121, 'U001', 'login', 'Entry System - Guard guard logged in successfully', '2026-01-03 01:42:31'),
(122, 'U001', 'logout', 'Entry System - Guard guard logged out', '2026-01-03 01:42:52'),
(123, 'U001', 'login', 'Entry System - Guard guard logged in successfully', '2026-01-03 01:48:27'),
(124, 'U001', 'logout', 'Entry System - Guard guard logged out', '2026-01-03 01:57:44'),
(126, 'U000', 'logout', 'User logged out', '2026-01-04 14:54:25');

-- --------------------------------------------------------

--
-- Table structure for table `applications`
--

CREATE TABLE `applications` (
  `applicationID` int(11) NOT NULL,
  `OwnerID` varchar(10) DEFAULT NULL,
  `schoolID` varchar(50) DEFAULT NULL,
  `fName` varchar(50) DEFAULT NULL,
  `lName` varchar(50) DEFAULT NULL,
  `mName` varchar(50) DEFAULT NULL,
  `role` varchar(30) DEFAULT NULL,
  `employment_type` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `contact_num` varchar(20) DEFAULT NULL,
  `college` varchar(100) DEFAULT NULL,
  `course` varchar(50) DEFAULT NULL,
  `year` varchar(10) DEFAULT NULL,
  `section` varchar(10) DEFAULT NULL,
  `academicYear` varchar(20) DEFAULT NULL,
  `drivers_license` varchar(255) DEFAULT NULL,
  `additional_driver_name` varchar(100) DEFAULT NULL,
  `additional_driver_relationship` varchar(50) DEFAULT NULL,
  `plateNum` varchar(20) DEFAULT NULL,
  `vehicleType` varchar(50) DEFAULT NULL,
  `model` varchar(50) DEFAULT NULL,
  `manufacturer` varchar(50) DEFAULT NULL,
  `color` varchar(30) DEFAULT NULL,
  `cubicCapacity` int(11) DEFAULT NULL,
  `numOfWheels` int(11) DEFAULT NULL,
  `fuelType` varchar(30) DEFAULT NULL,
  `offical_receipt` varchar(255) DEFAULT NULL,
  `cert_of_registration` varchar(255) DEFAULT NULL,
  `registrationStatus` enum('pending','approved','rejected') DEFAULT 'pending',
  `applicationDate` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `applications`
--

INSERT INTO `applications` (`applicationID`, `OwnerID`, `schoolID`, `fName`, `lName`, `mName`, `role`, `employment_type`, `email`, `contact_num`, `college`, `course`, `year`, `section`, `academicYear`, `drivers_license`, `additional_driver_name`, `additional_driver_relationship`, `plateNum`, `vehicleType`, `model`, `manufacturer`, `color`, `cubicCapacity`, `numOfWheels`, `fuelType`, `offical_receipt`, `cert_of_registration`, `registrationStatus`, `applicationDate`) VALUES
(12, 'A001', '2022-4391-A', 'David Natan', 'Apruebo', 'Villegas', 'student', NULL, 'davidnatan.apruebo@students.isatu.edu.ph', '09503903276', 'CCI', 'Bachelor of Science in Information Technology', '5th', 'A', '2026-2027', 'DL_upload/A001_1767509100_license.jpg', '', NULL, 'NBC1234', 'Car', 'Y', 'Tesla', 'Black', NULL, 4, 'Gasoline', 'OR_upload/A001_NBC1234_1767509100_OR.jpg', 'CR_upload/A001_NBC1234_1767509100_CR.jpg', 'approved', '2026-01-04 14:45:00');

-- --------------------------------------------------------

--
-- Table structure for table `entryexitlog`
--

CREATE TABLE `entryexitlog` (
  `logID` varchar(10) NOT NULL,
  `plateNum` varchar(20) DEFAULT NULL,
  `entryTime` datetime DEFAULT NULL,
  `exitTime` datetime DEFAULT NULL,
  `status` enum('entered','exited','denied') DEFAULT NULL,
  `gateLocation` enum('Old Site','New Site') DEFAULT 'Old Site'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `historical_log`
--

CREATE TABLE `historical_log` (
  `logID` varchar(36) NOT NULL,
  `plateNum` varchar(20) NOT NULL,
  `entryTime` timestamp NOT NULL DEFAULT current_timestamp(),
  `exitTime` timestamp NULL DEFAULT NULL,
  `status` enum('entered','exited') DEFAULT 'entered',
  `handled` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `parkingstatus`
--

CREATE TABLE `parkingstatus` (
  `id` int(11) NOT NULL,
  `totalCapacity` int(11) DEFAULT 200,
  `allocatedStudents` int(11) DEFAULT 100,
  `allocatedFaculty` int(11) DEFAULT 50,
  `allocatedStaff` int(11) DEFAULT 30,
  `allocatedGuests` int(11) DEFAULT 20,
  `currentOccupiedStudents` int(11) DEFAULT 0,
  `currentOccupiedFaculty` int(11) DEFAULT 0,
  `currentOccupiedStaff` int(11) DEFAULT 0,
  `currentOccupiedGuests` int(11) DEFAULT 0,
  `lastUpdated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `studentLimit` int(11) DEFAULT 20,
  `facultyLimit` int(11) DEFAULT 160,
  `guestLimit` int(11) DEFAULT 20
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `parkingstatus`
--

INSERT INTO `parkingstatus` (`id`, `totalCapacity`, `allocatedStudents`, `allocatedFaculty`, `allocatedStaff`, `allocatedGuests`, `currentOccupiedStudents`, `currentOccupiedFaculty`, `currentOccupiedStaff`, `currentOccupiedGuests`, `lastUpdated`, `studentLimit`, `facultyLimit`, `guestLimit`) VALUES
(2, 200, 40, 96, 40, 24, 0, 0, 0, 0, '2026-01-04 06:49:53', 20, 160, 20),
(3, 200, 100, 50, 30, 20, 0, 0, 0, 0, '2026-01-02 17:37:42', 20, 160, 20),
(4, 200, 100, 50, 30, 20, 0, 0, 0, 0, '2026-01-02 17:43:00', 20, 160, 20),
(5, 200, 100, 50, 30, 20, 0, 0, 0, 0, '2026-01-02 17:43:02', 20, 160, 20),
(6, 200, 100, 50, 30, 20, 0, 0, 0, 0, '2026-01-02 17:43:43', 20, 160, 20),
(7, 200, 100, 50, 30, 20, 0, 0, 0, 0, '2026-01-02 17:43:46', 20, 160, 20),
(8, 200, 100, 50, 30, 20, 0, 0, 0, 0, '2026-01-02 17:46:56', 20, 160, 20);

-- --------------------------------------------------------

--
-- Table structure for table `rfidtag`
--

CREATE TABLE `rfidtag` (
  `stickerID` varchar(25) NOT NULL,
  `rfidCode` varchar(50) NOT NULL,
  `issuedAt` datetime DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'inactive',
  `expirationDate` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `temporaryvehiclepass`
--

CREATE TABLE `temporaryvehiclepass` (
  `passID` varchar(10) NOT NULL,
  `visitorID` varchar(10) DEFAULT NULL,
  `issueDate` date DEFAULT NULL,
  `expiryDate` date DEFAULT NULL,
  `status` enum('active','expired') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `userID` varchar(10) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`userID`, `username`, `password`, `role`) VALUES
('U000', 'admin', '$2b$12$IJsZGZpGAoobVdFE3Cf9Y.uU2XYtIe3EM5TxHWl9hfTyG1t2s4P.C', 'SSEDMMO Admin'),
('U001', 'guard', '$2y$10$BzJIT2iuzz.PEN4BuIBkO.e1eqWXj5Cgjzds/YqeX73ozCMYiLyxi', 'guard'),
('U002', 'staff', '$2y$10$DVZN9ECS/ic0TtZkvtsrW.8.TAU.iZa/zt9JoW22TffmpMnTCQlue', 'SSEDMMO Staff');

-- --------------------------------------------------------

--
-- Table structure for table `vehicle`
--

CREATE TABLE `vehicle` (
  `plateNum` varchar(20) NOT NULL,
  `OwnerID` varchar(10) DEFAULT NULL,
  `stickerID` varchar(25) DEFAULT NULL,
  `visitorID` varchar(10) DEFAULT NULL,
  `vehicleType` varchar(50) DEFAULT NULL,
  `model` varchar(50) DEFAULT NULL,
  `manufacturer` varchar(50) DEFAULT NULL,
  `color` varchar(30) DEFAULT NULL,
  `cubicCapacity` int(11) DEFAULT NULL,
  `numOfWheels` int(11) DEFAULT NULL,
  `fuelType` varchar(30) DEFAULT NULL,
  `offical_receipt` varchar(255) DEFAULT NULL,
  `cert_of_registration` varchar(255) DEFAULT NULL,
  `carpassid` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vehicle`
--

INSERT INTO `vehicle` (`plateNum`, `OwnerID`, `stickerID`, `visitorID`, `vehicleType`, `model`, `manufacturer`, `color`, `cubicCapacity`, `numOfWheels`, `fuelType`, `offical_receipt`, `cert_of_registration`, `carpassid`) VALUES
('NBC1234', 'A001', NULL, NULL, 'Car', 'Y', 'Tesla', 'White', NULL, 4, 'Gasoline', 'OR_upload/A001_NBC1234_1767509100_OR.jpg', 'CR_upload/A001_NBC1234_1767509100_CR.jpg', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `vehicleowner`
--

CREATE TABLE `vehicleowner` (
  `OwnerID` varchar(10) NOT NULL,
  `schoolID` varchar(50) DEFAULT NULL,
  `fName` varchar(50) DEFAULT NULL,
  `lName` varchar(50) DEFAULT NULL,
  `mName` varchar(50) DEFAULT NULL,
  `role` varchar(30) DEFAULT NULL,
  `employment_type` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `contact_num` varchar(20) DEFAULT NULL,
  `college` varchar(100) DEFAULT NULL,
  `course` varchar(50) DEFAULT NULL,
  `year` varchar(10) DEFAULT NULL,
  `section` varchar(10) DEFAULT NULL,
  `academicYear` varchar(20) DEFAULT NULL,
  `registrationStatus` enum('pending','approved','rejected') DEFAULT 'pending',
  `approvalTimestamp` datetime DEFAULT NULL,
  `drivers_license` varchar(255) DEFAULT NULL,
  `additional_driver_name` varchar(100) DEFAULT NULL,
  `additional_driver_relationship` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vehicleowner`
--

INSERT INTO `vehicleowner` (`OwnerID`, `schoolID`, `fName`, `lName`, `mName`, `role`, `employment_type`, `email`, `contact_num`, `college`, `course`, `year`, `section`, `academicYear`, `registrationStatus`, `approvalTimestamp`, `drivers_license`, `additional_driver_name`, `additional_driver_relationship`) VALUES
('A001', NULL, 'David Natan', 'Apruebo', 'Villegas', 'student', NULL, 'davidnatan.apruebo@students.isatu.edu.ph', '09503903276', 'CCI', 'Bachelor of Science in Information Technology', '5th', 'A', '2026-2027', 'approved', '2026-01-04 07:45:21', 'DL_upload/A001_1767509100_license.jpg', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `vehicle_events`
--

CREATE TABLE `vehicle_events` (
  `id` int(11) NOT NULL,
  `plate_num` varchar(20) NOT NULL,
  `event_type` enum('entry','exit') NOT NULL,
  `event_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`event_data`)),
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `handled` tinyint(4) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `violations`
--

CREATE TABLE `violations` (
  `violationID` varchar(10) NOT NULL,
  `plateNum` varchar(20) NOT NULL,
  `violationType` enum('unauthorized_entry','expired_permit','invalid_parking','speeding','no_sticker','other') NOT NULL,
  `description` text DEFAULT NULL,
  `location` varchar(100) DEFAULT NULL,
  `violationDate` datetime DEFAULT current_timestamp(),
  `reportedBy` varchar(10) DEFAULT NULL,
  `status` enum('pending','resolved','dismissed') DEFAULT 'pending',
  `resolvedDate` datetime DEFAULT NULL,
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `visitor`
--

CREATE TABLE `visitor` (
  `visitorID` varchar(10) NOT NULL,
  `passID` varchar(10) DEFAULT NULL,
  `fullName` varchar(100) DEFAULT NULL,
  `plateNum` varchar(20) DEFAULT NULL,
  `purposeOfVisit` text DEFAULT NULL,
  `createdAt` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `visitorlog`
--

CREATE TABLE `visitorlog` (
  `visitorLogID` varchar(10) NOT NULL,
  `plateNum` varchar(20) DEFAULT NULL,
  `entryTime` datetime DEFAULT NULL,
  `exitTime` datetime DEFAULT NULL,
  `status` enum('entered','exited','denied') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `accesslog`
--
ALTER TABLE `accesslog`
  ADD PRIMARY KEY (`logID`),
  ADD KEY `accesslog_ibfk_user` (`userID`);

--
-- Indexes for table `applications`
--
ALTER TABLE `applications`
  ADD PRIMARY KEY (`applicationID`),
  ADD KEY `OwnerID_idx` (`OwnerID`);

--
-- Indexes for table `entryexitlog`
--
ALTER TABLE `entryexitlog`
  ADD PRIMARY KEY (`logID`),
  ADD KEY `plateNum_idx` (`plateNum`);

--
-- Indexes for table `historical_log`
--
ALTER TABLE `historical_log`
  ADD PRIMARY KEY (`logID`),
  ADD KEY `idx_plate_status` (`plateNum`,`status`),
  ADD KEY `idx_entry_time` (`entryTime`),
  ADD KEY `idx_handled` (`handled`);

--
-- Indexes for table `parkingstatus`
--
ALTER TABLE `parkingstatus`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `rfidtag`
--
ALTER TABLE `rfidtag`
  ADD PRIMARY KEY (`stickerID`),
  ADD UNIQUE KEY `unique_rfid_code` (`rfidCode`);

--
-- Indexes for table `temporaryvehiclepass`
--
ALTER TABLE `temporaryvehiclepass`
  ADD PRIMARY KEY (`passID`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`userID`),
  ADD UNIQUE KEY `username_unique` (`username`);

--
-- Indexes for table `vehicle`
--
ALTER TABLE `vehicle`
  ADD PRIMARY KEY (`plateNum`),
  ADD KEY `OwnerID_idx` (`OwnerID`),
  ADD KEY `stickerID_idx` (`stickerID`),
  ADD KEY `visitorID_idx` (`visitorID`);

--
-- Indexes for table `vehicleowner`
--
ALTER TABLE `vehicleowner`
  ADD PRIMARY KEY (`OwnerID`);

--
-- Indexes for table `vehicle_events`
--
ALTER TABLE `vehicle_events`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_handled_timestamp` (`handled`,`timestamp`),
  ADD KEY `idx_plate_timestamp` (`plate_num`,`timestamp`);

--
-- Indexes for table `violations`
--
ALTER TABLE `violations`
  ADD PRIMARY KEY (`violationID`),
  ADD KEY `violations_plate_idx` (`plateNum`),
  ADD KEY `violations_reporter_idx` (`reportedBy`),
  ADD KEY `violations_status_idx` (`status`),
  ADD KEY `violations_date_idx` (`violationDate`);

--
-- Indexes for table `visitor`
--
ALTER TABLE `visitor`
  ADD PRIMARY KEY (`visitorID`),
  ADD KEY `passID_idx` (`passID`);

--
-- Indexes for table `visitorlog`
--
ALTER TABLE `visitorlog`
  ADD PRIMARY KEY (`visitorLogID`),
  ADD KEY `visitorlog_plate_idx` (`plateNum`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `accesslog`
--
ALTER TABLE `accesslog`
  MODIFY `logID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=127;

--
-- AUTO_INCREMENT for table `applications`
--
ALTER TABLE `applications`
  MODIFY `applicationID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `parkingstatus`
--
ALTER TABLE `parkingstatus`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `vehicle_events`
--
ALTER TABLE `vehicle_events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `accesslog`
--
ALTER TABLE `accesslog`
  ADD CONSTRAINT `accesslog_ibfk_user` FOREIGN KEY (`userID`) REFERENCES `user` (`userID`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `entryexitlog`
--
ALTER TABLE `entryexitlog`
  ADD CONSTRAINT `entryexitlog_ibfk_vehicle` FOREIGN KEY (`plateNum`) REFERENCES `vehicle` (`plateNum`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `vehicle`
--
ALTER TABLE `vehicle`
  ADD CONSTRAINT `vehicle_ibfk_owner` FOREIGN KEY (`OwnerID`) REFERENCES `vehicleowner` (`OwnerID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `vehicle_ibfk_visitor` FOREIGN KEY (`visitorID`) REFERENCES `visitor` (`visitorID`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `violations`
--
ALTER TABLE `violations`
  ADD CONSTRAINT `violations_ibfk_reporter` FOREIGN KEY (`reportedBy`) REFERENCES `user` (`userID`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `violations_ibfk_vehicle` FOREIGN KEY (`plateNum`) REFERENCES `vehicle` (`plateNum`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `visitor`
--
ALTER TABLE `visitor`
  ADD CONSTRAINT `visitor_ibfk_pass` FOREIGN KEY (`passID`) REFERENCES `temporaryvehiclepass` (`passID`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `visitorlog`
--
ALTER TABLE `visitorlog`
  ADD CONSTRAINT `visitorlog_ibfk_vehicle` FOREIGN KEY (`plateNum`) REFERENCES `vehicle` (`plateNum`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
