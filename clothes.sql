-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3307
-- Generation Time: Sep 17, 2021 at 11:39 AM
-- Server version: 10.4.13-MariaDB
-- PHP Version: 7.3.21

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `clothes`
--

-- --------------------------------------------------------

--
-- Table structure for table `customer`
--

DROP TABLE IF EXISTS `customer`;
CREATE TABLE IF NOT EXISTS `customer` (
  `CustomerID` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(30) NOT NULL,
  `email` varchar(30) NOT NULL,
  `phone` int(11) NOT NULL,
  `postcode` int(4) NOT NULL,
  `password` varchar(32) NOT NULL,
  `usertype` varchar(10) NOT NULL,
  PRIMARY KEY (`CustomerID`)
) ENGINE=InnoDB AUTO_INCREMENT=68 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `customer`
--

INSERT INTO `customer` (`CustomerID`, `username`, `email`, `phone`, `postcode`, `password`, `usertype`) VALUES
(62, 'user', 'username@username', 1111111111, 1010, '5f4dcc3b5aa765d61d8327deb882cf99', 'user'),
(66, 'user', 'username@username', 1111111111, 1010, '5f4dcc3b5aa765d61d8327deb882cf99', 'user'),
(67, 'username', 'username@username', 1111111111, 1010, '5f4dcc3b5aa765d61d8327deb882cf99', 'user');

-- --------------------------------------------------------

--
-- Table structure for table `logtable`
--

DROP TABLE IF EXISTS `logtable`;
CREATE TABLE IF NOT EXISTS `logtable` (
  `logID` int(11) NOT NULL AUTO_INCREMENT,
  `CustomerID` int(11) NOT NULL,
  `ip_addr` varchar(1000) NOT NULL,
  `actionTime` timestamp NOT NULL DEFAULT current_timestamp(),
  `action` varchar(50) NOT NULL,
  `usertype` varchar(10) NOT NULL,
  `PHPSESSID` varchar(32) NOT NULL,
  PRIMARY KEY (`logID`),
  KEY `CustomerID` (`CustomerID`)
) ENGINE=InnoDB AUTO_INCREMENT=1426 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `logtable`
--

INSERT INTO `logtable` (`logID`, `CustomerID`, `ip_addr`, `actionTime`, `action`, `usertype`, `PHPSESSID`) VALUES
(1050, 64, '127.0.0.1', '2021-09-13 03:29:22', 'complete order', 'user', 'katl5r4n6nseq54shfemcbp7f1'),
(1051, 64, '127.0.0.1', '2021-09-13 03:29:22', 'update', 'user', 'katl5r4n6nseq54shfemcbp7f1'),
(1052, 64, '127.0.0.1', '2021-09-13 03:29:26', 'logout', 'user', 'katl5r4n6nseq54shfemcbp7f1'),
(1058, 64, '127.0.0.1', '2021-09-13 03:29:35', 'login', 'user', 'katl5r4n6nseq54shfemcbp7f1'),
(1059, 64, '127.0.0.1', '2021-09-13 03:29:35', 'start order', 'user', 'katl5r4n6nseq54shfemcbp7f1'),
(1060, 64, '127.0.0.1', '2021-09-13 03:29:36', 'logout', 'user', 'katl5r4n6nseq54shfemcbp7f1'),
(1422, 64, '::1', '2021-09-16 08:48:51', 'update', 'user', 'c97fg9fr2l6gv97lvri4l4hu3n'),
(1423, 64, '::1', '2021-09-16 08:48:51', 'complete order', 'user', 'c97fg9fr2l6gv97lvri4l4hu3n'),
(1424, 64, '::1', '2021-09-16 08:49:45', 'update', 'user', 'c97fg9fr2l6gv97lvri4l4hu3n');

-- --------------------------------------------------------

--
-- Table structure for table `orderform`
--

