-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 19, 2025 at 05:56 AM
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
(2, 2, 4, '2025-11-25', '00:37:00', 'Wellness Check / Intake', 0, NULL, NULL, NULL, NULL, NULL, NULL, 0, 'Completed', '2025-11-24 02:37:22', '2025-12-08 14:21:28'),
(3, 2, NULL, '2025-11-26', '13:40:00', 'Psycho-Emotional Concerns', 0, NULL, NULL, '[\"Psycho-Emotional Concerns\"]', 'j', 'J', 'Purok Robarub, Pating (Pob.)', 1, 'Confirmed', '2025-11-24 05:36:54', '2025-11-24 06:01:54'),
(4, 2, 4, '2025-11-26', '13:00:00', 'Sexuality-Related Concerns', 0, NULL, NULL, '[\"Sexuality-Related Concerns\"]', '', '', 'Purok Robarub, Pating (Pob.)', 1, 'Confirmed', '2025-11-24 06:55:00', '2025-12-09 06:40:35'),
(5, 3, 4, '2025-12-09', '09:00:00', 'Academic-Related Concerns', 0, NULL, NULL, '[\"Academic-Related Concerns\"]', 'yes', 'yes ', 'Sta Elena Virac', 1, 'Confirmed', '2025-12-08 12:11:06', '2025-12-08 14:11:49'),
(6, 4, NULL, '2025-12-10', '00:14:00', 'Academic-Related Concerns', 1, 'JKJK', '091', '[\"Academic-Related Concerns\"]', 'KL', 'KL', 'san fernando masbate\r\npating masbate city', 1, 'Confirmed', '2025-12-08 13:14:36', '2025-12-08 13:16:30'),
(7, 4, NULL, '2025-12-17', '21:24:00', 'Academic-Related Concerns', 0, NULL, NULL, '[\"Academic-Related Concerns\"]', 'HJ', 'JJK', 'san fernando masbate\r\npating masbate city', 1, 'Confirmed', '2025-12-08 13:19:22', '2025-12-08 13:25:33'),
(8, 5, NULL, '2025-12-20', '10:00:00', 'Academic-Related Concerns', 0, NULL, NULL, '[\"Academic-Related Concerns\"]', 'no', 'no', 'sta elena\r\n', 1, 'Confirmed', '2025-12-08 13:29:32', '2025-12-08 13:38:43'),
(9, 4, NULL, '2025-12-11', '12:45:00', 'Psycho-Emotional Concerns', 0, NULL, NULL, '[\"Psycho-Emotional Concerns\"]', 'JK', 'JKJ', 'san fernando masbate\r\npating masbate city', 1, 'Confirmed', '2025-12-08 13:45:50', '2025-12-08 13:47:51'),
(10, 7, 4, '2025-12-10', '13:50:00', 'Pregnancy-Related Concerns', 0, NULL, NULL, '[\"Pregnancy-Related Concerns\"]', 'Yes', 'Yes', 'bato catanduanes', 1, 'Confirmed', '2025-12-09 02:48:04', '2025-12-09 02:58:36'),
(11, 8, 4, '2025-12-10', '10:56:00', 'Sexuality-Related Concerns', 0, NULL, NULL, '[\"Sexuality-Related Concerns\"]', 'yes', 'yes', 'bgc catanduanes ', 1, 'Confirmed', '2025-12-09 02:56:44', '2025-12-09 03:00:25'),
(12, 4, NULL, '2025-12-25', '01:59:00', 'Abuse-Related Concerns', 0, NULL, NULL, '[\"Abuse-Related Concerns\"]', 'jkk', 'jkjk', 'san fernando masbate\r\npating masbate city', 1, 'Confirmed', '2025-12-09 02:59:39', '2025-12-09 06:33:01'),
(13, 7, NULL, '2025-12-10', '04:29:00', 'Academic-Related Concerns', 0, NULL, NULL, '[\"Academic-Related Concerns\"]', 'None', 'N/A', 'bato catanduanes', 1, 'Confirmed', '2025-12-09 06:26:48', '2025-12-09 06:35:10'),
(14, 7, 4, '2025-12-10', '06:52:00', 'Career-Related Concerns', 0, NULL, NULL, '[\"Career-Related Concerns\"]', '', '', 'bato catanduanes', 1, 'Confirmed', '2025-12-09 06:49:59', '2025-12-09 06:56:24'),
(15, 7, 4, '2025-12-12', '09:45:00', 'Suicidal Ideation/Tendencies', 0, NULL, NULL, '[\"Suicidal Ideation\\/Tendencies\"]', '', '', 'bato catanduanes', 1, 'Confirmed', '2025-12-11 08:45:19', '2025-12-11 08:53:06'),
(16, 7, NULL, '2025-12-13', '07:57:00', 'Pregnancy-Related Concerns', 0, NULL, NULL, '[\"Pregnancy-Related Concerns\"]', '', '', 'bato catanduanes', 1, 'Pending', '2025-12-11 08:57:09', '2025-12-11 08:57:09');

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

