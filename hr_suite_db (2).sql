-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 18, 2026 at 10:17 AM
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
-- Database: `hr_suite_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `check_in` time NOT NULL,
  `check_out` time DEFAULT NULL,
  `attendance_date` date NOT NULL,
  `status` enum('Present','Absent','Late') NOT NULL DEFAULT 'Present',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attendance`
--

INSERT INTO `attendance` (`id`, `user_id`, `check_in`, `check_out`, `attendance_date`, `status`, `created_at`) VALUES
(5, 5, '09:42:00', '19:42:00', '2026-05-09', 'Present', '2026-05-09 18:43:01'),
(15, 4, '07:00:00', '18:00:00', '2026-05-18', 'Late', '2026-05-18 07:55:36'),
(16, 4, '08:00:00', '18:00:00', '2026-05-18', 'Absent', '2026-05-18 08:04:58');

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `id` int(11) NOT NULL,
  `department_name` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`id`, `department_name`, `created_at`) VALUES
(2, 'Web-Developers', '2026-05-08 03:55:20'),
(3, 'UI/UX Designers', '2026-05-18 08:02:09');

-- --------------------------------------------------------

--
-- Table structure for table `leaves`
--

CREATE TABLE `leaves` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `leave_type` varchar(50) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `reason` text NOT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `leaves`
--

INSERT INTO `leaves` (`id`, `user_id`, `leave_type`, `start_date`, `end_date`, `reason`, `status`, `created_at`) VALUES
(6, 7, 'Annual', '2026-05-08', '2026-05-31', 'Annual Leave', 'approved', '2026-05-08 17:36:23'),
(7, 5, 'Sick', '2026-05-08', '2026-05-16', 'Sick in health', 'rejected', '2026-05-08 18:04:17'),
(8, 4, 'Sick Leave', '2026-05-14', '2026-05-14', 'Sick leave', 'approved', '2026-05-14 10:19:44'),
(9, 4, 'Annual Leave', '2026-05-16', '2026-05-16', 'ANNUAL LEAVE', 'rejected', '2026-05-16 14:34:14'),
(10, 3, 'Annual', '2026-05-18', '2026-05-18', 'annual', 'approved', '2026-05-18 08:01:38');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `message`, `is_read`, `created_at`) VALUES
(1, 0, 'Your payroll for this month has been generated', 0, '2026-05-14 20:19:55');

-- --------------------------------------------------------

--
-- Table structure for table `payroll`
--

CREATE TABLE `payroll` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `basic_salary` decimal(10,2) NOT NULL,
  `allowance` decimal(10,2) NOT NULL DEFAULT 0.00,
  `deduction` decimal(10,2) NOT NULL DEFAULT 0.00,
  `net_salary` decimal(10,2) GENERATED ALWAYS AS (`basic_salary` + `allowance` - `deduction`) STORED,
  `pay_month` varchar(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payroll`
--

INSERT INTO `payroll` (`id`, `user_id`, `basic_salary`, `allowance`, `deduction`, `pay_month`, `created_at`) VALUES
(7, 7, 100000.00, 5000.00, 1000.00, 'May 2026', '2026-05-08 13:45:18'),
(8, 4, 500000.00, 100000.00, 10000.00, 'May 2026', '2026-05-14 13:38:56'),
(9, 4, 600000.00, 200000.00, 20000.00, 'May 2026', '2026-05-14 20:20:40'),
(10, 10, 150000.00, 10000.00, 2000.00, 'May 2026', '2026-05-18 07:27:52');

-- --------------------------------------------------------

--
-- Table structure for table `tasks`
--

CREATE TABLE `tasks` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `assigned_to` int(11) NOT NULL,
  `assigned_by` int(11) NOT NULL,
  `status` enum('pending','in_progress','completed') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tasks`
--

INSERT INTO `tasks` (`id`, `title`, `description`, `assigned_to`, `assigned_by`, `status`, `created_at`) VALUES
(1, 'Get document from designated company', 'Get Document from CIS COMPANY Before Noon', 7, 3, 'completed', '2026-05-12 10:14:07'),
(2, 'Get me something to eat', 'Get me something to eat', 5, 3, 'pending', '2026-05-12 11:17:39'),
(3, 'GO get something', 'GO get something', 8, 3, 'pending', '2026-05-12 11:42:12'),
(5, 'Go Eat something', 'Go Eat something', 3, 3, 'pending', '2026-05-12 12:09:21'),
(6, 'clipboard ', 'get me a clipboard to my desk', 8, 7, 'pending', '2026-05-13 07:58:43'),
(8, 'RECENT FILES', 'GET ME RECENT FILES', 4, 3, 'pending', '2026-05-16 14:33:16');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `full_name` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `email` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','hr','employee') NOT NULL DEFAULT 'employee',
  `department_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `profile_image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `full_name`, `email`, `password`, `role`, `department_id`, `created_at`, `profile_image`) VALUES
