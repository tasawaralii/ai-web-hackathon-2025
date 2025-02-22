-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 22, 2025 at 03:59 PM
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
-- Database: `pantry`
--

-- --------------------------------------------------------

--
-- Table structure for table `ingredients`
--

CREATE TABLE `ingredients` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ingredients`
--

INSERT INTO `ingredients` (`id`, `name`) VALUES
(3, 'Garlic'),
(4, 'Olive Oil'),
(2, 'Onion'),
(5, 'Pasta'),
(15, 'Salt'),
(1, 'Tomato');

-- --------------------------------------------------------

--
-- Table structure for table `pantry_items`
--

CREATE TABLE `pantry_items` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `alias_name` varchar(100) DEFAULT NULL,
  `ingredient` varchar(50) NOT NULL,
  `quantity` int(11) DEFAULT NULL,
  `expiration_date` date DEFAULT NULL,
  `date_added` datetime DEFAULT current_timestamp(),
  `category` enum('Vegetables','Fruits','Dairy','Meat','Grains','Beverages','Other') NOT NULL DEFAULT 'Other'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pantry_items`
--

INSERT INTO `pantry_items` (`id`, `user_id`, `alias_name`, `ingredient`, `quantity`, `expiration_date`, `date_added`, `category`) VALUES
(22, 1, 'Onion', 'Garlic', 1, '2025-02-05', '2025-02-13 09:32:36', 'Vegetables'),
(23, 1, 'Fresh Onions', 'Onion', 1, '2025-02-14', '2025-02-13 10:03:06', 'Vegetables');

-- --------------------------------------------------------

--
-- Table structure for table `recipes`
--

CREATE TABLE `recipes` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `instructions` text NOT NULL,
  `cooking_time` int(11) DEFAULT NULL,
  `servings` int(11) DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `recipes`
--

INSERT INTO `recipes` (`id`, `title`, `instructions`, `cooking_time`, `servings`, `image_url`, `created_at`) VALUES
(1, 'Simple Pasta', '1. Cook pasta\n2. Sauté garlic and onions\n3. Mix everything', 20, 2, NULL, '2025-02-12 21:26:16'),
(2, 'Tomato Pasta', '1. Cook pasta\n2. Mix with tomatoes', 20, 7, NULL, '2025-02-12 21:32:25'),
(5, 'Omlate', 'Just Cook Man', 5, 1, NULL, '2025-02-12 22:48:29'),
(7, 'Pizza', 'Use your skills', 30, 4, NULL, '2025-02-13 06:16:30'),
(8, 'biryani', 'make it taste good!', 120, 1, NULL, '2025-02-13 09:06:30'),
(9, 'M,J.G', 'KSJZLDDS', 12, 1, NULL, '2025-02-17 05:52:30');

-- --------------------------------------------------------

--
-- Table structure for table `recipe_ingredients`
--

CREATE TABLE `recipe_ingredients` (
  `recipe_id` int(11) NOT NULL,
  `ingredient_id` int(11) NOT NULL,
  `quantity` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `recipe_ingredients`
--

INSERT INTO `recipe_ingredients` (`recipe_id`, `ingredient_id`, `quantity`) VALUES
(1, 1, '2 medium'),
(1, 2, '1 chopped'),
(1, 3, '2 cloves'),
(1, 4, '2 tbsp'),
(1, 5, '200g'),
(2, 2, '2'),
(5, 2, '1'),
(5, 3, '5 g'),
(5, 15, '1 tea spoon'),
(7, 2, '3'),
(7, 15, '2 spoons'),
(8, 1, '3'),
(8, 5, '1'),
(9, 3, '3');

-- --------------------------------------------------------

--
-- Table structure for table `shopping_list`
--

CREATE TABLE `shopping_list` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `ingredient` varchar(100) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `email`) VALUES
(1, 'babyxboss', '$2y$10$TCUKm7CWYLt1tS4XCNeS/Ohp3/RnOgpf8r3JxXLa/8bJq1GTGh/02', 'speedodubbers@gmail.com');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `ingredients`
--
ALTER TABLE `ingredients`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `pantry_items`
--
ALTER TABLE `pantry_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `category` (`category`),
  ADD KEY `expiration_date` (`expiration_date`),
  ADD KEY `ingredient` (`ingredient`);

--
-- Indexes for table `recipes`
--
ALTER TABLE `recipes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `title` (`title`);

--
-- Indexes for table `recipe_ingredients`
--
ALTER TABLE `recipe_ingredients`
  ADD PRIMARY KEY (`recipe_id`,`ingredient_id`),
  ADD KEY `ingredient_id` (`ingredient_id`);

--
-- Indexes for table `shopping_list`
--
ALTER TABLE `shopping_list`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `ingredients`
--
ALTER TABLE `ingredients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `pantry_items`
--
ALTER TABLE `pantry_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `recipes`
--
ALTER TABLE `recipes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `shopping_list`
--
ALTER TABLE `shopping_list`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `pantry_items`
--
ALTER TABLE `pantry_items`
  ADD CONSTRAINT `pantry_items_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `pantry_items_ibfk_2` FOREIGN KEY (`ingredient`) REFERENCES `ingredients` (`name`);

--
-- Constraints for table `recipe_ingredients`
--
ALTER TABLE `recipe_ingredients`
  ADD CONSTRAINT `recipe_ingredients_ibfk_1` FOREIGN KEY (`recipe_id`) REFERENCES `recipes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `recipe_ingredients_ibfk_2` FOREIGN KEY (`ingredient_id`) REFERENCES `ingredients` (`id`);

--
-- Constraints for table `shopping_list`
--
ALTER TABLE `shopping_list`
  ADD CONSTRAINT `shopping_list_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
