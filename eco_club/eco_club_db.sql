-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 08, 2026 at 12:51 PM
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
-- Database: `eco_club_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `event_date` date NOT NULL,
  `event_time` time DEFAULT NULL,
  `venue` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `capacity` int(11) NOT NULL DEFAULT 30,
  `status` enum('open','limited','closed') NOT NULL DEFAULT 'open',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`id`, `title`, `event_date`, `event_time`, `venue`, `description`, `capacity`, `status`, `created_at`) VALUES
(3, 'Remove trash ', '2026-11-13', NULL, 'Around mmu', NULL, 50, 'open', '2026-02-06 21:14:53'),
(4, 'Event talk ', '2026-02-11', NULL, 'DTC', NULL, 44, 'open', '2026-02-06 21:15:22'),
(5, 'Help remove trash at dtc', '2026-11-13', NULL, 'around mmu ', NULL, 100, 'open', '2026-02-07 16:04:15');

-- --------------------------------------------------------

--
-- Table structure for table `event_proposals`
--

CREATE TABLE `event_proposals` (
  `id` int(11) NOT NULL,
  `volunteer_id` int(11) NOT NULL,
  `organiser_id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `event_date` date NOT NULL,
  `event_time` time NOT NULL,
  `venue` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `capacity` int(11) NOT NULL DEFAULT 30,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `event_proposals`
--

INSERT INTO `event_proposals` (`id`, `volunteer_id`, `organiser_id`, `title`, `event_date`, `event_time`, `venue`, `description`, `capacity`, `status`, `created_at`) VALUES
(1, 11, 1, 'test', '2005-11-13', '13:11:00', 'Around mmu', 'test', 11, 'pending', '2026-02-06 19:00:33'),
(2, 26, 1, 'beach clenup', '0000-00-00', '15:33:00', 'beach', 'help clean beach please thansk', 50, 'pending', '2026-02-07 16:01:15');

-- --------------------------------------------------------

--
-- Table structure for table `participants`
--

CREATE TABLE `participants` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `status` varchar(30) NOT NULL DEFAULT 'Pending',
  `eco_points` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `recycling_logs`
--

CREATE TABLE `recycling_logs` (
  `id` int(11) NOT NULL,
  `volunteer_id` int(11) DEFAULT NULL,
  `student_id` int(11) DEFAULT NULL,
  `category` varchar(30) NOT NULL,
  `quantity` varchar(60) NOT NULL,
  `points` int(11) NOT NULL DEFAULT 0,
  `proof_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `recycling_logs`
--

INSERT INTO `recycling_logs` (`id`, `volunteer_id`, `student_id`, `category`, `quantity`, `points`, `proof_path`, `created_at`) VALUES
(1, 3, NULL, 'plastic', '1 box', 6, 'uploads/recycling_proofs/proof_v3_20260206_132651_f03bc6ec.png', '2026-02-06 12:26:51'),
(3, 5, NULL, 'plastic', '1 box', 6, 'uploads/recycling_proofs/proof_v5_20260206_190450_cfa514d9.png', '2026-02-06 18:04:50'),
(4, 5, NULL, 'plastic', '1 box', 6, 'uploads/recycling_proofs/proof_v5_20260206_190456_55ef02cb.png', '2026-02-06 18:04:56'),
(6, 11, NULL, 'plastic', '1 box', 6, 'uploads/recycling_proofs/proof_v11_20260206_200542_734b723b.png', '2026-02-06 19:05:42'),
(8, 17, NULL, 'plastic', '1 box', 6, 'uploads/recycling_proofs/proof_v17_20260206_210028_4153ee1e.png', '2026-02-06 20:00:28'),
(9, NULL, 18, 'plastic', '1 box', 6, 'uploads/recycling_proofs/proof_s18_20260206_211710_1f7c9d1a.png', '2026-02-06 20:17:10'),
(10, NULL, 18, 'plastic', '2 box', 12, 'uploads/recycling_proofs/proof_s18_20260206_222920_6da8bd8b.png', '2026-02-06 21:29:20'),
(11, 26, NULL, 'plastic', '1 box', 6, 'uploads/recycling_proofs/proof_v26_20260207_170028_63a270d9.png', '2026-02-07 16:00:28'),
(12, NULL, 27, 'plastic', '3 box', 18, 'uploads/recycling_proofs/proof_s27_20260207_170250_51763f9f.png', '2026-02-07 16:02:50'),
(13, NULL, 18, 'plastic', '2', 12, 'uploads/recycling_proofs/proof_s18_20260207_185006_1cc6283c.png', '2026-02-07 17:50:06');

-- --------------------------------------------------------

--
-- Table structure for table `registrations`
--

CREATE TABLE `registrations` (
  `id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `volunteer_id` int(11) DEFAULT NULL,
  `student_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `registrations`
--

INSERT INTO `registrations` (`id`, `event_id`, `volunteer_id`, `student_id`, `created_at`) VALUES
(25, 4, NULL, 2, '2026-02-06 21:15:29'),
(27, 3, NULL, 18, '2026-02-06 21:22:47'),
(35, 3, NULL, 27, '2026-02-07 16:02:27');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('student','volunteer','organiser','organizer','admin') NOT NULL,
  `points` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password_hash`, `role`, `points`, `created_at`) VALUES
