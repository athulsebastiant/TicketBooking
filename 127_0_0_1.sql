-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 27, 2024 at 07:21 AM
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
-- Database: `evm`
--
CREATE DATABASE IF NOT EXISTS `evm` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `evm`;

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `booking_id` int(10) UNSIGNED NOT NULL,
  `event_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `num_tickets` int(11) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`booking_id`, `event_id`, `user_id`, `num_tickets`, `total_price`, `created_at`) VALUES
(4, 102, 0, 5, 1000.00, '2024-07-27 08:46:21'),
(5, 101, 0, 5, 1000.00, '2024-07-27 08:47:28'),
(6, 101, 0, 1, 200.00, '2024-07-27 09:01:07'),
(7, 103, 0, 4, 200.00, '2024-07-27 09:06:37'),
(8, 103, 0, 2, 100.00, '2024-07-27 09:33:23'),
(9, 103, 0, 2, 100.00, '2024-07-27 09:35:24'),
(10, 101, 0, 2, 400.00, '2024-07-27 09:54:35'),
(11, 102, 0, 2, 400.00, '2024-07-27 10:15:47'),
(12, 103, 0, 3, 150.00, '2024-07-27 10:18:59'),
(13, 101, 0, 3, 600.00, '2024-07-27 10:31:52');

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `event_id` int(10) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `date` date NOT NULL,
  `time` time NOT NULL,
  `location` varchar(255) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `total_tickets` int(11) NOT NULL,
  `available_tickets` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`event_id`, `title`, `description`, `date`, `time`, `location`, `image`, `total_tickets`, `available_tickets`, `price`) VALUES
(101, 'Justin Bieber\'s concert', 'JB at Mumbai', '2024-07-31', '21:45:30', 'Mumbai', 'jbconcert.webp', 500, 242, 200.00),
(102, 'Tedx Mumbai', 'Ted talk season 4', '2024-07-31', '21:45:30', 'Mumbai', 'tedx.jpg', 500, 243, 200.00),
(103, 'Stand up with Atul Khatri', 'Renowned comedian Atul Khatri takes the audience for a fun time.', '2024-09-12', '10:17:46', 'Mumbai', 'atulkhatri.jpg', 100, 15, 50.00),
(104, 'IPL - MI vs CSK', 'Mumbai Indians vs Chennai Super Kings', '2024-08-14', '18:36:56', 'Mumbai', 'mi.webp', 600, 450, 400.00);

-- --------------------------------------------------------

--
-- Table structure for table `payment`
--

CREATE TABLE `payment` (
  `id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `mobile_number` varchar(255) DEFAULT NULL,
  `payment_amount` decimal(5,2) DEFAULT NULL,
  `order_id` varchar(10) DEFAULT NULL,
  `order_status` varchar(10) DEFAULT NULL,
  `booking_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payment`
--

INSERT INTO `payment` (`id`, `name`, `email`, `mobile_number`, `payment_amount`, `order_id`, `order_status`, `booking_id`) VALUES
(5, 'Athul Sebastian', 'athulsebastiant@gmail.com', '08921866268', 100.00, 'OR53124169', 'success', 9),
(6, 'Justin', 'jb23@gmail.com', '01234567891', 400.00, 'OR54275688', 'success', 10),
(7, 'Ram', 'rrkabel@gg.com', '7453532325', 400.00, 'OR55547962', 'success', 11),
(8, 'Faf', 'ff@gg.cm', '2121212122121', 150.00, 'OR55739607', 'success', 12),
(9, 'Raju', 'rr@69hh.com', '212121124', 600.00, 'OR56512058', 'success', 13);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(10) UNSIGNED NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `first_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`booking_id`),
  ADD KEY `event_id` (`event_id`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`event_id`);

--
-- Indexes for table `payment`
--
ALTER TABLE `payment`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `booking_id` (`booking_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `booking_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `event_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=105;

--
-- AUTO_INCREMENT for table `payment`
--
ALTER TABLE `payment`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`event_id`) REFERENCES `events` (`event_id`);

--
-- Constraints for table `payment`
--
ALTER TABLE `payment`
  ADD CONSTRAINT `fk_payment_bookings` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`booking_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
