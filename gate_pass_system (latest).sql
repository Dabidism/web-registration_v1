-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 07, 2026 at 04:41 PM
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
(126, 'U000', 'logout', 'User logged out', '2026-01-04 14:54:25'),
(127, NULL, 'failed_login', 'Failed login attempt for username: admin', '2026-01-05 11:16:00'),
(128, 'U000', 'login', 'User logged in successfully', '2026-01-05 11:16:07'),
(129, 'U002', 'login', 'User logged in successfully', '2026-01-05 11:16:41'),
(137, 'U000', 'logout', 'User logged out', '2026-01-05 11:27:15'),
(138, 'U000', 'login', 'User logged in successfully', '2026-01-05 11:27:45'),
(139, 'U001', 'logout', 'User logged out', '2026-01-05 11:27:59'),
(140, 'U000', 'login', 'User logged in successfully', '2026-01-05 11:28:04'),
(141, 'U000', 'logout', 'User logged out', '2026-01-05 11:28:13'),
(142, 'U000', 'logout', 'User logged out', '2026-01-05 11:28:13'),
(143, 'U001', 'failed_login', 'Access denied for role: guard', '2026-01-05 11:34:20'),
(144, 'U000', 'login', 'User logged in successfully', '2026-01-05 11:34:24'),
(145, 'U000', 'logout', 'User logged out', '2026-01-05 11:34:30'),
(146, 'U002', 'login', 'User logged in successfully', '2026-01-05 11:34:37'),
(147, 'U002', 'login', 'User logged in successfully', '2026-01-05 11:34:49'),
(148, 'U002', 'logout', 'User logged out', '2026-01-05 11:34:56'),
(149, 'U000', 'login', 'User logged in successfully', '2026-01-05 11:35:06'),
(150, 'U002', 'logout', 'User logged out', '2026-01-05 11:36:05'),
(151, 'U002', 'login', 'User logged in successfully', '2026-01-05 11:36:15'),
(152, 'U000', 'logout', 'User logged out', '2026-01-05 11:53:35'),
(153, NULL, 'failed_login', 'Failed login attempt for username: yanie', '2026-01-05 11:53:47'),
(154, NULL, 'login', 'User logged in successfully', '2026-01-05 11:54:01'),
(155, NULL, 'logout', 'User logged out', '2026-01-05 11:57:50'),
(156, 'U000', 'login', 'User logged in successfully', '2026-01-05 11:57:56'),
(157, 'U000', 'logout', 'User logged out', '2026-01-05 12:43:57'),
(158, 'U002', 'login', 'User logged in successfully', '2026-01-05 12:44:02'),
(159, 'U002', 'logout', 'User logged out', '2026-01-05 12:44:56'),
(160, 'U000', 'login', 'User logged in successfully', '2026-01-05 12:45:01'),
(161, 'U000', 'logout', 'User logged out', '2026-01-05 12:45:25'),
(162, 'U002', 'login', 'User logged in successfully', '2026-01-05 12:45:29'),
(163, 'U000', 'login', 'User logged in successfully', '2026-01-05 23:44:06');

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
  `reviewed_by` varchar(100) DEFAULT NULL,
  `applicationDate` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `applications`
--

