-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 18, 2026 at 10:58 AM
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
-- Database: `doctor_appointment`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `username`, `password`) VALUES
(2, 'admin', '$2y$10$6vJI8X7xthM2V7FEwSvK4.dLijP9bkjjExFGQeGMZ1xZh3ZKDGDcq');

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

CREATE TABLE `appointments` (
  `id` int(11) NOT NULL,
  `patient_id` int(11) DEFAULT NULL,
  `doctor_id` int(11) DEFAULT NULL,
  `appointment_date` date DEFAULT NULL,
  `appointment_time` time DEFAULT NULL,
  `appointment_end_time` time DEFAULT NULL,
  `reason` text DEFAULT NULL,
  `status` enum('Pending','Approved','Rejected','Cancelled') DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `appointments`
--

INSERT INTO `appointments` (`id`, `patient_id`, `doctor_id`, `appointment_date`, `appointment_time`, `appointment_end_time`, `reason`, `status`, `created_at`) VALUES
(1, 1, 1, '2026-04-19', '10:00:00', '10:30:00', 'Testing', 'Approved', '2026-04-18 08:51:47'),
(2, 1, 1, '2026-04-19', '10:30:00', '11:00:00', 'Checking ', 'Rejected', '2026-04-18 08:52:49'),
(3, 1, 1, '2026-04-19', '10:30:00', '11:00:00', 'checking 2', 'Approved', '2026-04-18 08:53:24'),
(4, 1, 1, '2026-04-19', '11:00:00', '11:30:00', 'checked', 'Approved', '2026-04-18 08:54:02');

-- --------------------------------------------------------

--
-- Table structure for table `blocked_slots`
--

CREATE TABLE `blocked_slots` (
  `id` int(11) NOT NULL,
  `doctor_id` int(11) NOT NULL,
  `block_date` date NOT NULL,
  `block_time` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `blocked_slots`
--

INSERT INTO `blocked_slots` (`id`, `doctor_id`, `block_date`, `block_time`) VALUES
(1, 1, '2026-04-18', '17:00:00'),
(2, 1, '2026-04-19', '17:00:00'),
(3, 1, '2026-04-20', '17:00:00'),
(4, 1, '2026-04-21', '17:00:00'),
(5, 1, '2026-04-22', '17:00:00'),
(6, 1, '2026-04-23', '17:00:00'),
(7, 1, '2026-04-24', '17:00:00'),
(8, 1, '2026-04-25', '17:00:00'),
(9, 1, '2026-04-26', '17:00:00'),
(10, 1, '2026-04-27', '17:00:00'),
(11, 1, '2026-04-28', '17:00:00'),
(12, 1, '2026-04-29', '17:00:00'),
(13, 1, '2026-04-30', '17:00:00'),
(14, 1, '2026-05-01', '17:00:00'),
(15, 1, '2026-05-02', '17:00:00'),
(16, 1, '2026-05-03', '17:00:00'),
(17, 1, '2026-05-04', '17:00:00'),
(18, 1, '2026-05-05', '17:00:00'),
(19, 1, '2026-05-06', '17:00:00'),
(20, 1, '2026-05-07', '17:00:00'),
(21, 1, '2026-05-08', '17:00:00'),
(22, 1, '2026-05-09', '17:00:00'),
(23, 1, '2026-05-10', '17:00:00'),
(24, 1, '2026-05-11', '17:00:00'),
(25, 1, '2026-05-12', '17:00:00'),
(26, 1, '2026-05-13', '17:00:00'),
(27, 1, '2026-05-14', '17:00:00'),
(28, 1, '2026-05-15', '17:00:00'),
(29, 1, '2026-05-16', '17:00:00'),
(30, 1, '2026-05-17', '17:00:00'),
(31, 1, '2026-05-18', '17:00:00'),
(32, 1, '2026-05-19', '17:00:00'),
(33, 1, '2026-05-20', '17:00:00'),
(34, 1, '2026-05-21', '17:00:00'),
(35, 1, '2026-05-22', '17:00:00'),
(36, 1, '2026-05-23', '17:00:00'),
(37, 1, '2026-05-24', '17:00:00'),
(38, 1, '2026-05-25', '17:00:00'),
(39, 1, '2026-05-26', '17:00:00'),
(40, 1, '2026-05-27', '17:00:00'),
(41, 1, '2026-05-28', '17:00:00'),
(42, 1, '2026-05-29', '17:00:00'),
(43, 1, '2026-05-30', '17:00:00'),
(44, 1, '2026-05-31', '17:00:00'),
(45, 1, '2026-06-01', '17:00:00'),
(46, 1, '2026-06-02', '17:00:00'),
(47, 1, '2026-06-03', '17:00:00'),
(48, 1, '2026-06-04', '17:00:00'),
(49, 1, '2026-06-05', '17:00:00'),
(50, 1, '2026-06-06', '17:00:00'),
(51, 1, '2026-06-07', '17:00:00'),
(52, 1, '2026-06-08', '17:00:00'),
(53, 1, '2026-06-09', '17:00:00'),
(54, 1, '2026-06-10', '17:00:00'),
(55, 1, '2026-06-11', '17:00:00'),
(56, 1, '2026-06-12', '17:00:00'),
(57, 1, '2026-06-13', '17:00:00'),
(58, 1, '2026-06-14', '17:00:00'),
(59, 1, '2026-06-15', '17:00:00'),
(60, 1, '2026-06-16', '17:00:00'),
(61, 1, '2026-06-17', '17:00:00'),
(62, 1, '2026-06-18', '17:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `doctors`
--

CREATE TABLE `doctors` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `specialization` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `consultation_fee` decimal(10,2) NOT NULL DEFAULT 0.00,
  `status` tinyint(4) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `doctors`
--

INSERT INTO `doctors` (`id`, `name`, `specialization`, `email`, `phone`, `password`, `consultation_fee`, `status`) VALUES
(1, 'Alpesh ', 'Cardiologist (Heart Specialist)', 'alpesh@gmail.com', '06354257903', '$2y$10$o2tSAL8u/iIVdAkCOnImleTKnDDgVKLcL7GxSzsVWyUIKdaox5dja', 450.00, 1);

-- --------------------------------------------------------

--
-- Table structure for table `patients`
--

CREATE TABLE `patients` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `patients`
--

INSERT INTO `patients` (`id`, `name`, `email`, `phone`, `password`, `created_at`) VALUES
(1, 'buha kishan', 'kishanbuha0301@gmail.com', '06354257903', '$2y$10$oFJLiYGmLGX0qo67QHnHrekVKcOBvuHoArRjVaiUV3EPb9W.g57a2', '2026-04-18 08:50:45');

-- --------------------------------------------------------

--
-- Table structure for table `patient_inquiries`
--

CREATE TABLE `patient_inquiries` (
  `id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `doctor_id` int(11) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `reply_message` text DEFAULT NULL,
  `status` enum('Unread','Read','Replied') DEFAULT 'Unread',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `patient_id` (`patient_id`),
  ADD KEY `doctor_id` (`doctor_id`);

--
-- Indexes for table `blocked_slots`
--
ALTER TABLE `blocked_slots`
  ADD PRIMARY KEY (`id`),
  ADD KEY `doctor_id` (`doctor_id`);

--
-- Indexes for table `doctors`
--
ALTER TABLE `doctors`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `patients`
--
ALTER TABLE `patients`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `patient_inquiries`
--
ALTER TABLE `patient_inquiries`
  ADD PRIMARY KEY (`id`),
  ADD KEY `patient_id` (`patient_id`),
  ADD KEY `doctor_id` (`doctor_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `appointments`
--
ALTER TABLE `appointments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `blocked_slots`
--
ALTER TABLE `blocked_slots`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=63;

--
-- AUTO_INCREMENT for table `doctors`
--
ALTER TABLE `doctors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `patients`
--
ALTER TABLE `patients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `patient_inquiries`
--
ALTER TABLE `patient_inquiries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `appointments`
--
ALTER TABLE `appointments`
  ADD CONSTRAINT `appointments_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `appointments_ibfk_2` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `blocked_slots`
--
ALTER TABLE `blocked_slots`
  ADD CONSTRAINT `blocked_slots_ibfk_1` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `patient_inquiries`
--
ALTER TABLE `patient_inquiries`
  ADD CONSTRAINT `patient_inquiries_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `patient_inquiries_ibfk_2` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
