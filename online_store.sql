-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Oct 20, 2024 at 04:55 PM
-- Server version: 8.0.31
-- PHP Version: 8.0.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `online_store`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_logs`
--

DROP TABLE IF EXISTS `admin_logs`;
CREATE TABLE IF NOT EXISTS `admin_logs` (
  `log_id` int NOT NULL AUTO_INCREMENT,
  `admin_id` int NOT NULL,
  `activity` text,
  `timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`log_id`),
  KEY `admin_id` (`admin_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

DROP TABLE IF EXISTS `cart`;
CREATE TABLE IF NOT EXISTS `cart` (
  `cart_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `product_id` int NOT NULL,
  `quantity` int NOT NULL,
  `added_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`cart_id`),
  KEY `user_id` (`user_id`),
  KEY `product_id` (`product_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
CREATE TABLE IF NOT EXISTS `orders` (
  `order_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `order_status` enum('pending','shipped','delivered','cancelled') DEFAULT 'pending',
  `total_amount` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`order_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

DROP TABLE IF EXISTS `order_items`;
CREATE TABLE IF NOT EXISTS `order_items` (
  `order_item_id` int NOT NULL AUTO_INCREMENT,
  `order_id` int NOT NULL,
  `product_id` int NOT NULL,
  `quantity` int NOT NULL,
  `price` decimal(10,2) NOT NULL,
  PRIMARY KEY (`order_item_id`),
  KEY `order_id` (`order_id`),
  KEY `product_id` (`product_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

DROP TABLE IF EXISTS `payments`;
CREATE TABLE IF NOT EXISTS `payments` (
  `payment_id` int NOT NULL AUTO_INCREMENT,
  `order_id` int NOT NULL,
  `payment_method` enum('credit_card','paypal','bank_transfer') NOT NULL,
  `payment_status` enum('pending','completed','failed') NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `payment_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `cardholder_name` varchar(255) DEFAULT NULL,
  `card_number` varchar(255) DEFAULT NULL,
  `expiry_date` varchar(10) DEFAULT NULL,
  `cvv` varchar(4) DEFAULT NULL,
  PRIMARY KEY (`payment_id`),
  KEY `order_id` (`order_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
CREATE TABLE IF NOT EXISTS `products` (
  `product_id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text,
  `category` varchar(100) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `stock` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`product_id`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `name`, `description`, `category`, `price`, `image_url`, `stock`, `created_at`) VALUES
(1, 'Creatine Monohydrate', 'Premium-grade creatine monohydrate designed to enhance muscle strength, power, and endurance during your workouts. Ideal for athletes and fitness enthusiasts looking to optimize performance.', 'supplements', '10.99', 'creatinemonohydrate.png', 100, '2024-09-30 02:33:50'),
(2, 'Whey Protein - Double Rich Chocolate', 'Indulge in the rich, decadent flavor of our Double Rich Chocolate whey protein. Gold Standard 100% with 58 servings to fuel your muscle growth and recovery.', 'supplements', '39.99', 'chocolatewhey.png', 100, '2024-09-30 02:33:50'),
(3, 'Whey Protein - Strawberry', 'Our delicious strawberry-flavored whey protein provides the essential nutrients your body needs for muscle growth and recovery after workouts.', 'supplements', '29.99', 'strawberryandcream.png', 100, '2024-09-30 02:33:50'),
(4, 'Whey Protein - Vanilla Ice Cream', 'Experience the delightful taste of vanilla ice cream with our whey protein. Perfectly mixes with milk or water for a delicious post-workout treat.', 'supplements', '29.99', 'vanillaicecreamwhey.png', 100, '2024-09-30 02:33:50'),
(8, 'Workout Straps', 'Enhance your lifting experience with our durable workout straps. Designed for a secure grip and optimal performance during heavy lifts.', 'gear', '19.99', 'gloves.png', 50, '2024-09-30 02:33:50'),
(9, 'Gym Pump Cover', 'Stay stylish and comfortable during your workouts with our Gym Pump Cover. Perfect for layering and designed to help you focus on your performance.', 'clothes', '15.99', 'pumpcovertshirt1.png', 50, '2024-09-30 02:33:50'),
(10, 'Compression Shirt', 'Lightweight and breathable compression shirt that enhances your performance and comfort during workouts. Supports muscle recovery.', 'clothes', '25.99', 'compressionshirt1.png', 50, '2024-09-30 02:33:50'),
(11, 'Gym Shorts', 'Flexible and durable gym shorts designed for high-performance workouts. Provides freedom of movement and moisture-wicking technology.', 'clothes', '22.99', 'gymshorts.png', 50, '2024-09-30 02:33:50'),
(12, 'Shaker Bottle', 'Convenient and durable shaker bottle for mixing protein shakes and supplements on the go. Leak-proof design with a secure lid.', 'gear', '12.99', 'shakerbottle.png', 50, '2024-09-30 02:33:50');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `user_id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `role` enum('user','admin') DEFAULT 'user',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
