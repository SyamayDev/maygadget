-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jun 20, 2025 at 05:23 PM
-- Server version: 8.0.30
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `lks_smk`
--

-- --------------------------------------------------------

--
-- Table structure for table `favorites`
--

CREATE TABLE `favorites` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `product_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `favorites`
--

INSERT INTO `favorites` (`id`, `user_id`, `product_id`, `created_at`) VALUES
(52, 2, 15, '2025-06-19 18:29:34'),
(54, 1, 15, '2025-06-20 15:13:26');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int NOT NULL,
  `order_group_id` varchar(50) DEFAULT NULL,
  `user_id` int DEFAULT NULL,
  `product_id` int DEFAULT NULL,
  `quantity` int DEFAULT NULL,
  `total_price` decimal(10,2) DEFAULT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `order_details` json DEFAULT NULL,
  `order_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `status` enum('pending','selesai','cancel') NOT NULL DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `order_group_id`, `user_id`, `product_id`, `quantity`, `total_price`, `payment_method`, `order_details`, `order_date`, `status`) VALUES
(1, 'ORD_6852b70665d3a', 1, 12, 1, '220000.00', 'DANA', '{\"name\": \"Syahril May Mubdi\", \"email\": \"syahrilmaymubdi2505@gmail.com\", \"phone\": \"082267403010\", \"address\": \"Jl. Bhayangkara 484\"}', '2025-06-18 12:54:30', 'pending'),
(2, 'ORD_6852b79338830', 1, 7, 1, '300000.00', 'QRIS', '{\"name\": \"Syahril May Mubdi\", \"email\": \"syahrilmaymubdi2505@gmail.com\", \"phone\": \"082267403010\", \"address\": \"Jl. Bhayangkara 484\"}', '2025-06-18 12:56:51', 'selesai'),
(3, 'ORD_6853684d4d1cd', NULL, 14, 1, '4000000.00', 'DANA', '{\"name\": \"Syahril May Mubdi\", \"email\": \"syahrilmaymubdi2505@gmail.com\", \"phone\": \"082267403010\", \"address\": \"Jl. Bhayangkara 484\"}', '2025-06-19 01:30:53', 'cancel');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text,
  `price` decimal(10,2) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `category` varchar(50) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `price`, `image`, `category`, `created_at`) VALUES