(3, 'Admin User', 'admin@gmail.com', '$2y$10$sGlRpqkZhNES5BmbN76mWerW5tW1d8xUXirCNiL4d6d6mki48zNCK', 'admin', 0, '2026-05-07 07:46:15', NULL),
(4, 'Favour Chinemerem Ifeanyi', 'ifeanyifavour041@gmail.com', '$2y$10$Rvod21n7XNgSDpnpoVHl2OETEyv3JiAD..Fk9OuLm7oKST8Q4av2.', 'employee', 0, '2026-05-07 09:23:46', '1778794939_Screenshot (1).png'),
(5, 'Delights', 'delight@gmail.com', '$2y$10$dYvLDYdvwgW4b23k7G4gFuPytRhUu9a7HSIeET9CdzS5Yt5ZlNs8G', 'hr', 0, '2026-05-07 09:24:55', NULL),
(7, 'kelvin', 'kelvin@gmail.com', '$2y$10$EzZFRgzxD5ufNQKuqpcq8.Vu8G1r6/JOp4Ywr1wKGjcecmJwA7FT.', 'hr', 2, '2026-05-08 07:42:24', NULL),
(8, 'ebubechukwuk', 'ebube@gmail', '$2y$10$sX/x6Rp5YKKAh8bZ9JNHN.iQGPeej8rwkVTK4e4lFbW/prkrwDy9C', 'employee', 2, '2026-05-08 07:54:50', NULL),
(9, 'Amarachi ifeanyi', 'amarachi@gmail.com', '$2y$10$oqe/oz3Up2rHdMiTcTmEIenHLbCqx06De0.ZOmzzvK.oVpelYc3oS', 'employee', 2, '2026-05-15 17:18:03', '1778866636_Screenshot (2).png'),
(10, 'jeremiah', 'jeremiah@gmail.com', '$2y$10$dbW.99/f3eV2650PGD50cu6rgiZ3syIYMz8z0ugtQ3rfUAr.9q/5W', 'employee', 2, '2026-05-18 07:25:28', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`id`),
  ADD KEY `attendance_ibfk_1` (`user_id`);

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `leaves`
--
ALTER TABLE `leaves`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_leaves_user` (`user_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payroll`
--
ALTER TABLE `payroll`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `tasks`
--
ALTER TABLE `tasks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tasks_ibfk_1` (`assigned_to`),
  ADD KEY `tasks_ibfk_2` (`assigned_by`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `leaves`
--
ALTER TABLE `leaves`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `payroll`
--
ALTER TABLE `payroll`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `tasks`
--
ALTER TABLE `tasks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attendance`
--
ALTER TABLE `attendance`
  ADD CONSTRAINT `attendance_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `leaves`
--
ALTER TABLE `leaves`
  ADD CONSTRAINT `fk_leaves_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `payroll`
--
ALTER TABLE `payroll`
  ADD CONSTRAINT `payroll_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tasks`
--
ALTER TABLE `tasks`
  ADD CONSTRAINT `tasks_ibfk_1` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tasks_ibfk_2` FOREIGN KEY (`assigned_by`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
