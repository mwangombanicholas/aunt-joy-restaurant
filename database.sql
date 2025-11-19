-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 19, 2025 at 08:18 PM
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
-- Database: `aunt_joy_restaurant`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `description`) VALUES
(1, 'Appetizers', 'Start your meal with our delicious starters'),
(2, 'Main Courses', 'Hearty and satisfying main dishes'),
(3, 'Desserts', 'Sweet treats to end your meal'),
(4, 'Drinks', 'Refreshing beverages');

-- --------------------------------------------------------

--
-- Table structure for table `meals`
--

CREATE TABLE `meals` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `image_url` varchar(500) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `is_available` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `meals`
--

INSERT INTO `meals` (`id`, `name`, `description`, `price`, `image_url`, `category_id`, `is_available`, `created_at`) VALUES
(1, 'Chicken Pizza', 'Delicious pizza with chicken toppings', 8500.00, 'chicken.jpg', 2, 1, '2025-11-13 13:25:08'),
(2, 'Beef Burger', 'Juicy beef burger with fresh vegetables', 4500.00, 'burger.jpg', 2, 1, '2025-11-13 13:25:08'),
(3, 'French Fries', 'Crispy golden fries', 2000.00, 'fries.jpg', 1, 1, '2025-11-13 13:25:08'),
(4, 'Chocolate Cake', 'Rich chocolate cake slice', 3000.00, 'cakes.jpg', 3, 1, '2025-11-13 13:25:08'),
(5, 'Soda', 'Cold refreshing soda', 1500.00, 'soda.jpg', 4, 1, '2025-11-13 13:25:08');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `status` enum('pending','preparing','out_for_delivery','delivered') DEFAULT 'pending',
  `delivery_address` text NOT NULL,
  `contact_number` varchar(20) NOT NULL,
  `customer_notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `total_amount`, `status`, `delivery_address`, `contact_number`, `customer_notes`, `created_at`) VALUES
(1, 2, 14500.00, 'delivered', 'Mzuzu City Test Address 1', '0881111111', NULL, '2025-11-01 08:00:00'),
(2, 2, 8500.00, 'delivered', 'Mzuzu City Test Address 2', '0881111111', NULL, '2025-11-05 12:30:00'),
(3, 2, 12000.00, 'delivered', 'Mzuzu City Test Address 3', '0881111111', NULL, '2025-11-10 16:15:00');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `meal_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `unit_price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `meal_id`, `quantity`, `unit_price`) VALUES
(1, 1, 1, 1, 8500.00),
(2, 1, 3, 2, 2000.00),
(3, 1, 5, 2, 1500.00),
(4, 2, 1, 1, 8500.00),
(5, 3, 2, 2, 4500.00),
(6, 3, 4, 1, 3000.00);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('customer','admin','sales','manager') NOT NULL DEFAULT 'customer',
  `full_name` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `role`, `full_name`, `phone`, `address`, `created_at`) VALUES
(1, 'admin', 'admin@auntjoy.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'System Administrator', NULL, NULL, '2025-11-13 13:25:08'),
(2, 'customer', 'customer@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'customer', 'John Customer', '0881234567', 'Mzuzu City', '2025-11-13 13:25:08'),
(3, 'sales', 'sales@auntjoy.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'sales', 'Sales Person', NULL, NULL, '2025-11-13 13:48:53'),
(4, 'manager', 'manager@auntjoy.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'manager', 'Store Manager', NULL, NULL, '2025-11-13 13:48:53'),
(5, 'nicholas123', 'Mwangombanicholas@gmail.com', '$2y$10$d1qyfK3PJQkS3lV2.FlzDeNnlRukAql5w6TpQqQ.JEwNVH5aG1aLW', 'customer', 'Nicholas Mwangomba', '0993437033', 'mzuzu city', '2025-11-13 14:37:34'),
(6, 'evelyn', 'everynngomba@gmail.com', '$2y$10$m/B9wxuEks2KjhYQ8EXFxuMH.NDQ4sO3RCKA9xo9QzYkzMfuOJe7G', 'customer', 'eveln mwangomba', '', 'mzuzu city', '2025-11-13 16:38:06'),
(7, 'Kidafricah', 'bkalindeh@gmail.com', '$2y$10$/AJDO8OL/8eb6vcYtuvfI.amajGtiyR1D2JLtsMWgVBTGhdxDqKnK', 'customer', 'blessings Kalindeh', '0993437033', 'Mzuzu  city', '2025-11-18 06:57:55'),
(8, 'Bright', 'bright@gmail.com', '$2y$10$PHnI2ku82UUuaVRnD6/roOo5uA2PpKUS4.uD84Ke9v5oupqfNTXAK', 'customer', 'Bright Msiska', '0992512940', 'Mzuzu City\r\n', '2025-11-18 08:44:32');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `meals`
--
ALTER TABLE `meals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `meal_id` (`meal_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `meals`
--
ALTER TABLE `meals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `meals`
--
ALTER TABLE `meals`
  ADD CONSTRAINT `meals_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`meal_id`) REFERENCES `meals` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
