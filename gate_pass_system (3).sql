-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 08, 2026 at 03:59 AM
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
(106, 'U000', 'login', 'User logged in successfully', '2026-03-01 18:48:14'),
(107, 'U000', 'login', 'User logged in successfully', '2026-03-04 12:31:42'),
(108, 'U000', 'login', 'User logged in successfully', '2026-03-04 14:03:07'),
(109, 'U000', 'login', 'User logged in successfully', '2026-03-04 14:34:21'),
(110, 'U000', 'logout', 'User logged out', '2026-03-04 14:52:56'),
(111, 'U000', 'login', 'User logged in successfully', '2026-03-04 14:53:25'),
(113, 'U002', 'login', 'Entry System - Guard guard logged in successfully', '2026-03-07 09:46:19'),
(114, 'U002', 'logout', 'Entry System - Guard guard logged out', '2026-03-07 10:25:25'),
(115, 'U002', 'login', 'Entry System - Guard guard logged in successfully', '2026-03-07 10:26:20'),
(116, 'U002', 'logout', 'Entry System - Guard guard logged out', '2026-03-07 10:26:24'),
(117, 'U002', 'login', 'Entry System - Guard guard logged in successfully', '2026-03-07 13:04:46'),
(118, 'U002', 'logout', 'Entry System - Guard guard logged out', '2026-03-07 13:05:30'),
(120, 'U002', 'logout', 'Entry System - Guard guard logged out', '2026-03-07 13:16:27'),
(121, 'U002', 'login', 'Entry System - Guard guard logged in successfully', '2026-03-07 13:16:37'),
(122, 'U002', 'logout', 'Entry System - Guard guard logged out', '2026-03-07 13:17:16'),
(123, 'U002', 'login', 'Entry System - Guard guard logged in successfully', '2026-03-07 13:18:24'),
(124, 'U002', 'logout', 'Entry System - Guard guard logged out', '2026-03-07 13:25:27'),
(125, 'U002', 'login', 'Entry System - Guard guard logged in successfully', '2026-03-07 13:29:35');

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
  `schoolID` varchar(50) DEFAULT NULL,
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
  `additional_driver_name` varchar(100) DEFAULT NULL,
  `additional_driver_relationship` varchar(50) DEFAULT NULL,
  `registrationStatus` enum('pending','approved','rejected') DEFAULT 'pending',
  `applicationDate` datetime DEFAULT current_timestamp(),
  `reviewed_by` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `applications`
--