(1, 'Admin', 'admin@eco.com', '1234', 'admin', 0, '2026-02-06 12:20:31'),
(2, 'Reze', 'reze@admin.com', '1234', 'admin', 0, '2026-02-06 12:22:53'),
(3, 'denji', 'denji@volun.com', '$2y$10$VRIiJsKPaxAdGP4lQhkdE.9klcz.Uu4KoqwxHyUzuoigo7PctTq7y', 'volunteer', 0, '2026-02-06 12:24:25'),
(5, 'vish', 'vish@volun.com', '$2y$10$b8GM/edn/8NouX0BuZrZxevjR8wE8tHWdCX5MTTBpLe8Pp6zhK9Se', 'volunteer', 0, '2026-02-06 12:38:50'),
(7, 'zim', 'zim@organ.com', '$2y$10$I7yeqPzSomKwddmGrfMTS.G0XbCqvWTroatkeOILdL9NIe4s8ySBK', 'organiser', 0, '2026-02-06 13:10:35'),
(8, 'mak', 'mak@organiser.com', '$2y$10$67TKNSsT/01kZ.je5yt8xOOjM2yWrsPs2xTIiGwmXPeeWmmQdHcha', 'organiser', 0, '2026-02-06 18:27:06'),
(9, 'Adam', 'adam@volunteer.com', '$2y$10$UnNcfOxuXTiLirNzLvhkduzo8g/JUd7GxnD91RmCpCgoUHEW0.p/.', 'volunteer', 0, '2026-02-06 18:46:04'),
(11, 'kicap', 'kicap@volun.com', '$2y$10$d9CUdNpQZv4qMFRKfZjGCumlL9QM/4uVjDP1gHeCDA62kUGBXze5y', 'volunteer', 0, '2026-02-06 19:00:04'),
(17, 'test2', 'test2@volun.com', '1234', 'volunteer', 0, '2026-02-06 19:59:44'),
(18, 'raze', 'raze@stud.com', '$2y$10$21qTDoucEtbGwVrFzdtcj.7zVzRuRaeYJtAC7qt0NCa1BGsv3E2tO', 'student', 0, '2026-02-06 20:16:18'),
(19, 'des', 'des@stu.com', '$2y$10$bNZUgImRHvMT79AeuJkguOnXWdRTuHFtxARfHHTNbdqq61R3xhjhW', 'student', 0, '2026-02-06 20:25:12'),
(20, 'obi', 'obi@event.com', '$2y$10$yDkK59YIgbmh.GOgjNlQzO9TjxFN6UawfemrLBjRpb9FRwLjrMuTi', 'organiser', 0, '2026-02-06 20:37:01'),
(21, 'ani', 'ani@event.com', '$2y$10$Da.f1DKrwBFc2Ka3aq6E0elt9D.lS83Lw9k7fybcWKudXsPt/VgZe', 'organiser', 0, '2026-02-06 21:13:22'),
(22, 'pim', 'pim@organiser.com', '$2y$10$3GvXEzowK00O1bEEMnpwEeVLBtwB1kM.8mreDIBd6dXChHKklyT46', 'organiser', 0, '2026-02-07 15:11:02'),
(23, 'ppp', 'ppp@student.com', '$2y$10$7GDIo6SuExd0NrqRfEUJRerGRjtRIK430hL1EUgrn5bpZO722146m', 'student', 0, '2026-02-07 15:48:05'),
(25, 'myname', 'myname@student.com', '$2y$10$e7r5YFmg2/hOXYZAbTnAP.w4bWr/LxlsWJCnJ5G5WBe5Id5ESOOZC', 'student', 0, '2026-02-07 15:57:39'),
(26, 'yes', 'yes@volunteer.com', '$2y$10$VNUK67.mbfkLSzPJihiWneYpyqbutQLwYnIcO7naG0NVI2eAc7JD2', 'volunteer', 0, '2026-02-07 15:58:54'),
(27, 'ulip', 'ulip@student.com', '$2y$10$HLOj6jpT2i5lopuG8dotg./.9UUxzlJaEn5GF2F2iU/7KwADL3.Ci', 'student', 0, '2026-02-07 16:01:48'),
(28, 'rose', 'rose@organise.com', '$2y$10$4uSkSLvXRttZf.64cy/Ht.D13CsTVFqllxryq28CwZoja9ihq/N0e', 'organiser', 0, '2026-02-07 16:03:19'),
(29, 'kongkek', 'kongkek@gmail.com', '$2y$10$yrQ8/Xvzy8lLfpgFOrmLqeGkAQRnUuRoI/fJJQ4GPJ1D1607IODMO', 'student', 0, '2026-04-03 07:11:14'),
(30, 'aaa', 'aaa@volun', '$2y$10$xFkYCGG4tCBh2KK90n.0K.GJ4k/ZqJFP.7jtvNFuzeUMK0uXoM/9K', 'volunteer', 0, '2026-04-03 07:59:54'),
(31, 'ccc', 'ccc@ccc', '$2y$10$qBVxmFxjX.uYKh0JgirQ4uF0Dudu6eacVLSrURJsVbyjw/e5vbgcy', 'organiser', 0, '2026-04-03 08:00:23');