INSERT INTO `applications` (`applicationID`, `OwnerID`, `schoolID`, `fName`, `lName`, `mName`, `role`, `employment_type`, `email`, `contact_num`, `college`, `course`, `year`, `section`, `academicYear`, `drivers_license`, `additional_driver_name`, `additional_driver_relationship`, `plateNum`, `vehicleType`, `model`, `manufacturer`, `color`, `cubicCapacity`, `numOfWheels`, `fuelType`, `offical_receipt`, `cert_of_registration`, `registrationStatus`, `reviewed_by`, `applicationDate`) VALUES
(12, 'A001', '2022-4391-A', 'David Natan', 'Apruebo', 'Villegas', 'student', NULL, 'davidnatan.apruebo@students.isatu.edu.ph', '09503903276', 'CCI', 'Bachelor of Science in Information Technology', '5th', 'A', '2026-2027', 'DL_upload/A001_1767509100_license.jpg', '', NULL, 'NBC1234', 'Car', 'Y', 'Tesla', 'Black', NULL, 4, 'Gasoline', 'OR_upload/A001_NBC1234_1767509100_OR.jpg', 'CR_upload/A001_NBC1234_1767509100_CR.jpg', 'approved', NULL, '2026-01-04 14:45:00'),
(13, 'A002', '2022-5472-A', 'Victor Jom', 'Sorita', 'Albo', 'student', NULL, 'victorjom.sorita@students.isatu.edu.ph', '09630437375', 'CCI', 'Bachelor of Science in Information Technology', '4th', 'A', '2025-2026', 'DL_upload/A002_1767584705_license.jpg', '', NULL, 'UVJ696', 'Car', 'FJ Cruiser', 'Toyota', 'Black', NULL, 4, 'Diesel', 'OR_upload/A002_UVJ696_1767584705_OR.jpg', 'CR_upload/A002_UVJ696_1767584705_CR.png', 'approved', NULL, '2026-01-05 11:45:05'),
(14, 'A003', '2026-9658-A', 'Ems', 'Cads', '', 'student', NULL, 'dummy@gmail.com', '09053013609', 'CCI', 'Bachelor of Science in Information Technology', '4th', 'BSIT 4A', '2025-2026', 'DL_upload/A003_1767584841_license.png', '', NULL, 'NDF3545', 'Car', 'sdfewa', 'Fortuner', ' blue', NULL, 4, 'Diesel', 'OR_upload/A003_NDF3545_1767584841_OR.png', 'CR_upload/A003_NDF3545_1767584841_CR.png', 'approved', NULL, '2026-01-05 11:47:21'),
(15, 'A004', '2099-1234-C', 'Freddy', 'Fazbear', 'Ur Ur Ur', 'student', NULL, 'freddyfazpizza@fnaf.com', '09123456789', 'CIT', 'Bachelor of Industrial Technology major in Electro', '5th', 'F', '2025-2026', 'DL_upload/A004_1767585878_license.png', '', NULL, 'ABC123', 'Car', 'Samsung Galaxy Pro Universe', 'Mihoyo', 'F2F2F2', NULL, 4, 'Gasoline', 'OR_upload/A004_ABC123_1767585878_OR.png', 'CR_upload/A004_ABC123_1767585878_CR.png', 'approved', NULL, '2026-01-05 12:04:38'),
(16, 'A004', '2099-1234-C', 'Freddy', 'Fazbear', 'Ur Ur Ur', 'student', NULL, 'freddyfazpizza@fnaf.com', '09123456789', 'CIT', 'Bachelor of Industrial Technology major in Electro', '5th', 'F', '2025-2026', 'DL_upload/A004_1767585878_license.png', '', NULL, 'DEF456', 'Other', 'Iphone 99 Pro Max Galaxy Solar System', 'Mojang', 'A3F425', NULL, 4, 'Electric', 'OR_upload/A004_DEF456_1767585878_OR.png', 'CR_upload/A004_DEF456_1767585878_CR.png', 'approved', NULL, '2026-01-05 12:04:38'),
(17, 'A005', '2099-4567-E', 'Bonnie', 'Fazbear', 'Bon Bon Bon', 'student', NULL, 'bonniefazpizza@fnaf.com', '09123456789', 'CIT', 'Bachelor of Industrial Technology major in Electro', '5th', 'F', '2025-2026', 'DL_upload/A005_1767586066_license.png', 'Chika Fazbear', 'Relative', 'GHI789', 'Motorcycle', 'sdfewa', 'Mihoyo', ' blue', 123455, 2, 'Diesel', 'OR_upload/A005_GHI789_1767586066_OR.png', 'CR_upload/A005_GHI789_1767586066_CR.png', 'pending', NULL, '2026-01-05 12:07:46');

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

--
-- Triggers `historical_log`
--
DELIMITER $$
CREATE TRIGGER `after_vehicle_entry` AFTER INSERT ON `historical_log` FOR EACH ROW BEGIN
    DECLARE user_role VARCHAR(50);
    DECLARE is_visitor BOOLEAN DEFAULT FALSE;
    
    -- Only process if vehicle entered today
    IF NEW.status = 'entered' AND DATE(NEW.entryTime) = CURDATE() THEN
        
        -- Determine role category
        SELECT 
            CASE 
                WHEN vo.role = 'student' THEN 'students'
                WHEN vo.role = 'faculty' THEN 'faculty'
                WHEN vo.role IN ('non-teaching', 'staff') THEN 'staff'
                WHEN v.visitorID IS NOT NULL THEN 'guests'
                ELSE 'guests'
            END,
            v.visitorID IS NOT NULL
        INTO user_role, is_visitor
        FROM vehicle v
        LEFT JOIN vehicleowner vo ON v.OwnerID = vo.OwnerID
        WHERE v.plateNum = NEW.plateNum;
        
        -- Increment appropriate counter
        IF user_role = 'students' THEN
            UPDATE parkingstatus 
            SET currentOccupiedStudents = currentOccupiedStudents + 1 
            WHERE id = 1;
        ELSEIF user_role = 'faculty' THEN
            UPDATE parkingstatus 
            SET currentOccupiedFaculty = currentOccupiedFaculty + 1 
            WHERE id = 1;
        ELSEIF user_role = 'staff' THEN
            UPDATE parkingstatus 
            SET currentOccupiedStaff = currentOccupiedStaff + 1 
            WHERE id = 1;
        ELSE
            UPDATE parkingstatus 
            SET currentOccupiedGuests = currentOccupiedGuests + 1 
            WHERE id = 1;
        END IF;
        
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `after_vehicle_exit` AFTER UPDATE ON `historical_log` FOR EACH ROW BEGIN
    DECLARE user_role VARCHAR(50);
    
    -- Only process if status changed from entered to exited
    IF OLD.status = 'entered' AND NEW.status = 'exited' AND DATE(OLD.entryTime) = CURDATE() THEN
        
        -- Determine role category
        SELECT 
            CASE 
                WHEN vo.role = 'student' THEN 'students'
                WHEN vo.role = 'faculty' THEN 'faculty'
                WHEN vo.role IN ('non-teaching', 'staff') THEN 'staff'
                WHEN v.visitorID IS NOT NULL THEN 'guests'
                ELSE 'guests'
            END
        INTO user_role
        FROM vehicle v
        LEFT JOIN vehicleowner vo ON v.OwnerID = vo.OwnerID
        WHERE v.plateNum = NEW.plateNum;
        
        -- Decrement appropriate counter (prevent negative values)
        IF user_role = 'students' THEN
            UPDATE parkingstatus 
            SET currentOccupiedStudents = GREATEST(currentOccupiedStudents - 1, 0) 
            WHERE id = 1;
        ELSEIF user_role = 'faculty' THEN
            UPDATE parkingstatus 
            SET currentOccupiedFaculty = GREATEST(currentOccupiedFaculty - 1, 0) 
            WHERE id = 1;
        ELSEIF user_role = 'staff' THEN
            UPDATE parkingstatus 
            SET currentOccupiedStaff = GREATEST(currentOccupiedStaff - 1, 0) 
            WHERE id = 1;
        ELSE
            UPDATE parkingstatus 
            SET currentOccupiedGuests = GREATEST(currentOccupiedGuests - 1, 0) 
            WHERE id = 1;
        END IF;
        
    END IF;
