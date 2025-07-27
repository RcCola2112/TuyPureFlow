-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jul 26, 2025 at 02:11 AM
-- Server version: 10.11.10-MariaDB-log
-- PHP Version: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `u549992181_DB_TuyPureFlow`
--

-- --------------------------------------------------------

--
-- Table structure for table `address`
--

CREATE TABLE `address` (
  `address_id` int(11) NOT NULL,
  `consumer_id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `contact_number` varchar(20) DEFAULT NULL,
  `street` varchar(100) DEFAULT NULL,
  `barangay` varchar(100) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `region` varchar(100) DEFAULT NULL,
  `zip_code` varchar(10) DEFAULT NULL,
  `latitude` decimal(10,7) DEFAULT NULL,
  `longitude` decimal(10,7) DEFAULT NULL,
  `is_default` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `admin_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`admin_id`, `username`, `password`, `email`, `created_at`) VALUES
(1, 'Arciee', '$2y$10$Q2t3mkro4PfO6/.TSzJNYuHDr5fwJ5icbUKLqTImeXa95X4UKXx4S', 'denvermartinez2112@gmail.com', '2025-07-23 08:47:37');

-- --------------------------------------------------------

--
-- Table structure for table `analytics_report`
--

CREATE TABLE `analytics_report` (
  `report_id` int(11) NOT NULL,
  `shop_id` int(11) NOT NULL,
  `report_type` varchar(100) NOT NULL,
  `value` decimal(10,2) NOT NULL,
  `report_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `cart_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `container_id` int(11) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `type` varchar(50) NOT NULL,
  `qty` int(11) NOT NULL DEFAULT 1,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `consumer`
--

CREATE TABLE `consumer` (
  `consumer_id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `contact_number` varchar(15) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `suspended` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `consumer_feedback`
--

CREATE TABLE `consumer_feedback` (
  `feedback_id` int(11) NOT NULL,
  `consumer_id` int(11) NOT NULL,
  `shop_id` int(11) NOT NULL,
  `rating` int(11) DEFAULT NULL CHECK (`rating` between 1 and 5),
  `comments` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `container`
--

CREATE TABLE `container` (
  `container_id` int(11) NOT NULL,
  `shop_id` int(11) NOT NULL,
  `type` varchar(50) NOT NULL,
  `stock_quantity` int(11) NOT NULL,
  `damaged_container` int(255) NOT NULL,
  `missing_container` int(255) NOT NULL,
  `price_new` decimal(10,2) DEFAULT NULL,
  `price_refill` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `delivery_record`
--

CREATE TABLE `delivery_record` (
  `delivery_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `employee_id` int(11) DEFAULT NULL,
  `delivery_status` varchar(50) DEFAULT 'pending',
  `delivery_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `distributor`
--

CREATE TABLE `distributor` (
  `distributor_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `status` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `distributor`
--

INSERT INTO `distributor` (`distributor_id`, `name`, `phone`, `email`, `password`, `created_at`, `status`) VALUES
(11, 'Arciee Roxas', '09362695356', 'denvermartinez2112@gmail.com', '$2y$10$3gMeUH5r3w9CZD0CXyOoIex4SsOdEQchnBcAD2anFKPpkewvTa17q', '2025-07-24 09:19:55', 'Rejected'),
(12, 'Vince Albert D. Alcaraz', '09454566364', 'DaVinci@gmail.com', '$2y$10$s3e.u.kTFxQF9irRO0K0suWX/.iQZg17IWxnBb4PM/pJrT0452DJO', '2025-07-26 02:09:54', 'Approved');

-- --------------------------------------------------------

--
-- Table structure for table `employee`
--

CREATE TABLE `employee` (
  `employee_id` int(11) NOT NULL,
  `distributor_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `contact_number` varchar(15) DEFAULT NULL,
  `position` varchar(50) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `date` datetime DEFAULT current_timestamp(),
  `resolved` tinyint(1) DEFAULT 0,
  `reply` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `message_id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `sender_type` enum('consumer','distributor','admin') NOT NULL,
  `receiver_type` enum('consumer','distributor','admin') NOT NULL,
  `content` text NOT NULL,
  `sent_at` datetime DEFAULT current_timestamp(),
  `is_read` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notification`
--

CREATE TABLE `notification` (
  `notification_id` int(11) NOT NULL,
  `consumer_id` int(11) DEFAULT NULL,
  `distributor_id` int(11) DEFAULT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `consumer_id` int(11) NOT NULL,
  `shop_id` int(11) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `status` varchar(50) DEFAULT 'pending',
  `order_date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `order_item_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `container_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `shop`
--

CREATE TABLE `shop` (
  `shop_id` int(11) NOT NULL,
  `distributor_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `location` varchar(255) NOT NULL,
  `contact_number` varchar(15) NOT NULL,
  `latitude` decimal(10,7) DEFAULT NULL,
  `longitude` decimal(10,7) DEFAULT NULL,
  `average_rating` decimal(3,2) DEFAULT NULL,
  `open_time` time DEFAULT NULL,
  `close_time` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `shop`
--

INSERT INTO `shop` (`shop_id`, `distributor_id`, `name`, `location`, `contact_number`, `latitude`, `longitude`, `average_rating`, `open_time`, `close_time`) VALUES
(11, 11, 'Arciee\'s WaterHub', '00178, Kabituls, Tuy, Region IV-A', '09362695356', 14.0231254, 120.7259059, NULL, '05:00:00', '17:00:00'),
(12, 12, 'D\'s Water Station', 'Rizal Street, Tuy, Region IV-A', '09454566364', 14.0169230, 120.7289851, NULL, '08:00:00', '17:00:00');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `address`
--
ALTER TABLE `address`
  ADD PRIMARY KEY (`address_id`),
  ADD KEY `consumer_id` (`consumer_id`);

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`admin_id`);

--
-- Indexes for table `analytics_report`
--
ALTER TABLE `analytics_report`
  ADD PRIMARY KEY (`report_id`),
  ADD KEY `shop_id` (`shop_id`);

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`cart_id`);

--
-- Indexes for table `consumer`
--
ALTER TABLE `consumer`
  ADD PRIMARY KEY (`consumer_id`);

--
-- Indexes for table `consumer_feedback`
--
ALTER TABLE `consumer_feedback`
  ADD PRIMARY KEY (`feedback_id`),
  ADD KEY `consumer_id` (`consumer_id`),
  ADD KEY `shop_id` (`shop_id`);

--
-- Indexes for table `container`
--
ALTER TABLE `container`
  ADD PRIMARY KEY (`container_id`),
  ADD KEY `shop_id` (`shop_id`);

--
-- Indexes for table `delivery_record`
--
ALTER TABLE `delivery_record`
  ADD PRIMARY KEY (`delivery_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `employee_id` (`employee_id`);

--
-- Indexes for table `distributor`
--
ALTER TABLE `distributor`
  ADD PRIMARY KEY (`distributor_id`);

--
-- Indexes for table `employee`
--
ALTER TABLE `employee`
  ADD PRIMARY KEY (`employee_id`),
  ADD KEY `distributor_id` (`distributor_id`);

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`message_id`);

--
-- Indexes for table `notification`
--
ALTER TABLE `notification`
  ADD PRIMARY KEY (`notification_id`),
  ADD KEY `consumer_id` (`consumer_id`),
  ADD KEY `distributor_id` (`distributor_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `consumer_id` (`consumer_id`),
  ADD KEY `shop_id` (`shop_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`order_item_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `container_id` (`container_id`);

--
-- Indexes for table `shop`
--
ALTER TABLE `shop`
  ADD PRIMARY KEY (`shop_id`),
  ADD KEY `distributor_id` (`distributor_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `address`
--
ALTER TABLE `address`
  MODIFY `address_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `analytics_report`
--
ALTER TABLE `analytics_report`
  MODIFY `report_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `cart_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `consumer`
--
ALTER TABLE `consumer`
  MODIFY `consumer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `consumer_feedback`
--
ALTER TABLE `consumer_feedback`
  MODIFY `feedback_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `container`
--
ALTER TABLE `container`
  MODIFY `container_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `delivery_record`
--
ALTER TABLE `delivery_record`
  MODIFY `delivery_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `distributor`
--
ALTER TABLE `distributor`
  MODIFY `distributor_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `employee`
--
ALTER TABLE `employee`
  MODIFY `employee_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `message_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notification`
--
ALTER TABLE `notification`
  MODIFY `notification_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `order_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `shop`
--
ALTER TABLE `shop`
  MODIFY `shop_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `address`
--
ALTER TABLE `address`
  ADD CONSTRAINT `address_ibfk_1` FOREIGN KEY (`consumer_id`) REFERENCES `consumer` (`consumer_id`) ON DELETE CASCADE;

--
-- Constraints for table `analytics_report`
--
ALTER TABLE `analytics_report`
  ADD CONSTRAINT `analytics_report_ibfk_1` FOREIGN KEY (`shop_id`) REFERENCES `shop` (`shop_id`) ON DELETE CASCADE;

--
-- Constraints for table `consumer_feedback`
--
ALTER TABLE `consumer_feedback`
  ADD CONSTRAINT `consumer_feedback_ibfk_1` FOREIGN KEY (`consumer_id`) REFERENCES `consumer` (`consumer_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `consumer_feedback_ibfk_2` FOREIGN KEY (`shop_id`) REFERENCES `shop` (`shop_id`) ON DELETE CASCADE;

--
-- Constraints for table `container`
--
ALTER TABLE `container`
  ADD CONSTRAINT `container_ibfk_1` FOREIGN KEY (`shop_id`) REFERENCES `shop` (`shop_id`) ON DELETE CASCADE;

--
-- Constraints for table `delivery_record`
--
ALTER TABLE `delivery_record`
  ADD CONSTRAINT `delivery_record_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `delivery_record_ibfk_2` FOREIGN KEY (`employee_id`) REFERENCES `employee` (`employee_id`) ON DELETE SET NULL;

--
-- Constraints for table `employee`
--
ALTER TABLE `employee`
  ADD CONSTRAINT `employee_ibfk_1` FOREIGN KEY (`distributor_id`) REFERENCES `distributor` (`distributor_id`) ON DELETE CASCADE;

--
-- Constraints for table `notification`
--
ALTER TABLE `notification`
  ADD CONSTRAINT `notification_ibfk_1` FOREIGN KEY (`consumer_id`) REFERENCES `consumer` (`consumer_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `notification_ibfk_2` FOREIGN KEY (`distributor_id`) REFERENCES `distributor` (`distributor_id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`consumer_id`) REFERENCES `consumer` (`consumer_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`shop_id`) REFERENCES `shop` (`shop_id`) ON DELETE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`container_id`) REFERENCES `container` (`container_id`) ON DELETE CASCADE;

--
-- Constraints for table `shop`
--
ALTER TABLE `shop`
  ADD CONSTRAINT `shop_ibfk_1` FOREIGN KEY (`distributor_id`) REFERENCES `distributor` (`distributor_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
