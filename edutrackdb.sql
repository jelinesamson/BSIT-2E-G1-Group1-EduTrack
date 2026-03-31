-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 31, 2026 at 03:10 PM
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
  `id` int(11) NOT NULL,
  `fullname` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `accounts`
--

INSERT INTO `accounts` (`id`, `fullname`, `email`, `password`) VALUES
(3, 'Jeline Buensuceso', 'admin@gmail.com', '$2y$10$k3sp/k0f.X5KZAPaM/59wuAT.ildMHwNTYDm93/TANngOmPeLWixG');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `product_code` varchar(50) NOT NULL,
  `product_type` varchar(50) NOT NULL,
  `size` varchar(50) NOT NULL,
  `department` varchar(50) NOT NULL,
  `quantity` int(11) NOT NULL,
  `incoming_qty` int(11) NOT NULL,
  `price` double NOT NULL,
  `status` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `product_code`, `product_type`, `size`, `department`, `quantity`, `incoming_qty`, `price`, `status`) VALUES
(33, 'UNI001', 'Uniform', 'Small', 'CICT', 10, 0, 450, 'Successfully');

-- --------------------------------------------------------

--
-- Table structure for table `product_journal`
--

CREATE TABLE `product_journal` (
  `id` int(11) NOT NULL,
  `product_code` varchar(50) NOT NULL,
  `product_type` varchar(100) NOT NULL,
  `incoming_quantity` int(11) DEFAULT 0,
  `quantity` int(11) DEFAULT 0,
  `sales` int(11) DEFAULT 0,
  `notes` varchar(50) NOT NULL,
  `created_by` varchar(50) NOT NULL,
  `date_time` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_journal`
--

INSERT INTO `product_journal` (`id`, `product_code`, `product_type`, `incoming_quantity`, `quantity`, `sales`, `notes`, `created_by`, `date_time`) VALUES
(55, 'UNI001', '', 10, 0, 0, 'Add', 'Admin', '2026-03-31 07:55:50'),
(56, 'UNI001', '', 0, 10, 0, 'Receive', 'Admin', '2026-03-31 07:59:45');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `accounts`
--
ALTER TABLE `accounts`
  ADD UNIQUE KEY `id` (`id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD UNIQUE KEY `id` (`id`),
  ADD UNIQUE KEY `id_2` (`id`);

--
-- Indexes for table `product_journal`
--
ALTER TABLE `product_journal`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `accounts`
--
ALTER TABLE `accounts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `product_journal`
--
ALTER TABLE `product_journal`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
