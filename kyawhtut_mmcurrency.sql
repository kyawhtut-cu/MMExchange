-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jun 24, 2019 at 04:51 PM
-- Server version: 5.6.44-log
-- PHP Version: 7.2.7

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `kyawhtut_mmcurrency`
--

-- --------------------------------------------------------

--
-- Table structure for table `bank_table`
--

CREATE TABLE `bank_table` (
  `id` int(11) NOT NULL,
  `bank_name` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `bank_logo` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `bank_description` varchar(250) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `bank_table`
--

INSERT INTO `bank_table` (`id`, `bank_name`, `bank_logo`, `bank_description`) VALUES
(1, 'KBZ Bank', 'https://logos-download.com/wp-content/uploads/2016/11/KBZ_Bank_logo_symbol.png', ''),
(2, 'AYA Bank', 'https://www.ayabank.com/wp-content/uploads/2016/11/Logo.png', ''),
(3, 'CB Bank', 'https://www.cbbank.com.mm/images/logo.png', ''),
(4, 'MOB Bank', 'https://www.mobmyanmar.com/wp-content/uploads/2019/01/logo.png', ''),
(5, 'AGD Bank', 'https://upload.wikimedia.org/wikipedia/my/thumb/8/8f/Logo_of_AGD_Bank.svg/1024px-Logo_of_AGD_Bank.svg.png', ''),
(6, 'UAB Bank', 'https://www.uabibanking.com/images/logo.jpg', '');

-- --------------------------------------------------------

--
-- Table structure for table `country_logo`
--

CREATE TABLE `country_logo` (
  `id` int(11) NOT NULL,
  `logo_short_cut` varchar(256) NOT NULL,
  `logo_url` varchar(512) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `exchange_rate`
--

CREATE TABLE `exchange_rate` (
  `id` int(11) NOT NULL,
  `exchange_currency` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `exchange_rate_buy` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `exchange_rate_sell` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `bank_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `exchange_rate`
--

INSERT INTO `exchange_rate` (`id`, `exchange_currency`, `exchange_rate_buy`, `exchange_rate_sell`, `bank_id`) VALUES
(1, 'USD', '1510', '1519', 2),
(2, 'EURO', '1690', '1705', 2),
(3, 'SGD', '1102', '1112', 2),
(4, 'USD', '1508', '1518', 3),
(5, 'EUR', '1685', '1705', 3),
(6, 'SGD', '1100', '1112', 3),
(7, 'THB', '47.4', '48.4', 3),
(8, 'MYR', '345', '365', 3),
(9, 'USD', '1507', '1517', 1),
(10, 'EUR', '1683', '1700', 1),
(11, 'SGD', '1085', '1105', 1),
(12, 'THB', '47.50', '49', 1),
(13, '   								USD   			', '1512', '1522', 6),
(14, '   								EUR   			', '1682', '1702', 6),
(15, '   								SGD   			', '1100', '1112', 6),
(16, 'USD', '1516', '1521', 5),
(17, 'EURO', '1685', '1705', 5),
(18, 'SGD', '1107', '1112', 5),
(19, 'THB', '48', '50', 5),
(20, 'USD', '1512', '1518', 4),
(21, 'SGD', '1108', '1113', 4),
(22, 'EUR', '1695', '1705', 4),
(23, 'THB', '48.40', '48.70', 4);

-- --------------------------------------------------------

--
-- Table structure for table `exchange_time`
--

CREATE TABLE `exchange_time` (
  `id` int(11) NOT NULL,
  `exchange_time` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `bank_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `exchange_time`
--

INSERT INTO `exchange_time` (`id`, `exchange_time`, `bank_id`) VALUES
(1, '24th June 2019\r\n (12:43 PM)', 2),
(2, '24/06/2019 12:43 PM', 3),
(3, '24.06.2019', 1),
(4, '', 6),
(5, '', 5),
(6, '24.06.2019', 4);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bank_table`
--
ALTER TABLE `bank_table`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `exchange_rate`
--
ALTER TABLE `exchange_rate`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `exchange_time`
--
ALTER TABLE `exchange_time`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bank_table`
--
ALTER TABLE `bank_table`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `exchange_rate`
--
ALTER TABLE `exchange_rate`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `exchange_time`
--
ALTER TABLE `exchange_time`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
