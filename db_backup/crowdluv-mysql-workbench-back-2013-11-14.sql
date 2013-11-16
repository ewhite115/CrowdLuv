CREATE DATABASE  IF NOT EXISTS `crowdluv` /*!40100 DEFAULT CHARACTER SET latin1 */;
USE `crowdluv`;
-- MySQL dump 10.13  Distrib 5.6.13, for Win32 (x86)
--
-- Host: 127.0.0.1    Database: crowdluv
-- ------------------------------------------------------
-- Server version	5.5.32

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `follower`
--

DROP TABLE IF EXISTS `follower`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `follower` (
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
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `follower`
--

LOCK TABLES `follower` WRITE;
/*!40000 ALTER TABLE `follower` DISABLE KEYS */;
INSERT INTO `follower` VALUES (1,'8822184','732-771-7293','108271735873202','Piscataway, New Jersey','','','ewhite115@gmail.com','male','1980-11-05','Single','2013-09-15'),(2,'100006876933611','','105756796124329','Jersey City, New Jersey','Karen','Zuckersky','atovihl_zuckersky_1381894064@tfbnw.net','female','1984-08-08','Single','2013-10-15'),(3,'100006932700316','','105756796124329','Jersey City, New Jersey','Dick','Laverdetescu','phghssk_laverdetescu_1381894061@tfbnw.net','male','1960-08-08','Single','2013-10-15'),(4,'100006903360895','','104057089630631','Hoboken, New Jersey','Mary','Yangsky','ejzhqjd_yangsky_1381894059@tfbnw.net','female','1940-08-08','In a relationship','2013-10-15'),(5,'100006798342536','','108188925868598','New Brunswick, New Jersey','Ed','Thedev','edwhite42@gmail.com','male','0000-00-00','','2013-10-15'),(6,'100003099877508','','112222822122196','Little Rock, Arkansas','Norma','Nerdleworth','normanerdleworth@gmail.com','female','1976-01-01','','2013-10-16'),(7,'1264253021','','102146663160831','Brisbane, Queensland, Australia','Cyndi','McCoy','yakovamusic@gmail.com','female','1978-08-09','Single','2013-10-16'),(8,'100006812112429','','105756796124329','Jersey City, New Jersey','Will','Shepardsenbergescuwitzskysteinsonman','rglmnuh_shepardsenbergescuwitzskysteinsonman_1381894057@tfbnw.net','male','1970-08-08','In a relationship','2013-10-21'),(9,'100006823420347','','113132652033783','Omaha, Nebraska','Margaret','McDonaldson','wmaitcj_mcdonaldson_1381639717@tfbnw.net','female','1980-08-08','','2013-10-21'),(10,'100006791862475','','111831402169990','Beerse, Belgium','Jennifer','Wongmanwitzsonsteinescusenbergsky','dkjzlzn_wongmanwitzsonsteinescusenbergsky_1381639714@tfbnw.net','female','1990-08-08','','2013-10-21'),(11,'100006841690030','','112111905481230','Brooklyn, New York','Carol','Okelolawitzsenescusonbergsteinskyman','mmhoqqm_okelolawitzsenescusonbergsteinskyman_1382407921@tfbnw.net','female','1980-08-08','Engaged','2013-10-21'),(12,'100006925385189','','112111905481230','Brooklyn, New York','Donna','Alisonson','cqqxofb_alisonson_1382407914@tfbnw.net','female','1980-08-08','Single','2013-10-21'),(13,'100006949894527','','112111905481230','Brooklyn, New York','Jennifer','Liangson','tnspkjk_liangson_1382407917@tfbnw.net','female','1975-08-08','Divorced','2013-10-21'),(14,'100006817480380','','0','Unspecified','Linda','Huiescu','wixyffl_huiescu_1381639712@tfbnw.net','female','1980-08-08','','2013-10-21'),(15,'100006886715811','','104057089630631','Hoboken, New Jersey','Will','Laverdetberg','xydhoah_laverdetberg_1382407919@tfbnw.net','male','1997-08-08','In a relationship','2013-10-21'),(16,'100006173978336','','0','Unspecified','Dina','Perkins','tugrescue@aol.com','female','1952-08-21','Unspecified','2013-11-06'),(17,'6833181','','110419212320033','Indianapolis, Indiana','Jason','Gross','jason@jasonagross.com','male','1986-12-04','In a relationship','2013-11-07'),(18,'657358400','5162387570','108424279189115','New York, New York','Shanna','Sobel','shannasobel@hotmail.com','female','1982-06-11','Unspecified','2013-11-14');
/*!40000 ALTER TABLE `follower` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `follower_luvs_talent`
--

DROP TABLE IF EXISTS `follower_luvs_talent`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `follower_luvs_talent` (
  `crowdluv_uid` char(15) NOT NULL,
  `crowdluv_tid` char(15) NOT NULL,
  `still_following` tinyint(1) NOT NULL DEFAULT '1',
  `allow_email` tinyint(1) NOT NULL DEFAULT '1',
  `allow_sms` tinyint(1) NOT NULL DEFAULT '1',
  `will_travel_distance` int(11) NOT NULL DEFAULT '5',
  `will_travel_time` int(11) NOT NULL DEFAULT '60',
  PRIMARY KEY (`crowdluv_uid`,`crowdluv_tid`),
  UNIQUE KEY `crowdluv_uid` (`crowdluv_uid`,`crowdluv_tid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `follower_luvs_talent`
--

LOCK TABLES `follower_luvs_talent` WRITE;
/*!40000 ALTER TABLE `follower_luvs_talent` DISABLE KEYS */;
INSERT INTO `follower_luvs_talent` VALUES ('0','2',1,1,1,5,64),('0','3',1,1,1,5,60),('0','6',1,1,1,5,60),('1','-1',1,1,1,5,60),('1','1',1,1,0,5,9),('1','15',1,1,1,5,18),('1','16',1,1,0,5,60),('1','17',1,1,1,5,60),('1','2',1,0,0,5,3),('1','3',1,1,1,5,60),('1','4',1,1,1,5,60),('1','5',1,1,1,5,60),('1','6',1,1,1,5,60),('1','7',1,1,1,5,60),('10','1',1,1,1,5,60),('11','1',1,1,1,5,60),('12','1',1,1,1,5,60),('12','2',1,1,1,5,60),('13','1',1,1,1,5,60),('14','1',1,1,1,5,60),('15','1',1,1,1,5,60),('18','1',1,1,1,5,60),('2','1',1,1,1,5,60),('2','2',1,1,1,5,60),('2','3',1,1,1,5,60),('2','7',1,1,1,5,60),('3','1',1,1,1,5,60),('3','2',1,1,1,5,60),('3','3',1,1,1,5,60),('3','7',1,1,1,5,60),('4','1',1,1,1,5,60),('4','2',1,1,1,5,60),('4','7',1,1,1,5,60),('5','1',1,1,1,5,60),('5','3',1,1,1,5,60),('5','6',1,1,1,5,60),('5','7',1,1,1,5,60),('6','1',1,1,1,5,60),('6','7',1,1,1,5,60),('7','1',1,1,1,5,60),('7','2',1,1,1,5,60),('8','1',1,1,1,5,60),('9','1',1,1,1,5,60),('9','2',1,1,1,5,60),('9','3',1,1,1,5,60),('9','6',1,1,1,5,60);
/*!40000 ALTER TABLE `follower_luvs_talent` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `talent`
--

DROP TABLE IF EXISTS `talent`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `talent` (
  `crowdluv_tid` int(15) NOT NULL AUTO_INCREMENT,
  `fb_pid` text NOT NULL,
  `fb_page_name` text NOT NULL,
  `firstname` text NOT NULL,
  `lastname` text NOT NULL,
  `mobile` text NOT NULL,
  `home_city` text NOT NULL,
  PRIMARY KEY (`crowdluv_tid`),
  UNIQUE KEY `crowdluv_tid` (`crowdluv_tid`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `talent`
--

LOCK TABLES `talent` WRITE;
/*!40000 ALTER TABLE `talent` DISABLE KEYS */;
INSERT INTO `talent` VALUES (1,'661469737211316','King Eduardo','','','',''),(2,'456881417762138','Eddie the Arteest','','','',''),(3,'592794577429209','Edward on Ice','','','',''),(6,'539324602803269','Linda\'s Traveling Contortionists','','','',''),(7,'480844715329745','Mother Daughter Bridge','','','',''),(8,'145475732307948','Staten Island Liberian Community Center','','','',''),(9,'254570684673418','Ikon NYC','','','',''),(10,'255450171177190','Pyrotheology','','','',''),(11,'291984720823444','Cyndi McCoy','','','',''),(12,'7601333997','Yakova','','','',''),(13,'150093631694647','iMatchbook.com','','','',''),(14,'1438463543046752','Lindas traveling contortionists','','','',''),(15,'186731738183037','Eddie cyndi bridge','','','',''),(16,'445410105569205','Johnny johns drugs','','','','');
/*!40000 ALTER TABLE `talent` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `talent_landingpage`
--

DROP TABLE IF EXISTS `talent_landingpage`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `talent_landingpage` (
  `crowdluv_tid` int(11) DEFAULT NULL,
  `message` text,
  `image` varchar(255) DEFAULT 'default',
  `message_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `crowdluv_tid` (`crowdluv_tid`,`message_timestamp`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `talent_landingpage`
--

LOCK TABLES `talent_landingpage` WRITE;
/*!40000 ALTER TABLE `talent_landingpage` DISABLE KEYS */;
INSERT INTO `talent_landingpage` VALUES (2,'There is an arteeest on stageeeeee!','388168_10101095027567859_870031484_n.jpg','2013-11-10 03:01:09'),(2,'There is an ar-teeest on staaage.','388168_10101095027567859_870031484_n.jpg','2013-11-10 03:26:27'),(1,'The rent is too damn high!','Rent-is-too-damn-high.jpg','2013-11-10 03:33:30');
/*!40000 ALTER TABLE `talent_landingpage` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2013-11-14 22:26:18
