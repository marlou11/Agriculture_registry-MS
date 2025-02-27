-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 27, 2025 at 05:43 AM
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
-- Database: `arms_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `batches`
--

CREATE TABLE `batches` (
  `batchId` int(11) NOT NULL,
  `batchName` varchar(255) NOT NULL,
  `batchNumber` int(11) NOT NULL,
  `totalQuantity` int(11) NOT NULL,
  `availableQuantity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `batches`
--

INSERT INTO `batches` (`batchId`, `batchName`, `batchNumber`, `totalQuantity`, `availableQuantity`) VALUES
(1, 'Seeds', 1, 11111, 11111),
(2, 'Seeds', 2, 300, 300),
(3, 'Herbicide', 1, 4000, 4000),
(4, 'Fertilizer', 1, 3000, 3000),
(5, 'Seeds', 3, 5000, 5000),
(6, 'Pesticide', 1, 5000, 5000);

--
-- Triggers `batches`
--
DELIMITER $$
CREATE TRIGGER `set_available_quantity` BEFORE INSERT ON `batches` FOR EACH ROW SET NEW.availableQuantity = NEW.totalQuantity
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `distributions`
--

CREATE TABLE `distributions` (
  `distributionId` int(11) NOT NULL,
  `batchId` int(11) NOT NULL,
  `farmerId` int(10) UNSIGNED NOT NULL,
  `rsbaNumber` varchar(255) NOT NULL,
  `name` varchar(300) NOT NULL,
  `barangay` varchar(255) NOT NULL,
  `quantity` int(11) NOT NULL CHECK (`quantity` > 0),
  `distributionDate` date NOT NULL,
  `recordedAt` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fertilizer_records`
--

CREATE TABLE `fertilizer_records` (
  `id` int(11) NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `fertilizer_type` varchar(50) NOT NULL,
  `fertilizer_quantity` decimal(10,2) NOT NULL,
  `application_method` varchar(50) NOT NULL,
  `application_date` date NOT NULL,
  `area_covered` decimal(10,2) NOT NULL,
  `effectiveness` text DEFAULT NULL,
  `photo_documentation` varchar(255) NOT NULL,
  `barangay` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `fertilizer_records`
--

INSERT INTO `fertilizer_records` (`id`, `user_id`, `fertilizer_type`, `fertilizer_quantity`, `application_method`, `application_date`, `area_covered`, `effectiveness`, `photo_documentation`, `barangay`, `created_at`) VALUES
(2, 52, 'organic', 5.00, 'fertigation', '2025-03-05', 3.00, '0', 'uploads/1740265842_DA3.JPG', 'Poblacion 3', '2025-02-26 02:18:47');

-- --------------------------------------------------------

--
-- Table structure for table `harvesting_records`
--

CREATE TABLE `harvesting_records` (
  `id` int(11) NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `harvest_quantity` decimal(10,2) NOT NULL,
  `harvesting_method` enum('manual','mechanical') NOT NULL,
  `harvest_date` date NOT NULL,
  `photo_documentation` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `barangay` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `harvesting_records`
--

INSERT INTO `harvesting_records` (`id`, `user_id`, `harvest_quantity`, `harvesting_method`, `harvest_date`, `photo_documentation`, `created_at`, `barangay`) VALUES
(3, 52, 2.00, 'mechanical', '2025-01-30', 'uploads/harvest_67ba5b5f1c6af9.01690555.jpg', '2025-02-22 23:18:55', 'Poblacion 3');

-- --------------------------------------------------------

--
-- Table structure for table `land_ownership`
--

CREATE TABLE `land_ownership` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `type` enum('Owned','Leased','Rented') NOT NULL,
  `size` decimal(10,2) NOT NULL,
  `location` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `land_ownership`
--

INSERT INTO `land_ownership` (`id`, `user_id`, `type`, `size`, `location`) VALUES
(1, 34, 'Owned', 6.00, 'Dayawan'),
(2, 35, 'Owned', 6.00, 'Dayawan'),
(3, 37, 'Leased', 6.00, 'Dayawan'),
(4, 37, 'Owned', 5.00, 'Dayawan'),
(38, 38, 'Owned', 2.10, 'Lot J, Dayawan'),
(39, 39, 'Leased', 1.80, 'Lot K, Imelda'),
(40, 40, 'Rented', 2.50, 'Lot L, Katipunan'),
(41, 41, 'Owned', 2.20, 'Lot M, Kimaya'),
(42, 42, 'Leased', 1.90, 'Lot N, Looc'),
(43, 43, 'Rented', 2.00, 'Lot O, Poblacion 1'),
(44, 44, 'Owned', 1.70, 'Lot P, Poblacion 2'),
(45, 45, 'Leased', 1.60, 'Lot Q, Poblacion 3'),
(46, 46, 'Rented', 2.40, 'Lot R, San Martin'),
(47, 47, 'Owned', 2.30, 'Lot S, Tambobong'),
(48, 48, 'Owned', 1.00, 'Dayawan'),
(49, 52, 'Rented', 1.00, 'Dayawan');

-- --------------------------------------------------------

--
-- Table structure for table `land_prep_records`
--

CREATE TABLE `land_prep_records` (
  `id` int(11) NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `land_area` decimal(10,2) NOT NULL,
  `prep_method` varchar(50) NOT NULL,
  `start_date` date NOT NULL,
  `completion_date` date NOT NULL,
  `prep_type` varchar(50) DEFAULT NULL,
  `additional_notes` text DEFAULT NULL,
  `photo_documentation` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `barangay` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `land_prep_records`
--

INSERT INTO `land_prep_records` (`id`, `user_id`, `land_area`, `prep_method`, `start_date`, `completion_date`, `prep_type`, `additional_notes`, `photo_documentation`, `created_at`, `barangay`) VALUES
(12, 52, 5.00, 'mechanized', '2025-02-21', '2025-03-07', 'harrowing', 'donee', 'uploads/1740302672_DA4.JPG', '2025-02-23 09:24:32', 'Poblacion 3'),
(13, 52, 2.00, 'mechanized', '2025-02-21', '2025-03-07', 'harrowing', 'donee', 'uploads/1740302940_DA4.JPG', '2025-02-23 09:29:00', 'Poblacion 3'),
(14, 52, 2.00, 'mechanized', '2025-02-21', '2025-03-07', 'harrowing', 'donee', 'uploads/1740302945_DA4.JPG', '2025-02-23 09:29:05', 'Poblacion 3'),
(15, 52, 5.00, 'mechanized', '2025-01-27', '2025-03-07', 'harrowing', 'done', 'uploads/1740302974_IMG_20240825_133527_348.jpg', '2025-02-23 09:29:34', 'Poblacion 3'),
(16, 52, 5.00, 'mechanized', '2025-01-27', '2025-03-07', 'harrowing', 'done', 'uploads/1740303091_IMG_20240825_133527_348.jpg', '2025-02-23 09:31:31', 'Poblacion 3'),
(17, 52, 1.00, 'mechanized', '2025-02-10', '2025-03-08', 'harrowing', 'done', 'uploads/1740303124_DA1.JPG', '2025-02-23 09:32:04', 'Poblacion 3'),
(24, 52, 1.00, 'manual', '2025-02-02', '2025-03-07', 'plowing', 'dvfchgjh', 'uploads/IMG_20240825_133527_348.png', '2025-02-23 10:11:54', 'Poblacion 3');

-- --------------------------------------------------------

--
-- Table structure for table `seeding_records`
--

CREATE TABLE `seeding_records` (
  `id` int(11) NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `seed_type` varchar(255) NOT NULL,
  `seed_quantity` int(11) NOT NULL,
  `seeding_method` varchar(50) NOT NULL,
  `seeding_date` date NOT NULL,
  `photo_documentation` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `barangay` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `seeding_records`
--

INSERT INTO `seeding_records` (`id`, `user_id`, `seed_type`, `seed_quantity`, `seeding_method`, `seeding_date`, `photo_documentation`, `created_at`, `barangay`) VALUES
(14, 52, 'corn', 4, 'broadcasting', '2025-02-28', 'uploads/1740535935_DA3.JPG', '2025-02-26 02:12:15', 'Poblacion 3');

-- --------------------------------------------------------

--
-- Table structure for table `spraying_record`
--

CREATE TABLE `spraying_record` (
  `id` int(11) NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `spray_type` enum('pesticides','herbicides','fungicides','nutrients') NOT NULL,
  `application_equipment` varchar(255) NOT NULL,
  `spraying_date` date NOT NULL,
  `area_sprayed` varchar(255) NOT NULL,
  `photo_documentation` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `barangay` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `spraying_record`
--

INSERT INTO `spraying_record` (`id`, `user_id`, `spray_type`, `application_equipment`, `spraying_date`, `area_sprayed`, `photo_documentation`, `created_at`, `barangay`) VALUES
(2, 52, 'fungicides', '2', '2025-02-28', '1', 'uploads/1740265398_DA1.JPG', '2025-02-22 23:03:18', 'Poblacion 3'),
(3, 52, 'fungicides', 'spraying galloons', '2025-02-28', '1 hectare', 'uploads/1740528247_DA2.JPG', '2025-02-26 00:04:07', 'Poblacion 3');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `firstName` varchar(255) NOT NULL,
  `middleInitial` varchar(1) DEFAULT NULL,
  `lastName` varchar(255) NOT NULL,
  `rsbaNumber` varchar(255) NOT NULL,
  `contactNumber` varchar(20) NOT NULL,
  `barangay` varchar(255) NOT NULL,
  `homeAddress` text DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `status` enum('Pending','Approved','Rejected') NOT NULL DEFAULT 'Pending',
  `registrationDate` timestamp NOT NULL DEFAULT current_timestamp(),
  `profileImage` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `firstName`, `middleInitial`, `lastName`, `rsbaNumber`, `contactNumber`, `barangay`, `homeAddress`, `password`, `status`, `registrationDate`, `profileImage`) VALUES
(1, 'Ruthchelle', 'A', 'Ellorin', '202130833277', '09919242754', 'Katipunan', 'dsjnejnkkkkedsc', '$2y$10$3ru/Uw90L5bEoP5cHq8P8.CrHSopkwu6.Fi21nCmRwMJikS2N0lo.', '', '2025-02-09 06:28:36', NULL),
(6, 'Ruthchelle', 'A', 'Hambre', '2021308450', '+639123412214', 'Dayawan', 'Purok 12', '$2y$10$iAR4iOFs5rl99drFCUaji.Ik35lWeE5Z2RHypily1bzxwUQvwYnwi', '', '2025-02-09 06:52:44', NULL),
(7, 'Ginarose', 'F', 'Beligolo', '2021308332112', '09919242754', 'Poblacion 1', 'Purok 12', '$2y$10$TlhRTnBPNTJncYAcsH/rOOQq3b0TqNo/YpcXeCtUbrI/dBo.c5XQO', '', '2025-02-09 06:58:07', NULL),
(9, 'Mareniela', 'M', 'namangtuan', '58582', '0934234', 'Tambobong', 'Purok 1', '$2y$10$vvz5q/NvIMMb5grhH9ApOedXS/mRHtN40NwXx2boMg74nOfnqmWiy', '', '2025-02-09 07:02:19', NULL),
(11, 'Ginarose', 'R', 'Ellorin', '84328294', '09919242754', 'Poblacion 1', 'Purok 1', '$2y$10$BE63QevPmte/R94kChQw8.ZOUoq6fZpamQyr.y38huhiAK5m50rtC', '', '2025-02-09 07:05:09', NULL),
(14, 'Ems', 'R', 'maganayon', '7432932', '0909123412214', 'Imelda', 'asvasd', '$2y$10$bKvJtH2Cs3IBiIvI711IWe1TJziteG8noexmuFFUGN1oLECLJ9Fhy', '', '2025-02-09 07:17:18', NULL),
(16, 'Alfred', 'A', 'Damas', '202130833211', '+639123412214', 'Tambobong', 'Purok 1', '$2y$10$rSEnqq0mYHUsWbqk.4gsXecRBWkf2M5Q930YzIKOLqImQK1QFBG4u', '', '2025-02-09 07:18:13', NULL),
(34, 'Ginarose', 'F', 'Ellorin', '202130833233123', '0909123412214', 'Poblacion 3', 'dsjnejnkkkkedsc', '$2y$10$h.ZDQYCUyGzRCcbcJGsxVu6bPuCaaWqvCWMzCX1hUqtVDp0khDOay', 'Pending', '2025-02-09 08:11:23', NULL),
(35, 'Ginarose', 'F', 'Ellorin', '53829823', '0909123412214', 'Poblacion 3', 'dsjnejnkkkkedsc', '$2y$10$SbIeNzI1e1AzgH0FVEst7u7KvMflqOb/fKiuRi9Yp/5InW4ZfUyPG', 'Pending', '2025-02-09 08:12:11', NULL),
(37, 'Ginarose', 'F', 'Ellorin', '538298231234', '0909123412214', 'Poblacion 3', 'dsjnejnkkkkedsc', '$2y$10$Q8gV3mROvY/7BpyUyPozluMeJQZ.A6qTTdER6aFVy3vCRFkkmoiWS', 'Approved', '2025-02-09 08:14:05', NULL),
(38, 'Jason', 'P', 'Dela Cruz', '10-43-26-002-000038', '09121234567', 'Dayawan', 'Purok 1, Dayawan', 'password123', 'Approved', '2025-02-09 09:02:41', NULL),
(39, 'Marites', 'R', 'Villanueva', '10-43-26-003-000039', '09223456789', 'Imelda', 'Purok 2, Imelda', 'password123', 'Approved', '2025-02-09 09:02:41', NULL),
(40, 'Ronald', 'T', 'Santos', '10-43-26-004-000040', '09334567890', 'Katipunan', 'Purok 3, Katipunan', 'password123', 'Approved', '2025-02-09 09:02:41', NULL),
(41, 'Leila', 'S', 'Garcia', '10-43-26-005-000041', '09445678901', 'Kimaya', 'Purok 4, Kimaya', 'password123', 'Approved', '2025-02-09 09:02:41', NULL),
(42, 'Edgar', 'B', 'Mendoza', '10-43-26-006-000042', '09556789012', 'Looc', 'Purok 5, Looc', 'password123', 'Approved', '2025-02-09 09:02:41', NULL),
(43, 'Ana', 'L', 'Reyes', '10-43-26-007-000043', '09667890123', 'Poblacion 1', 'Purok 6, Poblacion 1', 'password123', 'Approved', '2025-02-09 09:02:41', NULL),
(44, 'Samuel', 'J', 'Torres', '10-43-26-008-000044', '09778901234', 'Poblacion 2', 'Purok 7, Poblacion 2', 'password123', 'Approved', '2025-02-09 09:02:41', NULL),
(45, 'Melanie', 'C', 'Bautista', '10-43-26-009-000045', '09889012345', 'Poblacion 3', 'Purok 8, Poblacion 3', 'password123', 'Approved', '2025-02-09 09:02:41', NULL),
(46, 'Rico', 'N', 'Cabrera', '10-43-26-010-000046', '09990123456', 'San Martin', 'Purok 9, San Martin', 'password123', 'Approved', '2025-02-09 09:02:41', NULL),
(47, 'Cecilia', 'D', 'Fernandez', '10-43-26-011-000047', '09100123457', 'Tambobong', 'Purok 10, Tambobong', 'password123', 'Approved', '2025-02-09 09:02:41', NULL),
(48, 'Ruthchelle', 'A', 'Hambre', '5647382910', '09919242754', 'Kimaya', 'Purok 12', '$2y$10$runcYTsUAb6peaPAqU8QYeLI8bmizz25TeA/4gQtNH1FNeYptZHBy', 'Approved', '2025-02-11 13:00:55', NULL),
(52, 'Ruthchelle', 'A', 'Hambre', '09919242754', '09919242754', 'Poblacion 3', 'Purok 12', '$2y$10$ovmIIjzMSGuW36rJEGnn1uMnj6H3dVZTJjDcHXpM0pDvsOZCCPUXe', 'Approved', '2025-02-11 13:41:20', 'uploads/67be5a36943fc_DA1.JPG');

-- --------------------------------------------------------

--
-- Table structure for table `weeding_records`
--

CREATE TABLE `weeding_records` (
  `id` int(11) NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `weeding_method` varchar(50) NOT NULL,
  `weeding_start_date` date NOT NULL,
  `weeding_end_date` date NOT NULL,
  `photo_documentation` varchar(255) NOT NULL,
  `barangay` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `weeding_records`
--

INSERT INTO `weeding_records` (`id`, `user_id`, `weeding_method`, `weeding_start_date`, `weeding_end_date`, `photo_documentation`, `barangay`, `created_at`) VALUES
(2, 52, 'mechanical', '2025-02-23', '2025-03-08', 'uploads/1740306084_DA3.JPG', 'Poblacion 3', '2025-02-26 02:17:47'),
(3, 52, 'chemical', '2025-02-28', '2025-03-07', 'uploads/1740528411_DA3.JPG', 'Poblacion 3', '2025-02-26 02:17:47');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `batches`
--
ALTER TABLE `batches`
  ADD PRIMARY KEY (`batchId`);

--
-- Indexes for table `distributions`
--
ALTER TABLE `distributions`
  ADD PRIMARY KEY (`distributionId`),
  ADD KEY `batchId` (`batchId`),
  ADD KEY `farmerId` (`farmerId`);

--
-- Indexes for table `fertilizer_records`
--
ALTER TABLE `fertilizer_records`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `harvesting_records`
--
ALTER TABLE `harvesting_records`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `land_ownership`
--
ALTER TABLE `land_ownership`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `land_prep_records`
--
ALTER TABLE `land_prep_records`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `seeding_records`
--
ALTER TABLE `seeding_records`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `spraying_record`
--
ALTER TABLE `spraying_record`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `rsbaNumber` (`rsbaNumber`);

--
-- Indexes for table `weeding_records`
--
ALTER TABLE `weeding_records`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `batches`
--
ALTER TABLE `batches`
  MODIFY `batchId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `distributions`
--
ALTER TABLE `distributions`
  MODIFY `distributionId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fertilizer_records`
--
ALTER TABLE `fertilizer_records`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `harvesting_records`
--
ALTER TABLE `harvesting_records`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `land_ownership`
--
ALTER TABLE `land_ownership`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT for table `land_prep_records`
--
ALTER TABLE `land_prep_records`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `seeding_records`
--
ALTER TABLE `seeding_records`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `spraying_record`
--
ALTER TABLE `spraying_record`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT for table `weeding_records`
--
ALTER TABLE `weeding_records`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `distributions`
--
ALTER TABLE `distributions`
  ADD CONSTRAINT `distributions_ibfk_1` FOREIGN KEY (`batchId`) REFERENCES `batches` (`batchId`) ON DELETE CASCADE,
  ADD CONSTRAINT `distributions_ibfk_2` FOREIGN KEY (`farmerId`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `fertilizer_records`
--
ALTER TABLE `fertilizer_records`
  ADD CONSTRAINT `fertilizer_records_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `harvesting_records`
--
ALTER TABLE `harvesting_records`
  ADD CONSTRAINT `harvesting_records_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `land_ownership`
--
ALTER TABLE `land_ownership`
  ADD CONSTRAINT `land_ownership_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `land_prep_records`
--
ALTER TABLE `land_prep_records`
  ADD CONSTRAINT `land_prep_records_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `seeding_records`
--
ALTER TABLE `seeding_records`
  ADD CONSTRAINT `seeding_records_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `spraying_record`
--
ALTER TABLE `spraying_record`
  ADD CONSTRAINT `spraying_record_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `weeding_records`
--
ALTER TABLE `weeding_records`
  ADD CONSTRAINT `weeding_records_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
