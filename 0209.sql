-- phpMyAdmin SQL Dump
-- version 4.7.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 09, 2026 at 10:43 AM
-- Server version: 10.1.30-MariaDB
-- PHP Version: 7.2.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `mlaas_quotation`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_add_new_user` (IN `p_admin_id` INT, IN `p_first_name` VARCHAR(50), IN `p_last_name` VARCHAR(50), IN `p_email` VARCHAR(100), IN `p_password_hash` VARCHAR(255), IN `p_default_role_name` VARCHAR(50))  BEGIN
    DECLARE v_user_id INT;
    DECLARE v_role_id INT;
    DECLARE v_event_id INT;

    -- 1. Insert the user
    INSERT INTO users (first_name, last_name, email, password_hash)
    VALUES (p_first_name, p_last_name, p_email, p_password_hash);
    
    SET v_user_id = LAST_INSERT_ID();

    -- 2. Assign the role
    SELECT role_id INTO v_role_id FROM roles WHERE role_name = p_default_role_name;

    IF v_role_id IS NOT NULL THEN
        INSERT INTO user_roles (user_id, role_id) VALUES (v_user_id, v_role_id);
    END IF;
    
    -- 3. LOG THE ACTION
    SELECT event_type_id INTO v_event_id FROM log_event_types WHERE event_code = 'USER_CREATE';
    
    INSERT INTO audit_logs (user_id, event_type_id, target_id, action_details)
    VALUES (p_admin_id, v_event_id, v_user_id, JSON_OBJECT('new_user_email', p_email));

    -- Return the new ID
    SELECT v_user_id AS new_user_id;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `audit_logs`
--

CREATE TABLE `audit_logs` (
  `log_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `event_type_id` int(11) DEFAULT NULL,
  `target_id` int(11) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `action_details` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `audit_logs`
--

INSERT INTO `audit_logs` (`log_id`, `user_id`, `event_type_id`, `target_id`, `ip_address`, `action_details`, `created_at`) VALUES
(7, 6, 7, 8, '192.168.1.55', 'Changed status from Active to Suspended', '2026-02-09 06:02:57'),
(8, 6, 2, 6, '::1', '', '2026-02-09 06:15:37'),
(9, 2, 1, 2, '::1', '{\"ip\":\"::1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/144.0.0.0 Safari\\/537.36\"}', '2026-02-09 06:15:44'),
(11, 2, 1, 2, '::1', 'Login via Web (UA: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36)', '2026-02-09 06:18:42'),
(12, 2, 2, 2, '::1', '', '2026-02-09 06:19:04'),
(13, 8, 1, 8, '::1', 'Login via Web (UA: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36)', '2026-02-09 06:20:44'),
(14, 8, 2, NULL, '::1', 'User initiated logout', '2026-02-09 06:20:47'),
(15, 8, 1, 8, '::1', 'Login via Web (UA: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36)', '2026-02-09 06:21:58'),
(16, 8, 2, 8, '::1', 'User 8 initiated logout', '2026-02-09 06:22:01'),
(17, 2, 1, 2, '::1', 'Login via Web (UA: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36)', '2026-02-09 06:31:10'),
(18, 2, 7, 8, '::1', 'Updated profile details for jef@gmail.com', '2026-02-09 06:59:07'),
(19, 2, 3, NULL, '::1', 'Created new user account: adas@asdas.sad', '2026-02-09 06:59:29'),
(20, 2, 4, 9, '::1', 'Deleted user account', '2026-02-09 07:02:08'),
(21, 2, 3, NULL, '::1', 'Created new user account for asdas@sadasd.sa', '2026-02-09 07:02:21'),
(22, 2, 3, NULL, '::1', 'Created new user account for asdas@sadasd.saa', '2026-02-09 07:02:27'),
(23, 2, 4, 12, '::1', 'Deleted user account', '2026-02-09 07:02:53'),
(24, 2, 4, 10, '::1', 'Deleted user account', '2026-02-09 07:02:57');

-- --------------------------------------------------------

--
-- Table structure for table `log_event_types`
--

