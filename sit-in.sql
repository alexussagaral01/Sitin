-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 06, 2025 at 01:05 PM
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
-- Database: `sit-in`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `ADMIN_ID` int(11) NOT NULL,
  `USER_NAME` varchar(30) NOT NULL DEFAULT 'admin',
  `PASSWORD_HASH` varchar(30) NOT NULL DEFAULT 'admin'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `announcement`
--

CREATE TABLE `announcement` (
  `ID` int(11) NOT NULL,
  `TITLE` varchar(255) NOT NULL,
  `CONTENT` text NOT NULL,
  `CREATED_DATE` date NOT NULL,
  `CREATED_BY` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `STUD_NUM` int(11) NOT NULL,
  `IDNO` int(11) NOT NULL,
  `LAST_NAME` varchar(30) NOT NULL,
  `FIRST_NAME` varchar(30) NOT NULL,
  `MID_NAME` varchar(30) NOT NULL,
  `COURSE` enum('BS IN ACCOUNTANCY','BS IN BUSINESS ADMINISTRATION','BS IN CRIMINOLOGY','BS IN CUSTOMS ADMINISTRATION','BS IN INFORMATION TECHNOLOGY','BS IN COMPUTER SCIENCE','BS IN OFFICE ADMINISTRATION','BS IN SOCIAL WORK','BACHELOR OF SECONDARY EDUCATION','BACHELOR OF ELEMENTARY EDUCATION') NOT NULL,
  `YEAR_LEVEL` enum('1st Year','2nd Year','3rd Year','4th Year') NOT NULL,
  `USER_NAME` varchar(30) NOT NULL,
  `PASSWORD_HASH` varchar(255) NOT NULL,
  `UPLOAD_IMAGE` longblob DEFAULT NULL,
  `EMAIL` varchar(30) NOT NULL,
  `ADDRESS` varchar(255) NOT NULL,
  `SESSION` int(11) NOT NULL DEFAULT 30
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`STUD_NUM`, `IDNO`, `LAST_NAME`, `FIRST_NAME`, `MID_NAME`, `COURSE`, `YEAR_LEVEL`, `USER_NAME`, `PASSWORD_HASH`, `UPLOAD_IMAGE`, `EMAIL`, `ADDRESS`, `SESSION`) VALUES
(1, 22680649, 'Sagaral', 'Alexus Sundae', 'Jamilo', 'BS IN INFORMATION TECHNOLOGY', '3rd Year', 'alexus123', '$2y$10$0YT1OIVzEl/DMSkZlEa90et4KhbVxQfpbpSYnT7WnhY0mQ75Yl7Uq', 0x363763363831346131343834355f363762363164653731383739645f3437333031303239345f3539353133333735363436333136355f373134393832313131373437373839303134305f6e202831292e6a7067, 'alexussagaral3@gmail.com', '1101 Andres Abellana Brgy. Guadaluper Cebu City', 30);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`ADMIN_ID`);

--
-- Indexes for table `announcement`
--
ALTER TABLE `announcement`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`STUD_NUM`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `ADMIN_ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `announcement`
--
ALTER TABLE `announcement`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `STUD_NUM` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
