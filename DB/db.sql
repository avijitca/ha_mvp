-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 17, 2024 at 01:01 AM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `lending_mvp`
--

-- --------------------------------------------------------

--
-- Table structure for table `loans`
--

CREATE TABLE `loans` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `loan_amount` decimal(11,2) NOT NULL,
  `interest_rate` decimal(5,2) NOT NULL,
  `duration_years` int(11) NOT NULL,
  `start_date` date NOT NULL,
  `status` enum('active','completed','defaulted') NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `loans`
--

INSERT INTO `loans` (`id`, `user_id`, `loan_amount`, `interest_rate`, `duration_years`, `start_date`, `status`, `created_at`, `updated_at`) VALUES
(1, 2, '15000.00', '10.00', 6, '2024-08-12', 'completed', '2024-11-12 01:36:49', '2024-11-16 12:53:10'),
(3, 2, '50000.00', '18.00', 17, '2024-03-22', 'active', '2024-11-12 01:50:09', '2024-11-16 12:54:22'),
(4, 3, '20000.00', '9.12', 8, '2024-09-21', 'completed', '2024-11-12 01:51:02', '2024-11-12 12:29:58'),
(5, 1, '35000.00', '11.40', 20, '2024-09-14', 'completed', '2024-11-12 01:52:01', '2024-11-16 12:55:50'),
(6, 4, '48000.00', '9.50', 7, '2024-09-16', 'defaulted', '2024-11-12 01:53:15', '2024-11-16 12:56:43'),
(8, 2, '15000.00', '10.00', 10, '2024-10-20', 'active', '2024-11-12 02:31:21', '2024-11-12 12:31:25'),
(9, 2, '12000.00', '20.00', 8, '2024-07-15', 'active', '2024-11-12 02:37:09', '0000-00-00 00:00:00'),
(11, 1, '25000.00', '9.00', 12, '2024-05-10', 'active', '2024-11-12 02:47:41', '0000-00-00 00:00:00'),
(12, 2, '70000.00', '14.80', 20, '2024-11-16', 'active', '2024-11-12 02:56:31', '2024-11-16 12:58:07'),
(13, 4, '25000.00', '11.40', 5, '2024-10-15', 'completed', '2024-11-16 17:26:44', '0000-00-00 00:00:00'),
(14, 2, '40000.00', '14.50', 15, '2024-05-10', 'active', '2024-11-16 17:27:31', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(250) NOT NULL,
  `email` varchar(250) NOT NULL,
  `password` varchar(250) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `created_at`, `updated_at`) VALUES
(1, 'John Doe', 'john_doe@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', '2024-11-11 00:54:24', NULL),
(2, 'Chris Anderson', 'chris_anderson@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', '2024-11-11 00:54:24', NULL),
(3, 'Sameer Patil', 'sameer_patil@example.com', 'e10adc3949ba59abbe56e057f20f883e', '2024-11-16 23:58:05', NULL),
(4, 'Amol Sen', 'amol_sen@example.com', 'e10adc3949ba59abbe56e057f20f883e', '2024-11-16 23:58:05', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `loans`
--
ALTER TABLE `loans`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `loans`
--
ALTER TABLE `loans`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