INSERT INTO `applications` (`applicationID`, `OwnerID`, `fName`, `lName`, `mName`, `role`, `employment_type`, `email`, `contact_num`, `schoolID`, `college`, `course`, `year`, `section`, `academicYear`, `drivers_license`, `plateNum`, `vehicleType`, `model`, `manufacturer`, `color`, `cubicCapacity`, `numOfWheels`, `fuelType`, `offical_receipt`, `cert_of_registration`, `additional_driver_name`, `additional_driver_relationship`, `registrationStatus`, `applicationDate`, `reviewed_by`) VALUES
(1, 'A001', 'David Natan', 'Apruebo', 'Villegas', 'student', NULL, 'davidnatan.apruebo@students.isatu.edu.ph', '09503903276', '2022-4391-A', 'Other', 'Nigh Class', '4th', 'A', '2025-2026', 'DL_upload/A001_1772595644_license.jpg', 'NBC1565', 'Car', 'Civic', 'Honda', 'White', NULL, 4, 'Gasoline', 'OR_upload/A001_NBC1565_1772595644_OR.webp', 'CR_upload/A001_NBC1565_1772595644_CR.jpg', 'Mark Henry', 'Parent', 'pending', '2026-03-04 11:40:44', NULL),
(2, 'A001', 'David Natan', 'Apruebo', 'Villegas', 'student', NULL, 'davidnatan.apruebo@students.isatu.edu.ph', '09503903276', '2022-4391-A', 'Other', 'Nigh Class', '4th', 'A', '2025-2026', 'DL_upload/A001_1772595644_license.jpg', 'NAT0921', 'Motorcycle', 'Burgman', 'Kawasaki', 'Red', 125, 2, 'Gasoline', 'OR_upload/A001_NAT0921_1772595644_OR.webp', 'CR_upload/A001_NAT0921_1772595644_CR.jpg', 'Mark Henry', 'Parent', 'pending', '2026-03-04 11:40:44', NULL),
(3, 'A002', 'Jane ', 'Doe', '', 'non-teaching', 'permanent', 'dvdsmrf@gmail.com', '09503903276', '2026-1234-A', 'CAS', '', '', '', '', 'DL_upload/A002_1772596193_license.jpg', 'FAH0812', 'Car', 'Y', 'Tesla', 'White', NULL, 4, 'Electric', 'OR_upload/A002_FAH0812_1772596193_OR.jpg', 'CR_upload/A002_FAH0812_1772596193_CR.jpg', '', NULL, 'pending', '2026-03-04 11:49:53', NULL),
(4, 'A001', 'Michael', 'Reeves', '', 'faculty', 'part_time', 'blaned82@gmail.com', '09123456789', '2025-6172-B', 'CAS', '', '', '', '', 'DL_upload/A003_1772597479_license.jpg', 'NBC1234', 'Car', 'Raptor', 'Ford', 'Black', NULL, 4, 'Diesel', 'OR_upload/A003_DAB0912_1772597479_OR.jpg', 'CR_upload/A003_DAB0912_1772597479_CR.jpg', 'Totong', '', 'approved', '2026-03-04 12:11:19', 'admin');

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
('L001', 'TEST1234', '2026-03-07 08:48:31', '2026-03-07 08:48:31', 'exited', 'Old Site'),
('L002', 'NBC1234', '2026-03-08 10:07:02', NULL, 'entered', 'Old Site');

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
(1, 500, 100, 240, 100, 60, 0, 1, 0, 0, '2026-03-08 02:07:33'),
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
('S001', 'E0F8FEFE009806006000181800869E666000006678066060E6', '2025-11-28 02:52:45', 'unavailable', '2026-11-28', 'admin'),
('S002', 'E0F8FEFE009806006000181800869E66600000666060067E18', '2025-11-29 13:14:02', 'available', '2026-11-28', NULL),
('S003', 'E0F8FEFE009806006000181800869E66600000666060067E60', '2025-11-29 13:34:37', 'available', '2026-11-28', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `temporaryvehiclepass`
--

CREATE TABLE `temporaryvehiclepass` (
  `passID` varchar(10) NOT NULL,
  `visitorID` varchar(10) DEFAULT NULL,
  `status` enum('active','expired') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `temporaryvehiclepass`
--

INSERT INTO `temporaryvehiclepass` (`passID`, `visitorID`, `status`) VALUES
('TVP-001', NULL, 'active'),
('TVP-002', 'VIS-002', 'active'),
('TVP-003', NULL, 'active'),
('TVP-004', 'VIS-004', 'active'),
('TVP-005', NULL, 'active');

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
('U000', NULL, NULL, 'admin', '$2y$10$Jv6UDnv4YhyAE5zR7C6jKOW.gHIZa2je3GXN3jboUhV1hbaZ2oKKe', 'SSEDMMO Admin'),
('U001', NULL, NULL, 'staff', '$2y$10$DOHzkMOrdD4EMaxYZv.6eOsKLxLYU4S2ntwSupYbDTSO93PAKBYJq', 'SSEDMMO Staff'),
('U002', NULL, NULL, 'guard', '$2y$10$UUVWwfE8qRN4PTEZ6WyF4e9sUML8kwEDJYGknOZNOXEALu/tTgm5e', 'Guard'),
('U003', NULL, NULL, 'guard2', '$2y$10$dFCI4souNSq20l5gGNbqS.LMzB1aQ2AwYXcieCuAvz4CYmx6Sh38G', 'Guard');

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
('NBC1234', 'A001', 'S001', NULL, 'Car', 'Raptor', 'Ford', 'Black', NULL, 4, 'Diesel', 'OR_upload/A003_DAB0912_1772597479_OR.jpg', 'CR_upload/A003_DAB0912_1772597479_CR.jpg', 'VP-001'),
('TEST1234', 'O9999', NULL, NULL, 'Car', 'Model S', 'Tesla', 'Red', NULL, NULL, NULL, NULL, NULL, NULL);

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
  `schoolID` varchar(50) DEFAULT NULL,
  `college` varchar(100) DEFAULT NULL,
  `course` varchar(50) DEFAULT NULL,
  `year` varchar(10) DEFAULT NULL,
  `section` varchar(10) DEFAULT NULL,
  `academicYear` varchar(20) DEFAULT NULL,
  `registrationStatus` enum('pending','approved','rejected') DEFAULT 'pending',
  `drivers_license` varchar(255) DEFAULT NULL,
  `additional_driver_name` varchar(100) DEFAULT NULL,
  `additional_driver_relationship` varchar(50) DEFAULT NULL,
  `approvalTimestamp` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vehicleowner`
--

INSERT INTO `vehicleowner` (`OwnerID`, `fName`, `lName`, `mName`, `role`, `employment_type`, `email`, `contact_num`, `schoolID`, `college`, `course`, `year`, `section`, `academicYear`, `registrationStatus`, `drivers_license`, `additional_driver_name`, `additional_driver_relationship`, `approvalTimestamp`) VALUES
('A001', 'Michael', 'Reeves', '', 'faculty', NULL, 'blaned82@gmail.com', '09123456789', NULL, 'CAS', '', '', '', '', 'approved', 'DL_upload/A003_1772597479_license.jpg', NULL, NULL, '2026-03-07 05:41:34'),
('O9999', 'Test', 'Owner', NULL, 'guest', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'approved', NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `vehiclepass`
--

CREATE TABLE `vehiclepass` (
  `passID` varchar(20) NOT NULL,
  `plateNum` varchar(20) DEFAULT NULL,
  `status` enum('active','expired','available','unavailable') DEFAULT 'available',
  `issuedBy` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vehiclepass`
--

INSERT INTO `vehiclepass` (`passID`, `plateNum`, `status`, `issuedBy`) VALUES
('VP-001', NULL, 'unavailable', 'admin'),
('VP-002', NULL, 'available', NULL),
('VP-003', NULL, 'available', NULL),
('VP-004', NULL, 'available', NULL),
('VP-005', NULL, 'available', NULL);

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

--
-- Dumping data for table `vehicle_events`
--

INSERT INTO `vehicle_events` (`id`, `plate_num`, `event_type`, `event_data`, `timestamp`, `handled`) VALUES
(1, 'NBC1234', 'entry', '{\"plate\": \"NBC1234\", \"rfid\": \"S001\", \"owner\": \"Michael Reeves\", \"vehicle\": \"Ford Raptor\", \"color\": \"Black\", \"role\": \"faculty\", \"ownerType\": \"faculty\", \"college\": \"CAS\", \"timestamp\": \"13:43:09\", \"time\": \"13:43:09\", \"date\": \"2026-03-07\", \"rfid_match\": {\"match\": true, \"rfid_data\": {\"rfid_code\": \"E0F8FEFE009806006000181800869E666000006678066060E6\", \"plate\": \"NBC1234\", \"owner\": \"Michael Reeves\", \"vehicle\": \"Ford Raptor\", \"color\": \"Black\", \"owner_type\": \"faculty\", \"college\": \"CAS\", \"rfid_status\": \"unavailable\", \"timestamp\": \"01:42 PM\", \"date\": \"2026-03-07\", \"status\": \"matched\"}, \"message\": \"RFID and plate match confirmed\"}, \"action\": \"ENTERED\"}', '2026-03-07 05:43:09', 1),
(2, 'NBC1234', 'entry', '{\"plate\": \"NBC1234\", \"rfid\": \"S001\", \"owner\": \"Michael Reeves\", \"vehicle\": \"Ford Raptor\", \"color\": \"Black\", \"role\": \"faculty\", \"ownerType\": \"faculty\", \"college\": \"CAS\", \"timestamp\": \"10:07:02\", \"time\": \"10:07:02\", \"date\": \"2026-03-08\", \"rfid_match\": {\"match\": true, \"rfid_data\": {\"rfid_code\": \"E0F8FEFE009806006000181800869E666000006678066060E6\", \"plate\": \"NBC1234\", \"owner\": \"Michael Reeves\", \"vehicle\": \"Ford Raptor\", \"color\": \"Black\", \"owner_type\": \"faculty\", \"college\": \"CAS\", \"rfid_status\": \"unavailable\", \"timestamp\": \"10:06 AM\", \"date\": \"2026-03-08\", \"status\": \"matched\"}, \"message\": \"RFID and plate match confirmed\"}, \"action\": \"ENTERED\"}', '2026-03-08 02:07:02', 1);

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
  `status` enum('entered','exited','denied') DEFAULT NULL,
  `gateLocation` enum('Old Site','New Site') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `visitor`
--

INSERT INTO `visitor` (`visitorID`, `passID`, `fullName`, `plateNum`, `purposeOfVisit`, `entryTime`, `exitTime`, `status`, `gateLocation`) VALUES
('VIS-001', 'TVP-001', 'John Doe', 'ABC-123', 'Meeting with Dean', '2026-01-14 17:49:49', '2026-01-14 18:49:49', 'exited', 'New Site'),
('VIS-002', 'TVP-002', 'Jane Smith', 'XYZ-789', 'Delivery', '2026-03-01 18:49:49', NULL, 'entered', 'Old Site'),
('VIS-003', 'TVP-003', 'Michael Johnson', 'LMN-456', 'Inquiry', '2026-03-01 16:49:49', '2026-03-01 17:49:49', 'exited', 'New Site'),
('VIS-004', 'TVP-004', 'Emily Davis', 'QRS-012', 'Event Attendance', '2026-03-01 19:19:49', NULL, 'entered', 'Old Site'),
('VIS-005', 'TVP-005', 'Robert Wilson', 'DEF-345', 'Repair Service', '2026-03-01 19:49:49', '2026-03-01 19:49:49', 'denied', 'Old Site');

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
-- Indexes for table `parkingstatus`
--
ALTER TABLE `parkingstatus`
  ADD PRIMARY KEY (`id`);

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
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `accesslog`
--
ALTER TABLE `accesslog`
  MODIFY `logID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=126;

--
-- AUTO_INCREMENT for table `applications`
--
ALTER TABLE `applications`
  MODIFY `applicationID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `parkingstatus`
--
ALTER TABLE `parkingstatus`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `vehicle_events`
--
ALTER TABLE `vehicle_events`
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