(4, 'Asus ROG Strix G16', 'ASUS ROG Strix adalah lini laptop gaming dari ASUS yang dikenal dengan performa tinggi dan desain yang agresif. Laptop ini dirancang untuk memberikan pengalaman gaming yang imersif dan lancar, dengan spesifikasi yang kuat dan fitur-fitur yang dioptimalkan untuk gaming. ', '32000000.00', 'uploads/68556c4815d5f.png', 'Laptop', '2025-06-17 04:28:49'),
(5, 'Asus ROG Phone 8', 'ASUS ROG Phone 8 adalah smartphone gaming yang hadir dengan layar AMOLED 6.78 inci FHD+ (2400x1080) dengan pelindung Corning Gorilla Glass Victus 2. Ditenagai oleh prosesor Qualcomm Snapdragon 8 Gen 3, RAM 12GB, dan penyimpanan UFS4.0 256GB. Baterainya berkapasitas 5500 mAh dan tersedia dalam dua pilihan warna: Phantom Black dan Storm Grey. ', '8000000.00', 'uploads/6850ef5c270ea.png', 'Handphone', '2025-06-17 04:30:20'),
(7, 'Earphone Olike', 'Earphone Olike adalah perangkat audio nirkabel (TWS) yang menawarkan koneksi Bluetooth, desain yang nyaman, dan kualitas audio yang baik. Beberapa model memiliki fitur tambahan seperti daya tahan baterai yang lama, tahan air, dan tampilan indikator baterai. ', '300000.00', 'uploads/6850f8783a2d3.png', 'Audio', '2025-06-17 05:09:12'),
(12, 'Keyboard Gaming JETEX KBX2', 'Keyboard Gaming RGB JETE KBX2 USB Cable - Garansi 2 TahunKeyboard gaming JETEX KBX2 adalah keyboard gaming standar dengan 112 tombol dan lampu latar RGB. Keyboard ini dirancang untuk memberikan pengalaman bermain game yang lebih baik dengan fitur-fitur seperti tombol mekanik yang lebih besar, 19 anti-ghosting keys, dan 8 multimedia keys.', '220000.00', 'uploads/685215a462976.png', 'Aksesoris', '2025-06-18 01:25:56'),
(13, 'Acer Predator Helios Neo 16', 'Acer Predator Helios Neo 16 (PHN16) adalah laptop gaming yang menawarkan layar 16 inci dengan resolusi tinggi dan refresh rate cepat, ideal untuk pengalaman bermain game yang imersif. Ditenagai oleh prosesor Intel Core i7 generasi terbaru dan kartu grafis NVIDIA GeForce RTX, laptop ini mampu menangani game AAA dengan lancar dan tugas-tugas AI yang intensif.', '22000000.00', 'uploads/6852177bc9885.png', 'Laptop', '2025-06-18 01:33:47'),
(14, 'Infinix GT 10 Pro', 'Infinix GT 10 Pro adalah smartphone yang dirancang untuk para gamer, menonjolkan performa tinggi dan fitur yang mendukung pengalaman bermain game. Ponsel ini memiliki layar AMOLED 6.67 inci dengan refresh rate 120Hz, ditenagai oleh chipset MediaTek Dimensity 8050 5G, dan dilengkapi sistem pendingin vapor chamber. Kamera utamanya 108MP, dan tersedia dalam varian RAM 8GB dan penyimpanan 256GB. ', '4000000.00', 'uploads/68524cdbde14f.png', 'Handphone', '2025-06-18 05:21:31'),
(15, 'Apple Watch Ultra 2 Trail Loop', 'Apple Watch Ultra 2 adalah jam tangan pintar tangguh yang dirancang untuk petualangan dan olahraga ekstrem. Ia memiliki casing titanium 49mm, layar Retina yang paling terang dan besar, serta daya tahan baterai hingga 36 jam (72 jam dalam mode daya rendah). Fitur unggulannya meliputi GPS frekuensi ganda yang presisi, ketahanan air hingga 100 meter, dan berbagai fitur keselamatan canggih seperti Deteksi Jatuh dan Deteksi Tabrakan. ', '14500000.00', 'uploads/685300fa0b29b.png', 'Smartwatch', '2025-06-18 17:41:07'),
(16, 'Huawei Watch GT 4', 'Huawei Watch GT 4 adalah jam tangan pintar yang menawarkan desain elegan dan kokoh dengan bodi stainless steel. Jam ini memiliki layar AMOLED yang terang dan jernih, serta berbagai fitur kesehatan dan olahraga canggih, termasuk pemantauan detak jantung, SpO2, kualitas tidur, dan pelacakan lebih dari 100 mode olahraga. Huawei Watch GT 4 tersedia dalam dua ukuran, 41mm dan 46mm, dengan daya tahan baterai hingga 7 hari untuk ukuran 41mm dan 14 hari untuk ukuran 46mm. ', '3499000.00', 'uploads/68556b6dcd7f8.png', 'Smartwatch', '2025-06-20 14:07:01');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `role` enum('admin','user') DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `email`, `created_at`, `role`) VALUES
(1, 'admin', '$2y$10$QVR3kqMl.nwQ/5kzfHY2HuvaCbbuv9212nvM4eJpnELCMm50Oe4Fy', 'admin@example.com', '2025-06-17 03:38:36', 'admin'),
(2, 'Syahril', '$2y$10$V1uYaivH3yekpA6yUlqfEuPDfEJgAuYmj4jv9Ct4RzwuvqvOsCHaC', 'syahrilmaymubdi2505@gmail.com', '2025-06-19 18:08:04', 'user');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `favorites`
--
ALTER TABLE `favorites`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
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
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `favorites`
--
ALTER TABLE `favorites`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `favorites`
--
ALTER TABLE `favorites`
  ADD CONSTRAINT `favorites_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  ADD CONSTRAINT `favorites_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
