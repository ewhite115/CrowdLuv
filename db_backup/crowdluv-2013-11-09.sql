-- phpMyAdmin SQL Dump
-- version 4.0.4.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Nov 10, 2013 at 05:42 AM
-- Server version: 5.5.32
-- PHP Version: 5.4.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `crowdluv`
--
CREATE DATABASE IF NOT EXISTS `crowdluv` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `crowdluv`;

-- --------------------------------------------------------

--
-- Table structure for table `follower`
--

CREATE TABLE IF NOT EXISTS `follower` (
  `crowdluv_uid` int(15) NOT NULL AUTO_INCREMENT,
  `fb_uid` text NOT NULL,
  `mobile` text NOT NULL,
  `location_fb_id` text NOT NULL,
  `location_fbname` text NOT NULL,
  `firstname` text NOT NULL,
  `lastname` text NOT NULL,
  `email` text NOT NULL,
  `gender` varchar(10) NOT NULL,
  `birthdate` date NOT NULL,
  `fb_relationship_status` text NOT NULL,
  `signupdate` date NOT NULL,
  PRIMARY KEY (`crowdluv_uid`),
  KEY `crowdluv_uid` (`crowdluv_uid`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=18 ;

--
-- Dumping data for table `follower`
--

INSERT INTO `follower` (`crowdluv_uid`, `fb_uid`, `mobile`, `location_fb_id`, `location_fbname`, `firstname`, `lastname`, `email`, `gender`, `birthdate`, `fb_relationship_status`, `signupdate`) VALUES
(1, '8822184', '732-771-7293', '108271735873202', 'Piscataway, New Jersey', '', '', 'ewhite115@gmail.com', 'male', '1980-11-05', 'Single', '2013-09-15'),
(2, '100006876933611', '', '105756796124329', 'Jersey City, New Jersey', 'Karen', 'Zuckersky', 'atovihl_zuckersky_1381894064@tfbnw.net', 'female', '1984-08-08', 'Single', '2013-10-15'),
(3, '100006932700316', '', '105756796124329', 'Jersey City, New Jersey', 'Dick', 'Laverdetescu', 'phghssk_laverdetescu_1381894061@tfbnw.net', 'male', '1960-08-08', 'Single', '2013-10-15'),
(4, '100006903360895', '', '104057089630631', 'Hoboken, New Jersey', 'Mary', 'Yangsky', 'ejzhqjd_yangsky_1381894059@tfbnw.net', 'female', '1940-08-08', 'In a relationship', '2013-10-15'),
(5, '100006798342536', '', '108188925868598', 'New Brunswick, New Jersey', 'Ed', 'Thedev', 'edwhite42@gmail.com', 'male', '0000-00-00', '', '2013-10-15'),
(6, '100003099877508', '', '112222822122196', 'Little Rock, Arkansas', 'Norma', 'Nerdleworth', 'normanerdleworth@gmail.com', 'female', '1976-01-01', '', '2013-10-16'),
(7, '1264253021', '', '102146663160831', 'Brisbane, Queensland, Australia', 'Cyndi', 'McCoy', 'yakovamusic@gmail.com', 'female', '1978-08-09', 'Single', '2013-10-16'),
(8, '100006812112429', '', '105756796124329', 'Jersey City, New Jersey', 'Will', 'Shepardsenbergescuwitzskysteinsonman', 'rglmnuh_shepardsenbergescuwitzskysteinsonman_1381894057@tfbnw.net', 'male', '1970-08-08', 'In a relationship', '2013-10-21'),
(9, '100006823420347', '', '113132652033783', 'Omaha, Nebraska', 'Margaret', 'McDonaldson', 'wmaitcj_mcdonaldson_1381639717@tfbnw.net', 'female', '1980-08-08', '', '2013-10-21'),
(10, '100006791862475', '', '111831402169990', 'Beerse, Belgium', 'Jennifer', 'Wongmanwitzsonsteinescusenbergsky', 'dkjzlzn_wongmanwitzsonsteinescusenbergsky_1381639714@tfbnw.net', 'female', '1990-08-08', '', '2013-10-21'),
(11, '100006841690030', '', '112111905481230', 'Brooklyn, New York', 'Carol', 'Okelolawitzsenescusonbergsteinskyman', 'mmhoqqm_okelolawitzsenescusonbergsteinskyman_1382407921@tfbnw.net', 'female', '1980-08-08', 'Engaged', '2013-10-21'),
(12, '100006925385189', '', '112111905481230', 'Brooklyn, New York', 'Donna', 'Alisonson', 'cqqxofb_alisonson_1382407914@tfbnw.net', 'female', '1980-08-08', 'Single', '2013-10-21'),
(13, '100006949894527', '', '112111905481230', 'Brooklyn, New York', 'Jennifer', 'Liangson', 'tnspkjk_liangson_1382407917@tfbnw.net', 'female', '1975-08-08', 'Divorced', '2013-10-21'),
(14, '100006817480380', '', '0', 'Unspecified', 'Linda', 'Huiescu', 'wixyffl_huiescu_1381639712@tfbnw.net', 'female', '1980-08-08', '', '2013-10-21'),
(15, '100006886715811', '', '104057089630631', 'Hoboken, New Jersey', 'Will', 'Laverdetberg', 'xydhoah_laverdetberg_1382407919@tfbnw.net', 'male', '1997-08-08', 'In a relationship', '2013-10-21'),
(16, '100006173978336', '', '0', 'Unspecified', 'Dina', 'Perkins', 'tugrescue@aol.com', 'female', '1952-08-21', 'Unspecified', '2013-11-06'),
(17, '6833181', '', '110419212320033', 'Indianapolis, Indiana', 'Jason', 'Gross', 'jason@jasonagross.com', 'male', '1986-12-04', 'In a relationship', '2013-11-07');

-- --------------------------------------------------------

--
-- Table structure for table `follower_luvs_talent`
--

CREATE TABLE IF NOT EXISTS `follower_luvs_talent` (
  `crowdluv_uid` char(15) NOT NULL,
  `crowdluv_tid` char(15) NOT NULL,
  `still_following` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`crowdluv_uid`,`crowdluv_tid`),
  UNIQUE KEY `crowdluv_uid` (`crowdluv_uid`,`crowdluv_tid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `follower_luvs_talent`
--

INSERT INTO `follower_luvs_talent` (`crowdluv_uid`, `crowdluv_tid`, `still_following`) VALUES
('0', '2', 1),
('0', '3', 1),
('0', '6', 1),
('1', '-1', 1),
('1', '1', 1),
('1', '15', 1),
('1', '2', 1),
('1', '3', 1),
('1', '6', 1),
('1', '7', 1),
('10', '1', 1),
('11', '1', 1),
('12', '1', 1),
('12', '2', 1),
('13', '1', 1),
('14', '1', 1),
('15', '1', 1),
('2', '1', 1),
('2', '2', 1),
('2', '3', 1),
('2', '7', 1),
('3', '1', 1),
('3', '2', 1),
('3', '3', 1),
('3', '7', 1),
('4', '1', 1),
('4', '2', 1),
('4', '7', 1),
('5', '1', 1),
('5', '3', 1),
('5', '6', 1),
('5', '7', 1),
('6', '1', 1),
('6', '7', 1),
('7', '1', 1),
('7', '2', 1),
('8', '1', 1),
('9', '1', 1),
('9', '2', 1),
('9', '3', 1),
('9', '6', 1);

-- --------------------------------------------------------

--
-- Table structure for table `talent`
--

CREATE TABLE IF NOT EXISTS `talent` (
  `crowdluv_tid` int(15) NOT NULL AUTO_INCREMENT,
  `fb_pid` text NOT NULL,
  `fb_page_name` text NOT NULL,
  `firstname` text NOT NULL,
  `lastname` text NOT NULL,
  `mobile` text NOT NULL,
  `home_city` text NOT NULL,
  PRIMARY KEY (`crowdluv_tid`),
  UNIQUE KEY `crowdluv_tid` (`crowdluv_tid`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=17 ;

--
-- Dumping data for table `talent`
--

INSERT INTO `talent` (`crowdluv_tid`, `fb_pid`, `fb_page_name`, `firstname`, `lastname`, `mobile`, `home_city`) VALUES
(1, '661469737211316', 'King Eduardo', '', '', '', ''),
(2, '456881417762138', 'Eddie the Arteest', '', '', '', ''),
(3, '592794577429209', 'Edward on Ice', '', '', '', ''),
(6, '539324602803269', 'Linda''s Traveling Contortionists', '', '', '', ''),
(7, '480844715329745', 'Mother Daughter Bridge', '', '', '', ''),
(8, '145475732307948', 'Staten Island Liberian Community Center', '', '', '', ''),
(9, '254570684673418', 'Ikon NYC', '', '', '', ''),
(10, '255450171177190', 'Pyrotheology', '', '', '', ''),
(11, '291984720823444', 'Cyndi McCoy', '', '', '', ''),
(12, '7601333997', 'Yakova', '', '', '', ''),
(13, '150093631694647', 'iMatchbook.com', '', '', '', ''),
(14, '1438463543046752', 'Lindas traveling contortionists', '', '', '', ''),
(15, '186731738183037', 'Eddie cyndi bridge', '', '', '', ''),
(16, '445410105569205', 'Johnny johns drugs', '', '', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `talent_landingpage`
--

CREATE TABLE IF NOT EXISTS `talent_landingpage` (
  `crowdluv_tid` int(11) DEFAULT NULL,
  `message` text,
  `image` varchar(255) DEFAULT 'default',
  `message_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `crowdluv_tid` (`crowdluv_tid`,`message_timestamp`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `talent_landingpage`
--

INSERT INTO `talent_landingpage` (`crowdluv_tid`, `message`, `image`, `message_timestamp`) VALUES
(2, 'There is an arteeest on stageeeeee!', '388168_10101095027567859_870031484_n.jpg', '2013-11-10 03:01:09'),
(2, 'There is an ar-teeest on staaage.', '388168_10101095027567859_870031484_n.jpg', '2013-11-10 03:26:27'),
(1, 'The rent is too damn high!', 'Rent-is-too-damn-high.jpg', '2013-11-10 03:33:30');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
