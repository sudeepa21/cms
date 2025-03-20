-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Mar 20, 2025 at 08:51 PM
-- Server version: 9.1.0
-- PHP Version: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `campus_management`
--

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

DROP TABLE IF EXISTS `attendance`;
CREATE TABLE IF NOT EXISTS `attendance` (
  `id` int NOT NULL AUTO_INCREMENT,
  `event_id` int NOT NULL,
  `badge_id` int NOT NULL,
  `student_id` int NOT NULL,
  `status` enum('present','absent') NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_attendance` (`event_id`,`student_id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `attendance`
--

INSERT INTO `attendance` (`id`, `event_id`, `badge_id`, `student_id`, `status`, `created_at`, `updated_at`) VALUES
(1, 33, 2, 1, 'present', '2025-03-10 13:44:42', '2025-03-10 14:07:06'),
(2, 33, 2, 3, 'present', '2025-03-10 13:44:42', '2025-03-10 14:06:47'),
(3, 36, 2, 1, 'present', '2025-03-11 15:25:03', '2025-03-11 15:25:03'),
(4, 37, 2, 1, 'present', '2025-03-11 15:28:47', '2025-03-11 15:28:47'),
(5, 37, 2, 4, 'absent', '2025-03-11 17:27:53', '2025-03-11 17:45:54'),
(9, 36, 2, 4, 'absent', '2025-03-11 17:47:50', '2025-03-11 17:47:50'),
(10, 38, 2, 1, 'absent', '2025-03-11 17:50:12', '2025-03-15 07:51:55'),
(12, 38, 2, 4, 'present', '2025-03-11 17:56:16', '2025-03-15 07:51:22');

-- --------------------------------------------------------

--
-- Table structure for table `badges`
--

DROP TABLE IF EXISTS `badges`;
CREATE TABLE IF NOT EXISTS `badges` (
  `badge_id` int NOT NULL AUTO_INCREMENT,
  `badge_name` varchar(100) NOT NULL,
  PRIMARY KEY (`badge_id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `badges`
--

INSERT INTO `badges` (`badge_id`, `badge_name`) VALUES
(1, 'HND 38'),
(2, 'KU-T-BSC(B01)'),
(3, 'KU-T-BSC(B02)'),
(4, 'KU-T-BSC(B03)'),
(5, 'DTEC Badge 01'),
(6, 'DTEC Badge 02');

-- --------------------------------------------------------

--
-- Table structure for table `chat_messages`
--

DROP TABLE IF EXISTS `chat_messages`;
CREATE TABLE IF NOT EXISTS `chat_messages` (
  `id` int NOT NULL AUTO_INCREMENT,
  `message` text NOT NULL,
  `sent_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `status` enum('sent','delivered','read') DEFAULT 'sent',
  `attachments` text,
  `badge_id` int NOT NULL,
  `user_id` varchar(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_chat_badge` (`badge_id`),
  KEY `fk_chat_users` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=96 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `chat_messages`
--

INSERT INTO `chat_messages` (`id`, `message`, `sent_at`, `status`, `attachments`, `badge_id`, `user_id`) VALUES
(92, 'Dear Students,  As informed by your lecturer, Mr. Niroshan Dananjaya, this week\'s Internet Services and Protocols lecture session is scheduled for March 16, 2025, from 1:00 PM to 5:00 PM in E8-3/4', '2025-03-14 11:01:05', 'sent', NULL, 0, 'U67c5ba3df1014'),
(93, 'Dear Students, As informed by your lecturer, Mr. Niroshan Dananjaya, this week\'s Internet Services and Protocols lecture session is scheduled for March 16, 2025, from 1:00 PM to 5:00 PM in E8-3/4 Thank You', '2025-03-14 11:02:39', 'sent', NULL, 2, 'U67c5ba3df1014'),
(94, 'noted', '2025-03-14 11:03:06', 'sent', NULL, 2, 'U67cebf9863ee5'),
(95, 'Hello, Students', '2025-03-15 07:06:21', 'sent', NULL, 2, 'U67c5ba3df1014');

-- --------------------------------------------------------

--
-- Table structure for table `chat_users`
--

DROP TABLE IF EXISTS `chat_users`;
CREATE TABLE IF NOT EXISTS `chat_users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `role` enum('admin','lecturer','student') NOT NULL,
  `avatar` varchar(255) DEFAULT 'default.png',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `class_rooms`
--

DROP TABLE IF EXISTS `class_rooms`;
CREATE TABLE IF NOT EXISTS `class_rooms` (
  `class_id` varchar(10) NOT NULL,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`class_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `class_rooms`
--

INSERT INTO `class_rooms` (`class_id`, `name`) VALUES
('01', 'E8-1'),
('02', 'E8-2'),
('03', 'E8-3'),
('04', 'A2-1'),
('05', 'A2-2'),
('06', 'Main Auditorium');

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

DROP TABLE IF EXISTS `courses`;
CREATE TABLE IF NOT EXISTS `courses` (
  `course_id` int NOT NULL AUTO_INCREMENT,
  `course_name` varchar(100) NOT NULL,
  PRIMARY KEY (`course_id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`course_id`, `course_name`) VALUES
(1, 'BSc (Hons) in Information Technology'),
(2, 'Computer Science'),
(5, 'Cyber Security');

-- --------------------------------------------------------

--
-- Table structure for table `enrollments`
--

DROP TABLE IF EXISTS `enrollments`;
CREATE TABLE IF NOT EXISTS `enrollments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `student_id` int NOT NULL,
  `course_id` int NOT NULL,
  `badge_id` int NOT NULL,
  `enrollment_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_enrollments_student` (`student_id`),
  KEY `fk_enrollments_course` (`course_id`),
  KEY `fk_enrollments_badge` (`badge_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `enrollments`
--

INSERT INTO `enrollments` (`id`, `student_id`, `course_id`, `badge_id`, `enrollment_date`) VALUES
(1, 1, 1, 2, '2025-03-10 06:17:59'),
(2, 3, 1, 2, '2025-03-10 13:43:17'),
(3, 4, 1, 2, '2025-03-11 13:51:09');

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

DROP TABLE IF EXISTS `events`;
CREATE TABLE IF NOT EXISTS `events` (
  `id` int NOT NULL AUTO_INCREMENT,
  `type` varchar(50) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `location` varchar(100) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `time` time DEFAULT NULL,
  `badge_id` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=47 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`id`, `type`, `name`, `location`, `date`, `time`, `badge_id`) VALUES
(46, 'Guest Lecture', 'Special Guest Lecturer: Software Development Best Practices ', 'Esoft Campus Colombo', '2025-04-05', '08:30:00', 0);

-- --------------------------------------------------------

--
-- Table structure for table `event_badges`
--

DROP TABLE IF EXISTS `event_badges`;
CREATE TABLE IF NOT EXISTS `event_badges` (
  `id` int NOT NULL AUTO_INCREMENT,
  `event_id` int NOT NULL,
  `badge_id` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `event_id` (`event_id`),
  KEY `badge_id` (`badge_id`)
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lecturer`
--

DROP TABLE IF EXISTS `lecturer`;
CREATE TABLE IF NOT EXISTS `lecturer` (
  `user_id` varchar(50) NOT NULL,
  `course_id` varchar(50) NOT NULL,
  PRIMARY KEY (`user_id`,`course_id`),
  KEY `course_id` (`course_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `lecturer`
--

INSERT INTO `lecturer` (`user_id`, `course_id`) VALUES
('U67c815a3b2f22', '001'),
('U67c815a3b2f22', '002'),
('U67c815a3b2f22', '005');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
CREATE TABLE IF NOT EXISTS `notifications` (
  `id` int NOT NULL AUTO_INCREMENT,
  `type` enum('event','user_request','resource_booking') NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `status` enum('unread','read') DEFAULT 'unread',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=49 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `type`, `message`, `created_at`, `status`) VALUES
(35, 'event', 'An event has been removed from the calendar!', '2025-03-15 08:13:46', 'unread'),
(36, 'event', 'A new event has been added to the calendar!', '2025-03-15 08:15:04', 'unread'),
(34, 'event', 'An event has been removed from the calendar!', '2025-03-15 08:13:18', 'unread'),
(33, 'event', 'A new event has been added to the calendar!', '2025-03-14 08:59:13', 'unread'),
(32, 'event', 'A new event has been added to the calendar!', '2025-03-11 17:49:23', 'unread'),
(30, 'event', 'An event has been removed from the calendar!', '2025-03-11 17:48:18', 'unread'),
(31, 'event', 'An event has been removed from the calendar!', '2025-03-11 17:48:19', 'unread'),
(29, 'event', 'An event has been removed from the calendar!', '2025-03-11 17:48:18', 'unread'),
(28, 'event', 'An event has been removed from the calendar!', '2025-03-11 17:48:17', 'unread'),
(27, 'event', 'A new event has been added to the calendar!', '2025-03-11 15:27:09', 'unread'),
(26, 'event', 'A new event has been added to the calendar!', '2025-03-11 10:32:19', 'unread'),
(37, 'event', 'An event has been removed from the calendar!', '2025-03-15 16:10:23', 'unread'),
(38, 'event', 'A new event has been added to the calendar!', '2025-03-15 16:13:21', 'unread'),
(39, 'event', 'A new event has been added to the calendar!', '2025-03-18 13:44:43', 'unread'),
(40, 'event', 'A new event has been added to the calendar!', '2025-03-19 06:42:39', 'unread'),
(41, 'event', 'An event has been removed from the calendar!', '2025-03-19 06:43:08', 'unread'),
(42, 'event', 'An event has been removed from the calendar!', '2025-03-19 06:43:10', 'unread'),
(43, 'event', 'A new event has been added to the calendar!', '2025-03-19 06:50:36', 'unread'),
(44, 'event', 'An event has been removed from the calendar!', '2025-03-19 06:50:41', 'unread'),
(45, 'event', 'An event has been removed from the calendar!', '2025-03-19 07:25:30', 'unread'),
(46, 'event', 'A new event has been added to the calendar!', '2025-03-19 07:32:05', 'unread'),
(47, 'event', 'An event has been removed from the calendar!', '2025-03-19 07:41:46', 'unread'),
(48, 'event', 'A new event has been added to the calendar!', '2025-03-19 08:07:25', 'unread');

-- --------------------------------------------------------

--
-- Table structure for table `resource_requests`
--

DROP TABLE IF EXISTS `resource_requests`;
CREATE TABLE IF NOT EXISTS `resource_requests` (
  `id` int NOT NULL AUTO_INCREMENT,
  `badge_id` varchar(50) DEFAULT NULL,
  `course_id` varchar(10) DEFAULT NULL,
  `requested_date` date DEFAULT NULL,
  `class_id` varchar(10) DEFAULT NULL,
  `other_resources` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `status` enum('pending','approved','denied') DEFAULT 'pending',
  `user_id` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_badge_id` (`badge_id`),
  KEY `fk_course_id` (`course_id`),
  KEY `fk_class_id` (`class_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=35 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `resource_requests`
--

INSERT INTO `resource_requests` (`id`, `badge_id`, `course_id`, `requested_date`, `class_id`, `other_resources`, `status`, `user_id`) VALUES
(34, '2', '1', '2025-04-30', '06', '', 'denied', 'U67c815a3b2f22'),
(33, '1', '2', '2025-03-21', '06', 'Need a Projector', 'approved', 'U67c815a3b2f22');

-- --------------------------------------------------------

--
-- Table structure for table `scheduled_classes`
--

DROP TABLE IF EXISTS `scheduled_classes`;
CREATE TABLE IF NOT EXISTS `scheduled_classes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `course_code` varchar(10) DEFAULT NULL,
  `class_date` date DEFAULT NULL,
  `class_time` time DEFAULT NULL,
  `room` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
CREATE TABLE IF NOT EXISTS `sessions` (
  `session_id` varchar(128) NOT NULL,
  `user_id` varchar(50) NOT NULL,
  `role` enum('student','teacher','admin') NOT NULL,
  `login_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `last_activity` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`session_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`session_id`, `user_id`, `role`, `login_time`, `last_activity`) VALUES
('qrilknvdk1h1mgin0i6s4qgca9', 'U67c5ba3df1014', 'admin', '2025-03-05 13:40:01', '2025-03-05 13:42:38'),
('s616ckq9nc31829r07lf3mh3vh', 'U67c5ba3df1014', 'admin', '2025-03-05 13:47:30', '2025-03-05 13:47:39'),
('h1bn7dii1pnftf5acak4fh7n4p', 'U67c5ba3df1014', 'admin', '2025-03-05 13:47:59', '2025-03-05 13:49:26'),
('r94jshv5i322nft7o0qsqcumdi', 'U67c5ba3df1014', 'admin', '2025-03-05 14:02:32', '2025-03-05 14:04:52'),
('mfpvt1r5sl9ralf964m7otu86s', 'U67c5ba3df1014', 'admin', '2025-03-05 14:05:20', '2025-03-05 14:05:39'),
('l492g83hhhvbohds4d7m6g8m4l', 'U67c5ba3df1014', 'admin', '2025-03-05 14:09:18', '2025-03-05 14:09:18'),
('so75hqc7j56am1jvtv44mktkn8', 'U67c5ba3df1014', 'admin', '2025-03-05 14:09:42', '2025-03-05 14:09:42'),
('vgh6kk4vugrt7bp477b9kocj2t', 'U67c5ba3df1014', 'admin', '2025-03-05 14:13:06', '2025-03-05 14:13:06'),
('sbcql03eo0elui3levj4r9lli4', 'U67c5ba3df1014', 'admin', '2025-03-05 14:15:28', '2025-03-05 14:15:28'),
('a7nb16eo55vikaak1df1k6gbmg', 'U67c5ba3df1014', 'admin', '2025-03-05 14:20:23', '2025-03-05 14:20:23'),
('304m7av8280gassc892tscvfau', 'U67c5ba3df1014', 'admin', '2025-03-05 14:22:15', '2025-03-05 14:22:15'),
('gbpf0728hbkm1jrabukbn6l6i2', 'U67c815a3b2f22', '', '2025-03-05 14:24:38', '2025-03-05 14:24:38'),
('c9mi3cuvfbtot1mchgt5639keo', 'U67c6eba05a8f3', 'student', '2025-03-05 15:04:18', '2025-03-05 15:04:18'),
('bn95uga0sp0v7gnl8g1u1hg6f3', 'U67c6eba05a8f3', 'admin', '2025-03-05 15:05:16', '2025-03-05 15:05:16'),
('f7hu6juuhmp1uj7u4d75j9u8cr', 'U67c6eba05a8f3', 'admin', '2025-03-05 15:13:54', '2025-03-05 15:13:54'),
('gq1qf5l060c48gu8mihgev7074', 'U67c815a3b2f22', '', '2025-03-05 16:41:09', '2025-03-05 16:41:09'),
('rgq1gs8n3o743gbrqgp5ubm78q', 'U67c5ba3df1014', 'admin', '2025-03-05 16:41:38', '2025-03-05 16:41:38'),
('aga5m3v6oohoto590bo5c5l63s', 'U67c5ba3df1014', 'admin', '2025-03-05 17:46:06', '2025-03-05 17:46:06'),
('ui4kbgl9c450bj0kbdl183g2ie', 'U67c815a3b2f22', '', '2025-03-05 17:46:15', '2025-03-05 17:46:15'),
('jthrc9jm3hp0lffvpv8l0f88cs', 'U67c5ba3df1014', 'admin', '2025-03-05 18:11:25', '2025-03-05 18:11:25'),
('32vpc54kmfdm92vf0kc66i5snm', 'U67c89609b6528', '', '2025-03-05 20:00:34', '2025-03-05 20:00:34'),
('cftrbudfc91m9sts4trm64uf35', 'U67c5ba3df1014', 'admin', '2025-03-06 16:40:18', '2025-03-06 16:40:18'),
('j7jlgc6tb37vct2dpoiv4ojd73', 'U67c5ba3df1014', 'admin', '2025-03-07 06:23:41', '2025-03-07 06:23:41'),
('4i3g5phh61o42oduh3fi43r4gk', 'U67c89609b6528', '', '2025-03-07 06:39:40', '2025-03-07 06:39:40'),
('77b9suk0g5qti0l1bn3nt7rolp', 'U67c5ba3df1014', 'admin', '2025-03-07 17:37:15', '2025-03-07 17:37:15'),
('vt83fbtb7dk711puaikj94tvt3', 'U67c89609b6528', '', '2025-03-08 19:50:15', '2025-03-08 19:50:15'),
('t0t0ggeoueeeahkvojc7fj8pvj', 'U67cea31b20159', 'student', '2025-03-10 08:30:42', '2025-03-10 08:30:42'),
('k45dcjb42g2tucrq8bq27n33j4', 'U67c5ba3df1014', 'admin', '2025-03-10 18:20:48', '2025-03-10 18:20:48'),
('kstpatbm5pqqjhuam0k1bukm6e', 'U67cea31b20159', 'student', '2025-03-10 08:31:08', '2025-03-10 08:31:08'),
('i15hmgbqjdt3k0cm1hlqg15f1o', 'U67c5ba3df1014', 'admin', '2025-03-10 09:51:48', '2025-03-10 09:51:48'),
('c71lknj199tu38ekq70gfu4cv1', 'U67cea31b20159', 'student', '2025-03-10 18:14:21', '2025-03-10 18:14:21'),
('2pajd5of3odlbh3of8036cftc4', 'U67cea31b20159', 'student', '2025-03-10 18:16:07', '2025-03-10 18:16:07'),
('l8m2b32hcjcu8hrbjhm2qq7emv', 'U67cebf9863ee5', 'student', '2025-03-11 13:47:08', '2025-03-11 13:47:08'),
('ohj46h8css3lm2cb3lcgq80qn9', 'U67c815a3b2f22', '', '2025-03-11 08:21:16', '2025-03-11 08:21:16'),
('gf6hn5pomcu0bmrfak0789s99s', 'U67c5ba3df1014', 'admin', '2025-03-11 15:14:35', '2025-03-11 15:14:35'),
('grtkvis5nvphbanejfg4emvbnd', 'U67c5ba3df1014', 'admin', '2025-03-11 17:00:27', '2025-03-11 17:00:27'),
('skhmo6g3cojop4eq80dqd9c3le', 'U67c5ba3df1014', 'admin', '2025-03-11 15:33:57', '2025-03-11 15:33:57'),
('n5k00mflcm546ne825j02drejk', 'U67c5ba3df1014', 'admin', '2025-03-15 06:06:12', '2025-03-15 06:06:12'),
('6p3886fk955vum7smli8v4foiu', 'U67c815a3b2f22', '', '2025-03-14 16:10:40', '2025-03-14 16:10:40'),
('fk1ng17drfbtfer4oh8oihu8eo', 'U67c5ba3df1014', 'admin', '2025-03-15 06:07:41', '2025-03-15 06:07:41'),
('716nerq5icg078j6sbb8cjfrem', 'U67c815a3b2f22', '', '2025-03-15 06:12:41', '2025-03-15 06:12:41'),
('0ub55atmrj9iuplj3bsfnib5tm', 'U67c5ba3df1014', 'admin', '2025-03-15 16:26:16', '2025-03-15 16:26:16'),
('klhfe0oo3dtjqfbnqmhj361sp4', 'U67c5ba3df1014', 'admin', '2025-03-15 20:56:35', '2025-03-15 20:56:35'),
('1j0qo1cv3djnh8kd5j1cmoqbue', 'U67c815a3b2f22', '', '2025-03-15 20:59:34', '2025-03-15 20:59:34'),
('bihtbvnken56umdshmrpbdn7ot', 'U67c815a3b2f22', '', '2025-03-16 16:52:50', '2025-03-16 16:52:50'),
('m2pvodp8fkv1p0pj0rpvvn9eif', 'U67c5ba3df1014', 'admin', '2025-03-17 17:17:37', '2025-03-17 17:17:37'),
('ml85a4vcr3187s4bolv7m98lcm', 'U67c815a3b2f22', '', '2025-03-18 20:40:14', '2025-03-18 20:40:14'),
('51s7g4ft1ofdhu344mgp8v2bkk', 'U67c815a3b2f22', '', '2025-03-19 09:30:21', '2025-03-19 09:30:21'),
('qt4ujcvttfur8jugggca72q2fv', 'U67cebf9863ee5', 'student', '2025-03-19 21:10:19', '2025-03-19 21:10:19'),
('53lna48ufdb33mcggk0fl90dms', 'U67cebf9863ee5', 'student', '2025-03-20 17:17:36', '2025-03-20 17:17:36');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

DROP TABLE IF EXISTS `students`;
CREATE TABLE IF NOT EXISTS `students` (
  `student_id` int NOT NULL AUTO_INCREMENT,
  `user_id` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`student_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`student_id`, `user_id`) VALUES
(1, 'U67c6dad7ec3a7'),
(3, 'U67cea31b20159'),
(4, 'U67cebf9863ee5');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` varchar(20) NOT NULL,
  `uni_ID` varchar(50) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `mobile` varchar(15) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','lecturer','student') NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `status` enum('pending','approved') NOT NULL DEFAULT 'pending',
  `profile_picture` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `username` (`username`)
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `user_id`, `uni_ID`, `first_name`, `last_name`, `email`, `mobile`, `username`, `password`, `role`, `created_at`, `status`, `profile_picture`) VALUES
(1, 'U67c5ba3df1014', 'E25689', 'sudeepa', 'angulugaha', 'sudeepa7731@gmail.com', '0773121771', 'sudeepa', '$2y$10$SioDCpz1ZZJF/tCdUvJRv.1vn3onpsahH9CiMOvZTo2h2ReQdTZGa', 'admin', '2025-03-03 14:18:38', 'approved', 'pro_pic_3.jpg'),
(13, 'U67d5cd8c7fe33', 'E456875', 'Tharindu', 'Jayamanna', 'Tharu356@gmail.com', '0772562133', 'Tharindu', '$2y$10$KNeLXpvS3/rAfo4uSUYytuGlNlgY1zn8m4XgpPU2LJcXHWf7KSVra', 'student', '2025-03-15 18:57:16', 'pending', NULL),
(3, 'U67c6dad7ec3a7', 'E127658', 'pahan', 'ranuka', 'ranuka@gmail.com', '07756441658', 'pahan', '$2y$10$0FPyQbdnrUBJQmGYQ7pgVOaIKHnCM9ATUP9LFRz1.U1qmuVNELmCm', 'student', '2025-03-04 10:50:00', 'approved', NULL),
(6, 'U67c815a3b2f22', 'E12537', 'nimali', 'perera', 'nimali@gmail.com', '0774521659', 'nimali', '$2y$10$m5jR72rma3SBc2HJ4W0nfeBKfDPTYCQ1HZ3D3zSc0rNyeXhPsibh2', 'lecturer', '2025-03-05 09:13:07', 'approved', 'pro-pic5.jpg'),
(7, 'U67c89609b6528', 'E123659', 'Deshan', 'Dias', 'desha@gmail.com', '0764526597', 'deshan', '$2y$10$r8aCU6EzaGbOduvDalijR.RLXDjF1cEad1tOz4fNEor0bviCU7Z7u', 'lecturer', '2025-03-05 18:20:57', 'approved', 'profile pic1.jpg'),
(11, 'U67d3dda7f15e1', 'E524565', 'Sadushka', 'Perera', 'sadush778@gmail.com', '0773125453', 'Sadushka', '$2y$10$1/vkj5Wz/NzSV.lIPDfaDuVgAPrlCjiCvb3Qz5.x2g1M2yWqsak9C', 'student', '2025-03-14 07:41:28', 'approved', NULL),
(12, 'U67d54dbb070f0', 'E435688', 'Praveen', 'Santhush', 'Praveensth@gmail.com', '0765544852', 'Praveen_21', '$2y$10$sGF6K5ZOTyB8Lh3RwDeqCeIcIfVvkzuNvG/SMrqz1KwSX9xkscgz6', 'student', '2025-03-15 09:51:55', 'approved', NULL),
(10, 'U67cebf9863ee5', 'E565264', 'jagath', 'perera', 'newemail@student.com', '0754624562', 'jagath', '$2y$10$yi.Cs.1r66/jwWYf3be03.Qfu3p1Ld3lusdgANkIhFSmqTysL5IFS', 'student', '2025-03-10 10:31:52', 'approved', 'profile pic3.jpg');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