END
$$
DELIMITER ;

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
(2, 250, 50, 120, 50, 30, 0, 0, 0, 0, '2026-01-07 09:22:33', 20, 160, 20),
(3, 200, 100, 50, 30, 20, 0, 0, 0, 0, '2026-01-02 17:37:42', 20, 160, 20),
(4, 200, 100, 50, 30, 20, 0, 0, 0, 0, '2026-01-02 17:43:00', 20, 160, 20),
(5, 200, 100, 50, 30, 20, 0, 0, 0, 0, '2026-01-02 17:43:02', 20, 160, 20),
(6, 200, 100, 50, 30, 20, 0, 0, 0, 0, '2026-01-02 17:43:43', 20, 160, 20),
(7, 200, 100, 50, 30, 20, 0, 0, 0, 0, '2026-01-02 17:43:46', 20, 160, 20),
(8, 200, 100, 50, 30, 20, 0, 0, 0, 0, '2026-01-02 17:46:56', 20, 160, 20),
(9, 200, 100, 50, 30, 20, 0, 0, 0, 0, '2026-01-07 09:21:03', 20, 160, 20),
(10, 200, 100, 50, 30, 20, 0, 0, 0, 0, '2026-01-07 09:21:33', 20, 160, 20),
(11, 200, 100, 50, 30, 20, 0, 0, 0, 0, '2026-01-07 09:22:03', 20, 160, 20),
(12, 200, 100, 50, 30, 20, 0, 0, 0, 0, '2026-01-07 09:23:05', 20, 160, 20),
(13, 200, 100, 50, 30, 20, 0, 0, 0, 0, '2026-01-07 09:23:35', 20, 160, 20),
(14, 200, 100, 50, 30, 20, 0, 0, 0, 0, '2026-01-07 09:24:05', 20, 160, 20),
(15, 200, 100, 50, 30, 20, 0, 0, 0, 0, '2026-01-07 09:24:35', 20, 160, 20),
(16, 200, 100, 50, 30, 20, 0, 0, 0, 0, '2026-01-07 09:25:05', 20, 160, 20),
(17, 200, 100, 50, 30, 20, 0, 0, 0, 0, '2026-01-07 09:25:35', 20, 160, 20),
(18, 200, 100, 50, 30, 20, 0, 0, 0, 0, '2026-01-07 09:26:05', 20, 160, 20),
(19, 200, 100, 50, 30, 20, 0, 0, 0, 0, '2026-01-07 09:26:35', 20, 160, 20),
(20, 200, 100, 50, 30, 20, 0, 0, 0, 0, '2026-01-07 09:27:05', 20, 160, 20),
(21, 200, 100, 50, 30, 20, 0, 0, 0, 0, '2026-01-07 09:27:35', 20, 160, 20),
(22, 200, 100, 50, 30, 20, 0, 0, 0, 0, '2026-01-07 09:28:05', 20, 160, 20),
(23, 200, 100, 50, 30, 20, 0, 0, 0, 0, '2026-01-07 09:28:35', 20, 160, 20),
(24, 200, 100, 50, 30, 20, 0, 0, 0, 0, '2026-01-07 09:29:05', 20, 160, 20),
(25, 200, 100, 50, 30, 20, 0, 0, 0, 0, '2026-01-07 09:29:35', 20, 160, 20),
(26, 200, 100, 50, 30, 20, 0, 0, 0, 0, '2026-01-07 09:30:05', 20, 160, 20),
(27, 200, 100, 50, 30, 20, 0, 0, 0, 0, '2026-01-07 09:30:35', 20, 160, 20),
(28, 200, 100, 50, 30, 20, 0, 0, 0, 0, '2026-01-07 09:31:05', 20, 160, 20),
(29, 200, 100, 50, 30, 20, 0, 0, 0, 0, '2026-01-07 09:31:35', 20, 160, 20),
(30, 200, 100, 50, 30, 20, 0, 0, 0, 0, '2026-01-07 09:32:05', 20, 160, 20),
(31, 200, 100, 50, 30, 20, 0, 0, 0, 0, '2026-01-07 09:32:35', 20, 160, 20),
(32, 200, 100, 50, 30, 20, 0, 0, 0, 0, '2026-01-07 09:33:05', 20, 160, 20),
(33, 200, 100, 50, 30, 20, 0, 0, 0, 0, '2026-01-07 09:33:35', 20, 160, 20),
(34, 200, 100, 50, 30, 20, 0, 0, 0, 0, '2026-01-07 09:34:05', 20, 160, 20),
(35, 200, 100, 50, 30, 20, 0, 0, 0, 0, '2026-01-07 09:34:35', 20, 160, 20),
(36, 200, 100, 50, 30, 20, 0, 0, 0, 0, '2026-01-07 09:35:05', 20, 160, 20),
(37, 200, 100, 50, 30, 20, 0, 0, 0, 0, '2026-01-07 09:35:35', 20, 160, 20),
(38, 200, 100, 50, 30, 20, 0, 0, 0, 0, '2026-01-07 09:36:05', 20, 160, 20),
(39, 200, 100, 50, 30, 20, 0, 0, 0, 0, '2026-01-07 09:36:35', 20, 160, 20),
(40, 200, 100, 50, 30, 20, 0, 0, 0, 0, '2026-01-07 09:37:05', 20, 160, 20),
(41, 200, 100, 50, 30, 20, 0, 0, 0, 0, '2026-01-07 09:37:35', 20, 160, 20),
(42, 200, 100, 50, 30, 20, 0, 0, 0, 0, '2026-01-07 09:38:05', 20, 160, 20),
(43, 200, 100, 50, 30, 20, 0, 0, 0, 0, '2026-01-07 09:38:35', 20, 160, 20),
(44, 200, 100, 50, 30, 20, 0, 0, 0, 0, '2026-01-07 09:39:05', 20, 160, 20),
(45, 200, 100, 50, 30, 20, 0, 0, 0, 0, '2026-01-07 09:39:35', 20, 160, 20),
(46, 200, 100, 50, 30, 20, 0, 0, 0, 0, '2026-01-07 09:40:05', 20, 160, 20),
(47, 200, 100, 50, 30, 20, 0, 0, 0, 0, '2026-01-07 09:40:35', 20, 160, 20),
(48, 200, 100, 50, 30, 20, 0, 0, 0, 0, '2026-01-07 09:41:05', 20, 160, 20),
(49, 200, 100, 50, 30, 20, 0, 0, 0, 0, '2026-01-07 09:41:35', 20, 160, 20),
(50, 200, 100, 50, 30, 20, 0, 0, 0, 0, '2026-01-07 09:42:05', 20, 160, 20),
(51, 200, 100, 50, 30, 20, 0, 0, 0, 0, '2026-01-07 09:42:35', 20, 160, 20),
(52, 200, 100, 50, 30, 20, 0, 0, 0, 0, '2026-01-07 09:43:05', 20, 160, 20),
(53, 200, 100, 50, 30, 20, 0, 0, 0, 0, '2026-01-07 09:43:35', 20, 160, 20),
(54, 200, 100, 50, 30, 20, 0, 0, 0, 0, '2026-01-07 09:44:05', 20, 160, 20),
(55, 200, 100, 50, 30, 20, 0, 0, 0, 0, '2026-01-07 09:44:35', 20, 160, 20),
(56, 200, 100, 50, 30, 20, 0, 0, 0, 0, '2026-01-07 09:45:05', 20, 160, 20),
(57, 200, 100, 50, 30, 20, 0, 0, 0, 0, '2026-01-07 09:45:35', 20, 160, 20),
(58, 200, 100, 50, 30, 20, 0, 0, 0, 0, '2026-01-07 09:46:05', 20, 160, 20),
(59, 200, 100, 50, 30, 20, 0, 0, 0, 0, '2026-01-07 09:46:35', 20, 160, 20),
(60, 200, 100, 50, 30, 20, 0, 0, 0, 0, '2026-01-07 09:47:05', 20, 160, 20),
(61, 200, 100, 50, 30, 20, 0, 0, 0, 0, '2026-01-07 09:47:35', 20, 160, 20),
(62, 200, 100, 50, 30, 20, 0, 0, 0, 0, '2026-01-07 09:48:05', 20, 160, 20),
(63, 200, 100, 50, 30, 20, 0, 0, 0, 0, '2026-01-07 09:48:35', 20, 160, 20),
(64, 200, 100, 50, 30, 20, 0, 0, 0, 0, '2026-01-07 09:49:05', 20, 160, 20),
(65, 200, 100, 50, 30, 20, 0, 0, 0, 0, '2026-01-07 09:49:35', 20, 160, 20),
(66, 200, 100, 50, 30, 20, 0, 0, 0, 0, '2026-01-07 09:50:05', 20, 160, 20),
(67, 200, 100, 50, 30, 20, 0, 0, 0, 0, '2026-01-07 09:50:35', 20, 160, 20),
(68, 200, 100, 50, 30, 20, 0, 0, 0, 0, '2026-01-07 09:51:05', 20, 160, 20),
(69, 200, 100, 50, 30, 20, 0, 0, 0, 0, '2026-01-07 09:51:35', 20, 160, 20),
(70, 200, 100, 50, 30, 20, 0, 0, 0, 0, '2026-01-07 09:52:05', 20, 160, 20),
(71, 200, 100, 50, 30, 20, 0, 0, 0, 0, '2026-01-07 09:52:35', 20, 160, 20),
(72, 200, 100, 50, 30, 20, 0, 0, 0, 0, '2026-01-07 09:53:05', 20, 160, 20),
(73, 200, 100, 50, 30, 20, 0, 0, 0, 0, '2026-01-07 09:53:35', 20, 160, 20),
(74, 200, 100, 50, 30, 20, 0, 0, 0, 0, '2026-01-07 09:54:05', 20, 160, 20),
(75, 200, 100, 50, 30, 20, 0, 0, 0, 0, '2026-01-07 09:54:35', 20, 160, 20),
(76, 200, 100, 50, 30, 20, 0, 0, 0, 0, '2026-01-07 09:55:05', 20, 160, 20),
(77, 200, 100, 50, 30, 20, 0, 0, 0, 0, '2026-01-07 09:55:35', 20, 160, 20),
(78, 200, 100, 50, 30, 20, 0, 0, 0, 0, '2026-01-07 09:56:05', 20, 160, 20),
(79, 200, 100, 50, 30, 20, 0, 0, 0, 0, '2026-01-07 09:56:35', 20, 160, 20),
(80, 200, 100, 50, 30, 20, 0, 0, 0, 0, '2026-01-07 09:57:05', 20, 160, 20),
(81, 200, 100, 50, 30, 20, 0, 0, 0, 0, '2026-01-07 09:57:35', 20, 160, 20),
(82, 200, 100, 50, 30, 20, 0, 0, 0, 0, '2026-01-07 09:58:05', 20, 160, 20),
(83, 200, 100, 50, 30, 20, 0, 0, 0, 0, '2026-01-07 09:58:35', 20, 160, 20),
(84, 200, 100, 50, 30, 20, 0, 0, 0, 0, '2026-01-07 09:59:05', 20, 160, 20),
(85, 200, 100, 50, 30, 20, 0, 0, 0, 0, '2026-01-07 09:59:35', 20, 160, 20),
(86, 200, 100, 50, 30, 20, 0, 0, 0, 0, '2026-01-07 10:00:05', 20, 160, 20),
(87, 200, 100, 50, 30, 20, 0, 0, 0, 0, '2026-01-07 10:00:35', 20, 160, 20),
(88, 200, 100, 50, 30, 20, 0, 0, 0, 0, '2026-01-07 10:01:05', 20, 160, 20),
(89, 200, 100, 50, 30, 20, 0, 0, 0, 0, '2026-01-07 10:01:35', 20, 160, 20),
(90, 200, 100, 50, 30, 20, 0, 0, 0, 0, '2026-01-07 10:02:05', 20, 160, 20),
(91, 200, 100, 50, 30, 20, 0, 0, 0, 0, '2026-01-07 10:02:35', 20, 160, 20),
(92, 200, 100, 50, 30, 20, 0, 0, 0, 0, '2026-01-07 10:03:05', 20, 160, 20),
(93, 200, 100, 50, 30, 20, 0, 0, 0, 0, '2026-01-07 10:03:35', 20, 160, 20),
(94, 200, 100, 50, 30, 20, 0, 0, 0, 0, '2026-01-07 10:04:05', 20, 160, 20),
(95, 200, 100, 50, 30, 20, 0, 0, 0, 0, '2026-01-07 10:04:35', 20, 160, 20),
(96, 200, 100, 50, 30, 20, 0, 0, 0, 0, '2026-01-07 10:05:05', 20, 160, 20),
(97, 200, 100, 50, 30, 20, 0, 0, 0, 0, '2026-01-07 10:05:35', 20, 160, 20),
(98, 200, 100, 50, 30, 20, 0, 0, 0, 0, '2026-01-07 10:06:05', 20, 160, 20),
(99, 200, 100, 50, 30, 20, 0, 0, 0, 0, '2026-01-07 10:06:35', 20, 160, 20),
(100, 200, 100, 50, 30, 20, 0, 0, 0, 0, '2026-01-07 10:07:05', 20, 160, 20),
(101, 200, 100, 50, 30, 20, 0, 0, 0, 0, '2026-01-07 10:07:35', 20, 160, 20),
(102, 200, 100, 50, 30, 20, 0, 0, 0, 0, '2026-01-07 10:08:05', 20, 160, 20),
(103, 200, 100, 50, 30, 20, 0, 0, 0, 0, '2026-01-07 10:08:35', 20, 160, 20),
(104, 200, 100, 50, 30, 20, 0, 0, 0, 0, '2026-01-07 10:09:05', 20, 160, 20),
(105, 200, 100, 50, 30, 20, 0, 0, 0, 0, '2026-01-07 10:09:35', 20, 160, 20),
(106, 200, 100, 50, 30, 20, 0, 0, 0, 0, '2026-01-07 10:10:05', 20, 160, 20),
(107, 200, 100, 50, 30, 20, 0, 0, 0, 0, '2026-01-07 10:10:35', 20, 160, 20),
(108, 200, 100, 50, 30, 20, 0, 0, 0, 0, '2026-01-07 10:11:05', 20, 160, 20),
(109, 200, 100, 50, 30, 20, 0, 0, 0, 0, '2026-01-07 10:11:35', 20, 160, 20),
(110, 200, 100, 50, 30, 20, 0, 0, 0, 0, '2026-01-07 10:12:05', 20, 160, 20),
(111, 200, 100, 50, 30, 20, 0, 0, 0, 0, '2026-01-07 10:12:35', 20, 160, 20),
(112, 200, 100, 50, 30, 20, 0, 0, 0, 0, '2026-01-07 10:13:05', 20, 160, 20),
(113, 200, 100, 50, 30, 20, 0, 0, 0, 0, '2026-01-07 10:13:35', 20, 160, 20),
(114, 200, 100, 50, 30, 20, 0, 0, 0, 0, '2026-01-07 10:14:05', 20, 160, 20),
(115, 200, 100, 50, 30, 20, 0, 0, 0, 0, '2026-01-07 10:14:35', 20, 160, 20),
(116, 200, 100, 50, 30, 20, 0, 0, 0, 0, '2026-01-07 10:15:05', 20, 160, 20),
(117, 200, 100, 50, 30, 20, 0, 0, 0, 0, '2026-01-07 10:15:35', 20, 160, 20),
(118, 200, 100, 50, 30, 20, 0, 0, 0, 0, '2026-01-07 10:16:05', 20, 160, 20),
(119, 200, 100, 50, 30, 20, 0, 0, 0, 0, '2026-01-07 10:16:35', 20, 160, 20),
(120, 200, 100, 50, 30, 20, 0, 0, 0, 0, '2026-01-07 10:17:05', 20, 160, 20),
(121, 200, 100, 50, 30, 20, 0, 0, 0, 0, '2026-01-07 10:17:35', 20, 160, 20),
(122, 200, 100, 50, 30, 20, 0, 0, 0, 0, '2026-01-07 10:18:05', 20, 160, 20),
(123, 200, 100, 50, 30, 20, 0, 0, 0, 0, '2026-01-07 10:18:35', 20, 160, 20),
(124, 200, 100, 50, 30, 20, 0, 0, 0, 0, '2026-01-07 10:19:05', 20, 160, 20),
(125, 200, 100, 50, 30, 20, 0, 0, 0, 0, '2026-01-07 10:19:35', 20, 160, 20),
(126, 200, 100, 50, 30, 20, 0, 0, 0, 0, '2026-01-07 10:20:06', 20, 160, 20),
(127, 200, 100, 50, 30, 20, 0, 0, 0, 0, '2026-01-07 10:20:36', 20, 160, 20),
(128, 200, 100, 50, 30, 20, 0, 0, 0, 0, '2026-01-07 10:21:37', 20, 160, 20),
(129, 200, 100, 50, 30, 20, 0, 0, 0, 0, '2026-01-07 10:22:40', 20, 160, 20),
(130, 200, 100, 50, 30, 20, 0, 0, 0, 0, '2026-01-07 10:23:39', 20, 160, 20),
(131, 200, 100, 50, 30, 20, 0, 0, 0, 0, '2026-01-07 10:25:03', 20, 160, 20),
(132, 200, 100, 50, 30, 20, 0, 0, 0, 0, '2026-01-07 10:25:41', 20, 160, 20),
(133, 200, 100, 50, 30, 20, 0, 0, 0, 0, '2026-01-07 10:26:42', 20, 160, 20),
(134, 200, 100, 50, 30, 20, 0, 0, 0, 0, '2026-01-07 10:27:43', 20, 160, 20),
(135, 200, 100, 50, 30, 20, 0, 0, 0, 0, '2026-01-07 10:28:44', 20, 160, 20),
(136, 200, 100, 50, 30, 20, 0, 0, 0, 0, '2026-01-07 10:29:46', 20, 160, 20),
(137, 200, 100, 50, 30, 20, 0, 0, 0, 0, '2026-01-07 10:30:46', 20, 160, 20),
(138, 200, 100, 50, 30, 20, 0, 0, 0, 0, '2026-01-07 10:31:47', 20, 160, 20),
(139, 200, 100, 50, 30, 20, 0, 0, 0, 0, '2026-01-07 10:32:48', 20, 160, 20),
(140, 200, 100, 50, 30, 20, 0, 0, 0, 0, '2026-01-07 10:33:49', 20, 160, 20),
(141, 200, 100, 50, 30, 20, 0, 0, 0, 0, '2026-01-07 10:34:50', 20, 160, 20),
(142, 200, 100, 50, 30, 20, 0, 0, 0, 0, '2026-01-07 10:35:51', 20, 160, 20),
(143, 200, 100, 50, 30, 20, 0, 0, 0, 0, '2026-01-07 10:36:52', 20, 160, 20),
(144, 200, 100, 50, 30, 20, 0, 0, 0, 0, '2026-01-07 10:37:53', 20, 160, 20),
(145, 200, 100, 50, 30, 20, 0, 0, 0, 0, '2026-01-07 10:38:54', 20, 160, 20),
(146, 200, 100, 50, 30, 20, 0, 0, 0, 0, '2026-01-07 10:39:55', 20, 160, 20),
(147, 200, 100, 50, 30, 20, 0, 0, 0, 0, '2026-01-07 10:40:56', 20, 160, 20),
(148, 200, 100, 50, 30, 20, 0, 0, 0, 0, '2026-01-07 10:41:57', 20, 160, 20),
(149, 200, 100, 50, 30, 20, 0, 0, 0, 0, '2026-01-07 10:42:58', 20, 160, 20),
(150, 200, 100, 50, 30, 20, 0, 0, 0, 0, '2026-01-07 10:47:01', 20, 160, 20),
(151, 200, 100, 50, 30, 20, 0, 0, 0, 0, '2026-01-07 13:34:21', 20, 160, 20),
(152, 200, 100, 50, 30, 20, 0, 0, 0, 0, '2026-01-07 13:35:00', 20, 160, 20),
(153, 200, 100, 50, 30, 20, 0, 0, 0, 0, '2026-01-07 13:35:59', 20, 160, 20),
(154, 200, 100, 50, 30, 20, 0, 0, 0, 0, '2026-01-07 13:36:59', 20, 160, 20),
(155, 200, 100, 50, 30, 20, 0, 0, 0, 0, '2026-01-07 13:37:59', 20, 160, 20),
(156, 200, 100, 50, 30, 20, 0, 0, 0, 0, '2026-01-07 13:38:40', 20, 160, 20),
(157, 200, 100, 50, 30, 20, 0, 0, 0, 0, '2026-01-07 15:40:33', 20, 160, 20),
(158, 200, 100, 50, 30, 20, 0, 0, 0, 0, '2026-01-07 15:41:03', 20, 160, 20),
(159, 200, 100, 50, 30, 20, 0, 0, 0, 0, '2026-01-07 15:41:33', 20, 160, 20);

