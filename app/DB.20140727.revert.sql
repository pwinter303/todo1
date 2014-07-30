--
-- Schema Sync 0.9.1 Revert Script
-- Created: Mon, Jul 28, 2014
-- Server Version: 5.6.19
-- Apply To: localhost/aws
--

CREATE SCHEMA IF NOT EXISTS `db508430361` DEFAULT CHARACTER SET latin1 COLLATE latin1_general_ci ;
USE `db508430361` ;

DROP TABLE `account_period`;
DROP TABLE `account_period_status`;
DROP TABLE `account_type`;
DROP TABLE `credential_status`;
DROP TABLE `event`;
DROP TABLE `event_description`;
DROP TABLE `payment_method`;
CREATE TABLE `tag` ( `tag_id` int(11) NOT NULL, `tag_name` varchar(45) COLLATE latin1_general_ci DEFAULT NULL, `customer_id` int(11) NOT NULL, PRIMARY KEY (`tag_id`), KEY `fk_tags_customer1_idx` (`customer_id`), CONSTRAINT `fk_tags_customer1` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`) ON DELETE NO ACTION ON UPDATE NO ACTION) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;
CREATE TABLE `todo_tag_xref` ( `tag_Id` int(11) NOT NULL, PRIMARY KEY (`tag_Id`), KEY `fk_tags_has_todo_tags1_idx` (`tag_Id`), CONSTRAINT `fk_tags_has_todo_tags1` FOREIGN KEY (`tag_Id`) REFERENCES `tag` (`tag_id`) ON DELETE NO ACTION ON UPDATE NO ACTION) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;
ALTER TABLE `customer` DROP COLUMN `first_name`, DROP COLUMN `last_name`, DROP COLUMN `guid`, DROP COLUMN `credential_cd`, DROP INDEX `fk_customer_credential_status1_idx`, DROP FOREIGN KEY `fk_customer_credential_status1`;
ALTER TABLE `payment` DROP COLUMN `event_id`, DROP COLUMN `payment_method_cd`, DROP INDEX `fk_payment_events1_idx`, DROP INDEX `fk_payment_payment_method1_idx`, DROP FOREIGN KEY `fk_payment_events1`, DROP FOREIGN KEY `fk_payment_payment_method1`;