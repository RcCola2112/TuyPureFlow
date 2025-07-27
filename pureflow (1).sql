<<<<<<< HEAD
-- Tuy PureFlow Cleaned SQL Dump (ready for Hostinger import)

-- Drop and Create `admin`
DROP TABLE IF EXISTS `admin`;
CREATE TABLE `admin` (
  `admin_id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`admin_id`)
);

-- Drop and Create `consumer`
DROP TABLE IF EXISTS `consumer`;
CREATE TABLE `consumer` (
  `consumer_id` int(11) NOT NULL AUTO_INCREMENT,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `contact_number` varchar(15) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`consumer_id`)
);

-- Drop and Create `distributor`
DROP TABLE IF EXISTS `distributor`;
CREATE TABLE `distributor` (
  `distributor_id` int(11) NOT NULL AUTO_INCREMENT,
  `business_name` varchar(100) NOT NULL,
  `owner_name` varchar(100) NOT NULL,
  `contact_number` varchar(15) NOT NULL,
  `email` varchar(100) NOT NULL,
  `open_time` time DEFAULT NULL,
  `close_time` time DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`distributor_id`)
);

-- Drop and Create `shop`
DROP TABLE IF EXISTS `shop`;
CREATE TABLE `shop` (
  `shop_id` int(11) NOT NULL AUTO_INCREMENT,
  `distributor_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `location` varchar(255) NOT NULL,
  `contact_number` varchar(15) NOT NULL,
  `latitude` decimal(10,7) DEFAULT NULL,
  `longitude` decimal(10,7) DEFAULT NULL,
  `average_rating` decimal(3,2) DEFAULT 0.00,
  `open_time` time DEFAULT '08:00:00',
  `close_time` time DEFAULT '17:00:00',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`shop_id`),
  FOREIGN KEY (`distributor_id`) REFERENCES `distributor`(`distributor_id`) ON DELETE CASCADE
);

-- Drop and Create `container`
DROP TABLE IF EXISTS `container`;
CREATE TABLE `container` (
  `container_id` int(11) NOT NULL AUTO_INCREMENT,
  `shop_id` int(11) NOT NULL,
  `type` varchar(50) NOT NULL,
  `price_new` decimal(10,2) NOT NULL,
  `price_refill` decimal(10,2) NOT NULL,
  `stock_quantity` int(11) NOT NULL,
  `description` text,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`container_id`),
  FOREIGN KEY (`shop_id`) REFERENCES `shop`(`shop_id`) ON DELETE CASCADE
);