--
-- Dumping data for table `counseling_records`
--

INSERT INTO `counseling_records` (`record_id`, `appointment_id`, `session_notes`, `recommendations`, `observations`, `attendance_status`, `created_at`, `updated_at`) VALUES
(2, 2, 'sayang', 'sa uulitin', '', 'Absent', '2025-12-08 14:21:28', '2025-12-08 14:21:28');

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
(4, 7, 'Adrien Mikel', 'Aguilar', NULL, 'Mental Health', NULL, 'Active');

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
(3, 4, 'New appointment request from Jasper  Fernandez for November 26, 2025 at 1:40 PM. Appointment ID: 3', 1, '2025-11-24 05:36:55'),
(5, 3, 'Counselor Jessel C. Fernandez has been assigned to your appointment on November 26, 2025 at 1:40 PM.', 0, '2025-11-24 06:01:31'),
(6, 3, 'Your appointment request for November 26, 2025 at 1:00 PM has been submitted successfully. You will be notified once a counselor is assigned.', 0, '2025-11-24 06:55:00'),
(8, 4, 'New appointment request from Jasper  Fernandez for November 26, 2025 at 1:00 PM. Appointment ID: 4', 1, '2025-11-24 06:55:00'),
(9, 8, 'Your appointment request for December 09, 2025 at 9:00 AM has been submitted successfully. You will be notified once a counselor is assigned.', 0, '2025-12-08 12:11:06'),
(11, 7, 'New appointment request from adrien mikel aguilar for December 09, 2025 at 9:00 AM. Concerns: Academic-Related Concerns', 0, '2025-12-08 12:11:06'),
(12, 4, 'New appointment request from adrien mikel aguilar for December 09, 2025 at 9:00 AM. Appointment ID: 5', 1, '2025-12-08 12:11:06'),
(13, 9, 'Your appointment request for December 10, 2025 at 12:14 AM has been submitted successfully. You will be notified once a counselor is assigned.', 0, '2025-12-08 13:14:36'),
(15, 7, 'New appointment request from jasper fernandez for December 10, 2025 at 12:14 AM. Concerns: Academic-Related Concerns', 0, '2025-12-08 13:14:36'),
(16, 4, 'New appointment request from jasper fernandez for December 10, 2025 at 12:14 AM. Appointment ID: 6', 1, '2025-12-08 13:14:36'),
(17, 9, 'Your appointment request for December 17, 2025 at 9:24 PM has been submitted successfully. You will be notified once a counselor is assigned.', 0, '2025-12-08 13:19:22'),
(19, 7, 'New appointment request from jasper fernandez for December 17, 2025 at 9:24 PM. Concerns: Academic-Related Concerns', 0, '2025-12-08 13:19:22'),
(20, 4, 'New appointment request from jasper fernandez for December 17, 2025 at 9:24 PM. Appointment ID: 7', 1, '2025-12-08 13:19:22'),
(22, 9, 'Counselor Jessel C. Fernandez has been assigned to your appointment on December 17, 2025 at 9:24 PM.', 0, '2025-12-08 13:24:14'),
(23, 10, 'Your appointment request for December 20, 2025 at 10:00 AM has been submitted successfully. You will be notified once a counselor is assigned.', 0, '2025-12-08 13:29:32'),
(25, 7, 'New appointment request from Khenna Rode Baldomero for December 20, 2025 at 10:00 AM. Concerns: Academic-Related Concerns', 0, '2025-12-08 13:29:32'),
(26, 4, 'New appointment request from Khenna Rode Baldomero for December 20, 2025 at 10:00 AM. Appointment ID: 8', 1, '2025-12-08 13:29:32'),
(27, 9, 'Your appointment request for December 11, 2025 at 12:45 PM has been submitted successfully. You will be notified once a counselor is assigned.', 0, '2025-12-08 13:45:50'),
(29, 7, 'New appointment request from jasper fernandez for December 11, 2025 at 12:45 PM. Concerns: Psycho-Emotional Concerns', 0, '2025-12-08 13:45:50'),
(30, 4, 'New appointment request from jasper fernandez for December 11, 2025 at 12:45 PM. Appointment ID: 9', 0, '2025-12-08 13:45:50'),
(31, 7, 'You have been assigned to Jasper  Fernandez\'s appointment on November 25, 2025 at 12:37 AM. Purpose: Wellness Check / Intake', 0, '2025-12-08 13:48:14'),
(32, 3, 'Counselor Adrien Mikel Aguilar has been assigned to your appointment on November 25, 2025 at 12:37 AM.', 0, '2025-12-08 13:48:14'),
(33, 7, 'You have been assigned to adrien mikel aguilar\'s appointment on December 09, 2025 at 9:00 AM. Purpose: Academic-Related Concerns', 0, '2025-12-08 14:11:49'),
(34, 8, 'Counselor Adrien Mikel Aguilar has been assigned to your appointment on December 09, 2025 at 9:00 AM.', 0, '2025-12-08 14:11:49'),
(35, 7, 'You have been assigned to adrien mikel aguilar\'s appointment on December 09, 2025 at 9:00 AM. Purpose: Academic-Related Concerns', 0, '2025-12-08 14:11:53'),
(36, 8, 'Counselor Adrien Mikel Aguilar has been assigned to your appointment on December 09, 2025 at 9:00 AM.', 0, '2025-12-08 14:11:53'),
(37, 13, 'Your appointment request for December 10, 2025 at 1:50 PM has been submitted successfully. You will be notified once a counselor is assigned.', 0, '2025-12-09 02:48:05'),
(39, 7, 'New appointment request from erica nerpiol for December 10, 2025 at 1:50 PM. Concerns: Pregnancy-Related Concerns', 0, '2025-12-09 02:48:05'),
(40, 4, 'New appointment request from erica nerpiol for December 10, 2025 at 1:50 PM. Appointment ID: 10', 0, '2025-12-09 02:48:05'),
(41, 14, 'Your appointment request for December 10, 2025 at 10:56 AM has been submitted successfully. You will be notified once a counselor is assigned.', 0, '2025-12-09 02:56:44'),
(43, 7, 'New appointment request from Jay Jay Bonifacio  for December 10, 2025 at 10:56 AM. Concerns: Sexuality-Related Concerns', 0, '2025-12-09 02:56:44'),
(44, 4, 'New appointment request from Jay Jay Bonifacio  for December 10, 2025 at 10:56 AM. Appointment ID: 11', 0, '2025-12-09 02:56:44'),
(45, 7, 'You have been assigned to erica nerpiol\'s appointment on December 10, 2025 at 1:50 PM. Purpose: Pregnancy-Related Concerns', 0, '2025-12-09 02:58:36'),
(46, 13, 'Counselor Adrien Mikel Aguilar has been assigned to your appointment on December 10, 2025 at 1:50 PM.', 0, '2025-12-09 02:58:36'),
(47, 7, 'You have been assigned to Jay Jay Bonifacio \'s appointment on December 10, 2025 at 10:56 AM. Purpose: Sexuality-Related Concerns', 0, '2025-12-09 02:59:24'),
(48, 14, 'Counselor Adrien Mikel Aguilar has been assigned to your appointment on December 10, 2025 at 10:56 AM.', 0, '2025-12-09 02:59:24'),
(49, 9, 'Your appointment request for December 25, 2025 at 1:59 AM has been submitted successfully. You will be notified once a counselor is assigned.', 0, '2025-12-09 02:59:39'),
(51, 7, 'New appointment request from jasper fernandez for December 25, 2025 at 1:59 AM. Concerns: Abuse-Related Concerns', 0, '2025-12-09 02:59:39'),
(52, 4, 'New appointment request from jasper fernandez for December 25, 2025 at 1:59 AM. Appointment ID: 12', 0, '2025-12-09 02:59:39'),
(53, 13, 'Your appointment request for December 10, 2025 at 4:29 AM has been submitted successfully. You will be notified once a counselor is assigned.', 0, '2025-12-09 06:26:48'),
(55, 7, 'New appointment request from erica nerpiol for December 10, 2025 at 4:29 AM. Concerns: Academic-Related Concerns', 0, '2025-12-09 06:26:48'),
(56, 4, 'New appointment request from erica nerpiol for December 10, 2025 at 4:29 AM. Appointment ID: 13', 0, '2025-12-09 06:26:48'),
(58, 13, 'Counselor Jessel C. Fernandez has been assigned to your appointment on December 10, 2025 at 4:29 AM.', 0, '2025-12-09 06:35:10'),
(59, 7, 'You have been assigned to Jasper  Fernandez\'s appointment on November 26, 2025 at 1:00 PM. Purpose: Sexuality-Related Concerns', 0, '2025-12-09 06:40:35'),
(60, 3, 'Counselor Adrien Mikel Aguilar has been assigned to your appointment on November 26, 2025 at 1:00 PM.', 0, '2025-12-09 06:40:35'),
(61, 13, 'Your appointment request for December 10, 2025 at 6:52 AM has been submitted successfully. You will be notified once a counselor is assigned.', 0, '2025-12-09 06:49:59'),
(63, 7, 'New appointment request from erica nerpiol for December 10, 2025 at 6:52 AM. Concerns: Career-Related Concerns', 0, '2025-12-09 06:49:59'),
(64, 4, 'New appointment request from erica nerpiol for December 10, 2025 at 6:52 AM. Appointment ID: 14', 0, '2025-12-09 06:49:59'),
(65, 7, 'You have been assigned to erica nerpiol\'s appointment on December 10, 2025 at 6:52 AM. Purpose: Career-Related Concerns', 0, '2025-12-09 06:52:04'),
(66, 13, 'Counselor Adrien Mikel Aguilar has been assigned to your appointment on December 10, 2025 at 6:52 AM.', 0, '2025-12-09 06:52:04'),
(67, 13, 'Your appointment request for December 12, 2025 at 9:45 AM has been submitted successfully. You will be notified once a counselor is assigned.', 0, '2025-12-11 08:45:19'),
(68, 7, 'New appointment request from erica nerpiol for December 12, 2025 at 9:45 AM. Concerns: Suicidal Ideation/Tendencies', 0, '2025-12-11 08:45:19'),
(69, 4, 'New appointment request from erica nerpiol for December 12, 2025 at 9:45 AM. Appointment ID: 15', 0, '2025-12-11 08:45:19'),
(70, 7, 'You have been assigned to erica nerpiol\'s appointment on December 12, 2025 at 9:45 AM. Purpose: Suicidal Ideation/Tendencies', 0, '2025-12-11 08:48:51'),
(71, 13, 'Counselor Adrien Mikel Aguilar has been assigned to your appointment on December 12, 2025 at 9:45 AM.', 0, '2025-12-11 08:48:51'),
(72, 13, 'Your appointment request for December 13, 2025 at 7:57 AM has been submitted successfully. You will be notified once a counselor is assigned.', 0, '2025-12-11 08:57:09'),
(73, 7, 'New appointment request from erica nerpiol for December 13, 2025 at 7:57 AM. Concerns: Pregnancy-Related Concerns', 0, '2025-12-11 08:57:09'),
(74, 4, 'New appointment request from erica nerpiol for December 13, 2025 at 7:57 AM. Appointment ID: 16', 0, '2025-12-11 08:57:09');

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `reset_code` varchar(10) NOT NULL,
  `expires_at` datetime NOT NULL,
  `used` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `password_resets`
