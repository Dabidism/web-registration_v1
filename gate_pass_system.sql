-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 01, 2026 at 12:41 PM
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
(16, 'U000', 'login', 'User logged in successfully', '2025-10-01 21:24:34'),
(17, 'U000', 'logout', 'User logged out', '2025-10-01 22:15:13'),
(18, 'U000', 'logout', 'User logged out', '2025-10-01 22:58:45'),
(19, 'U000', 'login', 'User logged in successfully', '2025-10-01 22:58:54'),
(20, 'U000', 'logout', 'User logged out', '2025-10-03 15:59:13'),
(21, 'U000', 'login', 'User logged in successfully', '2025-10-06 09:52:33'),
(22, 'U000', 'login', 'User logged in successfully', '2025-10-20 09:56:45'),
(23, 'U000', 'logout', 'User logged out', '2025-10-20 15:52:52'),
(26, 'U000', 'login', 'User logged in successfully', '2025-10-22 14:02:09'),
(27, 'U000', 'login', 'User logged in successfully', '2025-11-10 17:09:44'),
(28, 'U000', 'logout', 'User logged out', '2025-11-12 09:56:07'),
(31, 'U000', 'login', 'User logged in successfully', '2025-11-12 09:56:44'),
(32, NULL, 'login', 'Guard guard logged in successfully', '2025-11-17 15:27:23'),
(33, 'U000', 'logout', 'User logged out', '2025-11-17 15:29:31'),
(34, NULL, 'login', 'User logged in successfully', '2025-11-17 15:29:36'),
(35, NULL, 'logout', 'User logged out', '2025-11-17 15:30:02'),
(36, 'U000', 'login', 'User logged in successfully', '2025-11-17 15:30:09'),
(38, NULL, 'login', 'Guard john logged in successfully', '2025-11-17 15:35:44'),
(39, NULL, 'logout', 'Guard john logged out', '2025-11-17 15:36:05'),
(40, NULL, 'login', 'Guard guard logged in successfully', '2025-11-17 15:38:58'),
(41, NULL, 'logout', 'Guard guard logged out', '2025-11-17 15:41:38'),
(42, NULL, 'login', 'Guard guard logged in successfully', '2025-11-17 15:41:53'),
(43, NULL, 'logout', 'Guard guard logged out', '2025-11-17 15:42:55'),
(44, NULL, 'login', 'Guard john logged in successfully', '2025-11-17 15:44:48'),
(45, NULL, 'logout', 'Guard john logged out', '2025-11-17 15:59:52'),
(46, NULL, 'login', 'Guard john logged in successfully', '2025-11-17 16:00:11'),
(47, NULL, 'logout', 'Guard john logged out', '2025-11-17 16:00:31'),
(48, 'U000', 'logout', 'User logged out', '2025-11-19 17:47:07'),
(49, 'U000', 'login', 'User logged in successfully', '2025-11-19 17:49:19'),
(50, 'U000', 'logout', 'User logged out', '2025-11-19 17:49:42'),
(51, 'U000', 'login', 'User logged in successfully', '2025-11-19 17:50:12'),
(52, NULL, 'login', 'Guard guard logged in successfully', '2025-11-19 18:01:16'),
(53, NULL, 'logout', 'Guard guard logged out', '2025-11-28 05:13:18'),
(54, NULL, 'login', 'Entry Guard guard logged in successfully', '2025-11-28 05:13:27'),
(55, 'U000', 'logout', 'User logged out', '2025-11-29 20:33:08'),
(56, 'U000', 'login', 'User logged in successfully', '2025-11-29 20:33:12'),
(57, 'U000', 'logout', 'User logged out', '2025-11-29 20:33:21'),
(58, NULL, 'login', 'Exit System - Guard guard logged in successfully', '2025-11-30 20:29:45'),
(59, NULL, 'logout', 'Exit System - Guard guard logged out', '2025-11-30 20:30:13'),
(60, NULL, 'login', 'Exit System - Guard guard logged in successfully', '2025-11-30 20:32:30'),
(61, NULL, 'logout', 'Exit System - Guard guard logged out', '2025-11-30 20:40:30'),
(62, NULL, 'login', 'Entry System - Guard guard logged in successfully', '2025-11-30 20:40:37'),
(63, NULL, 'logout', 'Entry System - Guard guard logged out', '2025-11-30 20:42:00'),
(64, NULL, 'login', 'Exit System - Guard guard logged in successfully', '2025-11-30 20:42:07'),
(65, NULL, 'logout', 'Exit System - Guard guard logged out', '2025-11-30 20:53:09'),
(66, NULL, 'login', 'Entry System - Guard guard logged in successfully', '2025-11-30 20:53:23'),
(67, NULL, 'login', 'Exit System - Guard guard logged in successfully (already logged in from another session)', '2025-11-30 20:53:28'),
(68, NULL, 'logout', 'Exit System - Guard guard logged out', '2025-11-30 20:53:38'),
(69, NULL, 'login', 'Exit System - Guard guard logged in successfully', '2025-11-30 20:56:40'),
(70, NULL, 'logout', 'Exit System - Guard guard logged out', '2025-11-30 20:57:27'),
(71, NULL, 'login', 'Entry System - Guard guard logged in successfully', '2025-11-30 20:57:32'),
(72, NULL, 'logout', 'Entry System - Guard guard logged out', '2025-11-30 20:57:35'),
(73, NULL, 'login', 'Entry System - Guard guard logged in successfully', '2025-11-30 21:18:46'),
(74, NULL, 'logout', 'Entry System - Guard guard logged out', '2025-11-30 21:19:27'),
(75, NULL, 'login', 'Entry System - Guard guard logged in successfully', '2025-11-30 21:22:41'),
(76, NULL, 'login', 'Exit System - Guard john logged in successfully', '2025-11-30 21:24:00'),
(77, 'U000', 'login', 'User logged in successfully', '2025-11-30 21:26:46'),
(78, NULL, 'logout', 'Exit System - Guard john logged out', '2025-11-30 21:37:26'),
(79, NULL, 'login', 'Entry System - Guard guard2 logged in successfully', '2025-11-30 21:42:43'),
(80, NULL, 'login', 'Exit System - Guard guard2 logged in successfully', '2025-11-30 21:43:07'),
(81, NULL, 'logout', 'Exit System - Guard guard2 logged out', '2025-12-01 00:12:28'),
(82, NULL, 'login', 'Entry System - Guard guard2 logged in successfully', '2025-12-01 00:13:37'),
(83, NULL, 'logout', 'Entry System - Guard guard2 logged out', '2025-12-01 00:13:44'),
(84, NULL, 'login', 'Entry System - Guard guard2 logged in successfully', '2025-12-01 00:19:37'),
(85, NULL, 'logout', 'Entry System - Guard guard2 logged out', '2025-12-01 00:20:01'),
(86, NULL, 'login', 'Entry System - Guard guard2 logged in successfully', '2025-12-01 00:21:27'),
(87, NULL, 'login', 'Exit System - Guard guard2 logged in successfully', '2025-12-01 00:21:36'),
(88, 'U000', 'logout', 'User logged out', '2025-12-01 01:08:10'),
(89, NULL, 'failed_login', 'Failed login attempt for username: admin', '2025-12-01 01:08:14'),
(90, 'U000', 'login', 'User logged in successfully', '2025-12-01 01:08:18'),
(91, NULL, 'login', 'Entry System - Guard guard3 logged in successfully', '2025-12-01 02:05:02'),
(92, 'U000', 'failed_login', 'Access denied for role: admin', '2026-03-01 18:33:49'),
(93, 'U000', 'failed_login', 'Access denied for role: admin', '2026-03-01 18:33:56'),
(94, NULL, 'failed_login', 'Failed login attempt for username: staff', '2026-03-01 18:34:03'),
(95, 'U000', 'failed_login', 'Access denied for role: admin', '2026-03-01 18:34:45'),
(96, 'U000', 'failed_login', 'Access denied for role: ', '2026-03-01 18:42:59'),
(97, 'U000', 'failed_login', 'Access denied for role: ', '2026-03-01 18:43:03'),
(98, 'U000', 'failed_login', 'Access denied for role: ', '2026-03-01 18:43:06'),
(99, 'U000', 'login', 'User logged in successfully', '2026-03-01 18:47:26'),
(100, 'U000', 'logout', 'User logged out', '2026-03-01 18:47:32'),
(101, NULL, 'failed_login', 'Failed login attempt for username: staff', '2026-03-01 18:47:38'),
(102, 'U000', 'login', 'User logged in successfully', '2026-03-01 18:47:41'),
(103, 'U000', 'logout', 'User logged out', '2026-03-01 18:48:00'),
(104, 'U001', 'login', 'User logged in successfully', '2026-03-01 18:48:04'),
(105, 'U001', 'logout', 'User logged out', '2026-03-01 18:48:10'),
(106, 'U000', 'login', 'User logged in successfully', '2026-03-01 18:48:14');

