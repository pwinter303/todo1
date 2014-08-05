--
-- Schema Sync 0.9.1 Revert Script
-- Created: Tue, Aug 05, 2014
-- Server Version: 5.5.38-0ubuntu0.14.04.1
-- Apply To: localhost/aws
--

USE `aws`;
ALTER DATABASE `aws` COLLATE=latin1_swedish_ci;
DROP TABLE `account_period`;
DROP TABLE `account_period_status`;
DROP TABLE `account_type`;
DROP TABLE `credential_status`;
DROP TABLE `event`;
DROP TABLE `event_description`;
DROP TABLE `payment_method`;
CREATE TABLE `tag` ( `tag_id` int(11) NOT NULL, `tag_name` varchar(45) COLLATE latin1_general_ci DEFAULT NULL, `customer_id` int(11) NOT NULL, PRIMARY KEY (`tag_id`), KEY `fk_tags_customer1_idx` (`customer_id`), CONSTRAINT `fk_tags_customer1` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`) ON DELETE NO ACTION ON UPDATE NO ACTION) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;
CREATE TABLE `todo_tag_xref` ( `tag_Id` int(11) NOT NULL, PRIMARY KEY (`tag_Id`), KEY `fk_tags_has_todo_tags1_idx` (`tag_Id`), CONSTRAINT `fk_tags_has_todo_tags1` FOREIGN KEY (`tag_Id`) REFERENCES `tag` (`tag_id`) ON DELETE NO ACTION ON UPDATE NO ACTION) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;
ALTER TABLE `customer` DROP COLUMN `email`, DROP COLUMN `first_name`, DROP COLUMN `last_name`, DROP COLUMN `guid`, DROP COLUMN `credential_status_cd`, DROP COLUMN `stripe_customer_id`, DROP COLUMN `display_days_done_todos`, ADD COLUMN `user_name` varchar(145) NULL AFTER `customer_id`, DROP INDEX `fk_customer_credential_status1_idx`, DROP INDEX `user_name_UNIQUE`, ADD UNIQUE INDEX `user_name_UNIQUE` (`user_name`) USING BTREE, DROP FOREIGN KEY `fk_customer_credential_status1`;
ALTER TABLE `payment` DROP COLUMN `payment_id`, DROP COLUMN `payment_dt`, DROP COLUMN `payment_amt`, DROP COLUMN `payment_method_cd`, DROP COLUMN `event_id`, ADD COLUMN `pmt_id` int(11) NOT NULL FIRST, ADD COLUMN `pmt_dt` datetime NULL AFTER `customer_Id`, ADD COLUMN `pmt_amt` decimal(11,2) NULL AFTER `pmt_dt`, MODIFY COLUMN `customer_Id` int(11) NOT NULL auto_increment AFTER `pmt_id`, DROP INDEX `fk_payment_payment_method1_idx`, DROP INDEX `fk_payment_event1_idx`, DROP PRIMARY KEY, ADD PRIMARY KEY (`pmt_id`) USING BTREE, DROP FOREIGN KEY `fk_payment_payment_method1`, DROP FOREIGN KEY `fk_payment_event1`;