--

INSERT INTO `password_resets` (`id`, `user_id`, `email`, `reset_code`, `expires_at`, `used`, `created_at`) VALUES
(4, 7, 'mikelaguilar92@gmail.com', '302139', '2025-12-08 14:02:13', 1, '2025-12-08 12:47:13');

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
(2, 3, '2211445', 'Jasper ', 'jas', 'Fernandez', 0, 'Male', '2008-05-06', 'BSCS', '2nd Year', 'College of Computer Studies', '09106266497', 'Purok Robarub, Pating (Pob.)', '{\"q1\":\"j\",\"q2\":\"j\",\"q3\":\"j\",\"q4\":\"j\",\"q5\":\"j\",\"q6\":\"j\",\"q7\":\"j\",\"q8\":\"j\",\"q9\":\"j\"}', '{\"q1\":\"j\",\"q2\":\"jj\",\"q3\":\"j\",\"q4\":\"j\",\"q5\":\"j\",\"q6\":\"j\"}', '{\"q1\":\"j\",\"q2\":\"j\",\"q3\":\"j\",\"q4\":\"j\",\"q5\":\"j\",\"q6\":\"j\"}'),
(3, 8, '2022-62401', 'adrien mikel', NULL, 'aguilar', 22, 'Male', '2003-08-30', 'Information Sytem', '4th Year', NULL, '09218648265', 'Sta Elena Virac', NULL, NULL, NULL),
(4, 9, '221224', 'jasper', NULL, 'fernandez', 0, 'Male', '2025-12-17', 'uwu', '1st Year', NULL, '09197960151', 'san fernando masbate\r\npating masbate city', NULL, NULL, NULL),
(5, 10, '2024-70791', 'Khenna Rode', NULL, 'Baldomero', 19, 'Female', '2006-02-27', 'Information Sytem', '2nd Year', NULL, '09074363146', 'sta elena\r\n', NULL, NULL, NULL),
(6, 12, '2022-61963', 'Jolly', NULL, 'Avila', 22, 'Male', '2003-10-15', 'Information System', '4th Year', NULL, '09100391450', NULL, NULL, NULL, NULL),
(7, 13, '2022-60480', 'erica', NULL, 'nerpiol', 22, 'Female', '2003-08-02', 'Information System', '2nd Year', NULL, '09218648265', 'bato catanduanes', NULL, NULL, NULL),
(8, 14, '2022-62743', 'Jay Jay', NULL, 'Bonifacio ', 22, 'Male', '2003-11-04', 'BS information systems ', '3rd Year', NULL, '09952019768', 'bgc catanduanes ', NULL, NULL, NULL);

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
  `email_verified` tinyint(1) DEFAULT 0,
  `verification_token` varchar(64) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `email`, `password`, `role`, `email_verified`, `verification_token`, `created_at`, `updated_at`) VALUES