-- --------------------------------------------------------

--
-- Table structure for table `rfidtag`
--

CREATE TABLE `rfidtag` (
  `stickerID` varchar(20) NOT NULL COMMENT 'RFID tag ID (e.g., RFID001)',
  `tagCode` varchar(100) DEFAULT NULL COMMENT 'Scanned RFID tag code from converter',
  `status` enum('available','unavailable') DEFAULT 'available' COMMENT 'Tag availability status',
  `issuedBy` varchar(50) DEFAULT NULL COMMENT 'Username of admin who issued the RFID tag'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='RFID tags inventory for registered vehicles';

--
-- Dumping data for table `rfidtag`
--

INSERT INTO `rfidtag` (`stickerID`, `tagCode`, `status`, `issuedBy`) VALUES
('RFID001', 'E0F8FEFE009806006000181800869E666000006678066060E6F8', 'unavailable', 'admin');

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
  `role` varchar(50) NOT NULL,
  `session_token` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`userID`, `username`, `password`, `role`, `session_token`, `created_at`) VALUES
('U000', 'admin', '$2b$12$IJsZGZpGAoobVdFE3Cf9Y.uU2XYtIe3EM5TxHWl9hfTyG1t2s4P.C', 'SSEDMMO Admin', NULL, '2026-01-05 16:00:24'),
('U001', 'guard', '$2y$10$BzJIT2iuzz.PEN4BuIBkO.e1eqWXj5Cgjzds/YqeX73ozCMYiLyxi', 'guard', NULL, '2026-01-05 16:00:24'),
('U002', 'staff', '$2y$10$DVZN9ECS/ic0TtZkvtsrW.8.TAU.iZa/zt9JoW22TffmpMnTCQlue', 'SSEDMMO Staff', NULL, '2026-01-05 16:00:24');

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
('ABC123', 'A004', NULL, NULL, 'Car', 'Y', 'Tesla', 'gray', NULL, 4, 'Gasoline', 'OR_upload/A004_ABC123_1767585878_OR.png', 'CR_upload/A004_ABC123_1767585878_CR.png', NULL),
('DEF456', 'A004', NULL, NULL, 'Car', 'Civic', 'Honda', 'Red', NULL, 4, 'Electric', 'OR_upload/A004_DEF456_1767585878_OR.png', 'CR_upload/A004_DEF456_1767585878_CR.png', NULL),
('NBC1234', 'A001', 'RFID001', NULL, 'Car', 'Z', 'Tesla', 'White', NULL, 4, 'Gasoline', 'OR_upload/A001_NBC1234_1767509100_OR.jpg', 'CR_upload/A001_NBC1234_1767509100_CR.jpg', 'CP01'),
('NDF3545', 'A003', NULL, NULL, 'Car', 'Fortuner', 'Toyota', ' blue', NULL, 4, 'Diesel', 'OR_upload/A003_NDF3545_1767584841_OR.png', 'CR_upload/A003_NDF3545_1767584841_CR.png', NULL),
('POI123', 'A001', NULL, NULL, 'Car', 'Toyota', 'Camry', 'Gray', NULL, 4, 'Electric', NULL, NULL, NULL),
('QWE3456', 'A001', NULL, NULL, 'Car', 'City', 'Hyundai', 'Black', NULL, 4, 'Electric', NULL, NULL, NULL),
('UVJ696', 'A002', NULL, NULL, 'Car', 'FJ Cruiser', 'Toyota', 'Black', NULL, 4, 'Diesel', 'OR_upload/A002_UVJ696_1767584705_OR.jpg', 'CR_upload/A002_UVJ696_1767584705_CR.png', NULL);

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
('A001', NULL, 'David Natan', 'Apruebo', 'Villegas', 'student', NULL, 'davidnatan.apruebo@students.isatu.edu.ph', '09503903276', 'CCI', 'Bachelor of Science in Information Technology', '5th', 'A', '2026-2027', 'approved', '2026-01-04 07:45:21', 'DL_upload/A001_1767509100_license.jpg', NULL, NULL),
('A002', NULL, 'Victor Jom', 'Sorita', 'Albo', 'student', NULL, 'victorjom.sorita@students.isatu.edu.ph', '09630437375', 'CCI', 'Bachelor of Science in Information Technology', '4th', 'A', '2025-2026', 'approved', '2026-01-05 04:45:51', 'DL_upload/A002_1767584705_license.jpg', NULL, NULL),
('A003', NULL, 'Ems', 'Cads', '', 'student', NULL, 'dummy@gmail.com', '09053013609', 'CCI', 'Bachelor of Science in Information Technology', '4th', 'BSIT 4A', '2025-2026', 'approved', '2026-01-05 04:55:39', 'DL_upload/A003_1767584841_license.png', NULL, NULL),
('A004', NULL, 'Freddy', 'Fazbear', 'Ur Ur Ur', 'student', NULL, 'freddyfazpizza@fnaf.com', '09123456789', 'CIT', 'Bachelor of Industrial Technology major in Electro', '5th', 'F', '2025-2026', 'approved', '2026-01-05 05:06:58', 'DL_upload/A004_1767585878_license.png', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `vehiclepass`
--

CREATE TABLE `vehiclepass` (
  `passID` varchar(20) NOT NULL COMMENT 'Car pass ID (e.g., CP01)',
  `status` enum('available','unavailable') DEFAULT 'available' COMMENT 'Pass availability status',
  `issuedBy` varchar(50) DEFAULT NULL COMMENT 'Username of admin who issued the pass'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Vehicle passes inventory for registered vehicles';

--
-- Dumping data for table `vehiclepass`
--

INSERT INTO `vehiclepass` (`passID`, `status`, `issuedBy`) VALUES
('CP01', 'unavailable', 'admin'),
('CP02', 'available', NULL),
('CP03', 'available', NULL),
('CP04', 'available', NULL),
('CP05', 'available', NULL),
('CP06', 'available', NULL),
('CP07', 'available', NULL),
('CP08', 'available', NULL),
('CP09', 'available', NULL),
('CP10', 'available', NULL),
('CP11', 'available', NULL),
('CP12', 'available', NULL),
('CP13', 'available', NULL),
('CP14', 'available', NULL),
('CP15', 'available', NULL),
('CP16', 'available', NULL),
('CP17', 'available', NULL),
('CP18', 'available', NULL),
('CP19', 'available', NULL),
('CP20', 'available', NULL);

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
  `violationEndDate` datetime DEFAULT NULL,
  `reportedBy` varchar(10) DEFAULT NULL,
  `status` enum('pending','resolved','dismissed') DEFAULT 'pending',
  `resolvedDate` datetime DEFAULT NULL,
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `violations`
--

INSERT INTO `violations` (`violationID`, `plateNum`, `violationType`, `description`, `location`, `violationDate`, `violationEndDate`, `reportedBy`, `status`, `resolvedDate`, `notes`) VALUES
('VIO-1930', 'QWE3456', 'expired_permit', 'Computed violation timeframe of 3 months.', 'Campus Grounds', '2025-12-29 04:50:52', '2026-03-29 04:50:52', NULL, 'pending', NULL, NULL),
('VIO-4391', 'NDF3545', 'invalid_parking', 'Computed violation timeframe of 1 months.', 'Campus Grounds', '2026-01-04 05:18:27', '2026-02-04 05:18:27', NULL, 'resolved', '2026-01-07 01:52:29', NULL),
('VIO-5444', 'UVJ696', 'expired_permit', 'Computed violation timeframe of 3 months.', 'Campus Grounds', '2026-01-05 04:19:06', '2026-04-05 04:19:06', NULL, 'pending', NULL, NULL),
('VIO-6097', 'QWE3456', 'invalid_parking', 'Parked in faculty area without permit', 'Building A Parking', '2026-01-06 10:58:06', '2026-02-06 10:58:06', NULL, 'pending', NULL, NULL),
('VIO-6512', 'DEF456', 'invalid_parking', 'Parked in faculty area without permit', 'Building A Parking', '2026-01-06 10:47:56', '2026-02-06 10:47:56', NULL, 'pending', NULL, NULL);

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

--
-- Dumping data for table `visitor`
--

INSERT INTO `visitor` (`visitorID`, `passID`, `fullName`, `plateNum`, `purposeOfVisit`, `createdAt`) VALUES
('V_DUMMY_1', NULL, 'John Doe Visitor', 'VIS-001', 'Meeting', '2026-01-07 15:55:45'),
('V_DUMMY_2', NULL, 'Jane Smith Visitor', 'VIS-002', 'Delivery', '2026-01-07 15:55:45'),
('V_DUMMY_3', NULL, 'Bob Brown Visitor', 'VIS-003', 'Guest', '2026-01-07 15:55:45');

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
  ADD KEY `idx_handled` (`handled`),
  ADD KEY `idx_historical_status_date` (`status`,`entryTime`,`exitTime`);

--
-- Indexes for table `parkingstatus`
--
ALTER TABLE `parkingstatus`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_parkingstatus_id` (`id`);

--
-- Indexes for table `rfidtag`
--
ALTER TABLE `rfidtag`
  ADD PRIMARY KEY (`stickerID`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_tagCode` (`tagCode`);

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
  ADD KEY `visitorID_idx` (`visitorID`),
  ADD KEY `idx_vehicle_owner_visitor` (`plateNum`,`OwnerID`,`visitorID`);

--
-- Indexes for table `vehicleowner`
--
ALTER TABLE `vehicleowner`
  ADD PRIMARY KEY (`OwnerID`),
  ADD KEY `idx_owner_role` (`OwnerID`,`role`);

--
-- Indexes for table `vehiclepass`
--
ALTER TABLE `vehiclepass`
  ADD PRIMARY KEY (`passID`),
  ADD KEY `idx_status` (`status`);

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
  MODIFY `logID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=164;

--
-- AUTO_INCREMENT for table `applications`
--
ALTER TABLE `applications`
  MODIFY `applicationID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `parkingstatus`
--
ALTER TABLE `parkingstatus`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=160;

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
