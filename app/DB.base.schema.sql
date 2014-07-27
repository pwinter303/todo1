-- MySQL dump 10.13  Distrib 5.5.38, for Linux (x86_64)
--
-- Host: localhost    Database: db508430361
-- ------------------------------------------------------
-- Server version	5.5.38

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
-- Table structure for table `customer`
--

DROP TABLE IF EXISTS `customer`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `customer` (
  `customer_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_name` varchar(145) COLLATE latin1_general_ci DEFAULT NULL,
  `password` varchar(145) COLLATE latin1_general_ci DEFAULT NULL,
  PRIMARY KEY (`customer_id`),
  UNIQUE KEY `user_name_UNIQUE` (`user_name`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `payment`
--

DROP TABLE IF EXISTS `payment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payment` (
  `pmt_id` int(11) NOT NULL,
  `customer_Id` int(11) NOT NULL AUTO_INCREMENT,
  `pmt_dt` datetime DEFAULT NULL,
  `pmt_amt` decimal(11,2) DEFAULT NULL,
  PRIMARY KEY (`pmt_id`),
  KEY `fk_payments_customer1_idx` (`customer_Id`),
  CONSTRAINT `fk_payments_customer1` FOREIGN KEY (`customer_Id`) REFERENCES `customer` (`customer_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tag`
--

DROP TABLE IF EXISTS `tag`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tag` (
  `tag_id` int(11) NOT NULL,
  `tag_name` varchar(45) COLLATE latin1_general_ci DEFAULT NULL,
  `customer_id` int(11) NOT NULL,
  PRIMARY KEY (`tag_id`),
  KEY `fk_tags_customer1_idx` (`customer_id`),
  CONSTRAINT `fk_tags_customer1` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `todo`
--

DROP TABLE IF EXISTS `todo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `todo` (
  `todo_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `group_id` int(11) NOT NULL,
  `task_name` varchar(250) COLLATE latin1_general_ci DEFAULT NULL,
  `due_dt` date DEFAULT NULL,
  `starred` tinyint(1) DEFAULT NULL,
  `priority_cd` tinyint(4) NOT NULL,
  `frequency_cd` tinyint(4) NOT NULL,
  `status_cd` tinyint(4) NOT NULL,
  `note` varchar(4500) COLLATE latin1_general_ci DEFAULT NULL,
  `customer_id` int(11) NOT NULL,
  `done` tinyint(1) DEFAULT NULL,
  `tags` varchar(145) COLLATE latin1_general_ci DEFAULT NULL,
  `done_dt` date DEFAULT NULL,
  `batch_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`todo_id`),
  KEY `fk_todo_todoGroup1_idx` (`group_id`),
  KEY `fk_todo_priority1_idx` (`priority_cd`),
  KEY `fk_todo_frequency1_idx` (`frequency_cd`),
  KEY `fk_todo_status1_idx` (`status_cd`),
  KEY `fk_todo_customer1_idx` (`customer_id`),
  KEY `fk_todo_todo_batch1_idx` (`batch_id`),
  CONSTRAINT `fk_todo_customer1` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_todo_frequency1` FOREIGN KEY (`frequency_cd`) REFERENCES `todo_frequency` (`frequency_cd`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_todo_priority1` FOREIGN KEY (`priority_cd`) REFERENCES `todo_priority` (`priority_cd`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_todo_status1` FOREIGN KEY (`status_cd`) REFERENCES `todo_status` (`status_cd`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_todo_todoGroup1` FOREIGN KEY (`group_id`) REFERENCES `todo_group` (`group_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_todo_todo_batch1` FOREIGN KEY (`batch_id`) REFERENCES `todo_batch` (`batch_id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=673 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `todo_batch`
--

DROP TABLE IF EXISTS `todo_batch`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `todo_batch` (
  `batch_id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_id` int(11) NOT NULL,
  `file_name` varchar(145) COLLATE latin1_general_ci DEFAULT NULL,
  `upload_dt` datetime DEFAULT NULL,
  `count_uploaded` int(11) DEFAULT NULL,
  `count_error_no_group` int(11) DEFAULT NULL,
  `count_error_above_limit` int(11) DEFAULT NULL,
  PRIMARY KEY (`batch_id`),
  KEY `fk_todo_batch_customer1_idx` (`customer_id`),
  CONSTRAINT `fk_todo_batch_customer1` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `todo_frequency`
--

DROP TABLE IF EXISTS `todo_frequency`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `todo_frequency` (
  `frequency_cd` tinyint(4) NOT NULL,
  `frequency_name` varchar(45) COLLATE latin1_general_ci DEFAULT NULL,
  PRIMARY KEY (`frequency_cd`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `todo_group`
--

DROP TABLE IF EXISTS `todo_group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `todo_group` (
  `group_id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_id` int(11) NOT NULL,
  `group_name` varchar(45) COLLATE latin1_general_ci DEFAULT NULL,
  `sort_order` smallint(6) DEFAULT NULL,
  `active` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`group_id`),
  KEY `fk_todoGroup_customer_idx` (`customer_id`),
  CONSTRAINT `fk_todoGroup_customer` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci COMMENT='																																																												';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `todo_priority`
--

DROP TABLE IF EXISTS `todo_priority`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `todo_priority` (
  `priority_cd` tinyint(4) NOT NULL,
  `priority_name` varchar(45) COLLATE latin1_general_ci DEFAULT NULL,
  PRIMARY KEY (`priority_cd`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `todo_status`
--

DROP TABLE IF EXISTS `todo_status`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `todo_status` (
  `status_cd` tinyint(4) NOT NULL,
  `status_name` varchar(45) COLLATE latin1_general_ci DEFAULT NULL,
  PRIMARY KEY (`status_cd`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `todo_tag_xref`
--

DROP TABLE IF EXISTS `todo_tag_xref`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `todo_tag_xref` (
  `tag_Id` int(11) NOT NULL,
  PRIMARY KEY (`tag_Id`),
  KEY `fk_tags_has_todo_tags1_idx` (`tag_Id`),
  CONSTRAINT `fk_tags_has_todo_tags1` FOREIGN KEY (`tag_Id`) REFERENCES `tag` (`tag_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2014-07-27 18:15:44