-- --------------------------------------------------------

--
-- Table structure for table `volunteer_registrations`
--

CREATE TABLE `volunteer_registrations` (
  `id` int(11) NOT NULL,
  `volunteer_id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `event_proposals`
--
ALTER TABLE `event_proposals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_prop_organiser` (`organiser_id`),
  ADD KEY `idx_prop_volunteer` (`volunteer_id`);

--
-- Indexes for table `participants`
--
ALTER TABLE `participants`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `recycling_logs`
--
ALTER TABLE `recycling_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_recycle_volunteer` (`volunteer_id`);

--
-- Indexes for table `registrations`
--
ALTER TABLE `registrations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_registration` (`event_id`,`student_id`),
  ADD KEY `idx_reg_event` (`event_id`),
  ADD KEY `idx_reg_student` (`student_id`),
  ADD KEY `idx_reg_volunteer` (`volunteer_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_users_email` (`email`);

--
-- Indexes for table `volunteer_registrations`
--
ALTER TABLE `volunteer_registrations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_vol_event` (`volunteer_id`,`event_id`),
  ADD KEY `idx_vol` (`volunteer_id`),
  ADD KEY `idx_event` (`event_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `event_proposals`
--
ALTER TABLE `event_proposals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `participants`
--
ALTER TABLE `participants`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `recycling_logs`
--
ALTER TABLE `recycling_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `registrations`
--
ALTER TABLE `registrations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `volunteer_registrations`
--
ALTER TABLE `volunteer_registrations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `event_proposals`
--
ALTER TABLE `event_proposals`
  ADD CONSTRAINT `fk_prop_organiser` FOREIGN KEY (`organiser_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_prop_volunteer` FOREIGN KEY (`volunteer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `recycling_logs`
--
ALTER TABLE `recycling_logs`
  ADD CONSTRAINT `fk_recycle_volunteer` FOREIGN KEY (`volunteer_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `registrations`
--
ALTER TABLE `registrations`
  ADD CONSTRAINT `fk_reg_event` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_reg_volunteer` FOREIGN KEY (`volunteer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `volunteer_registrations`
--
ALTER TABLE `volunteer_registrations`
  ADD CONSTRAINT `fk_vr_event` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_vr_volunteer` FOREIGN KEY (`volunteer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
