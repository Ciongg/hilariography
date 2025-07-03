-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 02, 2025 at 02:56 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `dbretailer`
--

-- --------------------------------------------------------

--
-- Table structure for table `dbusers`
--

CREATE TABLE `dbusers` (
  `userID` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phoneNumber` varchar(20) NOT NULL,
  `userRole` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL,
  `created_at` datetime(6) NOT NULL DEFAULT current_timestamp(6),
  `updated_at` datetime(6) NOT NULL DEFAULT current_timestamp(6) ON UPDATE current_timestamp(6)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dbusers`
--

INSERT INTO `dbusers` (`userID`, `username`, `email`, `phoneNumber`, `userRole`, `password`, `created_at`, `updated_at`) VALUES
(9, 'test', 'test@test.com', '09691590326', 'user', 'password123', '2025-07-02 20:39:13.000000', '2025-07-02 20:39:13.000000'),
(10, 'test1', 'test1@test.com', '09123789123', 'user', 'password123', '2025-07-02 20:39:38.000000', '2025-07-02 20:39:38.000000'),
(11, 'test2', 'test2@test.com', '09127382678', 'user', 'password123', '2025-07-02 20:40:09.000000', '2025-07-02 20:40:09.000000'),
(12, 'test3', 'test3@test.com', '09785167867', 'user', 'password123', '2025-07-02 20:40:42.000000', '2025-07-02 20:40:42.000000'),
(13, 'miguel.inciong', 'miguel.inciong@adamson.edu.ph', '09691590324', 'user', 'password123', '2025-07-02 20:41:07.000000', '2025-07-02 20:41:07.000000');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `dbusers`
--
ALTER TABLE `dbusers`
  ADD PRIMARY KEY (`userID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `dbusers`
--
ALTER TABLE `dbusers`
  MODIFY `userID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
