-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 04, 2025 at 06:05 AM
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
-- Database: `php_ezaviralindo`
--

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `booking_code` varchar(20) NOT NULL,
  `client_name` varchar(100) NOT NULL,
  `client_phone` varchar(20) NOT NULL,
  `event_type` varchar(50) DEFAULT NULL,
  `event_name` varchar(255) DEFAULT NULL,
  `client_email` varchar(100) DEFAULT NULL,
  `event_date` date NOT NULL,
  `event_venue` text NOT NULL,
  `tm_date` date DEFAULT NULL,
  `tm_time` time DEFAULT NULL,
  `tm_location` varchar(255) DEFAULT NULL,
  `package_id` int(11) DEFAULT NULL,
  `total_price` decimal(15,2) DEFAULT 0.00,
  `payment_option` varchar(50) DEFAULT NULL,
  `down_payment_amount` decimal(15,2) DEFAULT 0.00,
  `status` enum('Pending','Confirmed','Completed','Cancelled') DEFAULT 'Pending',
  `payment_status` enum('Unpaid','Down Payment','Paid') DEFAULT 'Unpaid',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `booking_code`, `client_name`, `client_phone`, `event_type`, `event_name`, `client_email`, `event_date`, `event_venue`, `tm_date`, `tm_time`, `tm_location`, `package_id`, `total_price`, `payment_option`, `down_payment_amount`, `status`, `payment_status`, `notes`, `created_at`) VALUES
(2, 'BOOK-251203442', 'testing', '0123456789', 'Wedding', 'romeo', 'testing@testing.id', '2025-12-05', 'jalan', NULL, NULL, NULL, 5, 1000000.00, 'Waiting Payment', 100000.00, 'Confirmed', 'Paid', 'fsdfds', '2025-12-03 07:44:22'),
(3, 'BOOK-251203571', 'sadasd', '082323196661', 'Wedding', 'dfsf', 'testing@testing.id', '2025-12-06', 'dsfsdf', NULL, NULL, NULL, 4, 500000.00, 'Full Payment', 500000.00, 'Completed', 'Paid', 'dfsfs', '2025-12-03 09:19:09'),
(4, 'BOOK-251204150', 'test', '082323196661', 'Wedding', 'romeo', 'ad@ad.ad', '2025-12-04', 'sdfdfsdf', NULL, NULL, NULL, 4, 500000.00, 'Down Payment', 50000.00, '', 'Unpaid', 'sdfsdf', '2025-12-04 03:17:11'),
(5, 'BOOK-251204286', 'tst', '082323196661', 'Wedding', 'romeo', 'a@a.a', '2025-12-10', 'asd', NULL, NULL, NULL, 6, 0.00, 'Full Payment', 0.00, '', 'Down Payment', 'asd', '2025-12-04 03:47:08'),
(6, 'BOOK-251204209', 'hhd', '082323196661', 'Wedding', 'romeo', 'testing@testing.id', '2025-12-11', 'fghdh', NULL, NULL, NULL, 5, 1000000.00, 'Down Payment', 100000.00, '', 'Down Payment', 'fghh', '2025-12-04 04:35:08');

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `packages`
--

CREATE TABLE `packages` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `type` varchar(50) NOT NULL,
  `price` decimal(15,2) DEFAULT 0.00,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `packages`
--

INSERT INTO `packages` (`id`, `name`, `type`, `price`, `description`, `created_at`, `is_active`) VALUES
(4, 'Silver', 'Wedding', 500000.00, 'MC Akad\r\n1x Tech Meet', '2025-12-03 07:40:18', 1),
(5, 'Gold', 'Wedding', 1000000.00, 'MC Akad\r\nMC Resepsi\r\n1x tech Meet', '2025-12-03 07:40:41', 1),
(6, 'Platinum', 'Wedding', 0.00, 'Sesuaikan Sendiri', '2025-12-03 07:41:09', 1),
(7, 'Silver', 'Event', 1000000.00, 'tesdt', '2025-12-03 07:45:32', 1),
(8, 'gold', 'Event', 0.00, 'fghfgh', '2025-12-03 07:45:46', 1),
(9, 'gold', 'Corporate', 0.00, 'gddgdf', '2025-12-03 07:46:05', 1);

-- --------------------------------------------------------

--
-- Table structure for table `portfolios`
--

CREATE TABLE `portfolios` (
  `id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `category` varchar(50) DEFAULT 'General',
  `type` enum('photo','video') NOT NULL DEFAULT 'photo',
  `image` varchar(255) NOT NULL,
  `video_link` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `portfolios`
--

INSERT INTO `portfolios` (`id`, `title`, `category`, `type`, `image`, `video_link`, `description`, `created_at`) VALUES
(2, 'nanamoy', 'Wedding', 'photo', 'porto_1764748719.jpg', NULL, 'asdas', '2025-12-03 07:58:39'),
(3, 'test', 'Wedding', 'photo', 'porto_1764748731.png', NULL, 'dfsa', '2025-12-03 07:58:51'),
(4, 'teadf', 'Wedding', 'video', 'https://img.youtube.com/vi/Hc5AIQ6-qWY/hqdefault.jpg', 'https://www.youtube.com/embed/Hc5AIQ6-qWY', 'ffsd', '2025-12-03 07:59:24'),
(5, 'gfgdf', 'Event', 'video', 'https://img.youtube.com/vi/lHFOzj1_suE/hqdefault.jpg', 'https://www.youtube.com/embed/lHFOzj1_suE', 'ggdfg', '2025-12-03 07:59:49');

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `site_name` varchar(100) DEFAULT 'Eza Viralindo MC',
  `hero_title` varchar(255) DEFAULT 'Professional Master of Ceremony',
  `hero_description` text DEFAULT NULL,
  `meta_description` text DEFAULT NULL,
  `owner_name` varchar(100) DEFAULT 'Eza Viralindo',
  `owner_description` text DEFAULT NULL,
  `owner_photo` varchar(255) DEFAULT 'profile.jpg',
  `contact_wa` varchar(20) DEFAULT NULL,
  `contact_ig` varchar(50) DEFAULT NULL,
  `contact_email` varchar(100) DEFAULT NULL,
  `contact_youtube` varchar(100) DEFAULT NULL,
  `contact_fb` varchar(100) DEFAULT NULL,
  `footer_text` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `site_name`, `hero_title`, `hero_description`, `meta_description`, `owner_name`, `owner_description`, `owner_photo`, `contact_wa`, `contact_ig`, `contact_email`, `contact_youtube`, `contact_fb`, `footer_text`) VALUES
(1, 'Eza Viralindo', 'Jadikan Momen Spesial Anda Tak Terlupakan', 'MC OK', 'MC Bogor Terbaik', 'Eza Viralindo', 'Ini hanya contoh saja', 'owner_1764735670.jpg', '62123123123', 'gfdgd', 'sdgdgfs2@df.g', '', '', 'Copyright © 2025 Eza Viralindo.');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `avatar` varchar(255) DEFAULT 'default.jpg',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `avatar`, `created_at`) VALUES
(1, 'MasMoy', 'admin@admin.com', '$2y$10$cjFG7D9.lyWY0kGEmctSOOzm//Kx5QluXtwoxRUor0CIyfahRUAJW', 'default.jpg', '2025-12-03 02:23:39');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `packages`
--
ALTER TABLE `packages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `portfolios`
--
ALTER TABLE `portfolios`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
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
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `packages`
--
ALTER TABLE `packages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `portfolios`
--
ALTER TABLE `portfolios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
