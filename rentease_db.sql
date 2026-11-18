-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Nov 18, 2026 at 07:24 PM
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
-- Database: `rentease_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `booking_id` int(11) NOT NULL,
  `property_id` int(11) NOT NULL,
  `tenant_id` int(11) NOT NULL,
  `move_in_date` date NOT NULL,
  `lease_months` int(11) DEFAULT 11,
  `tenant_message` text DEFAULT NULL,
  `booking_status` enum('pending','approved','rejected','cancelled') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`booking_id`, `property_id`, `tenant_id`, `move_in_date`, `lease_months`, `tenant_message`, `booking_status`, `created_at`, `updated_at`) VALUES
(1, 1, 3, '2026-12-01', 11, 'Looking to shift with family next month. Available for a visit this Sunday.', 'pending', '2026-11-16 14:19:05', '2026-11-16 14:19:05');

-- --------------------------------------------------------

--
-- Table structure for table `properties`
--

CREATE TABLE `properties` (
  `property_id` int(11) NOT NULL,
  `owner_id` int(11) NOT NULL,
  `title` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `city` varchar(80) NOT NULL,
  `locality` varchar(100) NOT NULL,
  `full_address` text NOT NULL,
  `bhk_type` enum('1RK','1BHK','2BHK','3BHK','4BHK+') NOT NULL,
  `furnishing` enum('Unfurnished','Semi-Furnished','Fully-Furnished') DEFAULT 'Semi-Furnished',
  `monthly_rent` decimal(10,2) NOT NULL,
  `security_deposit` decimal(10,2) NOT NULL,
  `amenities` varchar(255) DEFAULT NULL,
  `cover_image` varchar(255) DEFAULT 'default-apt.jpg',
  `availability_status` enum('available','booked') NOT NULL DEFAULT 'available',
  `approval_status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `properties`
--

INSERT INTO `properties` (`property_id`, `owner_id`, `title`, `description`, `city`, `locality`, `full_address`, `bhk_type`, `furnishing`, `monthly_rent`, `security_deposit`, `amenities`, `cover_image`, `availability_status`, `approval_status`, `created_at`) VALUES
(1, 2, 'Spacious 2BHK Near Metro Station', 'Well-ventilated apartment with 24/7 water and balcony.', 'Delhi', 'Dwarka Sector 12', 'Flat 302, Green View Apartments, Dwarka Sector 12, New Delhi', '2BHK', 'Semi-Furnished', 18000.00, 36000.00, 'Parking,Power Backup,Lift,Security', 'default-apt.jpg', 'available', 'approved', '2026-11-16 14:19:05'),
(2, 2, 'Modern 1BHK Studio Apartment', 'Ideal for working professionals and students.', 'Bengaluru', 'Koramangala', '4th Block, Near Sony Signal, Koramangala, Bengaluru', '1BHK', 'Fully-Furnished', 15500.00, 30000.00, 'WiFi,Power Backup,Geyser', 'default-apt.jpg', 'available', 'approved', '2026-11-16 14:19:05'),
(3, 2, 'Luxury 3BHK Family Flat', 'Gated society with park and gym access.', 'Pune', 'Hinjewadi', 'Tower B, Blue Ridge Township, Phase 1, Hinjewadi, Pune', '3BHK', 'Semi-Furnished', 28000.00, 56000.00, 'Parking,Gym,Swimming Pool,Lift', 'default-apt.jpg', 'available', 'pending', '2026-11-16 14:19:05');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `review_id` int(11) NOT NULL,
  `property_id` int(11) NOT NULL,
  `tenant_id` int(11) NOT NULL,
  `rating` tinyint(4) NOT NULL CHECK (`rating` between 1 and 5),
  `COMMENT` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `role` enum('tenant','owner','admin') NOT NULL DEFAULT 'tenant',
  `STATUS` enum('active','blocked') NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `full_name`, `email`, `password_hash`, `phone`, `role`, `STATUS`, `created_at`) VALUES
(1, 'System Admin', 'admin@rentease.in', 'admin123', '9800000001', 'admin', 'active', '2026-11-16 14:19:05'),
(2, 'Rajesh Sharma', 'owner1@rentease.in', 'owner123', '9811111111', 'owner', 'active', '2026-11-16 14:19:05'),
(3, 'Priya Verma', 'tenant1@rentease.in', 'tenant123', '9822222222', 'tenant', 'active', '2026-11-16 14:19:05');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`booking_id`),
  ADD KEY `fk_booking_property` (`property_id`),
  ADD KEY `fk_booking_tenant` (`tenant_id`);

--
-- Indexes for table `properties`
--
ALTER TABLE `properties`
  ADD PRIMARY KEY (`property_id`),
  ADD KEY `fk_property_owner` (`owner_id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`review_id`),
  ADD KEY `fk_review_property` (`property_id`),
  ADD KEY `fk_review_tenant` (`tenant_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `booking_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `properties`
--
ALTER TABLE `properties`
  MODIFY `property_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `review_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `fk_booking_property` FOREIGN KEY (`property_id`) REFERENCES `properties` (`property_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_booking_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `properties`
--
ALTER TABLE `properties`
  ADD CONSTRAINT `fk_property_owner` FOREIGN KEY (`owner_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `fk_review_property` FOREIGN KEY (`property_id`) REFERENCES `properties` (`property_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_review_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