(3, 'japs45', '$2y$10$MJZdwhFflxFgXxaGOKVIsea/dDZqaB61FuOIk2TkulUPRPqg2MikS', 'student', 0, NULL, '2025-11-23 13:58:34', '2025-11-30 14:52:06'),
(4, 'admin@gcms.edu', '$2y$10$8dOa7K2SGTKfbYZPENZYB.2bwajaKx65Z4W2ZWNzDq8C0EQynAKOm', 'admin', 0, NULL, '2025-11-23 14:56:39', '2025-11-23 14:56:39'),
(7, 'mikelaguilar92@gmail.com', '$2y$10$VeyHYPVEfIBgwepgB8hIguun3oc1AsZA2m0hlGl5FjyGITDiNFfgm', 'counselor', 0, NULL, '2025-12-01 15:48:04', '2025-12-08 12:47:55'),
(8, 'lillowkie', '$2y$10$YoFVUj6S9/pn9xibr4ZDIOXu/Cl4.d0ecCxlgiLYRBz.h53JHa0qq', 'student', 0, NULL, '2025-12-08 12:09:05', '2025-12-08 12:09:05'),
(9, 'fernandezmayma@gmail.com', '$2y$10$ITh8QHLLrW4KKzwAoopmbeePjKWOrKNU9a4F2MrgaDsgohgCFKBly', 'student', 1, NULL, '2025-12-08 12:59:49', '2025-12-08 13:00:05'),
(10, 'baldomerokhenna@gmail.com', '$2y$10$6dEGZWRL8/dztdTSIqwl5Ow0x6f61mW3vI5pQp20tUDXl4guT4Jz2', 'student', 1, NULL, '2025-12-08 13:24:36', '2025-12-08 13:27:32'),
(12, 'avilajolly639@gmail.com', '$2y$10$G/OJh.RG7.pDiFaGZlWdfuBdLwjv.Ods0mEyA.KiSE/.xppbjolaC', 'student', 0, '01f55609a233b217b017e3fda2df51d02de89f2ad9ab201cd896d1f935fe8f67', '2025-12-09 02:33:11', '2025-12-09 02:33:11'),
(13, 'bindings30@gmail.com', '$2y$10$6G2L8h/UIKrM9G8ObBmMluThbZqDJ4QaKTcRcGqXs.T95bIWZB1qa', 'student', 1, NULL, '2025-12-09 02:42:03', '2025-12-09 02:44:27'),
(14, 'jayjaybonifacio99@gmail.com', '$2y$10$xL4Jo55A3WKbvTKhsXKIYeZMIjMpQWaVoXU8J3Wi7nKlAR4sCwCNC', 'student', 1, NULL, '2025-12-09 02:52:42', '2025-12-09 02:53:40');

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
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `email` (`email`),
  ADD KEY `reset_code` (`reset_code`);

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
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_verification_token` (`verification_token`);

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
  MODIFY `appointment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `counseling_records`
--
ALTER TABLE `counseling_records`
  MODIFY `record_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `counselors`
--
ALTER TABLE `counselors`
  MODIFY `counselor_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `notification_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=75;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `student_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `system_settings`
--
ALTER TABLE `system_settings`
  MODIFY `setting_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

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
-- Constraints for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD CONSTRAINT `password_resets_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `students`
--
ALTER TABLE `students`
  ADD CONSTRAINT `students_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
