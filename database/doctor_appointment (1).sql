-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 18, 2026 at 02:29 PM
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
(6, 102, 2, '2026-04-18', '10:00:00', '10:30:00', 'Routine checkup for heart pressure.', 'Approved', '2026-04-18 12:21:00'),
(7, 103, 3, '2026-04-18', '11:00:00', '11:30:00', 'Skin rash consultation.', 'Pending', '2026-04-18 12:21:00'),
(8, 104, 4, '2026-04-19', '14:00:00', '14:30:00', 'Knee joint pain.', 'Approved', '2026-04-18 12:21:00'),
(9, 105, 5, '2026-04-20', '09:30:00', '10:00:00', 'Fever for 2 days.', 'Rejected', '2026-04-18 12:21:00'),
(10, 106, 6, '2026-04-17', '16:00:00', '16:30:00', 'Frequent headaches.', 'Cancelled', '2026-04-18 12:21:00'),
(11, 102, 8, '2026-04-21', '12:00:00', '12:30:00', 'General health checkup.', 'Pending', '2026-04-18 12:21:00'),
(12, 103, 15, '2026-04-23', '15:00:00', '15:30:00', 'Toothache.', 'Approved', '2026-04-18 12:21:00');

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
  `profile_image` varchar(255) DEFAULT NULL,
  `consultation_fee` decimal(10,2) NOT NULL DEFAULT 0.00,
  `status` tinyint(4) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `doctors`
--

