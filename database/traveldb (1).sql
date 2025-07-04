-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 04, 2025 at 07:54 AM
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
-- Database: `traveldb`
--

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `booking_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `package_id` int(11) DEFAULT NULL,
  `booking_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `travel_date` date NOT NULL,
  `number_of_travelers` int(11) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `status` enum('pending','confirmed','cancelled','booked') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `destinations`
--

CREATE TABLE `destinations` (
  `destination_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `country` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `is_international` tinyint(1) DEFAULT 0,
  `price` decimal(10,2) NOT NULL,
  `duration_days` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `destinations`
--

INSERT INTO `destinations` (`destination_id`, `name`, `country`, `description`, `image_path`, `is_international`, `price`, `duration_days`) VALUES
(1, 'Agra & New Delhi', 'India', 'Experience the magnificent Taj Mahal in Agra and explore the vibrant capital city of New Delhi.', 'Agra,new delhi.jpg', 0, 8000.00, 4),
(2, 'Kashmir', 'India', 'Discover the paradise on earth with its stunning valleys, lakes, and snow-capped mountains.', 'kashmir.jpg', 0, 12000.00, 5),
(3, 'Shimla', 'India', 'Visit the beautiful hill station known for its colonial architecture and scenic views.', 'shimla.jpg', 0, 7000.00, 3),
(4, 'Ooty', 'India', 'Explore the Queen of Hill Stations with its tea gardens and botanical gardens.', 'Ooty,tamilnadu.avif', 0, 6500.00, 3),
(5, 'Mahabalipuram', 'India', 'Discover the ancient temple town with its UNESCO World Heritage monuments.', 'Mahabalipuram-1.jpg', 0, 5000.00, 2),
(6, 'Kerala', 'India', 'Experience the serene backwaters, beaches, and rich cultural heritage of Gods own country.', 'kerala.jpg', 0, 9000.00, 5),
(7, 'Nepal', 'Nepal', 'Explore the majestic Himalayas and rich cultural heritage of Nepal.', 'Nepal.avif', 1, 15000.00, 6),
(8, 'Singapore', 'Singapore', 'Experience the modern city-state with its iconic Marina Bay Sands and Sentosa Island.', 'Singapore.avif', 1, 25000.00, 4),
(9, 'Bali', 'Indonesia', 'Discover the beautiful beaches, temples, and vibrant culture of Bali.', 'Bali.avif', 1, 20000.00, 5),
(10, 'Malaysia', 'Malaysia', 'Explore the diverse culture, modern cities, and beautiful islands of Malaysia.', 'malaysia.avif', 1, 18000.00, 5),
(11, 'Dubai', 'UAE', 'Experience the luxury and modern architecture of Dubai, including the Burj Khalifa.', 'Dubai.avif', 1, 30000.00, 4),
(12, 'Vietnam', 'Vietnam', 'Journey from Hanoi to Danang, exploring the rich history and beautiful landscapes.', 'Hanoi to Danang.jpg', 1, 22000.00, 6),
(13, 'Kuala Lumpur', 'Malaysia', 'Discover the vibrant capital of Malaysia with its iconic Petronas Towers.', 'kuala lumpur.avif', 1, 19000.00, 4),
(14, 'Bhutan', 'Bhutan', 'Experience the last Shangri-La with its monasteries and stunning mountain views.', 'bhutan.jpg', 1, 28000.00, 5),
(15, 'Mykonos', 'Greece', 'Visit the beautiful Greek island known for its white-washed buildings and vibrant nightlife.', 'Mykonos port  Greece.avif', 1, 35000.00, 5),
(16, 'Thailand', 'Thailand', 'Explore the beautiful beaches of Krabi and Phuket in this tropical paradise.', 'krabi & phuket getaway.avif', 1, 23000.00, 6);

-- --------------------------------------------------------

--
-- Table structure for table `packages`
--

CREATE TABLE `packages` (
  `package_id` int(11) NOT NULL,
  `destination_id` int(11) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `duration_days` int(11) NOT NULL,
  `max_travelers` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `subscribers`
--

CREATE TABLE `subscribers` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `subscribed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `whatsapp_number` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--



--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`booking_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `fk_bookings_destination` (`package_id`);

--
-- Indexes for table `destinations`
--
ALTER TABLE `destinations`
  ADD PRIMARY KEY (`destination_id`);

--
-- Indexes for table `packages`
--
ALTER TABLE `packages`
  ADD PRIMARY KEY (`package_id`),
  ADD KEY `destination_id` (`destination_id`);

--
-- Indexes for table `subscribers`
--
ALTER TABLE `subscribers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `booking_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT for table `destinations`
--
ALTER TABLE `destinations`
  MODIFY `destination_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `packages`
--
ALTER TABLE `packages`
  MODIFY `package_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `subscribers`
--
ALTER TABLE `subscribers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `fk_bookings_destination` FOREIGN KEY (`package_id`) REFERENCES `destinations` (`destination_id`);

--
-- Constraints for table `packages`
--
ALTER TABLE `packages`
  ADD CONSTRAINT `packages_ibfk_1` FOREIGN KEY (`destination_id`) REFERENCES `destinations` (`destination_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
