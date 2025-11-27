-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 27, 2025 at 09:50 AM
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
-- Database: `gcms_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `admin_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`admin_id`, `user_id`, `first_name`, `last_name`) VALUES
(1, 4, 'Admin', 'User');

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

CREATE TABLE `appointments` (
  `appointment_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `counselor_id` int(11) DEFAULT NULL,
  `appointment_date` date NOT NULL,
  `appointment_time` time NOT NULL,
  `purpose` varchar(255) DEFAULT NULL,
  `is_minor` tinyint(1) DEFAULT 0,
  `parent_guardian_name` varchar(200) DEFAULT NULL,
  `parent_guardian_contact` varchar(20) DEFAULT NULL,
  `counseling_concerns` text DEFAULT NULL,
  `current_medications` text DEFAULT NULL,
  `medical_conditions` text DEFAULT NULL,
  `student_address` text DEFAULT NULL,
  `consent_acknowledged` tinyint(1) DEFAULT 0,
  `status` enum('Pending','Confirmed','Declined','Rescheduled','Completed','Cancelled') DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `appointments`
--

INSERT INTO `appointments` (`appointment_id`, `student_id`, `counselor_id`, `appointment_date`, `appointment_time`, `purpose`, `is_minor`, `parent_guardian_name`, `parent_guardian_contact`, `counseling_concerns`, `current_medications`, `medical_conditions`, `student_address`, `consent_acknowledged`, `status`, `created_at`, `updated_at`) VALUES
(2, 2, 2, '2025-11-25', '00:37:00', 'Wellness Check / Intake', 0, NULL, NULL, NULL, NULL, NULL, NULL, 0, 'Confirmed', '2025-11-24 02:37:22', '2025-11-24 06:21:38'),
(3, 2, 3, '2025-11-26', '13:40:00', 'Psycho-Emotional Concerns', 0, NULL, NULL, '[\"Psycho-Emotional Concerns\"]', 'j', 'J', 'Purok Robarub, Pating (Pob.)', 1, 'Confirmed', '2025-11-24 05:36:54', '2025-11-24 06:01:54'),
(4, 2, NULL, '2025-11-26', '13:00:00', 'Sexuality-Related Concerns', 0, NULL, NULL, '[\"Sexuality-Related Concerns\"]', '', '', 'Purok Robarub, Pating (Pob.)', 1, 'Confirmed', '2025-11-24 06:55:00', '2025-11-24 06:57:20');

-- --------------------------------------------------------

--
-- Table structure for table `counseling_records`
--

CREATE TABLE `counseling_records` (
  `record_id` int(11) NOT NULL,
  `appointment_id` int(11) NOT NULL,
  `session_notes` text DEFAULT NULL,
  `recommendations` text DEFAULT NULL,
  `observations` text DEFAULT NULL,
  `attendance_status` enum('Present','Absent','Excused') DEFAULT 'Present',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `counselors`
--

CREATE TABLE `counselors` (
  `counselor_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `department` varchar(100) DEFAULT NULL,
  `specialization` varchar(100) DEFAULT NULL,
  `contact_number` varchar(20) DEFAULT NULL,
  `status` enum('Active','Inactive') DEFAULT 'Active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `counselors`
--

INSERT INTO `counselors` (`counselor_id`, `user_id`, `first_name`, `last_name`, `department`, `specialization`, `contact_number`, `status`) VALUES
(2, 5, 'Jasper A.', 'Fernandez', NULL, 'Academic', NULL, 'Inactive'),
(3, 6, 'Jessel C.', 'Fernandez', NULL, 'Personal', NULL, 'Active');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `notification_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`notification_id`, `user_id`, `message`, `is_read`, `created_at`) VALUES
(1, 3, 'Your appointment request for November 26, 2025 at 1:40 PM has been submitted successfully. You will be notified once a counselor is assigned.', 0, '2025-11-24 05:36:55'),
(2, 6, 'New appointment request from Jasper  Fernandez for November 26, 2025 at 1:40 PM. Concerns: Psycho-Emotional Concerns', 0, '2025-11-24 05:36:55'),
(3, 4, 'New appointment request from Jasper  Fernandez for November 26, 2025 at 1:40 PM. Appointment ID: 3', 1, '2025-11-24 05:36:55'),
(4, 6, 'You have been assigned to Jasper  Fernandez\'s appointment on November 26, 2025 at 1:40 PM. Purpose: Psycho-Emotional Concerns', 0, '2025-11-24 06:01:31'),
(5, 3, 'Counselor Jessel C. Fernandez has been assigned to your appointment on November 26, 2025 at 1:40 PM.', 0, '2025-11-24 06:01:31'),
(6, 3, 'Your appointment request for November 26, 2025 at 1:00 PM has been submitted successfully. You will be notified once a counselor is assigned.', 0, '2025-11-24 06:55:00'),
(7, 6, 'New appointment request from Jasper  Fernandez for November 26, 2025 at 1:00 PM. Concerns: Sexuality-Related Concerns', 0, '2025-11-24 06:55:00'),
(8, 4, 'New appointment request from Jasper  Fernandez for November 26, 2025 at 1:00 PM. Appointment ID: 4', 0, '2025-11-24 06:55:00');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `student_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `student_number` varchar(50) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `middle_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) NOT NULL,
  `age` int(11) DEFAULT NULL,
  `gender` enum('Male','Female','Other') DEFAULT NULL,
  `birthday` date DEFAULT NULL,
  `course` varchar(100) DEFAULT NULL,
  `year_level` varchar(50) DEFAULT NULL,
  `department` varchar(100) DEFAULT NULL,
  `contact_number` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `physical_wellness` text DEFAULT NULL,
  `intellectual_wellness` text DEFAULT NULL,
  `environmental_wellness` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`student_id`, `user_id`, `student_number`, `first_name`, `middle_name`, `last_name`, `age`, `gender`, `birthday`, `course`, `year_level`, `department`, `contact_number`, `address`, `physical_wellness`, `intellectual_wellness`, `environmental_wellness`) VALUES
(2, 3, '2211445', 'Jasper ', 'jas', 'Fernandez', 0, 'Male', '2008-05-06', 'BSCS', '2nd Year', 'College of Computer Studies', '09106266497', 'Purok Robarub, Pating (Pob.)', '{\"q1\":\"j\",\"q2\":\"j\",\"q3\":\"j\",\"q4\":\"j\",\"q5\":\"j\",\"q6\":\"j\",\"q7\":\"j\",\"q8\":\"j\",\"q9\":\"j\"}', '{\"q1\":\"j\",\"q2\":\"jj\",\"q3\":\"j\",\"q4\":\"j\",\"q5\":\"j\",\"q6\":\"j\"}', '{\"q1\":\"j\",\"q2\":\"j\",\"q3\":\"j\",\"q4\":\"j\",\"q5\":\"j\",\"q6\":\"j\"}');

-- --------------------------------------------------------

--
-- Table structure for table `system_settings`
--

CREATE TABLE `system_settings` (
  `setting_id` int(11) NOT NULL,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('student','counselor','admin') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `email`, `password`, `role`, `created_at`, `updated_at`) VALUES
(3, 'japs45', '$2y$10$IFwkn69WtyDKKF.wdScMXuONsvNrJpgzINTNlexmgcHkYc2DuDylu', 'student', '2025-11-23 13:58:34', '2025-11-23 13:58:34'),
(4, 'admin@gcms.edu', '$2y$10$8dOa7K2SGTKfbYZPENZYB.2bwajaKx65Z4W2ZWNzDq8C0EQynAKOm', 'admin', '2025-11-23 14:56:39', '2025-11-23 14:56:39'),
(5, 'fernandezmayma@gmail.com', '$2y$10$KnP2ylLSq9zSXXcHChAfBesmsxx3Q2PJhFCYJz1UW0nuXF0WGBJFm', 'counselor', '2025-11-24 02:09:33', '2025-11-24 02:09:33'),
(6, 'fernandezjasper463@gmail.com', '$2y$10$aiMtgfCYIlWbGyYP9ZDT7eoXrZ3xUnXlOGMffRfRUnL1zeZIW4EDi', 'counselor', '2025-11-24 02:15:47', '2025-11-24 02:28:58');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`admin_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`appointment_id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `counselor_id` (`counselor_id`);

--
-- Indexes for table `counseling_records`
--
ALTER TABLE `counseling_records`
  ADD PRIMARY KEY (`record_id`),
  ADD KEY `appointment_id` (`appointment_id`);

--
-- Indexes for table `counselors`
--
ALTER TABLE `counselors`
  ADD PRIMARY KEY (`counselor_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`notification_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`student_id`),
  ADD UNIQUE KEY `student_number` (`student_number`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `system_settings`
--
ALTER TABLE `system_settings`
  ADD PRIMARY KEY (`setting_id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `appointments`
--
ALTER TABLE `appointments`
  MODIFY `appointment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `counseling_records`
--
ALTER TABLE `counseling_records`
  MODIFY `record_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `counselors`
--
ALTER TABLE `counselors`
  MODIFY `counselor_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `notification_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `student_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `system_settings`
--
ALTER TABLE `system_settings`
  MODIFY `setting_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admins`
--
ALTER TABLE `admins`
  ADD CONSTRAINT `admins_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `appointments`
--
ALTER TABLE `appointments`
  ADD CONSTRAINT `appointments_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `appointments_ibfk_2` FOREIGN KEY (`counselor_id`) REFERENCES `counselors` (`counselor_id`) ON DELETE SET NULL;

--
-- Constraints for table `counseling_records`
--
ALTER TABLE `counseling_records`
  ADD CONSTRAINT `counseling_records_ibfk_1` FOREIGN KEY (`appointment_id`) REFERENCES `appointments` (`appointment_id`) ON DELETE CASCADE;

--
-- Constraints for table `counselors`
--
ALTER TABLE `counselors`
  ADD CONSTRAINT `counselors_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `students`
--
ALTER TABLE `students`
  ADD CONSTRAINT `students_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
