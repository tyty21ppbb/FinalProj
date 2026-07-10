-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 09, 2026 at 11:48 AM
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
-- Database: `chairhive`
--

-- --------------------------------------------------------

--
-- Table structure for table `audit_log`
--

CREATE TABLE `audit_log` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `actor_name` varchar(150) NOT NULL,
  `actor_role` varchar(20) NOT NULL,
  `action` varchar(100) NOT NULL,
  `description` varchar(255) NOT NULL,
  `date_created` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `audit_log`
--

INSERT INTO `audit_log` (`id`, `user_id`, `actor_name`, `actor_role`, `action`, `description`, `date_created`) VALUES
(1, 1, 'System Administrator', 'admin', 'LOGIN', 'System Administrator logged in.', '2026-07-09 05:44:41');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `shipping_address` varchar(255) NOT NULL,
  `contact_number` varchar(50) NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `payment_reference` varchar(100) DEFAULT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `status` varchar(30) NOT NULL DEFAULT 'Paid (Simulated)',
  `date_created` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `product_name` varchar(150) NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `quantity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `category` varchar(100) NOT NULL,
  `description` varchar(500) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `stock_qty` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `date_created` timestamp NOT NULL DEFAULT current_timestamp(),
  `image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `category`, `description`, `price`, `stock_qty`, `is_active`, `date_created`, `image`) VALUES
