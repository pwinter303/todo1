--
-- Schema Sync 0.9.1 Patch Script
-- Created: Tue, Aug 05, 2014
-- Server Version: 5.5.38-0ubuntu0.14.04.1
-- Apply To: localhost/aws
--

ALTER DATABASE `db508430361` COLLATE=latin1_general_ci;

CREATE TABLE `account_period_status` ( `account_period_status_cd` tinyint(4) NOT NULL, `description` varchar(45) COLLATE latin1_general_ci DEFAULT NULL, PRIMARY KEY (`account_period_status_cd`)) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;
CREATE TABLE `account_type` ( `account_type_cd` tinyint(4) NOT NULL, `description` varchar(45) COLLATE latin1_general_ci DEFAULT NULL, PRIMARY KEY (`account_type_cd`)) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `event_description` ( `event_cd` tinyint(4) NOT NULL, `description` varchar(45) COLLATE latin1_general_ci DEFAULT NULL, PRIMARY KEY (`event_cd`)) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `event` ( `event_id` int(11) NOT NULL AUTO_INCREMENT, `customer_id` int(11) NOT NULL, `create_dt` datetime DEFAULT NULL, `event_cd` tinyint(4) NOT NULL, `parent_event_id` int(11) DEFAULT NULL, PRIMARY KEY (`event_id`), KEY `fk_events_customer1_idx` (`customer_id`), KEY `fk_events_event_descriptions1_idx` (`event_cd`), KEY `fk_event_event1_idx` (`parent_event_id`), CONSTRAINT `fk_events_customer1` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`) ON DELETE NO ACTION ON UPDATE NO ACTION, CONSTRAINT `fk_events_event_descriptions1` FOREIGN KEY (`event_cd`) REFERENCES `event_description` (`event_cd`) ON DELETE NO ACTION ON UPDATE NO ACTION, CONSTRAINT `fk_event_event1` FOREIGN KEY (`parent_event_id`) REFERENCES `event` (`event_id`) ON DELETE NO ACTION ON UPDATE NO ACTION) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `account_period` ( `account_period_id` int(11) NOT NULL AUTO_INCREMENT, `begin_dt` date DEFAULT NULL, `end_dt` date DEFAULT NULL, `account_type_cd` tinyint(4) NOT NULL, `account_period_status_cd` tinyint(4) NOT NULL, `customer_id` int(11) NOT NULL, `event_id` int(11) NOT NULL, PRIMARY KEY (`account_period_id`), KEY `fk_account_period_account_type1_idx` (`account_type_cd`), KEY `fk_account_period_account_period_status1_idx` (`account_period_status_cd`), KEY `fk_account_period_customer1_idx` (`customer_id`), KEY `fk_account_period_event1_idx` (`event_id`), CONSTRAINT `fk_account_period_account_type1` FOREIGN KEY (`account_type_cd`) REFERENCES `account_type` (`account_type_cd`) ON DELETE NO ACTION ON UPDATE NO ACTION, CONSTRAINT `fk_account_period_account_period_status1` FOREIGN KEY (`account_period_status_cd`) REFERENCES `account_period_status` (`account_period_status_cd`) ON DELETE NO ACTION ON UPDATE NO ACTION, CONSTRAINT `fk_account_period_customer1` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`) ON DELETE NO ACTION ON UPDATE NO ACTION, CONSTRAINT `fk_account_period_event1` FOREIGN KEY (`event_id`) REFERENCES `event` (`event_id`) ON DELETE NO ACTION ON UPDATE NO ACTION) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `credential_status` ( `credential_cd` tinyint(4) NOT NULL, `description` varchar(45) COLLATE latin1_general_ci DEFAULT NULL, PRIMARY KEY (`credential_cd`)) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `payment_method` ( `payment_method_cd` tinyint(4) NOT NULL, `description` varchar(45) COLLATE latin1_general_ci DEFAULT NULL, PRIMARY KEY (`payment_method_cd`)) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

SET foreign_key_checks = 0;
DROP TABLE `todo_tag_xref`;
DROP TABLE `tag`;
SET foreign_key_checks = 1;

SET foreign_key_checks = 0;

ALTER TABLE `customer` CHANGE `user_name` `email` varchar(145) NULL;

ALTER TABLE `customer` ADD COLUMN `first_name` varchar(145) NULL AFTER `password`,
ADD COLUMN `last_name` varchar(145) NULL AFTER `first_name`,
ADD COLUMN `guid` varchar(45) NULL AFTER `last_name`,
ADD COLUMN `credential_status_cd` tinyint(4) NOT NULL DEFAULT '0' AFTER `guid`,
ADD COLUMN `stripe_customer_id` varchar(145) NULL AFTER `credential_status_cd`,
ADD COLUMN `display_days_done_todos` tinyint(4) NULL DEFAULT '5' AFTER `stripe_customer_id`,
ADD INDEX `fk_customer_credential_status1_idx` (`credential_status_cd`) USING BTREE,
DROP INDEX `user_name_UNIQUE`, ADD UNIQUE INDEX `user_name_UNIQUE` (`email`) USING BTREE,
ADD CONSTRAINT `fk_customer_credential_status1` FOREIGN KEY `fk_customer_credential_status1` (`credential_status_cd`)
REFERENCES `credential_status` (`credential_cd`) ON DELETE NO ACTION ON UPDATE NO ACTION;


drop table `payment`;

CREATE TABLE IF NOT EXISTS `payment` (
  `payment_id` INT(11) NOT NULL AUTO_INCREMENT,
  `customer_Id` INT(11) NOT NULL,
  `payment_dt` DATETIME NULL DEFAULT NULL,
  `payment_amt` DECIMAL(11,2) NULL DEFAULT NULL,
  `payment_method_cd` TINYINT(4) NOT NULL,
  `event_id` INT(11) NOT NULL,
  PRIMARY KEY (`payment_id`),
  INDEX `fk_payments_customer1_idx` (`customer_Id` ASC),
  INDEX `fk_payment_payment_method1_idx` (`payment_method_cd` ASC),
  INDEX `fk_payment_event1_idx` (`event_id` ASC),
  CONSTRAINT `fk_payments_customer1`
    FOREIGN KEY (`customer_Id`)
    REFERENCES `customer` (`customer_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_payment_payment_method1`
    FOREIGN KEY (`payment_method_cd`)
    REFERENCES `payment_method` (`payment_method_cd`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_payment_event1`
    FOREIGN KEY (`event_id`)
    REFERENCES `event` (`event_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = latin1
COLLATE = latin1_general_ci;

SET foreign_key_checks = 1;