CREATE TABLE `log_event_types` (
  `event_type_id` int(11) NOT NULL,
  `event_code` varchar(50) NOT NULL,
  `category` varchar(50) DEFAULT NULL,
  `description` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `log_event_types`
--

INSERT INTO `log_event_types` (`event_type_id`, `event_code`, `category`, `description`) VALUES
(1, 'AUTH_LOGIN', 'SECURITY', '%actor% has logged into the system from IP %ip%.'),
(2, 'AUTH_LOGOUT', 'SECURITY', '%actor% has logged out.'),
(3, 'USER_CREATE', 'USER_MGMT', '%actor% created a new user account for %target%.'),
(4, 'USER_DELETE', 'USER_MGMT', '%actor% deactivated the account of %target%. Reason: %details%'),
(5, 'QUOTE_CREATE', 'BUSINESS', '%actor% generated a new quotation. Reference: %details%'),
(6, 'QUOTE_UPDATE', 'BUSINESS', '%actor% updated a quotation. Changes: %details%'),
(7, 'USER_UPDATE', 'USER_MGMT', '%actor% updated %target%\'\'s profile. Changes: %details%');

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `permission_id` int(11) NOT NULL,
  `permission_key` varchar(50) NOT NULL,
  `description` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`permission_id`, `permission_key`, `description`) VALUES
(1, 'generate_quotation', 'The user should be able to generate quotation.'),
(2, 'add_user', 'The user is allowed to add user'),
(3, 'update_user', 'The user is allowed to update user'),
(4, 'view_user', 'The user is allowed to view users'),
(5, 'delete_user', 'The user is allowed to delete user');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `role_id` int(11) NOT NULL,
  `role_name` varchar(50) NOT NULL,
  `description` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`role_id`, `role_name`, `description`) VALUES
(1, 'Admin', 'From MCD'),
(2, 'Superadmin', 'Facilitates system'),
(3, 'Employee', 'Basic tasks');

-- --------------------------------------------------------

--
-- Table structure for table `role_permissions`
--

CREATE TABLE `role_permissions` (
  `role_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `role_permissions`
--

INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES
(1, 1),
(2, 2),
(2, 3),
(2, 4),
(2, 5);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_active` tinyint(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `first_name`, `last_name`, `email`, `password_hash`, `created_at`, `is_active`) VALUES
(2, 'Admin', 'One', 'admin1@gmail.com', '$2y$10$DSTNECSUIMSZ.Ho84L7bzuMGYQQTOozfQDSIFH5F3PTaK95q.DpNu', '2026-02-06 02:49:15', 1),
(6, 'Basic', 'Admin', 'adminbasic@gmail.com', '$2y$10$/VI5Xklap1.r4C5b1Y7Ss.UqwCcK2FkupIcQL/cbMni0K38ckGYxi', '2026-02-06 06:00:15', 1),
(8, 'jef', 'epstein', 'jef@gmail.com', '$2y$10$7XYsGsNDqeXfrRz8sn.nfO2e.t7qz02hDwvpaOoeF10Z47eIHE6De', '2026-02-09 05:13:10', 1);

-- --------------------------------------------------------

--
-- Table structure for table `user_roles`
--

CREATE TABLE `user_roles` (
  `user_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `user_roles`
--

INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES
(2, 2),
(6, 1),
(8, 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `event_type_id` (`event_type_id`);

--
-- Indexes for table `log_event_types`
--
ALTER TABLE `log_event_types`
  ADD PRIMARY KEY (`event_type_id`),
  ADD UNIQUE KEY `event_code` (`event_code`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`permission_id`),
  ADD UNIQUE KEY `permission_key` (`permission_key`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`role_id`),
  ADD UNIQUE KEY `role_name` (`role_name`);

--
-- Indexes for table `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD PRIMARY KEY (`role_id`,`permission_id`),
  ADD KEY `permission_id` (`permission_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `user_roles`
--
ALTER TABLE `user_roles`
  ADD PRIMARY KEY (`user_id`,`role_id`),
  ADD KEY `role_id` (`role_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `log_event_types`
--
ALTER TABLE `log_event_types`
  MODIFY `event_type_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `permission_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `role_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD CONSTRAINT `audit_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `audit_logs_ibfk_2` FOREIGN KEY (`event_type_id`) REFERENCES `log_event_types` (`event_type_id`);

--
-- Constraints for table `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD CONSTRAINT `role_permissions_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_permissions_ibfk_2` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`permission_id`) ON DELETE CASCADE;

--
-- Constraints for table `user_roles`
--
ALTER TABLE `user_roles`
  ADD CONSTRAINT `user_roles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_roles_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