DROP TABLE IF EXISTS `orderform`;
CREATE TABLE IF NOT EXISTS `orderform` (
  `orderID` int(11) NOT NULL AUTO_INCREMENT,
  `orderstatus` varchar(20) NOT NULL,
  `ordertime` timestamp NOT NULL DEFAULT current_timestamp(),
  `CustomerID` int(11) NOT NULL,
  `totalprice` int(10) NOT NULL,
  PRIMARY KEY (`orderID`),
  KEY `CustomerID` (`CustomerID`)
) ENGINE=InnoDB AUTO_INCREMENT=469 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `orderform`
--

INSERT INTO `orderform` (`orderID`, `orderstatus`, `ordertime`, `CustomerID`, `totalprice`) VALUES
(326, 'Notpayed', '2021-09-11 13:17:50', 64, 20),
(327, 'Notpayed', '2021-09-11 13:19:00', 64, 20),
(467, 'Notpayed', '2021-09-16 08:46:53', 64, 0),
(468, 'Notpayed', '2021-09-16 08:48:01', 64, 30);

-- --------------------------------------------------------

--
-- Table structure for table `orderitem`
--

DROP TABLE IF EXISTS `orderitem`;
CREATE TABLE IF NOT EXISTS `orderitem` (
  `orderitem_ID` int(30) NOT NULL AUTO_INCREMENT,
  `productID` int(11) NOT NULL,
  `productname` varchar(30) NOT NULL,
  `price` int(30) NOT NULL,
  `size` varchar(3) DEFAULT NULL,
  `orderID` int(11) NOT NULL,
  `image` varchar(100) NOT NULL,
  PRIMARY KEY (`orderitem_ID`),
  KEY `orderID` (`orderID`),
  KEY `F_ID` (`productID`)
) ENGINE=InnoDB AUTO_INCREMENT=910 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `orderitem`
--

INSERT INTO `orderitem` (`orderitem_ID`, `productID`, `productname`, `price`, `size`, `orderID`, `image`) VALUES
(729, 1, 'coolcoolshirt', 10, 'S', 390, 'TShirt'),
(902, 1, 'coolcoolshirt', 10, 'S', 467, 'TShirt'),
(903, 1, 'coolcoolshirt', 10, 'S', 467, 'TShirt'),
(904, 1, 'coolcoolshirt', 10, 'S', 467, 'TShirt'),
(905, 1, 'coolcoolshirt', 10, 'S', 467, 'TShirt'),
(906, 1, 'coolcoolshirt', 10, 'S', 467, 'TShirt'),
(908, 171, 'womenshirt', 20, 'S', 468, 'womenshirt'),
(909, 172, 'necklace', 10, NULL, 468, 'necklace');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
CREATE TABLE IF NOT EXISTS `products` (
  `productID` int(11) NOT NULL AUTO_INCREMENT,
  `productname` varchar(30) NOT NULL,
  `price` int(10) NOT NULL,
  `types` varchar(10) NOT NULL,
  `image` varchar(100) NOT NULL,
  PRIMARY KEY (`productID`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=173 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`productID`, `productname`, `price`, `types`, `image`) VALUES
(1, 'coolcoolshirt', 10, 'men', 'TShirt'),
(2, 'pants', 20, 'men', 'pants'),
(170, 'jacket', 10, 'men', 'jacket'),
(171, 'womenshirt', 20, 'women', 'womenshirt'),
(172, 'necklace', 10, 'other', 'necklace');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `logtable`
--
ALTER TABLE `logtable`
  ADD CONSTRAINT `logtable_ibfk_1` FOREIGN KEY (`CustomerID`) REFERENCES `customer` (`CustomerID`);

--
-- Constraints for table `orderform`
--
ALTER TABLE `orderform`
  ADD CONSTRAINT `orderform_ibfk_1` FOREIGN KEY (`CustomerID`) REFERENCES `customer` (`CustomerID`);

--
-- Constraints for table `orderitem`
--
ALTER TABLE `orderitem`
  ADD CONSTRAINT `orderitem_ibfk_2` FOREIGN KEY (`orderID`) REFERENCES `orderform` (`orderID`),
  ADD CONSTRAINT `orderitem_ibfk_3` FOREIGN KEY (`productID`) REFERENCES `products` (`productID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
