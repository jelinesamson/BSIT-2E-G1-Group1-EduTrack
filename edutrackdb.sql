-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 06, 2026 at 12:58 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `edutrackdb`
--

-- --------------------------------------------------------

--
-- Table structure for table `accounts`
--

CREATE TABLE `accounts` (
  `account_id` int(11) NOT NULL,
  `firstName` varchar(50) NOT NULL,
  `lastName` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(250) NOT NULL,
  `role` varchar(50) NOT NULL,
  `status` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `accounts`
--

INSERT INTO `accounts` (`account_id`, `firstName`, `lastName`, `email`, `password`, `role`, `status`) VALUES
(3, 'Jeline', 'Buensuceso', 'admin@gmail.com', '$2y$10$k3sp/k0f.X5KZAPaM/59wuAT.ildMHwNTYDm93/TANngOmPeLWixG', 'admin', 'approved'),
(4, 'Eugene', 'Agulto', 'eugene@gmail.com', '$2y$10$RNvHGFVIG3IIkahQiwUvJuDte4APhmiicbTFw93aXb7IJ9n9QPLdK', 'cashier', 'approved'),
(12, 'althea', 'cruz', 'althea@gmail.com', '$2y$10$Yvw/MakXYIuUUR0xZCCyuew.Uy7FzLHLcMlQ7ivORscO/K1bYmFhO', 'cashier', 'approved'),
(13, 'Calista', 'Ferrer', 'cali@gmail.com', '$2y$10$YGKjY4BPshW14BX5feSJw.H/LLY9zlK3LoSegqxsVvqYJXV74KXCu', '', 'pending');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` int(11) NOT NULL,
  `product_code` varchar(50) NOT NULL,
  `product_type` varchar(50) NOT NULL,
  `size` varchar(50) NOT NULL,
  `department` varchar(50) NOT NULL,
  `quantity` int(11) NOT NULL,
  `incoming_qty` int(11) NOT NULL,
  `price` double NOT NULL,
  `status` varchar(50) NOT NULL,
  `is_deleted` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `product_code`, `product_type`, `size`, `department`, `quantity`, `incoming_qty`, `price`, `status`, `is_deleted`) VALUES
(20, 'UNI001', 'Uniform', 'Small', 'CICT', 9, 5, 450, 'On the Way', 0),
(21, 'B001', 'Book', 'None', 'CBEA', 20, 0, 350, 'Successfully', 0),
(22, 'qq', 'UIKASJH', 'None', 'CICT', 0, 10, 150, 'On the Way', 1),
(23, 'L001', 'ID Lace', 'None', 'CICT', 0, 50, 120, 'On the Way', 0);

-- --------------------------------------------------------

--
-- Table structure for table `product_journal`
--

CREATE TABLE `product_journal` (
  `journal_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `incoming_quantity` int(11) DEFAULT 0,
  `sales` int(11) DEFAULT 0,
  `notes` varchar(50) NOT NULL,
  `journal_qty` int(11) NOT NULL,
  `account_id` int(11) DEFAULT NULL,
  `date_time` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_journal`
--

INSERT INTO `product_journal` (`journal_id`, `product_id`, `incoming_quantity`, `sales`, `notes`, `journal_qty`, `account_id`, `date_time`) VALUES
(53, 20, 10, 0, 'Add', 0, 3, '2026-04-02 06:31:35'),
(54, 20, 0, 0, 'Receive', 10, 3, '2026-04-02 06:31:55'),
(55, 20, 5, 0, 'Edit', 10, 3, '2026-04-02 06:32:13'),
(56, 21, 20, 0, 'Add', 0, 3, '2026-04-02 06:33:00'),
(57, 22, 10, 0, 'Add', 0, 3, '2026-04-02 06:33:58'),
(58, 22, 0, 0, 'Delete', 0, 3, '2026-04-02 06:34:21'),
(59, 21, 0, 0, 'Receive', 20, 3, '2026-04-02 06:35:07'),
(60, 21, 5, 0, 'Edit', 20, 3, '2026-04-02 06:35:16'),
(61, 20, 5, 1, 'Sale TXN-3936A5D', 9, 3, '2026-04-02 06:35:37'),
(62, 21, 5, 2, 'Sale TXN-4E9FC15', 18, 3, '2026-04-02 06:35:58'),
(63, 20, 5, 1, 'Sale TXN-073D971', 8, 3, '2026-04-02 07:00:23'),
(64, 21, 5, 1, 'Sale TXN-1AC8895', 17, 3, '2026-04-02 07:00:42'),
(65, 20, 5, 1, 'Sale TXN-1AC8895', 7, 3, '2026-04-02 07:00:42'),
(66, 21, 5, 1, 'Sale TXN-8A64232', 16, 3, '2026-04-02 07:02:34'),
(67, 20, 5, 1, 'Sale TXN-92756E0', 6, 3, '2026-04-02 07:02:42'),
(68, 21, 5, 1, 'Sale TXN-92756E0', 15, 3, '2026-04-02 07:02:42'),
(69, 20, 5, 1, 'Sale TXN-F528D7D', 5, 3, '2026-04-05 05:45:25'),
(70, 21, 5, 1, 'Sale TXN-F528D7D', 14, 3, '2026-04-05 05:45:25'),
(71, 20, 0, 0, 'Receive', 10, 3, '2026-04-05 05:47:15'),
(72, 21, 0, 0, 'Receive', 19, 3, '2026-04-05 05:47:20'),
(73, 20, 0, 1, 'Sale TXN-90072B6', 9, 3, '2026-04-05 05:48:00'),
(74, 20, 0, 1, 'Sale TXN-3026E5D', 8, 4, '2026-04-05 07:58:40'),
(75, 20, 10, 0, 'Edit', 8, 3, '2026-04-05 14:06:09'),
(76, 20, 2, 0, 'Edit', 8, 3, '2026-04-05 16:56:24'),
(77, 20, 2, 0, 'Edit', 8, 3, '2026-04-05 16:56:26'),
(78, 21, 1, 0, 'Edit', 19, 3, '2026-04-05 17:02:58'),
(79, 21, 0, 0, 'Receive', 20, 3, '2026-04-06 01:14:13'),
(80, 20, 0, 0, 'Receive', 10, 3, '2026-04-06 01:46:53'),
(81, 20, 5, 0, 'Edit', 10, 3, '2026-04-06 01:47:07'),
(82, 20, 0, 0, 'Edit', 10, 3, '2026-04-06 03:08:18'),
(83, 20, 5, 0, 'Edit', 10, 3, '2026-04-06 03:08:56'),
(84, 20, 5, 1, 'Sale TXN-70595B3', 9, 12, '2026-04-06 07:40:32'),
(85, 23, 50, 0, 'Add', 0, 3, '2026-04-06 08:12:10');

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `transaction_id` varchar(20) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `paid` decimal(10,2) NOT NULL,
  `change_amount` decimal(10,2) NOT NULL,
  `vat` decimal(10,2) NOT NULL,
  `status` varchar(20) DEFAULT 'Completed',
  `account_id` int(11) DEFAULT NULL,
  `date_time` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`transaction_id`, `total`, `paid`, `change_amount`, `vat`, `status`, `account_id`, `date_time`) VALUES
('TXN-073D971', 450.00, 450.00, 0.00, 54.00, 'completed', 3, '2026-04-02 15:00:23'),
('TXN-1AC8895', 800.00, 800.00, 0.00, 96.00, 'completed', 3, '2026-04-02 15:00:42'),
('TXN-3026E5D', 450.00, 450.00, 0.00, 54.00, 'completed', 4, '2026-04-05 15:58:40'),
('TXN-3936A5D', 450.00, 500.00, 50.00, 54.00, 'completed', 3, '2026-04-02 14:35:37'),
('TXN-4E9FC15', 700.00, 1000.00, 300.00, 84.00, 'completed', 3, '2026-04-02 14:35:58'),
('TXN-70595B3', 450.00, 500.00, 50.00, 54.00, 'completed', 12, '2026-04-06 15:40:32'),
('TXN-8A64232', 350.00, 350.00, 0.00, 42.00, 'completed', 3, '2026-04-02 15:02:34'),
('TXN-90072B6', 450.00, 450.00, 0.00, 54.00, 'completed', 3, '2026-04-05 13:48:00'),
('TXN-92756E0', 800.00, 900.00, 100.00, 96.00, 'completed', 3, '2026-04-02 15:02:42'),
('TXN-F528D7D', 800.00, 1000.00, 200.00, 96.00, 'completed', 3, '2026-04-05 13:45:25');

-- --------------------------------------------------------

--
-- Table structure for table `transaction_items`
--

CREATE TABLE `transaction_items` (
  `item_id` int(11) NOT NULL,
  `transaction_id` varchar(20) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `product_name` varchar(255) DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `qty` int(11) DEFAULT NULL,
  `unit_price` decimal(10,2) DEFAULT NULL,
  `total_price` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transaction_items`
--

INSERT INTO `transaction_items` (`item_id`, `transaction_id`, `product_id`, `product_name`, `category`, `qty`, `unit_price`, `total_price`) VALUES
(11, 'TXN-3936A5D', 20, 'UNI001', 'Uniform', 1, 450.00, 450.00),
(12, 'TXN-4E9FC15', 21, 'B001', 'Book', 2, 350.00, 700.00),
(13, 'TXN-073D971', 20, 'UNI001', 'Uniform', 1, 450.00, 450.00),
(14, 'TXN-1AC8895', 21, 'B001', 'Book', 1, 350.00, 350.00),
(15, 'TXN-1AC8895', 20, 'UNI001', 'Uniform', 1, 450.00, 450.00),
(16, 'TXN-8A64232', 21, 'B001', 'Book', 1, 350.00, 350.00),
(17, 'TXN-92756E0', 20, 'UNI001', 'Uniform', 1, 450.00, 450.00),
(18, 'TXN-92756E0', 21, 'B001', 'Book', 1, 350.00, 350.00),
(19, 'TXN-F528D7D', 20, 'UNI001', 'Uniform', 1, 450.00, 450.00),
(20, 'TXN-F528D7D', 21, 'B001', 'Book', 1, 350.00, 350.00),
(21, 'TXN-90072B6', 20, 'UNI001', 'Uniform', 1, 450.00, 450.00),
(22, 'TXN-3026E5D', 20, 'UNI001', 'Uniform', 1, 450.00, 450.00),
(23, 'TXN-70595B3', 20, 'UNI001', 'Uniform', 1, 450.00, 450.00);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `accounts`
--
ALTER TABLE `accounts`
  ADD UNIQUE KEY `id` (`account_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`);

--
-- Indexes for table `product_journal`
--
ALTER TABLE `product_journal`
  ADD PRIMARY KEY (`journal_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `fk_product_journal_account` (`account_id`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`transaction_id`);

--
-- Indexes for table `transaction_items`
--
ALTER TABLE `transaction_items`
  ADD PRIMARY KEY (`item_id`),
  ADD KEY `transaction_id` (`transaction_id`),
  ADD KEY `product_id` (`product_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `accounts`
--
ALTER TABLE `accounts`
  MODIFY `account_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `product_journal`
--
ALTER TABLE `product_journal`
  MODIFY `journal_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=86;

--
-- AUTO_INCREMENT for table `transaction_items`
--
ALTER TABLE `transaction_items`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `product_journal`
--
ALTER TABLE `product_journal`
  ADD CONSTRAINT `fk_product_journal_account` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`account_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `product_journal_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `transaction_items`
--
ALTER TABLE `transaction_items`
  ADD CONSTRAINT `transaction_items_ibfk_1` FOREIGN KEY (`transaction_id`) REFERENCES `transactions` (`transaction_id`),
  ADD CONSTRAINT `transaction_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
