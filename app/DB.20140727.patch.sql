--
-- Schema Sync 0.9.1 Patch Script
-- Created: Mon, Jul 28, 2014
-- Server Version: 5.6.19
-- Apply To: localhost/aws
--

CREATE SCHEMA IF NOT EXISTS `db508430361` DEFAULT CHARACTER SET latin1 COLLATE latin1_general_ci ;
USE `db508430361` ;

CREATE TABLE `account_period` ( `account_period_id` int(11) NOT NULL AUTO_INCREMENT, `begin_dt` date DEFAULT NULL, `end_dt` date DEFAULT NULL, `account_type_cd` tinyint(4) NOT NULL, `account_period_status_cd` tinyint(4) NOT NULL, PRIMARY KEY (`account_period_id`), KEY `fk_account_period_account_type1_idx` (`account_type_cd`), KEY `fk_account_period_account_period_status1_idx` (`account_period_status_cd`), CONSTRAINT `fk_account_period_account_type1` FOREIGN KEY (`account_type_cd`) REFERENCES `account_type` (`account_type_cd`) ON DELETE NO ACTION ON UPDATE NO ACTION, CONSTRAINT `fk_account_period_account_period_status1` FOREIGN KEY (`account_period_status_cd`) REFERENCES `account_period_status` (`account_period_status_cd`) ON DELETE NO ACTION ON UPDATE NO ACTION) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;
CREATE TABLE `account_period_status` ( `account_period_status_cd` tinyint(4) NOT NULL, `description` varchar(45) COLLATE latin1_general_ci DEFAULT NULL, PRIMARY KEY (`account_period_status_cd`)) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;
CREATE TABLE `account_type` ( `account_type_cd` tinyint(4) NOT NULL, `description` varchar(45) COLLATE latin1_general_ci DEFAULT NULL, PRIMARY KEY (`account_type_cd`)) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;
CREATE TABLE `credential_status` ( `credential_cd` int(11) NOT NULL, `description` varchar(45) COLLATE latin1_general_ci DEFAULT NULL, PRIMARY KEY (`credential_cd`)) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;
CREATE TABLE `event` ( `event_id` int(11) NOT NULL AUTO_INCREMENT, `customer_id` int(11) NOT NULL, `create_dt` varchar(45) COLLATE latin1_general_ci DEFAULT NULL, `event_cd` tinyint(4) NOT NULL, `account_period_id` int(11) DEFAULT NULL, PRIMARY KEY (`event_id`), KEY `fk_events_customer1_idx` (`customer_id`), KEY `fk_events_event_descriptions1_idx` (`event_cd`), KEY `fk_event_account_period1_idx` (`account_period_id`), CONSTRAINT `fk_events_customer1` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`) ON DELETE NO ACTION ON UPDATE NO ACTION, CONSTRAINT `fk_events_event_descriptions1` FOREIGN KEY (`event_cd`) REFERENCES `event_description` (`event_cd`) ON DELETE NO ACTION ON UPDATE NO ACTION, CONSTRAINT `fk_event_account_period1` FOREIGN KEY (`account_period_id`) REFERENCES `account_period` (`account_period_id`) ON DELETE NO ACTION ON UPDATE NO ACTION) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;
CREATE TABLE `event_description` ( `event_cd` tinyint(4) NOT NULL, `description` varchar(45) COLLATE latin1_general_ci DEFAULT NULL, PRIMARY KEY (`event_cd`)) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;
CREATE TABLE `payment_method` ( `payment_method_cd` tinyint(4) NOT NULL, `description` varchar(45) COLLATE latin1_general_ci DEFAULT NULL, PRIMARY KEY (`payment_method_cd`)) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;
DROP TABLE `todo_tag_xref`;
DROP TABLE `tag`;
ALTER TABLE `customer` ADD COLUMN `first_name` varchar(145) NULL AFTER `password`, ADD COLUMN `last_name` varchar(145) NULL AFTER `first_name`, ADD COLUMN `guid` varchar(45) NULL AFTER `last_name`, ADD COLUMN `credential_cd` int(11) NOT NULL DEFAULT '0' AFTER `guid`, ADD INDEX `fk_customer_credential_status1_idx` (`credential_cd`) USING BTREE, ADD CONSTRAINT `fk_customer_credential_status1` FOREIGN KEY `fk_customer_credential_status1` (`credential_cd`) REFERENCES `credential_status` (`credential_cd`) ON DELETE NO ACTION ON UPDATE NO ACTION;
ALTER TABLE `payment` ADD COLUMN `event_id` int(11) NOT NULL AFTER `pmt_amt`, ADD COLUMN `payment_method_cd` tinyint(4) NOT NULL AFTER `event_id`, ADD INDEX `fk_payment_events1_idx` (`event_id`) USING BTREE, ADD INDEX `fk_payment_payment_method1_idx` (`payment_method_cd`) USING BTREE, ADD CONSTRAINT `fk_payment_events1` FOREIGN KEY `fk_payment_events1` (`event_id`) REFERENCES `event` (`event_id`) ON DELETE NO ACTION ON UPDATE NO ACTION, ADD CONSTRAINT `fk_payment_payment_method1` FOREIGN KEY `fk_payment_payment_method1` (`payment_method_cd`) REFERENCES `payment_method` (`payment_method_cd`) ON DELETE NO ACTION ON UPDATE NO ACTION;
