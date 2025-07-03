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
-- Database: `dbfabricsupplier`
--

-- --------------------------------------------------------

--
-- Table structure for table `dbproduct`
--

CREATE TABLE `dbproduct` (
  `prodID` int(11) NOT NULL,
  `prodName` varchar(100) NOT NULL,
  `prodDescription` varchar(250) NOT NULL,
  `quantity` int(6) NOT NULL,
  `price` double NOT NULL,
  `size` varchar(50) DEFAULT NULL,
  `color` varchar(50) DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `created_at` datetime(6) NOT NULL DEFAULT current_timestamp(6),
  `updated_at` datetime(6) NOT NULL DEFAULT current_timestamp(6) ON UPDATE current_timestamp(6)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dbproduct`
--

INSERT INTO `dbproduct` (`prodID`, `prodName`, `prodDescription`, `quantity`, `price`, `size`, `color`, `image_path`, `created_at`, `updated_at`) VALUES
(10, 'Tote Bag', 'Eco-friendly canvas tote bag perfect for everyday use. Personalize it with names, logos, or custom artwork for a stylish touch.', 5, 20, 'Large', '0', 'product_10_1751443956.png', '2025-07-02 17:11:53.387607', '2025-07-02 20:51:13.000000'),
(30, 'Bag Tag', 'Crafted from premium genuine leather, this elegant bag tag combines durability with a classic touch. Features a secure buckle strap and a clear ID window—perfect for adding personalized flair to your luggage, backpacks, or work bags.', 30, 50, '', '', '686521073a8f5_tag_leather.png', '2025-07-02 20:07:35.241102', '2025-07-02 20:07:35.241102'),
(31, 'Plain Shirt', 'A timeless essential. This plain shirt is made from soft, breathable cotton for all-day comfort and easy styling. Ideal for casual wear, layering, or customizing with prints and embroidery.', 50, 150, 'Large', 'White', '68652151e011a_plain_shirt.png', '2025-07-02 20:08:49.919146', '2025-07-02 20:08:49.919146'),
(32, 'Leather Cover Notebook', 'This handcrafted notebook features a genuine leather cover that ages beautifully over time. Inside, you\'ll find high-quality paper perfect for journaling, sketching, or note-taking—an elegant companion for work, school, or personal reflection.', 100, 80, '', 'Black', '6865217751c0c_leather_cover_notebook.png', '2025-07-02 20:09:27.335813', '2025-07-02 20:09:27.335813'),
(33, 'Wooden Keychains', 'Charming and lightweight, these wooden keychains are laser-cut and polished for a natural, smooth finish. Ideal for personalization with names, logos, or messages—perfect as gifts, souvenirs, or everyday carry accessories.', 210, 30, '', '', '6865218cbc24b_wooden_keychains.png', '2025-07-02 20:09:48.771448', '2025-07-02 20:10:07.000000');

-- --------------------------------------------------------

--
-- Table structure for table `dbusers`
--

CREATE TABLE `dbusers` (
  `userID` int(11) NOT NULL,
  `userName` varchar(100) NOT NULL,
  `userRole` varchar(20) NOT NULL,
  `password` varchar(30) NOT NULL,
  `created_at` datetime(6) NOT NULL DEFAULT current_timestamp(6),
  `updated_at` datetime(6) DEFAULT current_timestamp(6)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dbusers`
--

INSERT INTO `dbusers` (`userID`, `userName`, `userRole`, `password`, `created_at`, `updated_at`) VALUES
(25, 'admin1', 'Admin', 'admin123', '2025-07-02 20:00:43.717063', '2025-07-02 20:00:43.717063'),
(30, 'admin', 'Admin', 'admin123', '2025-07-02 20:46:45.000000', '2025-07-02 20:46:45.000000'),
(31, 'emp1', 'Employee', 'password123', '2025-07-02 20:47:14.000000', '2025-07-02 20:47:14.000000'),
(32, 'emp2', 'Employee', 'password123', '2025-07-02 20:47:23.000000', '2025-07-02 20:47:23.000000'),
(33, 'emp3', 'Employee', 'password123', '2025-07-02 20:47:33.000000', '2025-07-02 20:47:33.000000');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `dbproduct`
--
ALTER TABLE `dbproduct`
  ADD PRIMARY KEY (`prodID`);

--
-- Indexes for table `dbusers`
--
ALTER TABLE `dbusers`
  ADD PRIMARY KEY (`userID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `dbproduct`
--
ALTER TABLE `dbproduct`
  MODIFY `prodID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `dbusers`
--
ALTER TABLE `dbusers`
  MODIFY `userID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