INSERT INTO `doctors` (`id`, `name`, `specialization`, `email`, `phone`, `password`, `profile_image`, `consultation_fee`, `status`) VALUES
(2, 'Dr. Rajesh Sharma', 'Cardiologist', 'rajesh.sharma@example.com', '9876543210', '$2y$10$123456789012345678901uOmjxspUyFLEdp6mxJQ4iRnbKlKw1aH6', NULL, 500.00, 1),
(3, 'Dr. Sneha Patel', 'Dermatologist', 'sneha.patel@example.com', '9876543211', '$2y$10$123456789012345678901uOmjxspUyFLEdp6mxJQ4iRnbKlKw1aH6', NULL, 400.00, 1),
(4, 'Dr. Amit Kumar', 'Orthopedist', 'amit.kumar@example.com', '9876543212', '$2y$10$123456789012345678901uOmjxspUyFLEdp6mxJQ4iRnbKlKw1aH6', NULL, 600.00, 1),
(5, 'Dr. Priya Singh', 'Pediatrician', 'priya.singh@example.com', '9876543213', '$2y$10$123456789012345678901uOmjxspUyFLEdp6mxJQ4iRnbKlKw1aH6', NULL, 450.00, 1),
(6, 'Dr. Vikram Reddy', 'Neurologist', 'vikram.reddy@example.com', '9876543214', '$2y$10$123456789012345678901uOmjxspUyFLEdp6mxJQ4iRnbKlKw1aH6', NULL, 800.00, 1),
(7, 'Dr. Anjali Desai', 'Gynecologist', 'anjali.desai@example.com', '9876543215', '$2y$10$123456789012345678901uOmjxspUyFLEdp6mxJQ4iRnbKlKw1aH6', NULL, 500.00, 1),
(8, 'Dr. Rohan Gupta', 'General Physician', 'rohan.gupta@example.com', '9876543216', '$2y$10$123456789012345678901uOmjxspUyFLEdp6mxJQ4iRnbKlKw1aH6', NULL, 300.00, 1),
(9, 'Dr. Kavita Iyer', 'Ophthalmologist', 'kavita.iyer@example.com', '9876543217', '$2y$10$123456789012345678901uOmjxspUyFLEdp6mxJQ4iRnbKlKw1aH6', NULL, 400.00, 1),
(10, 'Dr. Sanjay Verma', 'Psychiatrist', 'sanjay.verma@example.com', '9876543218', '$2y$10$123456789012345678901uOmjxspUyFLEdp6mxJQ4iRnbKlKw1aH6', NULL, 700.00, 1),
(11, 'Dr. Neha Joshi', 'ENT Specialist', 'neha.joshi@example.com', '9876543219', '$2y$10$123456789012345678901uOmjxspUyFLEdp6mxJQ4iRnbKlKw1aH6', NULL, 450.00, 1),
(12, 'Dr. Anil Kapoor', 'Urologist', 'anil.kapoor@example.com', '9876543220', '$2y$10$123456789012345678901uOmjxspUyFLEdp6mxJQ4iRnbKlKw1aH6', NULL, 650.00, 1),
(13, 'Dr. Meera Nair', 'Endocrinologist', 'meera.nair@example.com', '9876543221', '$2y$10$123456789012345678901uOmjxspUyFLEdp6mxJQ4iRnbKlKw1aH6', NULL, 550.00, 1),
(14, 'Dr. Suresh Menon', 'Gastroenterologist', 'suresh.menon@example.com', '9876543222', '$2y$10$123456789012345678901uOmjxspUyFLEdp6mxJQ4iRnbKlKw1aH6', NULL, 600.00, 1),
(15, 'Dr. Pooja Bhatia', 'Dentist', 'pooja.bhatia@example.com', '9876543223', '$2y$10$123456789012345678901uOmjxspUyFLEdp6mxJQ4iRnbKlKw1aH6', NULL, 350.00, 1),
(16, 'Dr. Deepak Chatterjee', 'Pulmonologist', 'deepak.chatterjee@example.com', '9876543224', '$2y$10$123456789012345678901uOmjxspUyFLEdp6mxJQ4iRnbKlKw1aH6', NULL, 600.00, 1),
(17, 'Dr. Ritu Agarwal', 'Oncologist', 'ritu.agarwal@example.com', '9876543225', '$2y$10$123456789012345678901uOmjxspUyFLEdp6mxJQ4iRnbKlKw1aH6', NULL, 1000.00, 1),
(18, 'Dr. Manish Tiwari', 'Rheumatologist', 'manish.tiwari@example.com', '9876543226', '$2y$10$123456789012345678901uOmjxspUyFLEdp6mxJQ4iRnbKlKw1aH6', NULL, 700.00, 1),
(19, 'Dr. Sunita Rao', 'Nephrologist', 'sunita.rao@example.com', '9876543227', '$2y$10$123456789012345678901uOmjxspUyFLEdp6mxJQ4iRnbKlKw1aH6', NULL, 750.00, 1),
(20, 'Dr. Karan Malhotra', 'Plastic Surgeon', 'karan.malhotra@example.com', '9876543228', '$2y$10$123456789012345678901uOmjxspUyFLEdp6mxJQ4iRnbKlKw1aH6', NULL, 1200.00, 1),
(21, 'Dr. Aarti Pillai', 'Dietitian', 'aarti.pillai@example.com', '9876543229', '$2y$10$123456789012345678901uOmjxspUyFLEdp6mxJQ4iRnbKlKw1aH6', NULL, 300.00, 1);

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
(2, 'Kiran Rao', 'kiran.rao1@example.com', '9800000001', '$2y$10$123456789012345678901uOmjxspUyFLEdp6mxJQ4iRnbKlKw1aH6', '2026-04-18 12:12:09'),
(3, 'Vijay Pillai', 'vijay.pillai2@example.com', '9800000002', '$2y$10$123456789012345678901uOmjxspUyFLEdp6mxJQ4iRnbKlKw1aH6', '2026-04-18 12:12:09'),
(4, 'Vishal Pillai', 'vishal.pillai3@example.com', '9800000003', '$2y$10$123456789012345678901uOmjxspUyFLEdp6mxJQ4iRnbKlKw1aH6', '2026-04-18 12:12:09'),
(5, 'Anita Desai', 'anita.desai4@example.com', '9800000004', '$2y$10$123456789012345678901uOmjxspUyFLEdp6mxJQ4iRnbKlKw1aH6', '2026-04-18 12:12:09'),
(6, 'Ashish Verma', 'ashish.verma5@example.com', '9800000005', '$2y$10$123456789012345678901uOmjxspUyFLEdp6mxJQ4iRnbKlKw1aH6', '2026-04-18 12:12:09'),
(7, 'Monika Joshi', 'monika.joshi6@example.com', '9800000006', '$2y$10$123456789012345678901uOmjxspUyFLEdp6mxJQ4iRnbKlKw1aH6', '2026-04-18 12:12:09'),
(8, 'Aarti Nair', 'aarti.nair7@example.com', '9800000007', '$2y$10$123456789012345678901uOmjxspUyFLEdp6mxJQ4iRnbKlKw1aH6', '2026-04-18 12:12:09'),
(9, 'Gaurav Rao', 'gaurav.rao8@example.com', '9800000008', '$2y$10$123456789012345678901uOmjxspUyFLEdp6mxJQ4iRnbKlKw1aH6', '2026-04-18 12:12:09'),
(10, 'Anil Tiwari', 'anil.tiwari9@example.com', '9800000009', '$2y$10$123456789012345678901uOmjxspUyFLEdp6mxJQ4iRnbKlKw1aH6', '2026-04-18 12:12:09'),
(11, 'Sushma Agarwal', 'sushma.agarwal10@example.com', '9800000010', '$2y$10$123456789012345678901uOmjxspUyFLEdp6mxJQ4iRnbKlKw1aH6', '2026-04-18 12:12:09'),
(12, 'Nisha Patel', 'nisha.patel11@example.com', '9800000011', '$2y$10$123456789012345678901uOmjxspUyFLEdp6mxJQ4iRnbKlKw1aH6', '2026-04-18 12:12:09'),
(13, 'Saurabh Sharma', 'saurabh.sharma12@example.com', '9800000012', '$2y$10$123456789012345678901uOmjxspUyFLEdp6mxJQ4iRnbKlKw1aH6', '2026-04-18 12:12:09'),
(14, 'Ashish Verma', 'ashish.verma13@example.com', '9800000013', '$2y$10$123456789012345678901uOmjxspUyFLEdp6mxJQ4iRnbKlKw1aH6', '2026-04-18 12:12:09'),
(15, 'Nitin Nair', 'nitin.nair14@example.com', '9800000014', '$2y$10$123456789012345678901uOmjxspUyFLEdp6mxJQ4iRnbKlKw1aH6', '2026-04-18 12:12:09'),
(16, 'Rupa Agarwal', 'rupa.agarwal15@example.com', '9800000015', '$2y$10$123456789012345678901uOmjxspUyFLEdp6mxJQ4iRnbKlKw1aH6', '2026-04-18 12:12:09'),
(17, 'Amit Joshi', 'amit.joshi16@example.com', '9800000016', '$2y$10$123456789012345678901uOmjxspUyFLEdp6mxJQ4iRnbKlKw1aH6', '2026-04-18 12:12:09'),
(18, 'Aarti Joshi', 'aarti.joshi17@example.com', '9800000017', '$2y$10$123456789012345678901uOmjxspUyFLEdp6mxJQ4iRnbKlKw1aH6', '2026-04-18 12:12:09'),
(19, 'Monika Chatterjee', 'monika.chatterjee18@example.com', '9800000018', '$2y$10$123456789012345678901uOmjxspUyFLEdp6mxJQ4iRnbKlKw1aH6', '2026-04-18 12:12:09'),
(20, 'Geeta Joshi', 'geeta.joshi19@example.com', '9800000019', '$2y$10$123456789012345678901uOmjxspUyFLEdp6mxJQ4iRnbKlKw1aH6', '2026-04-18 12:12:09'),
(21, 'Seema Bhatia', 'seema.bhatia20@example.com', '9800000020', '$2y$10$123456789012345678901uOmjxspUyFLEdp6mxJQ4iRnbKlKw1aH6', '2026-04-18 12:12:09'),
(22, 'Priya Iyer', 'priya.iyer21@example.com', '9800000021', '$2y$10$123456789012345678901uOmjxspUyFLEdp6mxJQ4iRnbKlKw1aH6', '2026-04-18 12:12:09'),
(23, 'Jyoti Kapoor', 'jyoti.kapoor22@example.com', '9800000022', '$2y$10$123456789012345678901uOmjxspUyFLEdp6mxJQ4iRnbKlKw1aH6', '2026-04-18 12:12:09'),
(24, 'Mamta Malhotra', 'mamta.malhotra23@example.com', '9800000023', '$2y$10$123456789012345678901uOmjxspUyFLEdp6mxJQ4iRnbKlKw1aH6', '2026-04-18 12:12:09'),
(25, 'Rajesh Gupta', 'rajesh.gupta24@example.com', '9800000024', '$2y$10$123456789012345678901uOmjxspUyFLEdp6mxJQ4iRnbKlKw1aH6', '2026-04-18 12:12:09'),
(26, 'Preeti Iyer', 'preeti.iyer25@example.com', '9800000025', '$2y$10$123456789012345678901uOmjxspUyFLEdp6mxJQ4iRnbKlKw1aH6', '2026-04-18 12:12:09'),
(27, 'Mukesh Reddy', 'mukesh.reddy26@example.com', '9800000026', '$2y$10$123456789012345678901uOmjxspUyFLEdp6mxJQ4iRnbKlKw1aH6', '2026-04-18 12:12:09'),
(28, 'Gaurav Malhotra', 'gaurav.malhotra27@example.com', '9800000027', '$2y$10$123456789012345678901uOmjxspUyFLEdp6mxJQ4iRnbKlKw1aH6', '2026-04-18 12:12:09'),
(29, 'Anjali Verma', 'anjali.verma28@example.com', '9800000028', '$2y$10$123456789012345678901uOmjxspUyFLEdp6mxJQ4iRnbKlKw1aH6', '2026-04-18 12:12:09'),
(30, 'Neha Tiwari', 'neha.tiwari29@example.com', '9800000029', '$2y$10$123456789012345678901uOmjxspUyFLEdp6mxJQ4iRnbKlKw1aH6', '2026-04-18 12:12:09'),
(31, 'Rakesh Menon', 'rakesh.menon30@example.com', '9800000030', '$2y$10$123456789012345678901uOmjxspUyFLEdp6mxJQ4iRnbKlKw1aH6', '2026-04-18 12:12:09'),
(32, 'Preeti Nair', 'preeti.nair31@example.com', '9800000031', '$2y$10$123456789012345678901uOmjxspUyFLEdp6mxJQ4iRnbKlKw1aH6', '2026-04-18 12:12:09'),
(33, 'Rahul Rao', 'rahul.rao32@example.com', '9800000032', '$2y$10$123456789012345678901uOmjxspUyFLEdp6mxJQ4iRnbKlKw1aH6', '2026-04-18 12:12:09'),
(34, 'Rekha Iyer', 'rekha.iyer33@example.com', '9800000033', '$2y$10$123456789012345678901uOmjxspUyFLEdp6mxJQ4iRnbKlKw1aH6', '2026-04-18 12:12:09'),
(35, 'Seema Chatterjee', 'seema.chatterjee34@example.com', '9800000034', '$2y$10$123456789012345678901uOmjxspUyFLEdp6mxJQ4iRnbKlKw1aH6', '2026-04-18 12:12:09'),
(36, 'Seema Bhatia', 'seema.bhatia35@example.com', '9800000035', '$2y$10$123456789012345678901uOmjxspUyFLEdp6mxJQ4iRnbKlKw1aH6', '2026-04-18 12:12:09'),
(37, 'Prakash Verma', 'prakash.verma36@example.com', '9800000036', '$2y$10$123456789012345678901uOmjxspUyFLEdp6mxJQ4iRnbKlKw1aH6', '2026-04-18 12:12:09'),
(38, 'Sachin Tiwari', 'sachin.tiwari37@example.com', '9800000037', '$2y$10$123456789012345678901uOmjxspUyFLEdp6mxJQ4iRnbKlKw1aH6', '2026-04-18 12:12:09'),
(39, 'Ramesh Bhatia', 'ramesh.bhatia38@example.com', '9800000038', '$2y$10$123456789012345678901uOmjxspUyFLEdp6mxJQ4iRnbKlKw1aH6', '2026-04-18 12:12:09'),
(40, 'Priya Kapoor', 'priya.kapoor39@example.com', '9800000039', '$2y$10$123456789012345678901uOmjxspUyFLEdp6mxJQ4iRnbKlKw1aH6', '2026-04-18 12:12:09'),
(41, 'Deepak Sharma', 'deepak.sharma40@example.com', '9800000040', '$2y$10$123456789012345678901uOmjxspUyFLEdp6mxJQ4iRnbKlKw1aH6', '2026-04-18 12:12:09'),
(42, 'Swati Kumar', 'swati.kumar41@example.com', '9800000041', '$2y$10$123456789012345678901uOmjxspUyFLEdp6mxJQ4iRnbKlKw1aH6', '2026-04-18 12:12:09'),
(43, 'Sanjay Nair', 'sanjay.nair42@example.com', '9800000042', '$2y$10$123456789012345678901uOmjxspUyFLEdp6mxJQ4iRnbKlKw1aH6', '2026-04-18 12:12:09'),
(44, 'Rakesh Verma', 'rakesh.verma43@example.com', '9800000043', '$2y$10$123456789012345678901uOmjxspUyFLEdp6mxJQ4iRnbKlKw1aH6', '2026-04-18 12:12:09'),
(45, 'Gaurav Verma', 'gaurav.verma44@example.com', '9800000044', '$2y$10$123456789012345678901uOmjxspUyFLEdp6mxJQ4iRnbKlKw1aH6', '2026-04-18 12:12:09'),
(46, 'Ritu Sharma', 'ritu.sharma45@example.com', '9800000045', '$2y$10$123456789012345678901uOmjxspUyFLEdp6mxJQ4iRnbKlKw1aH6', '2026-04-18 12:12:09'),
(47, 'Sanjay Pillai', 'sanjay.pillai46@example.com', '9800000046', '$2y$10$123456789012345678901uOmjxspUyFLEdp6mxJQ4iRnbKlKw1aH6', '2026-04-18 12:12:09'),
(48, 'Rohit Kapoor', 'rohit.kapoor47@example.com', '9800000047', '$2y$10$123456789012345678901uOmjxspUyFLEdp6mxJQ4iRnbKlKw1aH6', '2026-04-18 12:12:09'),
(49, 'Gaurav Rao', 'gaurav.rao48@example.com', '9800000048', '$2y$10$123456789012345678901uOmjxspUyFLEdp6mxJQ4iRnbKlKw1aH6', '2026-04-18 12:12:09'),
(50, 'Kavita Kumar', 'kavita.kumar49@example.com', '9800000049', '$2y$10$123456789012345678901uOmjxspUyFLEdp6mxJQ4iRnbKlKw1aH6', '2026-04-18 12:12:09'),
(51, 'Dinesh Reddy', 'dinesh.reddy50@example.com', '9800000050', '$2y$10$123456789012345678901uOmjxspUyFLEdp6mxJQ4iRnbKlKw1aH6', '2026-04-18 12:12:09'),
(52, 'Kavita Verma', 'kavita.verma51@example.com', '9800000051', '$2y$10$123456789012345678901uOmjxspUyFLEdp6mxJQ4iRnbKlKw1aH6', '2026-04-18 12:12:09'),
(53, 'Ajay Joshi', 'ajay.joshi52@example.com', '9800000052', '$2y$10$123456789012345678901uOmjxspUyFLEdp6mxJQ4iRnbKlKw1aH6', '2026-04-18 12:12:09'),
(54, 'Monika Menon', 'monika.menon53@example.com', '9800000053', '$2y$10$123456789012345678901uOmjxspUyFLEdp6mxJQ4iRnbKlKw1aH6', '2026-04-18 12:12:09'),
(55, 'Anita Iyer', 'anita.iyer54@example.com', '9800000054', '$2y$10$123456789012345678901uOmjxspUyFLEdp6mxJQ4iRnbKlKw1aH6', '2026-04-18 12:12:09'),
(56, 'Seema Reddy', 'seema.reddy55@example.com', '9800000055', '$2y$10$123456789012345678901uOmjxspUyFLEdp6mxJQ4iRnbKlKw1aH6', '2026-04-18 12:12:09'),
(57, 'Jyoti Iyer', 'jyoti.iyer56@example.com', '9800000056', '$2y$10$123456789012345678901uOmjxspUyFLEdp6mxJQ4iRnbKlKw1aH6', '2026-04-18 12:12:09'),
(58, 'Seema Nair', 'seema.nair57@example.com', '9800000057', '$2y$10$123456789012345678901uOmjxspUyFLEdp6mxJQ4iRnbKlKw1aH6', '2026-04-18 12:12:09'),
(59, 'Divya Iyer', 'divya.iyer58@example.com', '9800000058', '$2y$10$123456789012345678901uOmjxspUyFLEdp6mxJQ4iRnbKlKw1aH6', '2026-04-18 12:12:09'),
(60, 'Anjali Gupta', 'anjali.gupta59@example.com', '9800000059', '$2y$10$123456789012345678901uOmjxspUyFLEdp6mxJQ4iRnbKlKw1aH6', '2026-04-18 12:12:09'),
(61, 'Ashish Bhatia', 'ashish.bhatia60@example.com', '9800000060', '$2y$10$123456789012345678901uOmjxspUyFLEdp6mxJQ4iRnbKlKw1aH6', '2026-04-18 12:12:09'),
(62, 'Rajesh Patel', 'rajesh.patel61@example.com', '9800000061', '$2y$10$123456789012345678901uOmjxspUyFLEdp6mxJQ4iRnbKlKw1aH6', '2026-04-18 12:12:09'),
(63, 'Ajay Pillai', 'ajay.pillai62@example.com', '9800000062', '$2y$10$123456789012345678901uOmjxspUyFLEdp6mxJQ4iRnbKlKw1aH6', '2026-04-18 12:12:09'),
(64, 'Meera Nair', 'meera.nair63@example.com', '9800000063', '$2y$10$123456789012345678901uOmjxspUyFLEdp6mxJQ4iRnbKlKw1aH6', '2026-04-18 12:12:09'),
(65, 'Tarun Rao', 'tarun.rao64@example.com', '9800000064', '$2y$10$123456789012345678901uOmjxspUyFLEdp6mxJQ4iRnbKlKw1aH6', '2026-04-18 12:12:09'),
(66, 'Kavita Agarwal', 'kavita.agarwal65@example.com', '9800000065', '$2y$10$123456789012345678901uOmjxspUyFLEdp6mxJQ4iRnbKlKw1aH6', '2026-04-18 12:12:09'),
(67, 'Meera Tiwari', 'meera.tiwari66@example.com', '9800000066', '$2y$10$123456789012345678901uOmjxspUyFLEdp6mxJQ4iRnbKlKw1aH6', '2026-04-18 12:12:09'),
(68, 'Rakesh Sharma', 'rakesh.sharma67@example.com', '9800000067', '$2y$10$123456789012345678901uOmjxspUyFLEdp6mxJQ4iRnbKlKw1aH6', '2026-04-18 12:12:09'),
(69, 'Ritu Malhotra', 'ritu.malhotra68@example.com', '9800000068', '$2y$10$123456789012345678901uOmjxspUyFLEdp6mxJQ4iRnbKlKw1aH6', '2026-04-18 12:12:09'),
(70, 'Sachin Patel', 'sachin.patel69@example.com', '9800000069', '$2y$10$123456789012345678901uOmjxspUyFLEdp6mxJQ4iRnbKlKw1aH6', '2026-04-18 12:12:09'),
(71, 'Seema Reddy', 'seema.reddy70@example.com', '9800000070', '$2y$10$123456789012345678901uOmjxspUyFLEdp6mxJQ4iRnbKlKw1aH6', '2026-04-18 12:12:09'),
(72, 'Rekha Patel', 'rekha.patel71@example.com', '9800000071', '$2y$10$123456789012345678901uOmjxspUyFLEdp6mxJQ4iRnbKlKw1aH6', '2026-04-18 12:12:09'),
(73, 'Tarun Kapoor', 'tarun.kapoor72@example.com', '9800000072', '$2y$10$123456789012345678901uOmjxspUyFLEdp6mxJQ4iRnbKlKw1aH6', '2026-04-18 12:12:09'),
(74, 'Neha Menon', 'neha.menon73@example.com', '9800000073', '$2y$10$123456789012345678901uOmjxspUyFLEdp6mxJQ4iRnbKlKw1aH6', '2026-04-18 12:12:09'),
(75, 'Sushma Joshi', 'sushma.joshi74@example.com', '9800000074', '$2y$10$123456789012345678901uOmjxspUyFLEdp6mxJQ4iRnbKlKw1aH6', '2026-04-18 12:12:09'),
(76, 'Anita Kapoor', 'anita.kapoor75@example.com', '9800000075', '$2y$10$123456789012345678901uOmjxspUyFLEdp6mxJQ4iRnbKlKw1aH6', '2026-04-18 12:12:09'),
(77, 'Gaurav Chatterjee', 'gaurav.chatterjee76@example.com', '9800000076', '$2y$10$123456789012345678901uOmjxspUyFLEdp6mxJQ4iRnbKlKw1aH6', '2026-04-18 12:12:09'),
(78, 'Anita Reddy', 'anita.reddy77@example.com', '9800000077', '$2y$10$123456789012345678901uOmjxspUyFLEdp6mxJQ4iRnbKlKw1aH6', '2026-04-18 12:12:09'),
(79, 'Neha Gupta', 'neha.gupta78@example.com', '9800000078', '$2y$10$123456789012345678901uOmjxspUyFLEdp6mxJQ4iRnbKlKw1aH6', '2026-04-18 12:12:09'),
(80, 'Lata Sharma', 'lata.sharma79@example.com', '9800000079', '$2y$10$123456789012345678901uOmjxspUyFLEdp6mxJQ4iRnbKlKw1aH6', '2026-04-18 12:12:09'),
(81, 'Ashish Desai', 'ashish.desai80@example.com', '9800000080', '$2y$10$123456789012345678901uOmjxspUyFLEdp6mxJQ4iRnbKlKw1aH6', '2026-04-18 12:12:09'),
(82, 'Rupa Iyer', 'rupa.iyer81@example.com', '9800000081', '$2y$10$123456789012345678901uOmjxspUyFLEdp6mxJQ4iRnbKlKw1aH6', '2026-04-18 12:12:09'),
(83, 'Sushma Reddy', 'sushma.reddy82@example.com', '9800000082', '$2y$10$123456789012345678901uOmjxspUyFLEdp6mxJQ4iRnbKlKw1aH6', '2026-04-18 12:12:09'),
(84, 'Vikram Desai', 'vikram.desai83@example.com', '9800000083', '$2y$10$123456789012345678901uOmjxspUyFLEdp6mxJQ4iRnbKlKw1aH6', '2026-04-18 12:12:09'),
(85, 'Nisha Verma', 'nisha.verma84@example.com', '9800000084', '$2y$10$123456789012345678901uOmjxspUyFLEdp6mxJQ4iRnbKlKw1aH6', '2026-04-18 12:12:09'),
(86, 'Meera Verma', 'meera.verma85@example.com', '9800000085', '$2y$10$123456789012345678901uOmjxspUyFLEdp6mxJQ4iRnbKlKw1aH6', '2026-04-18 12:12:09'),
(87, 'Kiran Desai', 'kiran.desai86@example.com', '9800000086', '$2y$10$123456789012345678901uOmjxspUyFLEdp6mxJQ4iRnbKlKw1aH6', '2026-04-18 12:12:09'),
(88, 'Vishal Desai', 'vishal.desai87@example.com', '9800000087', '$2y$10$123456789012345678901uOmjxspUyFLEdp6mxJQ4iRnbKlKw1aH6', '2026-04-18 12:12:09'),
(89, 'Prakash Bhatia', 'prakash.bhatia88@example.com', '9800000088', '$2y$10$123456789012345678901uOmjxspUyFLEdp6mxJQ4iRnbKlKw1aH6', '2026-04-18 12:12:09'),
(90, 'Pooja Bhatia', 'pooja.bhatia89@example.com', '9800000089', '$2y$10$123456789012345678901uOmjxspUyFLEdp6mxJQ4iRnbKlKw1aH6', '2026-04-18 12:12:09'),
(91, 'Sachin Nair', 'sachin.nair90@example.com', '9800000090', '$2y$10$123456789012345678901uOmjxspUyFLEdp6mxJQ4iRnbKlKw1aH6', '2026-04-18 12:12:09'),
(92, 'Sachin Agarwal', 'sachin.agarwal91@example.com', '9800000091', '$2y$10$123456789012345678901uOmjxspUyFLEdp6mxJQ4iRnbKlKw1aH6', '2026-04-18 12:12:09'),
(93, 'Mukesh Agarwal', 'mukesh.agarwal92@example.com', '9800000092', '$2y$10$123456789012345678901uOmjxspUyFLEdp6mxJQ4iRnbKlKw1aH6', '2026-04-18 12:12:09'),
(94, 'Sushma Gupta', 'sushma.gupta93@example.com', '9800000093', '$2y$10$123456789012345678901uOmjxspUyFLEdp6mxJQ4iRnbKlKw1aH6', '2026-04-18 12:12:09'),
(95, 'Sunita Rao', 'sunita.rao94@example.com', '9800000094', '$2y$10$123456789012345678901uOmjxspUyFLEdp6mxJQ4iRnbKlKw1aH6', '2026-04-18 12:12:09'),
(96, 'Dinesh Joshi', 'dinesh.joshi95@example.com', '9800000095', '$2y$10$123456789012345678901uOmjxspUyFLEdp6mxJQ4iRnbKlKw1aH6', '2026-04-18 12:12:09'),
(97, 'Kiran Reddy', 'kiran.reddy96@example.com', '9800000096', '$2y$10$123456789012345678901uOmjxspUyFLEdp6mxJQ4iRnbKlKw1aH6', '2026-04-18 12:12:09'),
(98, 'Nisha Iyer', 'nisha.iyer97@example.com', '9800000097', '$2y$10$123456789012345678901uOmjxspUyFLEdp6mxJQ4iRnbKlKw1aH6', '2026-04-18 12:12:09'),
(99, 'Mamta Tiwari', 'mamta.tiwari98@example.com', '9800000098', '$2y$10$123456789012345678901uOmjxspUyFLEdp6mxJQ4iRnbKlKw1aH6', '2026-04-18 12:12:09'),
(100, 'Suresh Nair', 'suresh.nair99@example.com', '9800000099', '$2y$10$123456789012345678901uOmjxspUyFLEdp6mxJQ4iRnbKlKw1aH6', '2026-04-18 12:12:09'),
(101, 'Rakesh Verma', 'rakesh.verma100@example.com', '9800000100', '$2y$10$123456789012345678901uOmjxspUyFLEdp6mxJQ4iRnbKlKw1aH6', '2026-04-18 12:12:09'),
(102, 'Aarav Mehta', 'aarav.mehta@example.com', '9876543230', '$2y$10$123456789012345678901uOmjxspUyFLEdp6mxJQ4iRnbKlKw1aH6', '2026-04-18 12:20:50'),
(103, 'Priya Kapoor', 'priya.kapoor@example.com', '9876543231', '$2y$10$123456789012345678901uOmjxspUyFLEdp6mxJQ4iRnbKlKw1aH6', '2026-04-18 12:20:50'),
(104, 'Rahul Verma', 'rahul.verma@example.com', '9876543232', '$2y$10$123456789012345678901uOmjxspUyFLEdp6mxJQ4iRnbKlKw1aH6', '2026-04-18 12:20:50'),
(105, 'Sneha Desai', 'sneha.desai@example.com', '9876543233', '$2y$10$123456789012345678901uOmjxspUyFLEdp6mxJQ4iRnbKlKw1aH6', '2026-04-18 12:20:50'),
(106, 'Vikram Singh', 'vikram.singh@example.com', '9876543234', '$2y$10$123456789012345678901uOmjxspUyFLEdp6mxJQ4iRnbKlKw1aH6', '2026-04-18 12:20:50');

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
-- Dumping data for table `patient_inquiries`
--

INSERT INTO `patient_inquiries` (`id`, `patient_id`, `doctor_id`, `subject`, `message`, `reply_message`, `status`, `created_at`) VALUES
(3, 102, 2, 'Question about medication', 'Hi Doctor, should I take the blood pressure medicine before or after meals?', 'After meals,You can take your medicines.', 'Replied', '2026-04-18 12:21:09'),
(4, 103, 3, 'Ointment availability', 'The cream you prescribed is out of stock. Is there an alternative?', 'Yes, you can use DermaCare as an alternative.', 'Replied', '2026-04-17 12:21:09'),
(5, 104, 4, 'Follow-up query', 'The pain in my knee has reduced, do I still need the X-ray?', NULL, 'Read', '2026-04-18 12:21:09'),
(6, 4, 2, 'testing ', 'hello', 'Tested', 'Replied', '2026-04-18 11:23:35');

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `blocked_slots`
--
ALTER TABLE `blocked_slots`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=63;

--
-- AUTO_INCREMENT for table `doctors`
--
ALTER TABLE `doctors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `patients`
--
ALTER TABLE `patients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=107;

--
-- AUTO_INCREMENT for table `patient_inquiries`
--
ALTER TABLE `patient_inquiries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

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
