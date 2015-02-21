-- phpMyAdmin SQL Dump
-- version 3.3.9
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Mar 29, 2011 at 09:02 PM
-- Server version: 5.5.8
-- PHP Version: 5.3.5

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `ak_invoices`
--

-- --------------------------------------------------------

--
-- Table structure for table `vcode`
--

DROP TABLE IF EXISTS `vcode`;
CREATE TABLE IF NOT EXISTS `vcode` (
  `code` varchar(5) NOT NULL,
  `cost` decimal(10,2) NOT NULL,
  `description` varchar(255) NOT NULL,
  PRIMARY KEY (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `vcode`
--

INSERT INTO `vcode` (`code`, `cost`, `description`) VALUES
('V2020', 8.43, 'STANDARD FRAME'),
('V2100', 0.00, 'SPH, SV PL TO +/- 4.00'),
('V2101', 0.00, 'SPH, SV +/- 4.12 TO +/- 4.00'),
('V2102', 0.00, 'SPH, SV +/- 7.12 TO +/- 20.00'),
('V2103', 0.00, 'SV PL TO +/- 4.00 SPH, .12 TO 2.00 CYL'),
('V2104', 0.00, 'SV PL TO +/- 4.00 SPH, 2.12 TO 4.00 CYL'),
('V2105', 0.00, 'SV PL TO +/- 4.00 SPH, 4.25 TO 6.00 CYL'),
('V2107', 0.00, 'SV +/- 4.25 TO +/- 7.00 SPH, .12 TO 2.00 CYL'),
('V2108', 0.00, 'SV +/- 4.25 TO +/- 7.00 SPH, 2.12 TO 4.00 CYL'),
('V2109', 0.00, 'SV +/- 4.25 TO +/- 7.00 SPH, 4.25 TO 6.00 CYL'),
('V2110', 0.00, 'SV +/- 4.25 TO +/- 7.00 SPH, OVER 6.00 CYL'),
('V2111', 0.00, 'SV +/- 7.00 TO +/- 12.00 SPH, .25 TO 2.25 CYL'),
('V2112', 0.00, 'SV +/- 7.00 TO +/- 12.00 SPH, 2.25 TO 4.00 CYL'),
('V2113', 0.00, 'SV +/- 7.00 TO +/- 12.00 SPH, 4.25 TO 6.00 CYL'),
('V2114', 0.00, 'SV SPH OVER +/- 12.00'),
('V2200', 0.00, 'SPH, BF, PL TO +/- 4.00'),
('V2201', 0.00, 'SPH, BF, +/- 4.12 TO +/- 7.00'),
('V2202', 0.00, 'SPH, BF, +/- 7.12 TO +/- 20.00'),
('V2203', 0.00, 'BF, PL TO +/- 4.00 SPH, .12 TO 2.00 CYL'),
('V2204', 0.00, 'BF, PL TO +/- 4.00 SPH, 2.12 TO 4.00 CYL'),
('V2205', 0.00, 'BF, PL TO +/- 4.00 SPH, 4.25 TO 6.00 CYL'),
('V2206', 0.00, 'BF, PL TO +/- 4.00 SPH, OVER 6.00 CYL'),
('V2207', 0.00, 'BF, +/- 4.25 TO +/- 7.00 SPH, .12 TO 2.00 CYL'),
('V2208', 0.00, 'BF, +/- 4.25 TO +/- 7.00 SPH, 2.12 TO 4.00 CYL'),
('V2209', 0.00, 'BF, +/- 4.25 TO +/- 7.00 SPH, 4.25 TO 6.00 CYL'),
('V2211', 0.00, 'BF, +/- 7.25 TO +/- 12.00 SPH, .25 TO 2.25 CYL'),
('V2212', 0.00, 'BF, +/- 7.25 TO +/- 12.00 SPH, 2.25 TO 4.00 CYL'),
('V2214', 0.00, 'BF, SPH OVER +/- 12.00'),
('V2215', 0.00, 'LENTICULAR (MYODISC) BIFOCAL'),
('V2300', 0.00, 'SPH, TRI, PL TO +/- 4.00'),
('V2301', 0.00, 'SPH, TRI, +/- 4.12 TO +/- 7.00'),
('V2302', 0.00, 'SPH, TRI, +/- 7.12 TO +/- 20.00'),
('V2303', 0.00, 'TRI, PL TO +/- 4.00 SPH, .12 TO 2.00 CYL'),
('V2304', 0.00, 'TRI, PL TO +/- 4.00 SPH, 2.12 TO 4.00 CYL'),
('V2305', 0.00, 'TRI, PL TO +/- 4.00 SPH, 4.25 TO 6.00 CYL'),
('V2306', 0.00, 'TRI, PL TO +/- 4.00 SPH, OVER 6.00 CYL'),
('V2307', 0.00, 'TRI, +/- 4.25 TO +/- 7.00 SPH, .12 TO 2.00 CYL'),
('V2710', 0.00, 'SLAB OFF PRISM, GLASS OR PLASTIC, PER LENS'),
('V2715', 0.00, 'PRISM'),
('V2744', 0.00, 'TRANSITIONS'),
('V2799', 0.00, 'VISION SERVICE, MISCELLANEOUS');
