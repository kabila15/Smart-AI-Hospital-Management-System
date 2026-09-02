-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 02, 2026 at 09:52 AM
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
-- Database: `myhmsdb`
--

-- --------------------------------------------------------

--
-- Table structure for table `admintb`
--

CREATE TABLE `admintb` (
  `username` varchar(50) NOT NULL,
  `password` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `admintb`
--

INSERT INTO `admintb` (`username`, `password`) VALUES
('admin', 'admin123');

-- --------------------------------------------------------

--
-- Table structure for table `appointmenttb`
--

CREATE TABLE `appointmenttb` (
  `pid` int(11) NOT NULL,
  `ID` int(11) NOT NULL,
  `fname` varchar(20) NOT NULL,
  `lname` varchar(20) NOT NULL,
  `gender` varchar(10) NOT NULL,
  `email` varchar(30) NOT NULL,
  `contact` varchar(10) NOT NULL,
  `doctor` varchar(30) NOT NULL,
  `docFees` int(5) NOT NULL,
  `appdate` date NOT NULL,
  `apptime` time NOT NULL,
  `userStatus` int(5) NOT NULL,
  `doctorStatus` int(5) NOT NULL,
  `token_no` int(11) DEFAULT NULL,
  `expected_time` time DEFAULT NULL,
  `delayed_mins` int(11) DEFAULT 0,
  `qr_token` varchar(100) DEFAULT NULL,
  `arrival_status` int(11) DEFAULT 0 COMMENT '0=Pending, 1=Arrived, 2=Late',
  `serving_status` int(11) DEFAULT 0 COMMENT '0=Waiting, 1=Serving, 2=Completed, 3=Skipped',
  `paymentStatus` varchar(20) DEFAULT 'Pending',
  `symptom_text` text DEFAULT NULL,
  `is_priority` int(11) DEFAULT 0,
  `session_type` varchar(20) DEFAULT 'Morning',
  `checked_in` tinyint(4) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `appointmenttb`
--

INSERT INTO `appointmenttb` (`pid`, `ID`, `fname`, `lname`, `gender`, `email`, `contact`, `doctor`, `docFees`, `appdate`, `apptime`, `userStatus`, `doctorStatus`, `token_no`, `expected_time`, `delayed_mins`, `qr_token`, `arrival_status`, `serving_status`, `paymentStatus`, `symptom_text`, `is_priority`, `session_type`, `checked_in`) VALUES
(4, 1, 'Kishan', 'Lal', 'Male', 'kishansmart0@gmail.com', '8838489464', 'Ganesh', 550, '2020-02-14', '10:00:00', 1, 0, NULL, NULL, 0, NULL, 0, 0, 'Pending', NULL, 0, 'Morning', 0),
(4, 2, 'Kishan', 'Lal', 'Male', 'kishansmart0@gmail.com', '8838489464', 'Dinesh', 700, '2020-02-28', '10:00:00', 0, 1, NULL, NULL, 0, NULL, 0, 0, 'Pending', NULL, 0, 'Morning', 0),
(4, 3, 'Kishan', 'Lal', 'Male', 'kishansmart0@gmail.com', '8838489464', 'Amit', 1000, '2020-02-19', '03:00:00', 0, 1, NULL, NULL, 0, NULL, 0, 0, 'Pending', NULL, 0, 'Morning', 0),
(11, 4, 'Shraddha', 'Kapoor', 'Female', 'shraddha@gmail.com', '9768946252', 'ashok', 500, '2020-02-29', '20:00:00', 1, 1, NULL, NULL, 0, NULL, 0, 0, 'Pending', NULL, 0, 'Morning', 0),
(4, 5, 'Kishan', 'Lal', 'Male', 'kishansmart0@gmail.com', '8838489464', 'Dinesh', 700, '2020-02-28', '12:00:00', 1, 1, NULL, NULL, 0, NULL, 0, 0, 'Pending', NULL, 0, 'Morning', 0),
(4, 6, 'Kishan', 'Lal', 'Male', 'kishansmart0@gmail.com', '8838489464', 'Ganesh', 550, '2020-02-26', '15:00:00', 0, 1, NULL, NULL, 0, NULL, 0, 0, 'Pending', NULL, 0, 'Morning', 0),
(2, 8, 'Alia', 'Bhatt', 'Female', 'alia@gmail.com', '8976897689', 'Ganesh', 550, '2020-03-21', '10:00:00', 1, 1, NULL, NULL, 0, NULL, 0, 0, 'Pending', NULL, 0, 'Morning', 0),
(5, 9, 'Gautam', 'Shankararam', 'Male', 'gautam@gmail.com', '9070897653', 'Ganesh', 550, '2020-03-19', '20:00:00', 1, 0, NULL, NULL, 0, NULL, 0, 0, 'Pending', NULL, 0, 'Morning', 0),
(4, 10, 'Kishan', 'Lal', 'Male', 'kishansmart0@gmail.com', '8838489464', 'Ganesh', 550, '0000-00-00', '14:00:00', 1, 0, NULL, NULL, 0, NULL, 0, 0, 'Pending', NULL, 0, 'Morning', 0),
(4, 11, 'Kishan', 'Lal', 'Male', 'kishansmart0@gmail.com', '8838489464', 'Dinesh', 700, '2020-03-27', '15:00:00', 1, 1, NULL, NULL, 0, NULL, 0, 0, 'Pending', NULL, 0, 'Morning', 0),
(9, 12, 'William', 'Blake', 'Male', 'william@gmail.com', '8683619153', 'Kumar', 800, '2020-03-26', '12:00:00', 1, 1, NULL, NULL, 0, NULL, 0, 0, 'Pending', NULL, 0, 'Morning', 0),
(9, 13, 'William', 'Blake', 'Male', 'william@gmail.com', '8683619153', 'Tiwary', 450, '2020-03-26', '14:00:00', 1, 1, NULL, NULL, 0, NULL, 0, 0, 'Pending', NULL, 0, 'Morning', 0),
(12, 14, 'Test', 'User', 'Male', 'test@test.com', '1234567890', 'Amit', 1000, '2026-02-23', '10:00:00', 1, 1, 1, '10:00:00', 0, NULL, 0, 0, 'Pending', NULL, 0, 'Morning', 0),
(12, 15, 'Test', 'User', 'Male', 'test@test.com', '1234567890', 'Amit', 1000, '2026-02-24', '10:00:00', 1, 1, 1, '10:00:00', 0, NULL, 0, 0, 'Pending', 'severe chest pain and shortness of breath', 1, 'Morning', 0),
(12, 16, 'Test', 'User', 'Male', 'test@test.com', '1234567890', 'Dinesh', 700, '2026-02-24', '10:00:00', 1, 1, 1, '10:00:00', 0, NULL, 0, 0, 'Pending', 'allergies\r\n\r\n\r\n\r\n', 0, 'Morning', 0),
(12, 17, 'Test', 'User', 'Male', 'test@test.com', '1234567890', 'Dinesh', 700, '2026-02-23', '10:00:00', 1, 1, 1, '10:00:00', 0, NULL, 0, 0, 'Pending', 'vomit,pain ,fever', 0, 'Morning', 0),
(12, 18, 'Test', 'User', 'Male', 'test@test.com', '1234567890', 'ashok', 500, '2026-02-23', '10:00:00', 1, 1, 1, '10:00:00', 0, NULL, 0, 0, 'Pending', 'i have cough and fever\r\n\r\n\r\n', 0, 'Morning', 0),
(13, 19, 'kabila', 'V', 'Female', 'kabila15@gmail.com', '6369843994', 'Abbis', 1500, '2026-02-23', '10:00:00', 1, 0, 1, '10:00:00', 0, NULL, 0, 2, 'Paid', 'i have allergies\r\n', 0, 'Morning', 0),
(13, 20, 'kabila', 'V', 'Female', 'kabila15@gmail.com', '6369843994', 'Abbis', 1500, '2026-02-23', '10:15:00', 1, 0, 2, '10:15:00', 0, NULL, 0, 2, 'Pending', 'Atopic Dermatitis\r\n', 0, 'Morning', 0),
(12, 21, 'Test', 'User', 'Male', 'test@test.com', '1234567890', 'Abbis', 1500, '2026-02-23', '10:30:00', 1, 0, 3, '10:30:00', 0, NULL, 0, 3, 'Pending', 'i have a allergies', 0, 'Morning', 0),
(13, 22, 'kabila', 'V', 'Female', 'kabila15@gmail.com', '6369843994', 'ashok', 500, '2026-02-23', '10:15:00', 0, 1, 2, '10:15:00', 0, NULL, 0, 2, 'Pending', 'i have fever', 0, 'Morning', 0),
(13, 23, 'kabila', 'V', 'Female', 'kabila15@gmail.com', '6369843994', 'arun', 600, '2026-02-23', '10:00:00', 1, 1, 1, '10:00:00', 0, NULL, 0, 0, 'Pending', 'i have a chest pain', 1, 'Morning', 0),
(13, 24, 'kabila', 'V', 'Female', 'kabila15@gmail.com', '6369843994', 'arun', 600, '2026-02-23', '10:15:00', 1, 1, 2, '10:15:00', 0, NULL, 0, 0, 'Pending', 'chest', 0, 'Morning', 0),
(13, 25, 'kabila', 'V', 'Female', 'kabila15@gmail.com', '6369843994', 'ashok', 500, '2026-02-25', '10:00:00', 1, 1, 1, '10:00:00', 0, NULL, 0, 0, 'Pending', 'i have a fever', 0, 'Morning', 0),
(13, 26, 'kabila', 'V', 'Female', 'kabila15@gmail.com', '6369843994', 'ashok', 500, '2026-02-25', '10:15:00', 1, 1, 2, '10:15:00', 0, NULL, 0, 0, 'Pending', 'i have a fever', 0, 'Morning', 0),
(13, 27, 'kabila', 'V', 'Female', 'kabila15@gmail.com', '6369843994', 'Dinesh', 700, '2026-02-25', '10:00:00', 1, 1, 1, '10:00:00', 15, NULL, 0, 0, 'Pending', 'i have fever and stomach pain', 0, 'Morning', 0),
(13, 28, 'kabila', 'V', 'Female', 'kabila15@gmail.com', '6369843994', 'arun', 600, '2026-02-25', '10:00:00', 1, 1, 1, '10:00:00', 0, '620947a8e5ab9b796ab2020d7150bdc8', 0, 0, 'Pending', 'chest pain', 1, 'Morning', 0),
(13, 29, 'kabila', 'V', 'Female', 'kabila15@gmail.com', '6369843994', 'Dinesh', 700, '2026-02-25', '10:15:00', 1, 1, 2, '10:15:00', 15, '573e4d451ff73cf7fed7fa5d3ede92ac', 0, 0, 'Pending', 'i have cough', 0, 'Morning', 0),
(13, 30, 'kabila', 'V', 'Female', 'kabila15@gmail.com', '6369843994', 'Dinesh', 700, '2026-02-25', '10:30:00', 1, 1, 3, '10:30:00', 0, '1a3a594c1155ef378e5d4b288683e281', 0, 2, 'Pending', 'i have fever', 0, 'Morning', 0),
(13, 31, 'kabila', 'V', 'Female', 'kabila15@gmail.com', '6369843994', 'ashok', 500, '2026-02-27', '10:00:00', 1, 1, 1, '10:00:00', 0, '6f6d06ee6cc1efda3102b805d697a933', 0, 3, 'Pending', 'i have fever', 0, 'Morning', 0),
(13, 32, 'kabila', 'V', 'Female', 'kabila15@gmail.com', '6369843994', 'arun', 600, '2026-02-27', '10:00:00', 1, 1, 1, '10:00:00', 0, 'd9fe3035ec583d67141aae27d62ac808', 0, 1, 'Pending', 'i have chest pain', 1, 'Morning', 0),
(14, 33, 'Kavi ', 'Priya', 'Female', 'kavi19062006@gmail.com', '6382275276', 'ashok', 500, '2026-02-27', '10:15:00', 1, 1, 2, '10:15:00', 0, '03bc5376dd790dfd3f653ba95ef3442f', 1, 2, 'Pending', 'i have a fever', 0, 'Morning', 1),
(15, 34, 'Vijay', 'Kumar', 'Male', 'vijaykumar@gmail.com', '9865286374', 'Abbis', 1500, '2026-02-27', '10:00:00', 1, 1, 1, '10:00:00', 0, 'bcff17c767f878c55617502968ec9bf0', 0, 0, 'Pending', 'i have headache', 0, 'Morning', 0),
(15, 35, 'Vijay', 'Kumar', 'Male', 'vijaykumar@gmail.com', '9865286374', 'ashok', 500, '2026-02-27', '10:30:00', 1, 1, 3, '10:30:00', 0, '3eb81873369a5de3e57c0724bf379d1e', 0, 0, 'Pending', 'i have fever', 0, 'Morning', 0),
(15, 36, 'Vijay', 'Kumar', 'Male', 'vijaykumar@gmail.com', '9865286374', 'Abbis', 1500, '2026-02-27', '10:15:00', 1, 1, 2, '10:15:00', 0, 'aa42f624c36043159883c50235df9395', 0, 0, 'Pending', 'i have fever and headache', 0, 'Morning', 0),
(15, 37, 'Vijay', 'Kumar', 'Male', 'vijaykumar@gmail.com', '9865286374', 'arun', 600, '2026-02-27', '10:15:00', 1, 1, 2, '10:15:00', 0, '9e16ba03c66e17c537bf08d45a2ba87b', 0, 1, 'Pending', 'i have a leg pain', 1, 'Morning', 1),
(15, 38, 'Vijay', 'Kumar', 'Male', 'vijaykumar@gmail.com', '9865286374', 'ashok', 500, '2026-02-27', '10:45:00', 1, 1, 4, '10:45:00', 0, 'd4c3a68cc12ccdabca72907f8edbbf34', 0, 0, 'Pending', 'i have a fever', 0, 'Morning', 0),
(15, 39, 'Vijay', 'Kumar', 'Male', 'vijaykumar@gmail.com', '9865286374', 'Dinesh', 700, '2026-02-27', '17:00:00', 1, 1, 1, '17:00:00', 0, 'a52e74a783e7165ef3dae564b88a99b9', 0, 0, 'Pending', 'i have a stomach', 0, 'Evening', 0),
(15, 40, 'Vijay', 'Kumar', 'Male', 'vijaykumar@gmail.com', '9865286374', 'ashok', 500, '2026-02-27', '17:00:00', 1, 1, 1, '17:00:00', 0, '4780673a24fa886743f5c44bb93dfec6', 0, 0, 'Pending', 'i have cough', 0, 'Evening', 0),
(13, 41, 'kabila', 'V', 'Female', 'kabila15@gmail.com', '6369843994', 'arun', 600, '2026-02-27', '10:30:00', 1, 1, 3, '10:30:00', 0, 'fe7b26815c9448ccd818ace3557a280d', 0, 1, 'Pending', 'i have a chest pain', 1, 'Morning', 0),
(16, 42, 'Shammu', 'V', 'Female', 'Shanmuga13@gmail.com', '6382394025', 'ashok', 500, '2026-03-02', '17:00:00', 1, 1, 1, '17:00:00', 0, 'e05ea3675f337f9693fa660e89c0c399', 0, 0, 'Pending', 'i have a fever', 0, 'Evening', 0),
(17, 43, 'Test', 'Patient', 'Male', 'testpatient@example.com', '1234567890', 'ashok', 500, '2026-02-27', '11:00:00', 1, 1, 5, '11:00:00', 0, '634c90226c7b0274d4ac2fdf746690eb', 0, 0, 'Pending', 'Testing WhatsApp Today', 0, 'Morning', 0),
(16, 44, 'Shammu', 'V', 'Female', 'Shanmuga13@gmail.com', '6382394025', 'Dinesh', 700, '2026-03-02', '10:00:00', 1, 1, 1, '10:00:00', 0, '6f344bd050aac7a5c95fad22da152987', 0, 0, 'Pending', 'i have a fever', 0, 'Morning', 0),
(16, 45, 'Shammu', 'V', 'Female', 'Shanmuga13@gmail.com', '6382394025', 'ashok', 500, '2026-03-02', '10:00:00', 1, 1, 1, '10:00:00', 0, '14d456eb517dff0a68917a779e8f8d9a', 0, 0, 'Pending', 'i have a cough', 0, 'Morning', 0),
(16, 46, 'Shammu', 'V', 'Female', 'Shanmuga13@gmail.com', '6382394025', 'ashok', 500, '2026-03-02', '10:15:00', 1, 1, 2, '10:15:00', 0, '90e2935cf84a8e0a4dacecda105ef2cc', 0, 0, 'Pending', 'i have a fever', 0, 'Morning', 0),
(16, 47, 'Shammu', 'V', 'Female', 'Shanmuga13@gmail.com', '6382394025', 'Dinesh', 700, '2026-03-02', '10:15:00', 1, 1, 2, '10:15:00', 0, 'd94dc5729a695d81ba6b2d9feaa8d14e', 0, 0, 'Pending', 'i have a stomach', 0, 'Morning', 0),
(16, 48, 'Shammu', 'V', 'Female', 'Shanmuga13@gmail.com', '6382394025', 'Dinesh', 700, '2026-03-02', '10:30:00', 1, 1, 3, '10:30:00', 0, '0fd0e255490c3f43f61024fabb4e9f05', 0, 0, 'Pending', 'i have a fever', 0, 'Morning', 0),
(16, 49, 'Shammu', 'V', 'Female', 'Shanmuga13@gmail.com', '6382394025', 'Amit', 1000, '2026-03-02', '10:00:00', 1, 1, 1, '10:00:00', 0, 'bf130646e959b34395d2aa71093de8ff', 0, 0, 'Pending', 'i have a chest pain', 1, 'Morning', 0),
(16, 50, 'Shammu', 'V', 'Female', 'Shanmuga13@gmail.com', '6382394025', 'Abbis', 1500, '2026-03-02', '10:00:00', 1, 1, 1, '10:00:00', 0, '5ca856743d65303e3dd93dc4ce1a32df', 0, 0, 'Pending', 'i have headache', 0, 'Morning', 0),
(16, 51, 'Shammu', 'V', 'Female', 'Shanmuga13@gmail.com', '6382394025', 'Abbis', 1500, '2026-03-02', '10:15:00', 1, 1, 2, '10:15:00', 0, 'e99e18ea55925f21bebe3c3b00e06e11', 0, 0, 'Pending', 'i have a brain stroke', 0, 'Morning', 0),
(16, 52, 'Shammu', 'V', 'Female', 'Shanmuga13@gmail.com', '6382394025', 'arun', 600, '2026-03-02', '10:00:00', 1, 1, 1, '10:00:00', 0, 'd5aa09daa2f9ef9856d57d5482b20ec9', 0, 2, 'Pending', 'i have leg pain', 1, 'Morning', 0),
(16, 53, 'Shammu', 'V', 'Female', 'Shanmuga13@gmail.com', '6382394025', 'Dinesh', 700, '2026-03-03', '10:00:00', 1, 1, 1, '10:00:00', 0, '640af0b95d9db1df6e00f2f4d788ad35', 0, 0, 'Pending', 'i have cough', 0, 'Morning', 0),
(16, 54, 'Shammu', 'V', 'Female', 'Shanmuga13@gmail.com', '6382394025', 'Dinesh', 700, '2026-03-02', '10:45:00', 1, 1, 4, '10:45:00', 0, 'f4868932c2cbe3688ed97795bc9b13e1', 0, 0, 'Pending', 'i have a fever', 0, 'Morning', 0),
(16, 55, 'Shammu', 'V', 'Female', 'Shanmuga13@gmail.com', '6382394025', 'arun', 600, '2026-03-02', '10:15:00', 1, 1, 2, '10:15:00', 0, '9fd918a6cab7297882d5669b59097d95', 0, 1, 'Pending', 'i have a chest', 1, 'Morning', 0),
(16, 56, 'Shammu', 'V', 'Female', 'Shanmuga13@gmail.com', '6382394025', 'Dinesh', 700, '2026-02-28', '14:00:00', 1, 1, 1, '14:00:00', 0, 'd771b9beffc7f3a43528595ade3cce1d', 0, 0, 'Pending', NULL, 0, 'Evening', 0),
(16, 57, 'Shammu', 'V', 'Female', 'Shanmuga13@gmail.com', '6382394025', 'Abbis', 1500, '2026-02-28', '10:00:00', 1, 1, 1, '10:00:00', 0, '73dad765956fbc723c97e9efe45dff95', 1, 0, 'Pending', NULL, 0, 'Morning', 1),
(14, 58, 'Kavi ', 'Priya', 'Female', 'kavi19062006@gmail.com', '6382275276', 'Dinesh', 700, '2026-03-02', '11:00:00', 1, 1, 5, '11:00:00', 0, 'cf9c60b5bc815629bb2f71cb09097958', 0, 0, 'Pending', 'i have a cough', 0, 'Morning', 0),
(14, 59, 'Kavi ', 'Priya', 'Female', 'kavi19062006@gmail.com', '6382275276', 'ashok', 500, '2026-03-02', '10:30:00', 1, 1, 3, '10:30:00', 0, '0b9385b2bac1be16e67990b669e790aa', 0, 0, 'Pending', 'i have fever', 0, 'Morning', 0),
(18, 60, 'Archana', 'M', 'Female', 'archana12@gmail.com', '7639360884', 'Abbis', 1500, '2026-07-29', '10:00:00', 1, 1, 1, '10:00:00', 0, 'eeaa7caa47a3db208ac9fcf452da669e', 0, 0, 'Pending', 'i have a headache', 0, 'Morning', 0),
(13, 61, 'kabila', 'V', 'Female', 'kabila15@gmail.com', '6369843994', 'ashok', 500, '2026-08-24', '10:00:00', 1, 1, 1, '10:00:00', 0, '20245adb741e4621310dd88e1c306e86', 1, 0, 'Pending', 'i have a mild fever and stomach ', 0, 'Morning', 1),
(13, 62, 'kabila', 'V', 'Female', 'kabila15@gmail.com', '6369843994', 'ashok', 500, '2026-08-21', '10:00:00', 0, 1, 1, '10:00:00', 0, 'defa6d7991332e07e51ebb2be9b40424', 0, 0, 'Pending', 'i have a fever', 0, 'Morning', 0),
(13, 63, 'kabila', 'V', 'Female', 'kabila15@gmail.com', '6369843994', 'arun', 600, '2026-08-24', '10:00:00', 1, 1, 1, '10:00:00', 0, '77c9f60d795391a5649f9c2d5c11003e', 0, 2, 'Pending', 'heart problem', 1, 'Morning', 0),
(16, 64, 'Shammu', 'V', 'Female', 'Shanmuga13@gmail.com', '6382394025', 'ashok', 500, '2026-08-24', '10:15:00', 1, 1, 2, '10:15:00', 0, 'c4fc3fe92437a8689086660e8e52d2d7', 0, 0, 'Pending', 'i have a fever', 0, 'Morning', 0),
(16, 65, 'Shammu', 'V', 'Female', 'Shanmuga13@gmail.com', '6382394025', 'ashok', 500, '2026-08-24', '10:30:00', 1, 1, 3, '10:30:00', 0, '2842ceae63ec76b27a67c1cdbdde84ca', 0, 0, 'Pending', 'i have a fever', 0, 'Morning', 0),
(13, 66, 'kabila', 'V', 'Female', 'kabila15@gmail.com', '6369843994', 'ashok', 500, '2026-08-24', '10:45:00', 1, 1, 4, '10:45:00', 0, 'c740cd61573f400749eafd3274b9f348', 0, 0, 'Pending', 'i have a fever', 0, 'Morning', 0),
(13, 67, 'kabila', 'V', 'Female', 'kabila15@gmail.com', '6369843994', 'ashok', 500, '2026-08-24', '11:00:00', 1, 1, 5, '11:00:00', 0, '147c019712b14bc8c86fa8fa0d08ce36', 0, 0, 'Pending', 'i have a fever', 0, 'Morning', 0),
(13, 68, 'kabila', 'V', 'Female', 'kabila15@gmail.com', '6369843994', 'ashok', 500, '2026-08-24', '11:15:00', 1, 1, 6, '11:15:00', 0, '595602df7af25213b1c5680199a01cfb', 0, 0, 'Pending', 'i have a fever', 0, 'Morning', 0),
(13, 69, 'kabila', 'V', 'Female', 'kabila15@gmail.com', '6369843994', 'ashok', 500, '2026-08-24', '11:30:00', 1, 1, 7, '11:30:00', 0, '6b002f84098888f82a9a7872b9e4fb05', 0, 0, 'Pending', 'i have a fever', 0, 'Morning', 0),
(13, 70, 'kabila', 'V', 'Female', 'kabila15@gmail.com', '6369843994', 'ashok', 500, '2026-08-24', '11:45:00', 1, 1, 8, '11:45:00', 0, '1a33485856608ffabf79c1ab03006ccd', 0, 0, 'Pending', 'i ahve a fever', 0, 'Morning', 0),
(13, 71, 'kabila', 'V', 'Female', 'kabila15@gmail.com', '6369843994', 'ashok', 500, '2026-08-24', '12:00:00', 1, 1, 9, '12:00:00', 0, 'c8dd33edb9f6236401b46b727601caa2', 0, 0, 'Pending', 'i have fever', 0, 'Morning', 0),
(13, 72, 'kabila', 'V', 'Female', 'kabila15@gmail.com', '6369843994', 'ashok', 500, '2026-08-25', '10:00:00', 1, 1, 1, '10:00:00', 0, '73231dd56dfce2b9e45544d4918ebe5f', 0, 0, 'Pending', 'i have a fever', 0, 'Morning', 0),
(16, 73, 'Shammu', 'V', 'Female', 'Shanmuga13@gmail.com', '6382394025', 'ashok', 500, '2026-08-23', '10:00:00', 1, 1, 1, '10:00:00', 0, 'cbb6bd098fde38ad51a7440188bb3715', 0, 2, 'Pending', NULL, 0, 'Morning', 0),
(13, 74, 'kabila', 'V', 'Female', 'kabila15@gmail.com', '6369843994', 'ashok', 500, '2026-08-26', '10:00:00', 1, 1, 1, '10:00:00', 0, 'ca16ec557bb6d10931b0fd714eb369dd', 0, 0, 'Pending', 'i have a fever', 0, 'Morning', 0),
(13, 75, 'kabila', 'V', 'Female', 'kabila15@gmail.com', '6369843994', 'ashok', 500, '2026-08-24', '12:15:00', 1, 1, 10, '12:15:00', 0, 'df34cedea71110fc9b0208276a49c29a', 0, 0, 'Pending', 'i have a allergy', 0, 'Morning', 0),
(16, 76, 'Shammu', 'V', 'Female', 'Shanmuga13@gmail.com', '6382394025', 'Dinesh', 700, '2026-08-25', '10:00:00', 1, 1, 1, '10:00:00', 0, '31690e44868e39d239fc93a67d89a368', 0, 0, 'Pending', 'i have fever', 0, 'Morning', 0),
(16, 77, 'Shammu', 'V', 'Female', 'Shanmuga13@gmail.com', '6382394025', 'Dinesh', 700, '2026-08-25', '10:15:00', 1, 1, 2, '10:15:00', 0, 'ad5d9cefd3bf07b0c1e709f411aa8e85', 0, 0, 'Pending', 'i have fever', 0, 'Morning', 0),
(16, 78, 'Shammu', 'V', 'Female', 'Shanmuga13@gmail.com', '6382394025', 'Dinesh', 700, '2026-08-25', '10:30:00', 1, 1, 3, '10:30:00', 0, 'f6531802ad3071c9dab66231160b13c5', 0, 0, 'Pending', 'i have fever', 0, 'Morning', 0),
(16, 79, 'Shammu', 'V', 'Female', 'Shanmuga13@gmail.com', '6382394025', 'ashok', 500, '2026-08-25', '10:15:00', 1, 1, 2, '10:15:00', 0, '642d817a0a3f72924eb1817a37dabfc5', 0, 0, 'Pending', 'i have a fever', 0, 'Morning', 0),
(16, 80, 'Shammu', 'V', 'Female', 'Shanmuga13@gmail.com', '6382394025', 'ashok', 500, '2026-08-25', '10:30:00', 1, 1, 3, '10:30:00', 0, '1b560ca39135736346a9ee9076a5e9a5', 0, 0, 'Pending', 'i have a fever', 0, 'Morning', 0),
(16, 81, 'Shammu', 'V', 'Female', 'Shanmuga13@gmail.com', '6382394025', 'ashok', 500, '2026-08-25', '10:45:00', 1, 1, 4, '10:45:00', 0, '0acc0811cbc379392706b00b3150cc60', 0, 0, 'Pending', 'i have a fever', 0, 'Morning', 0),
(13, 82, 'kabila', 'V', 'Female', 'kabila15@gmail.com', '6369843994', 'ashok', 500, '2026-08-28', '10:00:00', 1, 1, 1, '10:00:00', 0, '363d7653e495059207e8dd110acbdad7', 0, 0, 'Pending', 'i have a fever', 0, 'Morning', 0),
(13, 83, 'kabila', 'V', 'Female', 'kabila15@gmail.com', '6369843994', 'Abbis', 1500, '2026-08-28', '10:00:00', 1, 1, 1, '10:00:00', 0, 'b71cb70f8453c743da60b3adab34007d', 0, 0, 'Pending', 'i have a headche', 0, 'Morning', 0),
(13, 84, 'kabila', 'V', 'Female', 'kabila15@gmail.com', '6369843994', 'ashok', 500, '2026-08-28', '10:15:00', 1, 1, 2, '10:15:00', 0, 'be140cad5bce0a40474ecc09d07d791e', 0, 0, 'Pending', 'i have a fever', 0, 'Morning', 0),
(13, 85, 'kabila', 'V', 'Female', 'kabila15@gmail.com', '6369843994', 'Abbis', 1500, '2026-08-28', '10:15:00', 1, 1, 2, '10:15:00', 0, '06313c571ef8fb8c774f53a893499cff', 0, 0, 'Pending', 'i have a fver', 0, 'Morning', 0),
(13, 86, 'kabila', 'V', 'Female', 'kabila15@gmail.com', '6369843994', 'ashok', 500, '2026-08-28', '10:30:00', 1, 1, 3, '10:30:00', 0, '0e1aaf7f92a7a921e26def5db6539d13', 0, 0, 'Pending', 'i have a fever', 0, 'Morning', 0),
(13, 87, 'kabila', 'V', 'Female', 'kabila15@gmail.com', '6369843994', 'ashok', 500, '2026-08-28', '10:45:00', 1, 1, 4, '10:45:00', 0, 'e8fd41cad9e61b096d31e07a43dda0a1', 0, 0, 'Pending', 'i have a fever', 0, 'Morning', 0),
(13, 88, 'kabila', 'V', 'Female', 'kabila15@gmail.com', '6369843994', 'Dinesh', 700, '2026-09-02', '10:00:00', 0, 1, 1, '10:00:00', 0, '7a8321985991e65efe62121c7e32c89c', 0, 0, 'Pending', 'i have a fever', 0, 'Morning', 0),
(13, 89, 'kabila', 'V', 'Female', 'kabila15@gmail.com', '6369843994', 'Dinesh', 700, '2026-08-30', '10:00:00', 1, 1, 1, '10:00:00', 0, '658cd3658239a8757e0e4398b6760a6e', 1, 0, 'Pending', 'i have fever', 0, 'Morning', 1),
(13, 90, 'kabila', 'V', 'Female', 'kabila15@gmail.com', '6369843994', 'Abbis', 1500, '2026-08-31', '10:00:00', 1, 1, 1, '10:00:00', 0, '97375b6cac0fa81303ed94bb765a21be', 0, 0, 'Pending', 'I have a allergies ', 0, 'Morning', 0);

-- --------------------------------------------------------

--
-- Table structure for table `contact`
--

CREATE TABLE `contact` (
  `name` varchar(30) NOT NULL,
  `email` text NOT NULL,
  `contact` varchar(10) NOT NULL,
  `message` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `contact`
--

INSERT INTO `contact` (`name`, `email`, `contact`, `message`) VALUES
('Anu', 'anu@gmail.com', '7896677554', 'Hey Admin'),
(' Viki', 'viki@gmail.com', '9899778865', 'Good Job, Pal'),
('Ananya', 'ananya@gmail.com', '9997888879', 'How can I reach you?'),
('Aakash', 'aakash@gmail.com', '8788979967', 'Love your site'),
('Mani', 'mani@gmail.com', '8977768978', 'Want some coffee?'),
('Karthick', 'karthi@gmail.com', '9898989898', 'Good service'),
('Abbis', 'abbis@gmail.com', '8979776868', 'Love your service'),
('Asiq', 'asiq@gmail.com', '9087897564', 'Love your service. Thank you!'),
('Jane', 'jane@gmail.com', '7869869757', 'I love your service!');

-- --------------------------------------------------------

--
-- Table structure for table `doctb`
--

CREATE TABLE `doctb` (
  `username` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `spec` varchar(50) NOT NULL,
  `docFees` int(10) NOT NULL,
  `m_start` time DEFAULT '10:00:00',
  `m_end` time DEFAULT '13:00:00',
  `m_cap` int(11) DEFAULT 12,
  `e_start` time DEFAULT '17:00:00',
  `e_end` time DEFAULT '20:00:00',
  `e_cap` int(11) DEFAULT 12
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `doctb`
--

INSERT INTO `doctb` (`username`, `password`, `email`, `spec`, `docFees`, `m_start`, `m_end`, `m_cap`, `e_start`, `e_end`, `e_cap`) VALUES
('ashok', 'ashok123', 'ashok@gmail.com', 'General', 500, '10:00:00', '13:00:00', 12, '17:00:00', '20:00:00', 12),
('arun', 'arun123', 'arun@gmail.com', 'Cardiologist', 600, '10:00:00', '13:00:00', 12, '17:00:00', '20:00:00', 12),
('Dinesh', 'dinesh123', 'dinesh@gmail.com', 'General', 700, '10:00:00', '13:00:00', 12, '17:00:00', '20:00:00', 12),
('Ganesh', 'ganesh123', 'ganesh@gmail.com', 'Pediatrician', 550, '10:00:00', '13:00:00', 12, '17:00:00', '20:00:00', 12),
('Kumar', 'kumar123', 'kumar@gmail.com', 'Pediatrician', 800, '10:00:00', '13:00:00', 12, '17:00:00', '20:00:00', 12),
('Amit', 'amit123', 'amit@gmail.com', 'Cardiologist', 1000, '10:00:00', '13:00:00', 12, '17:00:00', '20:00:00', 12),
('Abbis', 'abbis123', 'abbis@gmail.com', 'Neurologist', 1500, '10:00:00', '13:00:00', 12, '17:00:00', '20:00:00', 12),
('Tiwary', 'tiwary123', 'tiwary@gmail.com', 'Pediatrician', 450, '10:00:00', '13:00:00', 12, '17:00:00', '20:00:00', 12),
('Priya', 'priya123', 'priya35@gmail.com', 'Dermatology', 600, '10:00:00', '13:00:00', 12, '17:00:00', '20:00:00', 12),
('Sanjay', 'sanjay123', 'sanju@gmail.com', 'Pulmonology', 600, '10:00:00', '13:00:00', 12, '17:00:00', '20:00:00', 12),
('Sanjay', 'sanjay123', 'sanju@gmail.com', 'Pulmonology', 600, '10:00:00', '13:00:00', 12, '17:00:00', '20:00:00', 12);

-- --------------------------------------------------------

--
-- Table structure for table `doctor_holidays`
--

CREATE TABLE `doctor_holidays` (
  `id` int(11) NOT NULL,
  `doctor_name` varchar(50) NOT NULL,
  `holiday_date` date NOT NULL,
  `reason` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `doctor_leaves`
--

CREATE TABLE `doctor_leaves` (
  `id` int(11) NOT NULL,
  `doctor` varchar(50) NOT NULL,
  `leave_date` date NOT NULL,
  `leave_type` varchar(20) NOT NULL,
  `reason` varchar(255) DEFAULT NULL,
  `status` varchar(20) DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `doctor_leaves`
--

INSERT INTO `doctor_leaves` (`id`, `doctor`, `leave_date`, `leave_type`, `reason`, `status`) VALUES
(1, 'Abbis', '2026-02-23', 'Planned', 'i have a one emergency', 'Approved'),
(2, 'Abbis', '2026-02-25', 'Planned', 'I have a plan on the day', 'Approved'),
(3, 'arun', '2026-08-21', 'Emergency', 'i have a emergency', 'Approved'),
(4, 'arun', '2026-08-25', 'Personal Leave', '', 'Rejected'),
(5, 'ashok', '2026-08-31', 'Personal Leave', '', 'Approved');

-- --------------------------------------------------------

--
-- Table structure for table `doctor_schedule`
--

CREATE TABLE `doctor_schedule` (
  `id` int(11) NOT NULL,
  `doctor_name` varchar(50) NOT NULL,
  `day_of_week` varchar(20) NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `avg_consult_time` int(11) DEFAULT 15,
  `session_capacity` int(11) DEFAULT 12,
  `session_type` varchar(20) DEFAULT 'Morning'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `doctor_schedule`
--

INSERT INTO `doctor_schedule` (`id`, `doctor_name`, `day_of_week`, `start_time`, `end_time`, `avg_consult_time`, `session_capacity`, `session_type`) VALUES
(3, 'ashok', 'Wednesday', '10:00:00', '13:00:00', 15, 12, 'Morning'),
(4, 'ashok', 'Thursday', '10:00:00', '13:00:00', 15, 12, 'Morning'),
(5, 'ashok', 'Friday', '10:00:00', '13:00:00', 15, 12, 'Morning'),
(6, 'arun', 'Monday', '10:00:00', '13:00:00', 15, 12, 'Morning'),
(7, 'arun', 'Tuesday', '10:00:00', '13:00:00', 15, 12, 'Morning'),
(8, 'arun', 'Wednesday', '10:00:00', '13:00:00', 15, 12, 'Morning'),
(9, 'arun', 'Thursday', '10:00:00', '13:00:00', 15, 12, 'Morning'),
(10, 'arun', 'Friday', '10:00:00', '13:00:00', 15, 12, 'Morning'),
(11, 'Dinesh', 'Monday', '10:00:00', '13:00:00', 15, 12, 'Morning'),
(12, 'Dinesh', 'Tuesday', '10:00:00', '13:00:00', 15, 12, 'Morning'),
(13, 'Dinesh', 'Wednesday', '10:00:00', '13:00:00', 15, 12, 'Morning'),
(14, 'Dinesh', 'Thursday', '10:00:00', '13:00:00', 15, 12, 'Morning'),
(15, 'Dinesh', 'Friday', '10:00:00', '13:00:00', 15, 12, 'Morning'),
(16, 'Ganesh', 'Monday', '10:00:00', '13:00:00', 15, 12, 'Morning'),
(17, 'Ganesh', 'Tuesday', '10:00:00', '13:00:00', 15, 12, 'Morning'),
(18, 'Ganesh', 'Wednesday', '10:00:00', '13:00:00', 15, 12, 'Morning'),
(19, 'Ganesh', 'Thursday', '10:00:00', '13:00:00', 15, 12, 'Morning'),
(20, 'Ganesh', 'Friday', '10:00:00', '13:00:00', 15, 12, 'Morning'),
(21, 'Kumar', 'Monday', '10:00:00', '13:00:00', 15, 12, 'Morning'),
(22, 'Kumar', 'Tuesday', '10:00:00', '13:00:00', 15, 12, 'Morning'),
(23, 'Kumar', 'Wednesday', '10:00:00', '13:00:00', 15, 12, 'Morning'),
(24, 'Kumar', 'Thursday', '10:00:00', '13:00:00', 15, 12, 'Morning'),
(25, 'Kumar', 'Friday', '10:00:00', '13:00:00', 15, 12, 'Morning'),
(26, 'Amit', 'Monday', '10:00:00', '13:00:00', 15, 12, 'Morning'),
(27, 'Amit', 'Tuesday', '10:00:00', '13:00:00', 15, 12, 'Morning'),
(28, 'Amit', 'Wednesday', '10:00:00', '13:00:00', 15, 12, 'Morning'),
(29, 'Amit', 'Thursday', '10:00:00', '13:00:00', 15, 12, 'Morning'),
(30, 'Amit', 'Friday', '10:00:00', '13:00:00', 15, 12, 'Morning'),
(31, 'Abbis', 'Monday', '10:00:00', '13:00:00', 15, 12, 'Morning'),
(32, 'Abbis', 'Tuesday', '10:00:00', '13:00:00', 15, 12, 'Morning'),
(33, 'Abbis', 'Wednesday', '10:00:00', '13:00:00', 15, 12, 'Morning'),
(34, 'Abbis', 'Thursday', '10:00:00', '13:00:00', 15, 12, 'Morning'),
(35, 'Abbis', 'Friday', '10:00:00', '13:00:00', 15, 12, 'Morning'),
(36, 'Tiwary', 'Monday', '10:00:00', '13:00:00', 15, 12, 'Morning'),
(37, 'Tiwary', 'Tuesday', '10:00:00', '13:00:00', 15, 12, 'Morning'),
(38, 'Tiwary', 'Wednesday', '10:00:00', '13:00:00', 15, 12, 'Morning'),
(39, 'Tiwary', 'Thursday', '10:00:00', '13:00:00', 15, 12, 'Morning'),
(40, 'Tiwary', 'Friday', '10:00:00', '13:00:00', 15, 12, 'Morning'),
(41, 'ashok', 'Monday', '17:00:00', '20:00:00', 15, 12, 'Evening'),
(42, 'ashok', 'Tuesday', '17:00:00', '20:00:00', 15, 12, 'Evening'),
(43, 'ashok', 'Wednesday', '17:00:00', '20:00:00', 15, 12, 'Evening'),
(44, 'ashok', 'Thursday', '17:00:00', '20:00:00', 15, 12, 'Evening'),
(45, 'ashok', 'Friday', '17:00:00', '20:00:00', 15, 12, 'Evening'),
(46, 'arun', 'Monday', '17:00:00', '20:00:00', 15, 12, 'Evening'),
(47, 'arun', 'Tuesday', '17:00:00', '20:00:00', 15, 12, 'Evening'),
(48, 'arun', 'Wednesday', '17:00:00', '20:00:00', 15, 12, 'Evening'),
(49, 'arun', 'Thursday', '17:00:00', '20:00:00', 15, 12, 'Evening'),
(50, 'arun', 'Friday', '17:00:00', '20:00:00', 15, 12, 'Evening'),
(51, 'Dinesh', 'Monday', '17:00:00', '20:00:00', 15, 12, 'Evening'),
(52, 'Dinesh', 'Tuesday', '17:00:00', '20:00:00', 15, 12, 'Evening'),
(53, 'Dinesh', 'Wednesday', '17:00:00', '20:00:00', 15, 12, 'Evening'),
(54, 'Dinesh', 'Thursday', '17:00:00', '20:00:00', 15, 12, 'Evening'),
(55, 'Dinesh', 'Friday', '17:00:00', '20:00:00', 15, 12, 'Evening'),
(56, 'Ganesh', 'Monday', '17:00:00', '20:00:00', 15, 12, 'Evening'),
(57, 'Ganesh', 'Tuesday', '17:00:00', '20:00:00', 15, 12, 'Evening'),
(58, 'Ganesh', 'Wednesday', '17:00:00', '20:00:00', 15, 12, 'Evening'),
(59, 'Ganesh', 'Thursday', '17:00:00', '20:00:00', 15, 12, 'Evening'),
(60, 'Ganesh', 'Friday', '17:00:00', '20:00:00', 15, 12, 'Evening'),
(61, 'Kumar', 'Monday', '17:00:00', '20:00:00', 15, 12, 'Evening'),
(62, 'Kumar', 'Tuesday', '17:00:00', '20:00:00', 15, 12, 'Evening'),
(63, 'Kumar', 'Wednesday', '17:00:00', '20:00:00', 15, 12, 'Evening'),
(64, 'Kumar', 'Thursday', '17:00:00', '20:00:00', 15, 12, 'Evening'),
(65, 'Kumar', 'Friday', '17:00:00', '20:00:00', 15, 12, 'Evening'),
(66, 'Amit', 'Monday', '17:00:00', '20:00:00', 15, 12, 'Evening'),
(67, 'Amit', 'Tuesday', '17:00:00', '20:00:00', 15, 12, 'Evening'),
(68, 'Amit', 'Wednesday', '17:00:00', '20:00:00', 15, 12, 'Evening'),
(69, 'Amit', 'Thursday', '17:00:00', '20:00:00', 15, 12, 'Evening'),
(70, 'Amit', 'Friday', '17:00:00', '20:00:00', 15, 12, 'Evening'),
(71, 'Abbis', 'Monday', '17:00:00', '20:00:00', 15, 12, 'Evening'),
(72, 'Abbis', 'Tuesday', '17:00:00', '20:00:00', 15, 12, 'Evening'),
(73, 'Abbis', 'Wednesday', '17:00:00', '20:00:00', 15, 12, 'Evening'),
(74, 'Abbis', 'Thursday', '17:00:00', '20:00:00', 15, 12, 'Evening'),
(75, 'Abbis', 'Friday', '17:00:00', '20:00:00', 15, 12, 'Evening'),
(76, 'Tiwary', 'Monday', '17:00:00', '20:00:00', 15, 12, 'Evening'),
(77, 'Tiwary', 'Tuesday', '17:00:00', '20:00:00', 15, 12, 'Evening'),
(78, 'Tiwary', 'Wednesday', '17:00:00', '20:00:00', 15, 12, 'Evening'),
(79, 'Tiwary', 'Thursday', '17:00:00', '20:00:00', 15, 12, 'Evening'),
(80, 'Tiwary', 'Friday', '17:00:00', '20:00:00', 15, 12, 'Evening'),
(81, 'Priya', 'Monday', '10:00:00', '13:00:00', 15, 12, 'Morning'),
(82, 'Priya', 'Monday', '17:00:00', '20:00:00', 15, 12, 'Evening'),
(83, 'Priya', 'Tuesday', '10:00:00', '13:00:00', 15, 12, 'Morning'),
(84, 'Priya', 'Tuesday', '17:00:00', '20:00:00', 15, 12, 'Evening'),
(85, 'Priya', 'Wednesday', '10:00:00', '13:00:00', 15, 12, 'Morning'),
(86, 'Priya', 'Wednesday', '17:00:00', '20:00:00', 15, 12, 'Evening'),
(87, 'Priya', 'Thursday', '10:00:00', '13:00:00', 15, 12, 'Morning'),
(88, 'Priya', 'Thursday', '17:00:00', '20:00:00', 15, 12, 'Evening'),
(89, 'Priya', 'Friday', '10:00:00', '13:00:00', 15, 12, 'Morning'),
(90, 'Priya', 'Friday', '17:00:00', '20:00:00', 15, 12, 'Evening'),
(91, 'Sanjay', 'Monday', '10:00:00', '13:00:00', 15, 12, 'Morning'),
(92, 'Sanjay', 'Monday', '17:00:00', '20:00:00', 15, 12, 'Evening'),
(93, 'Sanjay', 'Tuesday', '10:00:00', '13:00:00', 15, 12, 'Morning'),
(94, 'Sanjay', 'Tuesday', '17:00:00', '20:00:00', 15, 12, 'Evening'),
(95, 'Sanjay', 'Wednesday', '10:00:00', '13:00:00', 15, 12, 'Morning'),
(96, 'Sanjay', 'Wednesday', '17:00:00', '20:00:00', 15, 12, 'Evening'),
(97, 'Sanjay', 'Thursday', '10:00:00', '13:00:00', 15, 12, 'Morning'),
(98, 'Sanjay', 'Thursday', '17:00:00', '20:00:00', 15, 12, 'Evening'),
(99, 'Sanjay', 'Friday', '10:00:00', '13:00:00', 15, 12, 'Morning'),
(100, 'Sanjay', 'Friday', '17:00:00', '20:00:00', 15, 12, 'Evening'),
(101, 'Sanjay', 'Monday', '10:00:00', '13:00:00', 15, 12, 'Morning'),
(102, 'Sanjay', 'Monday', '17:00:00', '20:00:00', 15, 12, 'Evening'),
(103, 'Sanjay', 'Tuesday', '10:00:00', '13:00:00', 15, 12, 'Morning'),
(104, 'Sanjay', 'Tuesday', '17:00:00', '20:00:00', 15, 12, 'Evening'),
(105, 'Sanjay', 'Wednesday', '10:00:00', '13:00:00', 15, 12, 'Morning'),
(106, 'Sanjay', 'Wednesday', '17:00:00', '20:00:00', 15, 12, 'Evening'),
(107, 'Sanjay', 'Thursday', '10:00:00', '13:00:00', 15, 12, 'Morning'),
(108, 'Sanjay', 'Thursday', '17:00:00', '20:00:00', 15, 12, 'Evening'),
(109, 'Sanjay', 'Friday', '10:00:00', '13:00:00', 15, 12, 'Morning'),
(110, 'Sanjay', 'Friday', '17:00:00', '20:00:00', 15, 12, 'Evening'),
(111, 'ashok', 'Saturday', '17:00:00', '20:00:00', 15, 12, 'Evening'),
(112, 'Dinesh', 'Sunday', '10:00:00', '13:00:00', 15, 12, 'Morning');

-- --------------------------------------------------------

--
-- Table structure for table `patreg`
--

CREATE TABLE `patreg` (
  `pid` int(11) NOT NULL,
  `fname` varchar(20) NOT NULL,
  `lname` varchar(20) NOT NULL,
  `gender` varchar(10) NOT NULL,
  `email` varchar(30) NOT NULL,
  `contact` varchar(10) NOT NULL,
  `password` varchar(30) NOT NULL,
  `cpassword` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `patreg`
--

INSERT INTO `patreg` (`pid`, `fname`, `lname`, `gender`, `email`, `contact`, `password`, `cpassword`) VALUES
(1, 'Ram', 'Kumar', 'Male', 'ram@gmail.com', '9876543210', 'ram123', 'ram123'),
(2, 'Alia', 'Bhatt', 'Female', 'alia@gmail.com', '8976897689', 'alia123', 'alia123'),
(3, 'Shahrukh', 'khan', 'Male', 'shahrukh@gmail.com', '8976898463', 'shahrukh123', 'shahrukh123'),
(4, 'Kishan', 'Lal', 'Male', 'kishansmart0@gmail.com', '8838489464', 'kishan123', 'kishan123'),
(5, 'Gautam', 'Shankararam', 'Male', 'gautam@gmail.com', '9070897653', 'gautam123', 'gautam123'),
(6, 'Sushant', 'Singh', 'Male', 'sushant@gmail.com', '9059986865', 'sushant123', 'sushant123'),
(7, 'Nancy', 'Deborah', 'Female', 'nancy@gmail.com', '9128972454', 'nancy123', 'nancy123'),
(8, 'Kenny', 'Sebastian', 'Male', 'kenny@gmail.com', '9809879868', 'kenny123', 'kenny123'),
(9, 'William', 'Blake', 'Male', 'william@gmail.com', '8683619153', 'william123', 'william123'),
(10, 'Peter', 'Norvig', 'Male', 'peter@gmail.com', '9609362815', 'peter123', 'peter123'),
(11, 'Shraddha', 'Kapoor', 'Female', 'shraddha@gmail.com', '9768946252', 'shraddha123', 'shraddha123'),
(12, 'Test', 'User', 'Male', 'test@test.com', '1234567890', '123456', '123456'),
(13, 'kabila', 'V', 'Female', 'kabila15@gmail.com', '6369843994', 'kabila123', 'kabila123'),
(14, 'Kavi ', 'Priya', 'Female', 'kavi19062006@gmail.com', '6382275276', 'kavi123', 'kavi123'),
(15, 'Vijay', 'Kumar', 'Male', 'vijaykumar@gmail.com', '9865286374', 'vijay123', 'vijay123'),
(16, 'Shammu', 'V', 'Female', 'Shanmuga13@gmail.com', '6382394025', 'shammu123', 'shammu123'),
(17, 'Test', 'Patient', 'Male', 'testpatient@example.com', '1234567890', 'password123', 'password123'),
(18, 'Archana', 'M', 'Female', 'archana12@gmail.com', '7639360884', 'archana12', 'archana12');

-- --------------------------------------------------------

--
-- Table structure for table `prestb`
--

CREATE TABLE `prestb` (
  `doctor` varchar(50) NOT NULL,
  `pid` int(11) NOT NULL,
  `ID` int(11) NOT NULL,
  `fname` varchar(50) NOT NULL,
  `lname` varchar(50) NOT NULL,
  `appdate` date NOT NULL,
  `apptime` time NOT NULL,
  `disease` varchar(250) NOT NULL,
  `allergy` varchar(250) NOT NULL,
  `prescription` varchar(1000) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `prestb`
--

INSERT INTO `prestb` (`doctor`, `pid`, `ID`, `fname`, `lname`, `appdate`, `apptime`, `disease`, `allergy`, `prescription`) VALUES
('Dinesh', 4, 11, 'Kishan', 'Lal', '2020-03-27', '15:00:00', 'Cough', 'Nothing', 'Just take a teaspoon of Benadryl every night'),
('Ganesh', 2, 8, 'Alia', 'Bhatt', '2020-03-21', '10:00:00', 'Severe Fever', 'Nothing', 'Take bed rest'),
('Kumar', 9, 12, 'William', 'Blake', '2020-03-26', '12:00:00', 'Sever fever', 'nothing', 'Paracetamol -> 1 every morning and night'),
('Tiwary', 9, 13, 'William', 'Blake', '2020-03-26', '14:00:00', 'Cough', 'Skin dryness', 'Intake fruits with more water content'),
('Abbis', 13, 19, 'kabila', 'V', '2026-02-23', '10:00:00', 'fever', 'no', 'take a medicine'),
('arun', 13, 63, 'kabila', 'V', '2026-08-24', '10:00:00', 'heart ache', 'noo', 'take this medicine'),
('arun', 16, 52, 'Shammu', 'V', '2026-03-02', '10:00:00', 'fever', 'no', 'take'),
('ashok', 16, 73, 'Shammu', 'V', '2026-08-23', '10:00:00', 'fever', 'no', 'takee rest well');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `appointmenttb`
--
ALTER TABLE `appointmenttb`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `doctor_holidays`
--
ALTER TABLE `doctor_holidays`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `doctor_leaves`
--
ALTER TABLE `doctor_leaves`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `doctor_schedule`
--
ALTER TABLE `doctor_schedule`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `patreg`
--
ALTER TABLE `patreg`
  ADD PRIMARY KEY (`pid`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `appointmenttb`
--
ALTER TABLE `appointmenttb`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=91;

--
-- AUTO_INCREMENT for table `doctor_holidays`
--
ALTER TABLE `doctor_holidays`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `doctor_leaves`
--
ALTER TABLE `doctor_leaves`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `doctor_schedule`
--
ALTER TABLE `doctor_schedule`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=113;

--
-- AUTO_INCREMENT for table `patreg`
--
ALTER TABLE `patreg`
  MODIFY `pid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
