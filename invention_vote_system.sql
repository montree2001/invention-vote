-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Aug 05, 2025 at 05:48 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `invention_vote_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `audit_logs`
--

CREATE TABLE `audit_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(100) NOT NULL COMMENT 'การกระทำ เช่น LOGIN, VOTE, APPROVE',
  `table_name` varchar(100) DEFAULT NULL COMMENT 'ตารางที่มีการเปลี่ยนแปลง',
  `record_id` int(11) DEFAULT NULL COMMENT 'ID ของข้อมูลที่เปลี่ยนแปลง',
  `old_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'ค่าเดิม' CHECK (json_valid(`old_values`)),
  `new_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'ค่าใหม่' CHECK (json_valid(`new_values`)),
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `competitions`
--

CREATE TABLE `competitions` (
  `id` int(11) NOT NULL,
  `competition_name` varchar(255) NOT NULL COMMENT 'ชื่อรายการแข่งขัน',
  `competition_year` int(11) NOT NULL COMMENT 'ปีการศึกษา',
  `level_id` int(11) NOT NULL,
  `province_name` varchar(100) DEFAULT NULL COMMENT 'ชื่อจังหวัด (สำหรับระดับจังหวัด)',
  `region_name` varchar(100) DEFAULT NULL COMMENT 'ชื่อภาค (สำหรับระดับภาค)',
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `registration_start` date DEFAULT NULL,
  `registration_end` date DEFAULT NULL,
  `voting_start` datetime DEFAULT NULL,
  `voting_end` datetime DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `status` enum('PREPARING','REGISTRATION','VOTING','COMPLETED') DEFAULT 'PREPARING',
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `competition_admins`
--

CREATE TABLE `competition_admins` (
  `id` int(11) NOT NULL,
  `competition_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `role` enum('ADMIN','CHAIRMAN') NOT NULL,
  `category_id` int(11) DEFAULT NULL COMMENT 'ประเภทที่รับผิดชอบ (สำหรับประธานกรรมการ)',
  `assigned_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `competition_levels`
--

CREATE TABLE `competition_levels` (
  `id` int(11) NOT NULL,
  `level_name` varchar(100) NOT NULL COMMENT 'ชื่อระดับ เช่น จังหวัด, ภาค, ชาติ',
  `level_code` varchar(20) NOT NULL COMMENT 'รหัสระดับ เช่น PROVINCE, REGION, NATIONAL',
  `level_order` int(11) NOT NULL COMMENT 'ลำดับระดับ 1=จังหวัด, 2=ภาค, 3=ชาติ',
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `competition_levels`
--

INSERT INTO `competition_levels` (`id`, `level_name`, `level_code`, `level_order`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'ระดับจังหวัด (สอจ.)', 'PROVINCE', 1, 1, '2025-08-05 15:48:01', '2025-08-05 15:48:01'),
(2, 'ระดับภาค', 'REGION', 2, 1, '2025-08-05 15:48:01', '2025-08-05 15:48:01'),
(3, 'ระดับชาติ', 'NATIONAL', 3, 1, '2025-08-05 15:48:01', '2025-08-05 15:48:01');

-- --------------------------------------------------------

--
-- Table structure for table `inventions`
--

CREATE TABLE `inventions` (
  `id` int(11) NOT NULL,
  `invention_code` varchar(50) NOT NULL COMMENT 'รหัสสิ่งประดิษฐ์',
  `invention_name` varchar(255) NOT NULL COMMENT 'ชื่อสิ่งประดิษฐ์',
  `competition_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `institution_name` varchar(255) NOT NULL COMMENT 'สถานศึกษา',
  `province` varchar(100) DEFAULT NULL,
  `inventors` text DEFAULT NULL COMMENT 'รายชื่อผู้ประดิษฐ์ (JSON)',
  `advisors` text DEFAULT NULL COMMENT 'รายชื่อครูที่ปรึกษา (JSON)',
  `abstract` text DEFAULT NULL COMMENT 'บทคัดย่อ',
  `objectives` text DEFAULT NULL COMMENT 'วัตถุประสงค์',
  `thaiinvention_url` varchar(500) DEFAULT NULL COMMENT 'URL ใน thaiinvention.net',
  `document_v_sost_2` varchar(500) DEFAULT NULL COMMENT 'ไฟล์ แบบ ว-สอศ-2',
  `document_v_sost_3` varchar(500) DEFAULT NULL COMMENT 'ไฟล์ แบบ ว-สอศ-3',
  `document_appendix` varchar(500) DEFAULT NULL COMMENT 'ไฟล์ภาคผนวก',
  `images` text DEFAULT NULL COMMENT 'รูปภาพประกอบ (JSON)',
  `videos` text DEFAULT NULL COMMENT 'วิดีโอประกอบ (JSON)',
  `submission_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` enum('DRAFT','SUBMITTED','APPROVED','REJECTED') DEFAULT 'DRAFT',
  `is_qualified` tinyint(1) DEFAULT 0 COMMENT 'ผ่านการคัดเลือกหรือไม่',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `invention_categories`
--

CREATE TABLE `invention_categories` (
  `id` int(11) NOT NULL,
  `category_name` varchar(255) NOT NULL COMMENT 'ชื่อประเภท',
  `category_code` varchar(50) NOT NULL COMMENT 'รหัสประเภท',
  `description` text DEFAULT NULL COMMENT 'คำอธิบายประเภท',
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `invention_categories`
--

INSERT INTO `invention_categories` (`id`, `category_name`, `category_code`, `description`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'ประเภทที่ 1 สิ่งประดิษฐ์ด้านอุตสาหกรรมและเกษตรกรรม', 'CATEGORY_1', 'สิ่งประดิษฐ์ที่เกี่ยวข้องกับอุตสาหกรรมและเกษตรกรรม', 1, '2025-08-05 15:48:01', '2025-08-05 15:48:01'),
(2, 'ประเภทที่ 2 สิ่งประดิษฐ์ด้านนวัตกรรมและเทคโนโลยีปัญญาประดิษฐ์ อุปกรณ์อัจฉริยะ', 'CATEGORY_2', 'สิ่งประดิษฐ์ที่ใช้เทคโนโลยี AI และอุปกรณ์อัจฉริยะ', 1, '2025-08-05 15:48:01', '2025-08-05 15:48:01'),
(3, 'ประเภทที่ 3 สิ่งประดิษฐ์ด้านพลังงานทดแทนและสิ่งแวดล้อม', 'CATEGORY_3', 'สิ่งประดิษฐ์ที่เกี่ยวข้องกับพลังงานสะอาดและสิ่งแวดล้อม', 1, '2025-08-05 15:48:01', '2025-08-05 15:48:01');

-- --------------------------------------------------------

--
-- Stand-in structure for view `invention_scores_summary`
-- (See below for the actual view)
--
CREATE TABLE `invention_scores_summary` (
`invention_id` int(11)
,`invention_name` varchar(255)
,`competition_id` int(11)
,`category_id` int(11)
,`institution_name` varchar(255)
,`total_judges` bigint(21)
,`total_score` decimal(27,2)
,`average_score` decimal(9,6)
,`last_voted_at` timestamp
);

-- --------------------------------------------------------

--
-- Table structure for table `judge_assignments`
--

CREATE TABLE `judge_assignments` (
  `id` int(11) NOT NULL,
  `competition_id` int(11) NOT NULL,
  `judge_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `assigned_by` int(11) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Stand-in structure for view `judge_voting_progress`
-- (See below for the actual view)
--
CREATE TABLE `judge_voting_progress` (
`competition_id` int(11)
,`category_id` int(11)
,`judge_id` int(11)
,`first_name` varchar(100)
,`last_name` varchar(100)
,`institution_name` varchar(255)
,`total_inventions` bigint(21)
,`voted_inventions` bigint(21)
,`voting_percentage` decimal(26,2)
);

-- --------------------------------------------------------

--
-- Table structure for table `result_approvals`
--

CREATE TABLE `result_approvals` (
  `id` int(11) NOT NULL,
  `competition_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `chairman_id` int(11) NOT NULL,
  `approved_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `notes` text DEFAULT NULL COMMENT 'หมายเหตุ',
  `is_active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `scoring_criteria_main`
--

CREATE TABLE `scoring_criteria_main` (
  `id` int(11) NOT NULL,
  `criteria_name` varchar(255) NOT NULL COMMENT 'ชื่อเกณฑ์หลัก',
  `max_score` int(11) NOT NULL COMMENT 'คะแนนเต็ม',
  `order_no` int(11) NOT NULL COMMENT 'ลำดับการแสดง',
  `category_id` int(11) NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `scoring_criteria_sub`
--

CREATE TABLE `scoring_criteria_sub` (
  `id` int(11) NOT NULL,
  `main_criteria_id` int(11) NOT NULL,
  `sub_criteria_name` varchar(255) NOT NULL COMMENT 'ชื่อจุดให้คะแนน',
  `max_score` int(11) NOT NULL COMMENT 'คะแนนเต็มของจุดนี้',
  `order_no` int(11) NOT NULL COMMENT 'ลำดับการแสดง',
  `description` text DEFAULT NULL COMMENT 'คำอธิบายเกณฑ์',
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `scoring_levels`
--

CREATE TABLE `scoring_levels` (
  `id` int(11) NOT NULL,
  `sub_criteria_id` int(11) NOT NULL,
  `level_name` varchar(50) NOT NULL COMMENT 'ชื่อระดับ เช่น ดีมาก, ดี, พอใช้, ปรับปรุง',
  `score_value` decimal(5,2) NOT NULL COMMENT 'คะแนนของระดับนี้',
  `level_order` int(11) NOT NULL COMMENT 'ลำดับระดับ 1=ดีมาก, 2=ดี, 3=พอใช้, 4=ปรับปรุง',
  `description` text DEFAULT NULL COMMENT 'คำอธิบายเกณฑ์ระดับนี้',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `scoring_point_settings`
--

CREATE TABLE `scoring_point_settings` (
  `id` int(11) NOT NULL,
  `competition_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `sub_criteria_id` int(11) NOT NULL,
  `is_enabled` tinyint(1) DEFAULT 1 COMMENT 'เปิด-ปิดจุดให้คะแนน',
  `updated_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `system_settings`
--

CREATE TABLE `system_settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `system_settings`
--

INSERT INTO `system_settings` (`id`, `setting_key`, `setting_value`, `description`, `updated_by`, `updated_at`) VALUES
(1, 'system_name', 'ระบบประมวลผลสิ่งประดิษฐ์คนรุ่นใหม่ (INVENTION-VOTE)', 'ชื่อระบบ', NULL, '2025-08-05 15:48:01'),
(2, 'system_version', '2.0', 'เวอร์ชันระบบ', NULL, '2025-08-05 15:48:01'),
(3, 'default_theme', 'white', 'ธีมเริ่มต้น', NULL, '2025-08-05 15:48:01'),
(4, 'default_font', 'Kanit', 'ฟอนต์เริ่มต้น', NULL, '2025-08-05 15:48:01'),
(5, 'max_file_size', '10485760', 'ขนาดไฟล์สูงสุด (bytes)', NULL, '2025-08-05 15:48:01'),
(6, 'allowed_file_types', 'pdf,doc,docx,jpg,jpeg,png,mp4', 'ประเภทไฟล์ที่อนุญาต', NULL, '2025-08-05 15:48:01');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `user_type` enum('SUPER_ADMIN','ADMIN','CHAIRMAN','JUDGE') NOT NULL,
  `institution_name` varchar(255) DEFAULT NULL COMMENT 'ชื่อสถานศึกษา/หน่วยงาน',
  `province` varchar(100) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `last_login` timestamp NULL DEFAULT NULL,
  `last_ip` varchar(45) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `first_name`, `last_name`, `email`, `phone`, `user_type`, `institution_name`, `province`, `is_active`, `last_login`, `last_ip`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'ผู้ดูแล', 'ระบบ', 'admin@example.com', NULL, 'SUPER_ADMIN', NULL, NULL, 1, NULL, NULL, NULL, '2025-08-05 15:48:01', '2025-08-05 15:48:01');

-- --------------------------------------------------------

--
-- Table structure for table `voting_restrictions`
--

CREATE TABLE `voting_restrictions` (
  `id` int(11) NOT NULL,
  `invention_id` int(11) NOT NULL,
  `judge_id` int(11) NOT NULL,
  `restriction_type` enum('SAME_INSTITUTION','MANUAL_BLOCK') NOT NULL,
  `reason` text DEFAULT NULL,
  `restricted_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `voting_scores`
--

CREATE TABLE `voting_scores` (
  `id` int(11) NOT NULL,
  `competition_id` int(11) NOT NULL,
  `invention_id` int(11) NOT NULL,
  `judge_id` int(11) NOT NULL,
  `sub_criteria_id` int(11) NOT NULL,
  `scoring_level_id` int(11) NOT NULL,
  `score_value` decimal(5,2) NOT NULL,
  `comments` text DEFAULT NULL COMMENT 'ความเห็นเพิ่มเติม',
  `voted_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure for view `invention_scores_summary`
--
DROP TABLE IF EXISTS `invention_scores_summary`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `invention_scores_summary`  AS SELECT `i`.`id` AS `invention_id`, `i`.`invention_name` AS `invention_name`, `i`.`competition_id` AS `competition_id`, `i`.`category_id` AS `category_id`, `i`.`institution_name` AS `institution_name`, count(distinct `vs`.`judge_id`) AS `total_judges`, sum(`vs`.`score_value`) AS `total_score`, avg(`vs`.`score_value`) AS `average_score`, max(`vs`.`voted_at`) AS `last_voted_at` FROM (`inventions` `i` left join `voting_scores` `vs` on(`i`.`id` = `vs`.`invention_id`)) WHERE `i`.`status` = 'APPROVED' GROUP BY `i`.`id`, `i`.`invention_name`, `i`.`competition_id`, `i`.`category_id`, `i`.`institution_name` ;

-- --------------------------------------------------------

--
-- Structure for view `judge_voting_progress`
--
DROP TABLE IF EXISTS `judge_voting_progress`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `judge_voting_progress`  AS SELECT `ja`.`competition_id` AS `competition_id`, `ja`.`category_id` AS `category_id`, `ja`.`judge_id` AS `judge_id`, `u`.`first_name` AS `first_name`, `u`.`last_name` AS `last_name`, `u`.`institution_name` AS `institution_name`, count(distinct `i`.`id`) AS `total_inventions`, count(distinct `vs`.`invention_id`) AS `voted_inventions`, round(count(distinct `vs`.`invention_id`) / count(distinct `i`.`id`) * 100,2) AS `voting_percentage` FROM (((`judge_assignments` `ja` join `users` `u` on(`ja`.`judge_id` = `u`.`id`)) join `inventions` `i` on(`ja`.`competition_id` = `i`.`competition_id` and `ja`.`category_id` = `i`.`category_id`)) left join `voting_scores` `vs` on(`ja`.`judge_id` = `vs`.`judge_id` and `i`.`id` = `vs`.`invention_id`)) WHERE `ja`.`is_active` = 1 AND `i`.`status` = 'APPROVED' GROUP BY `ja`.`competition_id`, `ja`.`category_id`, `ja`.`judge_id`, `u`.`first_name`, `u`.`last_name`, `u`.`institution_name` ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_action` (`user_id`,`action`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indexes for table `competitions`
--
ALTER TABLE `competitions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_competition_year` (`competition_year`),
  ADD KEY `idx_level_status` (`level_id`,`status`);

--
-- Indexes for table `competition_admins`
--
ALTER TABLE `competition_admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_admin_competition` (`competition_id`,`user_id`,`category_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `competition_levels`
--
ALTER TABLE `competition_levels`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `level_code` (`level_code`);

--
-- Indexes for table `inventions`
--
ALTER TABLE `inventions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `invention_code` (`invention_code`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `idx_competition_category` (`competition_id`,`category_id`),
  ADD KEY `idx_institution` (`institution_name`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `invention_categories`
--
ALTER TABLE `invention_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `category_code` (`category_code`);

--
-- Indexes for table `judge_assignments`
--
ALTER TABLE `judge_assignments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_judge_assignment` (`competition_id`,`judge_id`,`category_id`),
  ADD KEY `judge_id` (`judge_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `result_approvals`
--
ALTER TABLE `result_approvals`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_approval` (`competition_id`,`category_id`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `chairman_id` (`chairman_id`);

--
-- Indexes for table `scoring_criteria_main`
--
ALTER TABLE `scoring_criteria_main`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_category_order` (`category_id`,`order_no`);

--
-- Indexes for table `scoring_criteria_sub`
--
ALTER TABLE `scoring_criteria_sub`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_main_order` (`main_criteria_id`,`order_no`);

--
-- Indexes for table `scoring_levels`
--
ALTER TABLE `scoring_levels`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_sub_level` (`sub_criteria_id`,`level_order`);

--
-- Indexes for table `scoring_point_settings`
--
ALTER TABLE `scoring_point_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_scoring_setting` (`competition_id`,`category_id`,`sub_criteria_id`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `sub_criteria_id` (`sub_criteria_id`);

--
-- Indexes for table `system_settings`
--
ALTER TABLE `system_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`),
  ADD KEY `updated_by` (`updated_by`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `idx_username` (`username`),
  ADD KEY `idx_user_type` (`user_type`),
  ADD KEY `idx_institution` (`institution_name`);

--
-- Indexes for table `voting_restrictions`
--
ALTER TABLE `voting_restrictions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_restriction` (`invention_id`,`judge_id`),
  ADD KEY `judge_id` (`judge_id`);

--
-- Indexes for table `voting_scores`
--
ALTER TABLE `voting_scores`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_vote` (`invention_id`,`judge_id`,`sub_criteria_id`),
  ADD KEY `judge_id` (`judge_id`),
  ADD KEY `sub_criteria_id` (`sub_criteria_id`),
  ADD KEY `scoring_level_id` (`scoring_level_id`),
  ADD KEY `idx_invention_judge` (`invention_id`,`judge_id`),
  ADD KEY `idx_competition_invention` (`competition_id`,`invention_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `competitions`
--
ALTER TABLE `competitions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `competition_admins`
--
ALTER TABLE `competition_admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `competition_levels`
--
ALTER TABLE `competition_levels`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `inventions`
--
ALTER TABLE `inventions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `invention_categories`
--
ALTER TABLE `invention_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `judge_assignments`
--
ALTER TABLE `judge_assignments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `result_approvals`
--
ALTER TABLE `result_approvals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `scoring_criteria_main`
--
ALTER TABLE `scoring_criteria_main`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `scoring_criteria_sub`
--
ALTER TABLE `scoring_criteria_sub`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `scoring_levels`
--
ALTER TABLE `scoring_levels`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `scoring_point_settings`
--
ALTER TABLE `scoring_point_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `system_settings`
--
ALTER TABLE `system_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `voting_restrictions`
--
ALTER TABLE `voting_restrictions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `voting_scores`
--
ALTER TABLE `voting_scores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD CONSTRAINT `audit_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `competitions`
--
ALTER TABLE `competitions`
  ADD CONSTRAINT `competitions_ibfk_1` FOREIGN KEY (`level_id`) REFERENCES `competition_levels` (`id`);

--
-- Constraints for table `competition_admins`
--
ALTER TABLE `competition_admins`
  ADD CONSTRAINT `competition_admins_ibfk_1` FOREIGN KEY (`competition_id`) REFERENCES `competitions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `competition_admins_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `competition_admins_ibfk_3` FOREIGN KEY (`category_id`) REFERENCES `invention_categories` (`id`);

--
-- Constraints for table `inventions`
--
ALTER TABLE `inventions`
  ADD CONSTRAINT `inventions_ibfk_1` FOREIGN KEY (`competition_id`) REFERENCES `competitions` (`id`),
  ADD CONSTRAINT `inventions_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `invention_categories` (`id`);

--
-- Constraints for table `judge_assignments`
--
ALTER TABLE `judge_assignments`
  ADD CONSTRAINT `judge_assignments_ibfk_1` FOREIGN KEY (`competition_id`) REFERENCES `competitions` (`id`),
  ADD CONSTRAINT `judge_assignments_ibfk_2` FOREIGN KEY (`judge_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `judge_assignments_ibfk_3` FOREIGN KEY (`category_id`) REFERENCES `invention_categories` (`id`);

--
-- Constraints for table `result_approvals`
--
ALTER TABLE `result_approvals`
  ADD CONSTRAINT `result_approvals_ibfk_1` FOREIGN KEY (`competition_id`) REFERENCES `competitions` (`id`),
  ADD CONSTRAINT `result_approvals_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `invention_categories` (`id`),
  ADD CONSTRAINT `result_approvals_ibfk_3` FOREIGN KEY (`chairman_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `scoring_criteria_main`
--
ALTER TABLE `scoring_criteria_main`
  ADD CONSTRAINT `scoring_criteria_main_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `invention_categories` (`id`);

--
-- Constraints for table `scoring_criteria_sub`
--
ALTER TABLE `scoring_criteria_sub`
  ADD CONSTRAINT `scoring_criteria_sub_ibfk_1` FOREIGN KEY (`main_criteria_id`) REFERENCES `scoring_criteria_main` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `scoring_levels`
--
ALTER TABLE `scoring_levels`
  ADD CONSTRAINT `scoring_levels_ibfk_1` FOREIGN KEY (`sub_criteria_id`) REFERENCES `scoring_criteria_sub` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `scoring_point_settings`
--
ALTER TABLE `scoring_point_settings`
  ADD CONSTRAINT `scoring_point_settings_ibfk_1` FOREIGN KEY (`competition_id`) REFERENCES `competitions` (`id`),
  ADD CONSTRAINT `scoring_point_settings_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `invention_categories` (`id`),
  ADD CONSTRAINT `scoring_point_settings_ibfk_3` FOREIGN KEY (`sub_criteria_id`) REFERENCES `scoring_criteria_sub` (`id`);

--
-- Constraints for table `system_settings`
--
ALTER TABLE `system_settings`
  ADD CONSTRAINT `system_settings_ibfk_1` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `voting_restrictions`
--
ALTER TABLE `voting_restrictions`
  ADD CONSTRAINT `voting_restrictions_ibfk_1` FOREIGN KEY (`invention_id`) REFERENCES `inventions` (`id`),
  ADD CONSTRAINT `voting_restrictions_ibfk_2` FOREIGN KEY (`judge_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `voting_scores`
--
ALTER TABLE `voting_scores`
  ADD CONSTRAINT `voting_scores_ibfk_1` FOREIGN KEY (`competition_id`) REFERENCES `competitions` (`id`),
  ADD CONSTRAINT `voting_scores_ibfk_2` FOREIGN KEY (`invention_id`) REFERENCES `inventions` (`id`),
  ADD CONSTRAINT `voting_scores_ibfk_3` FOREIGN KEY (`judge_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `voting_scores_ibfk_4` FOREIGN KEY (`sub_criteria_id`) REFERENCES `scoring_criteria_sub` (`id`),
  ADD CONSTRAINT `voting_scores_ibfk_5` FOREIGN KEY (`scoring_level_id`) REFERENCES `scoring_levels` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
