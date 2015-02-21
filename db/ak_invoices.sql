-- phpMyAdmin SQL Dump
-- version 3.3.9
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Apr 07, 2011 at 11:29 PM
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
-- Table structure for table `customer`
--

DROP TABLE IF EXISTS `customer`;
CREATE TABLE IF NOT EXISTS `customer` (
  `customer_num` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `comp_name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`customer_num`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=891 ;

--
-- Dumping data for table `customer`
--

INSERT INTO `customer` (`customer_num`, `comp_name`) VALUES
(888, 'ACME, Inc.'),
(889, 'ACME, Inc.'),
(890, 'ACME, Inc.');

-- --------------------------------------------------------

--
-- Table structure for table `orderinfo`
--

DROP TABLE IF EXISTS `orderinfo`;
CREATE TABLE IF NOT EXISTS `orderinfo` (
  `job_id` int(10) unsigned NOT NULL,
  `customer_num` int(10) unsigned NOT NULL,
  `service_date` date NOT NULL,
  `amount` decimal(10,0) NOT NULL,
  `order_status` int(10) unsigned NOT NULL DEFAULT '0',
  `invoice_num` int(10) unsigned DEFAULT NULL,
  `recipient_id` int(10) unsigned NOT NULL,
  `patient_fname` varchar(45) NOT NULL,
  `patient_lname` varchar(45) NOT NULL,
  `patient_dob` date NOT NULL,
  `patient_gender` char(1) NOT NULL,
  `prior_auth_num` int(10) unsigned DEFAULT NULL,
  `diagnosis_code` varchar(20) NOT NULL,
  `od_sph` decimal(10,0) DEFAULT NULL,
  `od_cyl` decimal(10,0) DEFAULT NULL,
  `od_multi` varchar(20) DEFAULT NULL,
  `od_psm` varchar(20) DEFAULT NULL,
  `os_sph` decimal(10,0) DEFAULT NULL,
  `os_cyl` decimal(10,0) DEFAULT NULL,
  `os_multi` varchar(20) DEFAULT NULL,
  `os_psm` varchar(20) DEFAULT NULL,
  `frame` tinyint(1) NOT NULL DEFAULT '0',
  `tint` tinyint(1) NOT NULL DEFAULT '0',
  `slab_off` tinyint(1) NOT NULL DEFAULT '0',
  `misc_service` tinyint(1) NOT NULL DEFAULT '0',
  `misc_service_cost` decimal(10,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`job_id`),
  KEY `orderinfo_FKIndex1` (`customer_num`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `orderinfo`
--

INSERT INTO `orderinfo` (`job_id`, `customer_num`, `service_date`, `amount`, `order_status`, `invoice_num`, `recipient_id`, `patient_fname`, `patient_lname`, `patient_dob`, `patient_gender`, `prior_auth_num`, `diagnosis_code`, `od_sph`, `od_cyl`, `od_multi`, `od_psm`, `os_sph`, `os_cyl`, `os_multi`, `os_psm`, `frame`, `tint`, `slab_off`, `misc_service`, `misc_service_cost`) VALUES
(555555, 888, '2011-03-28', 0, 0, NULL, 600000001, 'Eric', 'Hui', '1978-09-04', 'M', 0, '', 3, 2, 'StraightTop28', '', 3, 5, 'StraightTop28', '', 0, 0, 0, 0, 0.00),
(555556, 890, '2011-03-25', 0, 0, NULL, 1230712310, 'Adrian', 'Hui', '1984-11-10', 'M', 88888888, '', 3, 0, 'SingleVision', '', -3, 0, 'SingleVision', '', 0, 0, 0, 0, 0.00),
(555557, 890, '2011-03-25', 0, 0, NULL, 1230712311, 'Soriano', 'Jovie', '1964-11-10', 'F', 88888887, '', 3, 0, 'StraightTop35', '', -3, 0, 'StraightTop35', '', 0, 0, 0, 0, 0.00),
(555558, 890, '2011-04-12', 0, 0, NULL, 391752, 'Jocelyn', 'Cheng', '1988-04-12', 'F', 88888886, '', 2, 0, 'StraightTop28', '1', 2, 0, 'StraightTop28', '1', 0, 0, 0, 0, 0.00),
(555559, 890, '2011-04-14', 0, 0, NULL, 391753, 'Steve', 'Wong', '1977-01-17', 'M', 88888885, '', 1, 0, 'SingleVision', '1', 2, 0, 'SingleVision', '1', 0, 0, 0, 0, 0.00),
(555560, 890, '2011-04-17', 0, 0, NULL, 391752, 'Karen', 'Poon', '1977-01-17', 'F', 88888881, '', 1, 0, 'SingleVision', '1', 2, 0, 'SingleVision', '1', 0, 0, 0, 0, 0.00),
(555561, 890, '2011-01-01', 0, 0, NULL, 89789235, 'Jeremy', 'Ho', '1980-05-05', 'M', 898908, '', 3, 0, 'SingleVision', '', 3, 0, 'SingleVision', '', 0, 0, 0, 0, 0.00),
(565058, 890, '2011-04-05', 0, 0, NULL, 600000001, 'Chang', 'Wunan', '1960-01-01', 'M', 12345678, '', 3, 3, '7x28Trifocal', '3', 3, 5, '7x28Trifocal', '5', 0, 1, 1, 1, 8.00),
(565059, 890, '2011-04-05', 0, 0, NULL, 600000002, 'Montha', 'Chang', '1960-02-01', 'F', 12345678, '', 3, 3, 'StraightTop35', '3', 3, 5, 'StraightTop35', '5', 0, 1, 1, 1, 8.00),
(565060, 890, '2011-04-05', 0, 0, NULL, 600000003, 'Ben', 'Chang', '1988-02-01', 'M', 12345679, '', 1, 3, 'SingleVision', '3', 1, 5, 'SingleVision', '3', 1, 1, 1, 1, 10.00);

-- --------------------------------------------------------

--
-- Table structure for table `orderinfo_has_vcode`
--

DROP TABLE IF EXISTS `orderinfo_has_vcode`;
CREATE TABLE IF NOT EXISTS `orderinfo_has_vcode` (
  `orderinfo_job_id` int(10) unsigned NOT NULL,
  `vcode_code` varchar(5) NOT NULL,
  `count` int(10) NOT NULL,
  PRIMARY KEY (`orderinfo_job_id`,`vcode_code`),
  KEY `orderinfo_has_vcode_FKIndex1` (`orderinfo_job_id`),
  KEY `orderinfo_has_vcode_FKIndex2` (`vcode_code`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `orderinfo_has_vcode`
--

INSERT INTO `orderinfo_has_vcode` (`orderinfo_job_id`, `vcode_code`, `count`) VALUES
(555555, 'V2020', 1),
(555555, 'V2200', 1),
(555555, 'V2201', 1),
(555555, 'V2203', 1),
(555555, 'V2205', 1),
(555555, 'V2304', 2),
(555555, 'V2710', 2),
(555555, 'V2715', 1),
(555556, 'V2020', 1),
(555556, 'V2100', 2),
(555557, 'V2020', 1),
(555557, 'V2200', 2),
(555558, 'V2020', 1),
(555558, 'V2200', 2),
(555558, 'V2710', 2),
(555559, 'V2020', 1),
(555559, 'V2100', 2),
(555559, 'V2710', 2),
(555560, 'V2020', 1),
(555560, 'V2100', 2),
(555560, 'V2710', 2),
(555561, 'V2020', 1),
(555561, 'V2100', 2),
(565058, 'V2020', 1),
(565058, 'V2204', 1),
(565058, 'V2205', 1),
(565058, 'V2304', 1),
(565058, 'V2305', 1),
(565058, 'V2710', 2),
(565058, 'V2715', 2),
(565058, 'V2745', 1),
(565058, 'V2799', 1),
(565059, 'V2020', 1),
(565059, 'V2204', 1),
(565059, 'V2205', 1),
(565059, 'V2710', 2),
(565059, 'V2715', 2),
(565059, 'V2745', 1),
(565059, 'V2799', 1),
(565060, 'V2020', 1),
(565060, 'V2104', 1),
(565060, 'V2105', 1),
(565060, 'V2710', 2),
(565060, 'V2715', 2),
(565060, 'V2745', 1),
(565060, 'V2799', 1);

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
('V2100', 12.64, 'SPH, SV PL TO +/- 4.00'),
('V2101', 12.64, 'SPH, SV +/- 4.12 TO +/- 4.00'),
('V2102', 12.64, 'SPH, SV +/- 7.12 TO +/- 20.00'),
('V2103', 12.64, 'SV PL TO +/- 4.00 SPH, .12 TO 2.00 CYL'),
('V2104', 12.64, 'SV PL TO +/- 4.00 SPH, 2.12 TO 4.00 CYL'),
('V2105', 12.64, 'SV PL TO +/- 4.00 SPH, 4.25 TO 6.00 CYL'),
('V2107', 12.64, 'SV +/- 4.25 TO +/- 7.00 SPH, .12 TO 2.00 CYL'),
('V2108', 12.64, 'SV +/- 4.25 TO +/- 7.00 SPH, 2.12 TO 4.00 CYL'),
('V2109', 12.64, 'SV +/- 4.25 TO +/- 7.00 SPH, 4.25 TO 6.00 CYL'),
('V2110', 12.64, 'SV +/- 4.25 TO +/- 7.00 SPH, OVER 6.00 CYL'),
('V2111', 12.64, 'SV +/- 7.00 TO +/- 12.00 SPH, .25 TO 2.25 CYL'),
('V2112', 12.64, 'SV +/- 7.00 TO +/- 12.00 SPH, 2.25 TO 4.00 CYL'),
('V2113', 12.64, 'SV +/- 7.00 TO +/- 12.00 SPH, 4.25 TO 6.00 CYL'),
('V2114', 12.64, 'SV SPH OVER +/- 12.00'),
('V2200', 16.88, 'SPH, BF, PL TO +/- 4.00'),
('V2201', 16.88, 'SPH, BF, +/- 4.12 TO +/- 7.00'),
('V2202', 16.88, 'SPH, BF, +/- 7.12 TO +/- 20.00'),
('V2203', 16.88, 'BF, PL TO +/- 4.00 SPH, .12 TO 2.00 CYL'),
('V2204', 16.88, 'BF, PL TO +/- 4.00 SPH, 2.12 TO 4.00 CYL'),
('V2205', 16.88, 'BF, PL TO +/- 4.00 SPH, 4.25 TO 6.00 CYL'),
('V2206', 16.88, 'BF, PL TO +/- 4.00 SPH, OVER 6.00 CYL'),
('V2207', 16.88, 'BF, +/- 4.25 TO +/- 7.00 SPH, .12 TO 2.00 CYL'),
('V2208', 16.88, 'BF, +/- 4.25 TO +/- 7.00 SPH, 2.12 TO 4.00 CYL'),
('V2209', 16.88, 'BF, +/- 4.25 TO +/- 7.00 SPH, 4.25 TO 6.00 CYL'),
('V2211', 16.88, 'BF, +/- 7.25 TO +/- 12.00 SPH, .25 TO 2.25 CYL'),
('V2212', 16.88, 'BF, +/- 7.25 TO +/- 12.00 SPH, 2.25 TO 4.00 CYL'),
('V2214', 16.88, 'BF, SPH OVER +/- 12.00'),
('V2215', 16.88, 'LENTICULAR (MYODISC) BIFOCAL'),
('V2300', 21.08, 'SPH, TRI, PL TO +/- 4.00'),
('V2301', 21.08, 'SPH, TRI, +/- 4.12 TO +/- 7.00'),
('V2302', 21.08, 'SPH, TRI, +/- 7.12 TO +/- 20.00'),
('V2303', 21.08, 'TRI, PL TO +/- 4.00 SPH, .12 TO 2.00 CYL'),
('V2304', 21.08, 'TRI, PL TO +/- 4.00 SPH, 2.12 TO 4.00 CYL'),
('V2305', 21.08, 'TRI, PL TO +/- 4.00 SPH, 4.25 TO 6.00 CYL'),
('V2306', 21.08, 'TRI, PL TO +/- 4.00 SPH, OVER 6.00 CYL'),
('V2307', 21.08, 'TRI, +/- 4.25 TO +/- 7.00 SPH, .12 TO 2.00 CYL'),
('V2710', 10.55, 'SLAB OFF PRISM, GLASS OR PLASTIC, PER LENS'),
('V2715', 21.08, 'PRISM'),
('V2744', 0.00, 'TRANSITIONS'),
('V2745', 5.00, 'Addition to lens; tint, any color, solid, gradient or equal'),
('V2799', 0.00, 'VISION SERVICE, MISCELLANEOUS');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `orderinfo`
--
ALTER TABLE `orderinfo`
  ADD CONSTRAINT `orderinfo_ibfk_1` FOREIGN KEY (`customer_num`) REFERENCES `customer` (`customer_num`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `orderinfo_has_vcode`
--
ALTER TABLE `orderinfo_has_vcode`
  ADD CONSTRAINT `orderinfo_has_vcode_ibfk_1` FOREIGN KEY (`orderinfo_job_id`) REFERENCES `orderinfo` (`job_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `orderinfo_has_vcode_ibfk_2` FOREIGN KEY (`vcode_code`) REFERENCES `vcode` (`code`) ON DELETE NO ACTION ON UPDATE NO ACTION;

  
--
-- Set Prices for vcodes
--
UPDATE vcode SET cost=12.64 WHERE code LIKE 'V21%';
UPDATE vcode SET cost=16.88 WHERE code LIKE 'V22%';
UPDATE vcode SET cost=21.08 WHERE code LIKE 'V23%';
UPDATE vcode SET cost=10.55 WHERE code LIKE 'V2710';
UPDATE vcode SET cost=21.08 WHERE code LIKE 'V2715';

  