-- --------------------------------------------------------

--
-- Table structure for table `applications`
--

CREATE TABLE `applications` (
  `applicationID` int(11) NOT NULL,
  `OwnerID` varchar(10) DEFAULT NULL,
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

INSERT INTO `applications` (`applicationID`, `OwnerID`, `fName`, `lName`, `mName`, `role`, `employment_type`, `email`, `contact_num`, `college`, `course`, `year`, `section`, `academicYear`, `drivers_license`, `plateNum`, `vehicleType`, `model`, `manufacturer`, `color`, `cubicCapacity`, `numOfWheels`, `fuelType`, `offical_receipt`, `cert_of_registration`, `registrationStatus`, `applicationDate`) VALUES
(1, 'A012', 'David Natan', 'Apruebo', 'Villegas', 'student', NULL, 'davidnatan.apruebo@students.isatu.edu.ph', '09503903276', 'CCI', 'Bachelor of Science in Information Technology', '4th', 'A', '2025-2026', 'DL_upload/A001_license.jpg', 'NBC 1234', 'Car', 'Prius', 'Toyota', 'Black', NULL, 4, 'Diesel', 'OR_upload/A001_NBC 1234_OR.jpg', 'CR_upload/A001_NBC 1234_CR.jpg', 'approved', '2025-11-11 14:56:55'),
(2, 'A013', 'David', 'Smurf', '', 'student', NULL, 'dvdsmrf@gmail.com', '09503903276', 'CCI', 'Bachelor of Science in Information Technology', '4th', 'A', '2025-2026', 'DL_upload/A001_license.jpg', 'NAT 0921', 'Motorcycle', 'CF 152', 'Keeway', 'Black', 6000, 2, 'Gasoline', 'OR_upload/A001_NAT 0921_OR.jpg', 'CR_upload/A001_NAT 0921_CR.jpg', 'approved', '2025-11-11 15:02:43'),
(3, 'A014', 'Reynalds', 'Ilangos', '', 'faculty', 'permanent', 'dvdsmrf@gmail.com', '09503903276', 'CCI', '', '', '', '', 'DL_upload/A001_license.jpg', 'DBA 4658', 'Motorcycle', 'PG-1', 'Toyota', 'Brown', 6000, 2, 'Gasoline', 'OR_upload/A001_DBA 4658_OR.jpg', 'CR_upload/A001_DBA 4658_CR.jpg', 'approved', '2025-11-11 15:15:58'),
(4, NULL, 'Kapid', 'G', 'P', 'student', NULL, 'shekinah.gayonoche@students.isatu.edu.ph', '09123456789', 'COE', 'Bachelor of Elementary Education', '1st', 'F', '2025-2026', 'DL_upload/A001_license.jpeg', 'DFG098', 'Car', 'Crius', 'Toyota', 'Red', NULL, 4, 'Gasoline', 'OR_upload/A001_DFG098_OR.jpeg', 'CR_upload/A001_DFG098_CR.jpeg', 'approved', '2025-11-12 09:50:42'),
(5, NULL, 'Kapid', 'G', 'P', 'student', NULL, 'shekinah.gayonoche@students.isatu.edu.ph', '09123456789', 'COE', 'Bachelor of Elementary Education', '1st', 'F', '2025-2026', 'DL_upload/A001_license.jpeg', 'DFG098', 'Car', 'Crius', 'Toyota', 'Red', NULL, 4, 'Gasoline', 'OR_upload/A001_DFG098_OR.jpeg', 'CR_upload/A001_DFG098_CR.jpeg', 'approved', '2025-11-12 09:50:42'),
(6, 'A018', 'Chris', 'POrs', '3vles', 'student', NULL, 'davidnatan.apruebo@students.isatu.edu.ph', '318135132', 'CCI', 'Bachelor of Science in Computer Science', '4th', 'A', '2025-2026', 'DL_upload/A001_license.jpg', 'NBC5678', 'Motorcycle', 'Y', 'Tesla', 'black', 450, 2, 'Hybrid', 'OR_upload/A001_NBC5678_OR.jpg', 'CR_upload/A001_NBC5678_CR.jpg', 'approved', '2025-11-29 13:31:12');

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

--
-- Dumping data for table `entryexitlog`
--

INSERT INTO `entryexitlog` (`logID`, `plateNum`, `entryTime`, `exitTime`, `status`, `gateLocation`) VALUES
('L001', 'NBC1234', '2025-11-28 03:28:32', '2025-11-28 03:48:17', 'exited', 'Old Site'),
('L002', 'NBC1234', '2025-11-28 04:18:53', '2025-11-28 04:19:46', 'exited', 'Old Site'),
('L003', 'NBC1234', '2025-11-28 04:32:49', '2025-11-28 04:41:23', 'exited', 'Old Site'),
('L004', 'NBC1234', '2025-11-28 04:51:31', '2025-11-28 04:52:10', 'exited', 'Old Site'),
('L005', 'DBA4658', '2025-11-29 14:24:48', '2025-11-29 14:49:07', 'exited', 'Old Site');

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

--
-- Dumping data for table `historical_log`
--

INSERT INTO `historical_log` (`logID`, `plateNum`, `entryTime`, `exitTime`, `status`, `handled`) VALUES
('L001', 'DBA4658', '2025-11-30 14:17:18', NULL, 'entered', 0);

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
  `lastUpdated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `parkingstatus`
--

INSERT INTO `parkingstatus` (`id`, `totalCapacity`, `allocatedStudents`, `allocatedFaculty`, `allocatedStaff`, `allocatedGuests`, `currentOccupiedStudents`, `currentOccupiedFaculty`, `currentOccupiedStaff`, `currentOccupiedGuests`, `lastUpdated`) VALUES
(1, 500, 100, 240, 100, 60, 0, 1, 0, 0, '2026-03-01 11:13:22'),
(2, 100, 40, 30, 20, 10, 0, 0, 0, 0, '2026-03-01 10:48:30');

-- --------------------------------------------------------

--
-- Table structure for table `rfidtag`
--

CREATE TABLE `rfidtag` (
  `stickerID` varchar(25) NOT NULL,
  `rfidCode` varchar(50) NOT NULL,
  `issuedAt` datetime DEFAULT NULL,
  `status` enum('active','inactive','available','unavailable') DEFAULT 'available',
  `expirationDate` date DEFAULT NULL,
  `issuedBy` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rfidtag`
--

INSERT INTO `rfidtag` (`stickerID`, `rfidCode`, `issuedAt`, `status`, `expirationDate`, `issuedBy`) VALUES
('S001', 'E0F8FEFE009806006000181800869E666000006678066060E6', '2025-11-28 02:52:45', 'available', '2026-11-28', 'admin'),
('S002', 'E0F8FEFE009806006000181800869E66600000666060067E18', '2025-11-29 13:14:02', 'available', '2026-11-28', NULL),
('S003', 'E0F8FEFE009806006000181800869E66600000666060067E60', '2025-11-29 13:34:37', 'available', '2026-11-28', NULL);

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

--
-- Dumping data for table `temporaryvehiclepass`
--

INSERT INTO `temporaryvehiclepass` (`passID`, `visitorID`, `issueDate`, `expiryDate`, `status`) VALUES
('TVP-001', NULL, '2026-03-01', '2026-03-02', 'active'),
('TVP-002', NULL, '2026-03-01', '2026-03-02', 'active'),
('TVP-003', NULL, '2026-03-01', '2026-03-02', 'active'),
('TVP-004', NULL, '2026-03-01', '2026-03-02', 'active'),
('TVP-005', NULL, '2026-03-01', '2026-03-02', 'active');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `userID` varchar(10) NOT NULL,
  `registrationID` varchar(10) DEFAULT NULL,
  `passID` varchar(10) DEFAULT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('SSEDMMO Admin','SSEDMMO Staff','Guard') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`userID`, `registrationID`, `passID`, `username`, `password`, `role`) VALUES