(1, 'A34 Executive Chair', 'Executive Chairs', 'Premium executive office chair.', 16500.00, 15, 1, '2026-07-09 07:33:25', 'images/products/a34-executive-chair.jpg'),
(2, 'YS901A Executive Chair', 'Executive Chairs', 'Comfortable executive office chair.', 7850.00, 20, 1, '2026-07-09 07:33:25', 'images/products/ys901a-executive-chair.jpg'),
(3, 'Clarion High-Back Executive Office Chair', 'Executive Chairs', 'High-back executive chair for professional offices.', 7500.00, 18, 1, '2026-07-09 07:33:25', 'images/products/clarion-high-back-executive-office-chair.jpg'),
(4, 'B75 Executive Chair', 'Executive Chairs', 'Executive chair with ergonomic support.', 5750.00, 25, 1, '2026-07-09 07:33:25', 'images/products/b75-executive-chair.jpg'),
(5, 'CX300H Executive Chair', 'Executive Chairs', 'Affordable executive office chair.', 4199.00, 30, 1, '2026-07-09 07:33:25', 'images/products/cx300h-executive-chair.jpg'),
(6, 'Avius 746 Executive Chair', 'Executive Chairs', 'Luxury executive office chair.', 19000.00, 10, 1, '2026-07-09 07:33:25', 'images/products/avius-746-executive-chair.jpg'),
(7, 'Virello Executive Chair – Reclining Chair w/ Adjustable Headrest', 'Executive Chairs', 'Reclining executive chair with adjustable headrest.', 18950.00, 12, 1, '2026-07-09 07:33:25', 'images/products/virello-executive-chair.jpg'),
(8, 'Apollo Reclining Executive Chair', 'Executive Chairs', 'Executive reclining office chair.', 17000.00, 10, 1, '2026-07-09 07:33:25', 'images/products/apollo-reclining-executive-chair.jpg'),
(9, 'Orion Reclining Executive Office Chair with Footrest', 'Executive Chairs', 'Executive chair with reclining backrest and footrest.', 16950.00, 8, 1, '2026-07-09 07:33:25', 'images/products/orion-reclining-executive-chair.jpg'),
(10, 'Titan Reclining Executive Chair', 'Executive Chairs', 'Heavy-duty reclining executive chair.', 13950.00, 12, 1, '2026-07-09 07:33:25', 'images/products/titan-reclining-executive-chair.jpg'),
(11, 'Cradle Comfort Ergonomic Office Chair', 'Ergonomic Chairs', 'Premium ergonomic office chair.', 11990.00, 20, 1, '2026-07-09 07:33:25', 'images/products/cradle-comfort-ergonomic-office-chair.jpg'),
(12, 'Cradle Comfort Lite Ergonomic Office Chair', 'Ergonomic Chairs', 'Lightweight ergonomic office chair.', 9490.00, 18, 1, '2026-07-09 07:33:25', 'images/products/cradle-comfort-lite.jpg'),
(13, 'Stance Aero Form Ergonomic Office Chair', 'Ergonomic Chairs', 'Breathable ergonomic office chair.', 6990.00, 22, 1, '2026-07-09 07:33:25', 'images/products/stance-aero-form.jpg'),
(14, 'Cradle Flexi Prestige Edition', 'Ergonomic Chairs', 'Prestige ergonomic office chair.', 10990.00, 15, 1, '2026-07-09 07:33:25', 'images/products/cradle-flexi-prestige-edition.jpg'),
(15, 'Cradle Flexi Ergonomic Office Chair', 'Ergonomic Chairs', 'Adjustable ergonomic office chair.', 7990.00, 20, 1, '2026-07-09 07:33:25', 'images/products/cradle-flexi.jpg'),
(16, 'Cradle Pro Ergonomic Office Chair', 'Ergonomic Chairs', 'Professional ergonomic office chair.', 17490.00, 10, 1, '2026-07-09 07:33:25', 'images/products/cradle-pro.jpg'),
(17, 'Stance Halo Ergonomic Office Chair', 'Ergonomic Chairs', 'High-end ergonomic office chair.', 22990.00, 8, 1, '2026-07-09 07:33:25', 'images/products/stance-halo.jpg'),
(18, 'Stance BetterWork Pro Ergonomic Office Chair', 'Ergonomic Chairs', 'Office chair for long working hours.', 8290.00, 18, 1, '2026-07-09 07:33:25', 'images/products/stance-betterwork-pro.jpg'),
(19, 'Stance Stylite Ergonomic Office Chair', 'Ergonomic Chairs', 'Stylish ergonomic office chair.', 7490.00, 20, 1, '2026-07-09 07:33:25', 'images/products/stance-stylite.jpg'),
(20, 'Novo Thorne Ergonomic Office Chair', 'Ergonomic Chairs', 'Modern ergonomic office chair.', 11990.00, 14, 1, '2026-07-09 07:33:25', 'images/products/novo-thorne.jpg'),
(21, 'TTRacing Maxx Pro Gaming Chair', 'Gaming Chairs', 'Professional gaming chair.', 23999.00, 12, 1, '2026-07-09 07:33:25', 'images/products/ttracing-maxx-pro.jpg'),
(22, 'TTRacing Maxx Pro Air Threads Fabric Gaming Chair', 'Gaming Chairs', 'Fabric gaming chair with breathable material.', 24499.00, 10, 1, '2026-07-09 07:33:25', 'images/products/ttracing-maxx-pro-air-threads.jpg'),
(23, 'TTRacing Maxx Gaming Chair', 'Gaming Chairs', 'Comfortable racing-style gaming chair.', 16599.00, 15, 1, '2026-07-09 07:33:25', 'images/products/ttracing-maxx.jpg'),
(24, 'TTRacing Maxx Air Threads Fabric Gaming Chair', 'Gaming Chairs', 'Fabric gaming chair with ergonomic support.', 17099.00, 12, 1, '2026-07-09 07:33:25', 'images/products/ttracing-maxx-air-threads.jpg'),
(25, 'DXRACER DRIFTING Series', 'Gaming Chairs', 'DXRacer Drifting Series gaming chair.', 18999.00, 10, 1, '2026-07-09 07:33:25', 'images/products/dxracer-drifting-series.jpg'),
(26, 'DXRACER CRAFT Series', 'Gaming Chairs', 'DXRacer Craft Series gaming chair.', 24999.00, 8, 1, '2026-07-09 07:33:25', 'images/products/dxracer-craft-series.jpg'),
(27, 'DXRACER MARTIAN Series', 'Gaming Chairs', 'Premium DXRacer Martian Series gaming chair.', 31999.00, 5, 1, '2026-07-09 07:33:25', 'images/products/dxracer-martian-series.jpg'),
(28, 'DXRACER TANK Series', 'Gaming Chairs', 'Heavy-duty DXRacer Tank Series gaming chair.', 29999.00, 6, 1, '2026-07-09 07:33:25', 'images/products/dxracer-tank-series.jpg'),
(29, 'DXRACER BLADE Series', 'Gaming Chairs', 'DXRacer Blade Series gaming chair.', 21999.00, 8, 1, '2026-07-09 07:33:25', 'images/products/dxracer-blade-series.jpg'),
(30, 'DXRACER FORMULA Series', 'Gaming Chairs', 'Classic DXRacer Formula Series gaming chair.', 16999.00, 12, 1, '2026-07-09 07:33:25', 'images/products/dxracer-formula-series.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `full_name` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `contact_number` varchar(50) DEFAULT NULL,
  `role` enum('buyer','admin') NOT NULL DEFAULT 'buyer',
  `is_verified` tinyint(1) NOT NULL DEFAULT 0,
  `verify_token` varchar(64) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `date_created` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `full_name`, `email`, `password_hash`, `address`, `contact_number`, `role`, `is_verified`, `verify_token`, `is_active`, `date_created`) VALUES
(1, 'System Administrator', 'admin@chairhive.test', '$2b$04$WXt7lZsc8hCdpftGnTiz3uahe1xLgn3JwYVKMr9G/JIhOatP.iLRa', 'ChairHive Head Office, Quezon City', '09170000000', 'admin', 1, NULL, 1, '2026-07-06 12:10:35');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `audit_log`
--
ALTER TABLE `audit_log`
  ADD PRIMARY KEY (`id`);

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
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

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
-- AUTO_INCREMENT for table `audit_log`
--
ALTER TABLE `audit_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