-- Drop and Create `cart`
DROP TABLE IF EXISTS `cart`;
CREATE TABLE `cart` (
  `cart_id` int(11) NOT NULL AUTO_INCREMENT,
=======
-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 16, 2025 at 05:09 AM
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
-- Database: `pureflow`
--

-- --------------------------------------------------------

--
-- Table structure for table `address`
--

CREATE TABLE `address` (
  `address_id` int(11) NOT NULL,
  `consumer_id` int(11) DEFAULT NULL,
  `street` varchar(255) NOT NULL,
  `city` varchar(255) NOT NULL,
  `province` varchar(100) NOT NULL,
  `zip_code` varchar(10) NOT NULL,
  `latitude` decimal(10,7) DEFAULT NULL,
  `longitude` decimal(10,7) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `analytics_report`
--

CREATE TABLE `analytics_report` (
  `report_id` int(11) NOT NULL,
  `distributor_id` int(11) NOT NULL,
  `report_type` enum('Sales','Inventory','Customer Trends') NOT NULL,
  `data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`data`)),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `cart_id` int(11) NOT NULL,
>>>>>>> 7f9642d66ff7737ffa27b0722c5051d57b9ba1ee
  `user_id` int(11) NOT NULL,
  `container_id` int(11) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `type` varchar(50) NOT NULL,
  `qty` int(11) NOT NULL DEFAULT 1,
<<<<<<< HEAD
  `price` decimal(10,2) NOT NULL,
  PRIMARY KEY (`cart_id`)
);

-- Drop and Create `address`
DROP TABLE IF EXISTS `address`;
CREATE TABLE `address` (
  `address_id` int(11) NOT NULL AUTO_INCREMENT,
  `consumer_id` int(11) NOT NULL,
  `street` varchar(100),
  `barangay` varchar(100),
  `city` varchar(100),
  `region` varchar(100),
  `zip_code` varchar(10),
  `latitude` decimal(10,7),
  `longitude` decimal(10,7),
  `is_default` boolean DEFAULT FALSE,
  PRIMARY KEY (`address_id`),
  FOREIGN KEY (`consumer_id`) REFERENCES `consumer`(`consumer_id`) ON DELETE CASCADE
);

-- Drop and Create `orders`
DROP TABLE IF EXISTS `orders`;
CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL AUTO_INCREMENT,
  `consumer_id` int(11) NOT NULL,
  `shop_id` int(11) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `status` varchar(50) DEFAULT 'pending',
  `order_date` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`order_id`),
  FOREIGN KEY (`consumer_id`) REFERENCES `consumer`(`consumer_id`) ON DELETE CASCADE,
  FOREIGN KEY (`shop_id`) REFERENCES `shop`(`shop_id`) ON DELETE CASCADE
);

-- Drop and Create `order_items`
DROP TABLE IF EXISTS `order_items`;
CREATE TABLE `order_items` (
  `order_item_id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `container_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  PRIMARY KEY (`order_item_id`),
  FOREIGN KEY (`order_id`) REFERENCES `orders`(`order_id`) ON DELETE CASCADE,
  FOREIGN KEY (`container_id`) REFERENCES `container`(`container_id`) ON DELETE CASCADE
);

-- Drop and Create `employee`
DROP TABLE IF EXISTS `employee`;
CREATE TABLE `employee` (
  `employee_id` int(11) NOT NULL AUTO_INCREMENT,
  `distributor_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `contact_number` varchar(15),
  `position` varchar(50),
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`employee_id`),
  FOREIGN KEY (`distributor_id`) REFERENCES `distributor`(`distributor_id`) ON DELETE CASCADE
);

-- Drop and Create `delivery_record`
DROP TABLE IF EXISTS `delivery_record`;
CREATE TABLE `delivery_record` (
  `delivery_id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `employee_id` int(11),
  `delivery_status` varchar(50) DEFAULT 'pending',
  `delivery_date` datetime,
  PRIMARY KEY (`delivery_id`),
  FOREIGN KEY (`order_id`) REFERENCES `orders`(`order_id`) ON DELETE CASCADE,
  FOREIGN KEY (`employee_id`) REFERENCES `employee`(`employee_id`) ON DELETE SET NULL
);

-- Drop and Create `consumer_feedback`
DROP TABLE IF EXISTS `consumer_feedback`;
CREATE TABLE `consumer_feedback` (
  `feedback_id` int(11) NOT NULL AUTO_INCREMENT,
  `consumer_id` int(11) NOT NULL,
  `shop_id` int(11) NOT NULL,
  `rating` int(11) CHECK (rating BETWEEN 1 AND 5),
  `comments` text,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`feedback_id`),
  FOREIGN KEY (`consumer_id`) REFERENCES `consumer`(`consumer_id`) ON DELETE CASCADE,
  FOREIGN KEY (`shop_id`) REFERENCES `shop`(`shop_id`) ON DELETE CASCADE
);

-- Drop and Create `notification`
DROP TABLE IF EXISTS `notification`;
CREATE TABLE `notification` (
  `notification_id` int(11) NOT NULL AUTO_INCREMENT,
  `consumer_id` int(11),
  `distributor_id` int(11),
  `message` text NOT NULL,
  `is_read` boolean DEFAULT FALSE,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`notification_id`),
  FOREIGN KEY (`consumer_id`) REFERENCES `consumer`(`consumer_id`) ON DELETE CASCADE,
  FOREIGN KEY (`distributor_id`) REFERENCES `distributor`(`distributor_id`) ON DELETE CASCADE
);

-- Drop and Create `analytics_report`
DROP TABLE IF EXISTS `analytics_report`;
CREATE TABLE `analytics_report` (
  `report_id` int(11) NOT NULL AUTO_INCREMENT,
  `shop_id` int(11) NOT NULL,
  `report_type` varchar(100) NOT NULL,
  `value` decimal(10,2) NOT NULL,
  `report_date` date NOT NULL,
  PRIMARY KEY (`report_id`),
  FOREIGN KEY (`shop_id`) REFERENCES `shop`(`shop_id`) ON DELETE CASCADE
);
=======
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`cart_id`, `user_id`, `container_id`, `product_name`, `type`, `qty`, `price`) VALUES
(1, 1, 1, '5 Gallon Container', '5 Gallon', 2, 50.00),
(2, 1, 2, '3 Gallon Container', '3 Gallon', 1, 35.00),
(3, 2, 3, '5 Gallon Premium', '5 Gallon', 1, 55.00),
(4, 3, 4, '3 Gallon Budget', '3 Gallon', 3, 38.00),
(10, 4, 0, 'Container', 'with-container', 3, 53.00);

-- --------------------------------------------------------

--
-- Table structure for table `consumer`
--

CREATE TABLE `consumer` (
  `consumer_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `consumer`
--

INSERT INTO `consumer` (`consumer_id`, `name`, `email`, `phone`, `password`, `created_at`) VALUES
(1, 'Mark Rivera', 'mark@example.com', '09190000001', 'password123', '2025-07-15 01:42:35'),
(2, 'Jasmine Cruz', 'jasmine@example.com', '09190000002', 'password123', '2025-07-15 01:42:35'),
(3, 'Leo Santos', 'leo@example.com', '09190000003', 'password123', '2025-07-15 01:42:35'),
(4, 'John Rc Denver Roxas', 'denvermartinez2112@gmail.com', '', '$2y$10$qs5tf2hDtvi2Ddz8Q/gAWOLbEzZrMSr8hbSs/LTIkuUN6Rb.mxGJW', '2025-07-15 01:53:31');

-- --------------------------------------------------------

--
-- Table structure for table `consumer_feedback`
--

CREATE TABLE `consumer_feedback` (
  `feedback_id` int(11) NOT NULL,
  `consumer_id` int(11) NOT NULL,
  `shop_id` int(11) DEFAULT NULL,
  `type` enum('Feedback','Damage Report') NOT NULL,
  `rating` int(11) DEFAULT NULL CHECK (`rating` between 1 and 5),
  `photo` varchar(255) DEFAULT NULL,
  `concern` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `container`
--

CREATE TABLE `container` (
  `container_id` int(11) NOT NULL,
  `shop_id` int(11) NOT NULL,
  `type` enum('5 Gallon','3 Gallon') NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock_quantity` int(11) NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `container`
--

INSERT INTO `container` (`container_id`, `shop_id`, `type`, `price`, `stock_quantity`, `updated_at`) VALUES
(11, 6, '5 Gallon', 50.00, 100, '2025-07-15 01:34:51'),
(12, 6, '3 Gallon', 35.00, 80, '2025-07-15 01:34:51'),
(13, 7, '5 Gallon', 55.00, 90, '2025-07-15 01:34:51'),
(14, 7, '3 Gallon', 38.00, 70, '2025-07-15 01:34:51'),
(15, 8, '5 Gallon', 52.00, 120, '2025-07-15 01:34:51'),
(16, 8, '3 Gallon', 36.00, 60, '2025-07-15 01:34:51'),
(17, 9, '5 Gallon', 54.00, 110, '2025-07-15 01:34:51'),
(18, 9, '3 Gallon', 37.00, 65, '2025-07-15 01:34:51'),
(19, 10, '5 Gallon', 53.00, 95, '2025-07-15 01:34:51'),
(20, 10, '3 Gallon', 36.50, 75, '2025-07-15 01:34:51');

-- --------------------------------------------------------

--
-- Table structure for table `delivery_record`
--

CREATE TABLE `delivery_record` (
  `delivery_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `employee_id` int(11) DEFAULT NULL,
  `status` enum('Scheduled','In Transit','Delivered','Failed') DEFAULT 'Scheduled',
  `time_slot` enum('Morning','Afternoon','Evening') DEFAULT 'Morning',
  `delivery_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `distributor`
--

CREATE TABLE `distributor` (
  `distributor_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `role` enum('Distributor') DEFAULT 'Distributor',
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `distributor`
--

INSERT INTO `distributor` (`distributor_id`, `name`, `email`, `phone`, `role`, `password`, `created_at`) VALUES
(1, 'Juan Dela Cruz', 'juan1@example.com', '09170000001', 'Distributor', 'password1', '2025-07-15 00:51:39'),
(2, 'Maria Santos', 'maria2@example.com', '09170000002', 'Distributor', 'password2', '2025-07-15 00:51:39'),
(3, 'Jose Rizal', 'jose3@example.com', '09170000003', 'Distributor', 'password3', '2025-07-15 00:51:39'),
(4, 'Ana Dela Cruz', 'ana4@example.com', '09170000004', 'Distributor', 'password4', '2025-07-15 00:51:39'),
(5, 'Carlos Reyes', 'carlos5@example.com', '09170000005', 'Distributor', 'password5', '2025-07-15 00:51:39');

-- --------------------------------------------------------

--
-- Table structure for table `employee`
--

CREATE TABLE `employee` (
  `employee_id` int(11) NOT NULL,
  `distributor_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `role` enum('Driver','Staff') NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `notification_id` int(11) NOT NULL,
  `recipient_type` enum('Consumer','Distributor','Employee','Admin') NOT NULL,
  `recipient_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `status` enum('Unread','Read') DEFAULT 'Unread',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `consumer_id` int(11) NOT NULL,
  `shop_id` int(11) NOT NULL,
  `status` enum('Pending','Processing','Out for Delivery','Completed','Cancelled') DEFAULT 'Pending',
  `total_price` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `shop`
--

CREATE TABLE `shop` (
  `shop_id` int(11) NOT NULL,
  `distributor_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `location` varchar(255) NOT NULL,
  `contact_number` varchar(20) NOT NULL,
  `latitude` decimal(10,7) DEFAULT NULL,
  `longitude` decimal(10,7) DEFAULT NULL,
  `average_rating` decimal(3,2) DEFAULT 0.00,
  `open_time` time DEFAULT '08:00:00',
  `close_time` time DEFAULT '17:00:00',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `shop`
--

INSERT INTO `shop` (`shop_id`, `distributor_id`, `name`, `location`, `contact_number`, `latitude`, `longitude`, `average_rating`, `open_time`, `close_time`, `created_at`) VALUES
(6, 1, 'Tuy AquaFresh Station', 'Brgy. Poblacion, Tuy, Batangas', '09171234567', 13.9375000, 120.7248000, 4.50, '08:00:00', '17:00:00', '2025-07-15 00:52:07'),
(7, 2, 'PureDrop Water Refill', 'Brgy. Talon, Tuy, Batangas', '09181234567', 13.9397000, 120.7263000, 4.20, '08:00:00', '17:00:00', '2025-07-15 00:52:07'),
(8, 3, 'Blue Oasis Purified Water', 'Brgy. Rizal, Tuy, Batangas', '09191234567', 13.9352000, 120.7281000, 4.70, '08:00:00', '17:00:00', '2025-07-15 00:52:07'),
(9, 4, 'CrystalClear Water Depot', 'Brgy. Sabang, Tuy, Batangas', '09201234567', 13.9338000, 120.7290000, 4.60, '08:00:00', '17:00:00', '2025-07-15 00:52:07'),
(10, 5, 'AquaSafe Tuy Branch', 'Brgy. Tuyon-Tuyon, Tuy, Batangas', '09211234567', 13.9312000, 120.7305000, 4.40, '08:00:00', '17:00:00', '2025-07-15 00:52:07');

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
-- Indexes for table `analytics_report`
--
ALTER TABLE `analytics_report`
  ADD PRIMARY KEY (`report_id`),
  ADD KEY `distributor_id` (`distributor_id`);

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`cart_id`);

--
-- Indexes for table `consumer`
--
ALTER TABLE `consumer`
  ADD PRIMARY KEY (`consumer_id`),
  ADD UNIQUE KEY `email` (`email`);

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
  ADD PRIMARY KEY (`distributor_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `employee`
--
ALTER TABLE `employee`
  ADD PRIMARY KEY (`employee_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `distributor_id` (`distributor_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`notification_id`);

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
-- AUTO_INCREMENT for table `analytics_report`
--
ALTER TABLE `analytics_report`
  MODIFY `report_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `cart_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

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
  MODIFY `container_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `delivery_record`
--
ALTER TABLE `delivery_record`
  MODIFY `delivery_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `distributor`
--
ALTER TABLE `distributor`
  MODIFY `distributor_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `employee`
--
ALTER TABLE `employee`
  MODIFY `employee_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `notification_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `order_item_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `shop`
--
ALTER TABLE `shop`
  MODIFY `shop_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

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
  ADD CONSTRAINT `analytics_report_ibfk_1` FOREIGN KEY (`distributor_id`) REFERENCES `distributor` (`distributor_id`) ON DELETE CASCADE;

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

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `message_id` INT AUTO_INCREMENT PRIMARY KEY,
  `sender_id` INT NOT NULL,
  `sender_role` ENUM('Consumer', 'Distributor', 'Admin') NOT NULL,
  `receiver_id` INT NOT NULL,
  `receiver_role` ENUM('Consumer', 'Distributor', 'Admin') NOT NULL,
  `category` ENUM('Order', 'Report', 'General') DEFAULT 'General',
  `message` TEXT NOT NULL,
  `status` ENUM('Unread', 'Read') DEFAULT 'Unread',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
>>>>>>> 7f9642d66ff7737ffa27b0722c5051d57b9ba1ee