('U000', NULL, NULL, 'admin', '$2b$12$IJsZGZpGAoobVdFE3Cf9Y.uU2XYtIe3EM5TxHWl9hfTyG1t2s4P.C', 'SSEDMMO Admin'),
('U001', NULL, NULL, 'staff', '$2y$10$DOHzkMOrdD4EMaxYZv.6eOsKLxLYU4S2ntwSupYbDTSO93PAKBYJq', 'SSEDMMO Staff');

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
('DBA4658', 'A014', NULL, NULL, 'Motorcycle', 'PG-1', 'Toyota', 'Brown', 6000, 2, 'Gasoline', 'OR_upload/A001_DBA 4658_OR.jpg', 'CR_upload/A001_DBA 4658_CR.jpg', NULL),
('NBC1234', 'A013', NULL, NULL, 'Motorcycle', 'CF 152', 'Keeway', 'Black', 6000, 2, 'Gasoline', 'OR_upload/A001_NAT 0921_OR.jpg', 'CR_upload/A001_NAT 0921_CR.jpg', NULL),
('NBC5678', 'A018', NULL, NULL, 'Motorcycle', 'Y', 'Tesla', 'black', 450, 2, 'Hybrid', 'OR_upload/A001_NBC5678_OR.jpg', 'CR_upload/A001_NBC5678_CR.jpg', NULL),
('VIS101', NULL, NULL, 'V101', 'Car', 'Mirage', 'Mitsubishi', 'Gray', 1200, 4, 'Gasoline', 'OR_upload/VIS101_OR.png', 'CR_upload/VIS101_CR.png', NULL),
('VIS102', NULL, NULL, 'V102', 'Van', 'Hiace', 'Toyota', 'White', 2800, 4, 'Diesel', 'OR_upload/VIS102_OR.png', 'CR_upload/VIS102_CR.png', NULL),
('VIS103', NULL, NULL, 'V103', 'Car', 'Accent', 'Hyundai', 'Blue', 1400, 4, 'Diesel', 'OR_upload/VIS103_OR.png', 'CR_upload/VIS103_CR.png', NULL),
('VIS104', NULL, NULL, 'V104', 'Motorcycle', 'PCX', 'Honda', 'Matte Black', 160, 2, 'Gasoline', 'OR_upload/VIS104_OR.png', 'CR_upload/VIS104_CR.png', NULL),
('VIS105', NULL, NULL, 'V105', 'Car', 'CX-5', 'Mazda', 'Red', 2500, 4, 'Gasoline', 'OR_upload/VIS105_OR.png', 'CR_upload/VIS105_CR.png', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `vehicleowner`
--

CREATE TABLE `vehicleowner` (
  `OwnerID` varchar(10) NOT NULL,
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
  `drivers_license` varchar(255) DEFAULT NULL,
  `approvalTimestamp` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vehicleowner`
--

INSERT INTO `vehicleowner` (`OwnerID`, `fName`, `lName`, `mName`, `role`, `employment_type`, `email`, `contact_num`, `college`, `course`, `year`, `section`, `academicYear`, `registrationStatus`, `drivers_license`, `approvalTimestamp`) VALUES
('A012', 'David Natan', 'Apruebo', 'Villegas', 'student', '', 'davidnatan.apruebo@students.isatu.edu.ph', '09503903276', 'CCI', 'Bachelor of Science in Information Technology', '4th', 'A', '2025-2026', 'approved', 'DL_upload/A001_license.jpg', NULL),
('A013', 'David', 'Smurf', '', 'student', NULL, 'dvdsmrf@gmail.com', '09503903276', 'CCI', 'Bachelor of Science in Information Technology', '4th', 'A', '2025-2026', 'approved', 'DL_upload/A001_license.jpg', NULL),
('A014', 'Reynalds', 'Ilangos', '', 'faculty', NULL, 'dvdsmrf@gmail.com', '09503903276', 'CCI', '', '', '', '', 'approved', 'DL_upload/A001_license.jpg', NULL),
('A018', 'Chris', 'POrs', '3vles', 'student', NULL, 'davidnatan.apruebo@students.isatu.edu.ph', '318135132', 'CCI', 'Bachelor of Science in Computer Science', '4th', 'A', '2025-2026', 'approved', 'DL_upload/A001_license.jpg', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `vehiclepass`
--

CREATE TABLE `vehiclepass` (
  `passID` varchar(20) NOT NULL,
  `plateNum` varchar(20) DEFAULT NULL,
  `issueDate` date DEFAULT NULL,
  `expiryDate` date DEFAULT NULL,
  `status` enum('active','expired','available','unavailable') DEFAULT 'available',
  `issuedBy` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vehiclepass`
--

INSERT INTO `vehiclepass` (`passID`, `plateNum`, `issueDate`, `expiryDate`, `status`, `issuedBy`) VALUES
('VP-001', NULL, '2026-03-01', '2027-03-01', 'available', 'admin'),
('VP-002', NULL, '2026-03-01', '2027-03-01', 'available', NULL),
('VP-003', NULL, '2026-03-01', '2027-03-01', 'available', NULL),
('VP-004', NULL, '2026-03-01', '2027-03-01', 'available', NULL),
('VP-005', NULL, '2026-03-01', '2027-03-01', 'available', NULL);

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
  `fineAmount` decimal(10,2) DEFAULT 0.00,
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
  `entryTime` datetime DEFAULT NULL,
  `exitTime` datetime DEFAULT NULL,
  `status` enum('entered','exited','denied') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `visitor`
--

INSERT INTO `visitor` (`visitorID`, `passID`, `fullName`, `plateNum`, `purposeOfVisit`, `entryTime`, `exitTime`, `status`) VALUES
('V101', NULL, 'Visitor One', 'VIS101', 'Meeting with Registrar', NULL, NULL, NULL),
('V102', NULL, 'Visitor Two', 'VIS102', 'Deliver supplies', NULL, NULL, NULL),
('V103', NULL, 'Visitor Three', 'VIS103', 'Seminar guest speaker', NULL, NULL, NULL),
('V104', NULL, 'Visitor Four', 'VIS104', 'Parent of student', NULL, NULL, NULL),
('V105', NULL, 'Visitor Five', 'VIS105', 'Attend conference', NULL, NULL, NULL),
('V106', NULL, 'Visitor Six', 'VIS106', 'Maintenance check', NULL, NULL, NULL),
('V107', NULL, 'Visitor Seven', 'VIS107', 'Interview', NULL, NULL, NULL),
('V108', NULL, 'Visitor Eight', 'VIS108', 'Alumni visit', NULL, NULL, NULL),
('V109', NULL, 'Visitor Nine', 'VIS109', 'Pick up document', NULL, NULL, NULL),
('V110', NULL, 'Visitor Ten', 'VIS110', 'Food delivery', NULL, NULL, NULL);

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
-- Indexes for table `vehiclepass`
--
ALTER TABLE `vehiclepass`
  ADD PRIMARY KEY (`passID`),
  ADD KEY `vehiclePass_plate_idx` (`plateNum`);

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
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `accesslog`
--
ALTER TABLE `accesslog`
  MODIFY `logID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=107;

--
-- AUTO_INCREMENT for table `applications`
--
ALTER TABLE `applications`
  MODIFY `applicationID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `parkingstatus`
--
ALTER TABLE `parkingstatus`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `accesslog`
--
ALTER TABLE `accesslog`
  ADD CONSTRAINT `accesslog_ibfk_user` FOREIGN KEY (`userID`) REFERENCES `user` (`userID`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `applications`
--
ALTER TABLE `applications`
  ADD CONSTRAINT `applications_ibfk_owner` FOREIGN KEY (`OwnerID`) REFERENCES `vehicleowner` (`OwnerID`) ON DELETE SET NULL ON UPDATE CASCADE;

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
-- Constraints for table `vehiclepass`
--
ALTER TABLE `vehiclepass`
  ADD CONSTRAINT `vehiclePass_ibfk_vehicle` FOREIGN KEY (`plateNum`) REFERENCES `vehicle` (`plateNum`) ON DELETE CASCADE ON UPDATE CASCADE;

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
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
