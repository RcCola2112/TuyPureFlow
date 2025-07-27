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
  `user_id` int(11) NOT NULL,
  `container_id` int(11) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `type` varchar(50) NOT NULL,
  `qty` int(11) NOT NULL DEFAULT 1,
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
