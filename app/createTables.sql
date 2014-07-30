SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

DROP SCHEMA IF EXISTS `db508430361` ;
CREATE SCHEMA IF NOT EXISTS `db508430361` DEFAULT CHARACTER SET latin1 COLLATE latin1_general_ci ;
USE `db508430361` ;

-- -----------------------------------------------------
-- Table `db508430361`.`credential_status`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `db508430361`.`credential_status` (
  `credential_cd` TINYINT(4) NOT NULL,
  `description` VARCHAR(45) NULL,
  PRIMARY KEY (`credential_cd`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `db508430361`.`customer`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `db508430361`.`customer` (
  `customer_id` INT(11) NOT NULL AUTO_INCREMENT,
  `email` VARCHAR(145) CHARACTER SET 'latin1' COLLATE 'latin1_general_ci' NULL DEFAULT NULL,
  `password` VARCHAR(145) CHARACTER SET 'latin1' COLLATE 'latin1_general_ci' NULL DEFAULT NULL,
  `first_name` VARCHAR(145) NULL,
  `last_name` VARCHAR(145) NULL,
  `guid` VARCHAR(45) NULL,
  `credential_status_cd` TINYINT(4) NOT NULL DEFAULT 0,
  PRIMARY KEY (`customer_id`),
  UNIQUE INDEX `user_name_UNIQUE` (`email` ASC),
  INDEX `fk_customer_credential_status1_idx` (`credential_status_cd` ASC),
  CONSTRAINT `fk_customer_credential_status1`
    FOREIGN KEY (`credential_status_cd`)
    REFERENCES `db508430361`.`credential_status` (`credential_cd`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 3
DEFAULT CHARACTER SET = latin1
COLLATE = latin1_general_ci;


-- -----------------------------------------------------
-- Table `db508430361`.`payment_method`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `db508430361`.`payment_method` (
  `payment_method_cd` TINYINT(4) NOT NULL,
  `description` VARCHAR(45) NULL,
  PRIMARY KEY (`payment_method_cd`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `db508430361`.`event_description`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `db508430361`.`event_description` (
  `event_cd` TINYINT(4) NOT NULL,
  `description` VARCHAR(45) NULL,
  PRIMARY KEY (`event_cd`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `db508430361`.`event`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `db508430361`.`event` (
  `event_id` INT(11) NOT NULL AUTO_INCREMENT,
  `customer_id` INT(11) NOT NULL,
  `create_dt` VARCHAR(45) NULL,
  `event_cd` TINYINT(4) NOT NULL,
  `parent_event_id` INT(11) NULL,
  PRIMARY KEY (`event_id`),
  INDEX `fk_events_customer1_idx` (`customer_id` ASC),
  INDEX `fk_events_event_descriptions1_idx` (`event_cd` ASC),
  INDEX `fk_event_event1_idx` (`parent_event_id` ASC),
  CONSTRAINT `fk_events_customer1`
    FOREIGN KEY (`customer_id`)
    REFERENCES `db508430361`.`customer` (`customer_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_events_event_descriptions1`
    FOREIGN KEY (`event_cd`)
    REFERENCES `db508430361`.`event_description` (`event_cd`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_event_event1`
    FOREIGN KEY (`parent_event_id`)
    REFERENCES `db508430361`.`event` (`event_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `db508430361`.`payment`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `db508430361`.`payment` (
  `pmt_id` INT(11) NOT NULL,
  `customer_Id` INT(11) NOT NULL AUTO_INCREMENT,
  `payment_dt` DATETIME NULL DEFAULT NULL,
  `payment_amt` DECIMAL(11,2) NULL DEFAULT NULL,
  `payment_method_cd` TINYINT(4) NOT NULL,
  `event_id` INT(11) NOT NULL,
  PRIMARY KEY (`pmt_id`),
  INDEX `fk_payments_customer1_idx` (`customer_Id` ASC),
  INDEX `fk_payment_payment_method1_idx` (`payment_method_cd` ASC),
  INDEX `fk_payment_event1_idx` (`event_id` ASC),
  CONSTRAINT `fk_payments_customer1`
    FOREIGN KEY (`customer_Id`)
    REFERENCES `db508430361`.`customer` (`customer_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_payment_payment_method1`
    FOREIGN KEY (`payment_method_cd`)
    REFERENCES `db508430361`.`payment_method` (`payment_method_cd`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_payment_event1`
    FOREIGN KEY (`event_id`)
    REFERENCES `db508430361`.`event` (`event_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = latin1
COLLATE = latin1_general_ci;


-- -----------------------------------------------------
-- Table `db508430361`.`todo_frequency`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `db508430361`.`todo_frequency` (
  `frequency_cd` TINYINT(4) NOT NULL,
  `frequency_name` VARCHAR(45) CHARACTER SET 'latin1' COLLATE 'latin1_general_ci' NULL DEFAULT NULL,
  PRIMARY KEY (`frequency_cd`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = latin1
COLLATE = latin1_general_ci;


-- -----------------------------------------------------
-- Table `db508430361`.`todo_priority`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `db508430361`.`todo_priority` (
  `priority_cd` TINYINT(4) NOT NULL,
  `priority_name` VARCHAR(45) CHARACTER SET 'latin1' COLLATE 'latin1_general_ci' NULL DEFAULT NULL,
  PRIMARY KEY (`priority_cd`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = latin1
COLLATE = latin1_general_ci;


-- -----------------------------------------------------
-- Table `db508430361`.`todo_status`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `db508430361`.`todo_status` (
  `status_cd` TINYINT(4) NOT NULL,
  `status_name` VARCHAR(45) CHARACTER SET 'latin1' COLLATE 'latin1_general_ci' NULL DEFAULT NULL,
  PRIMARY KEY (`status_cd`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = latin1
COLLATE = latin1_general_ci;


-- -----------------------------------------------------
-- Table `db508430361`.`todo_group`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `db508430361`.`todo_group` (
  `group_id` INT(11) NOT NULL AUTO_INCREMENT,
  `customer_id` INT(11) NOT NULL,
  `group_name` VARCHAR(45) CHARACTER SET 'latin1' COLLATE 'latin1_general_ci' NULL DEFAULT NULL,
  `sort_order` SMALLINT(6) NULL DEFAULT NULL,
  `active` TINYINT(4) NULL DEFAULT NULL,
  PRIMARY KEY (`group_id`),
  INDEX `fk_todoGroup_customer_idx` (`customer_id` ASC),
  CONSTRAINT `fk_todoGroup_customer`
    FOREIGN KEY (`customer_id`)
    REFERENCES `db508430361`.`customer` (`customer_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 5
DEFAULT CHARACTER SET = latin1
COLLATE = latin1_general_ci
COMMENT = '																																																												';


-- -----------------------------------------------------
-- Table `db508430361`.`todo_batch`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `db508430361`.`todo_batch` (
  `batch_id` INT(11) NOT NULL AUTO_INCREMENT,
  `customer_id` INT(11) NOT NULL,
  `file_name` VARCHAR(145) CHARACTER SET 'latin1' COLLATE 'latin1_general_ci' NULL DEFAULT NULL,
  `upload_dt` DATETIME NULL DEFAULT NULL,
  `count_uploaded` INT(11) NULL DEFAULT NULL,
  `count_error_no_group` INT(11) NULL DEFAULT NULL,
  `count_error_above_limit` INT(11) NULL DEFAULT NULL,
  PRIMARY KEY (`batch_id`),
  INDEX `fk_todo_batch_customer1_idx` (`customer_id` ASC),
  CONSTRAINT `fk_todo_batch_customer1`
    FOREIGN KEY (`customer_id`)
    REFERENCES `db508430361`.`customer` (`customer_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = latin1
COLLATE = latin1_general_ci;


-- -----------------------------------------------------
-- Table `db508430361`.`todo`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `db508430361`.`todo` (
  `todo_id` BIGINT(20) NOT NULL AUTO_INCREMENT,
  `group_id` INT(11) NOT NULL,
  `task_name` VARCHAR(250) CHARACTER SET 'latin1' COLLATE 'latin1_general_ci' NULL DEFAULT NULL,
  `due_dt` DATE NULL DEFAULT NULL,
  `starred` TINYINT(1) NULL DEFAULT NULL,
  `priority_cd` TINYINT(4) NOT NULL,
  `frequency_cd` TINYINT(4) NOT NULL,
  `status_cd` TINYINT(4) NOT NULL,
  `note` VARCHAR(4500) CHARACTER SET 'latin1' COLLATE 'latin1_general_ci' NULL DEFAULT NULL,
  `customer_id` INT(11) NOT NULL,
  `done` TINYINT(1) NULL DEFAULT NULL,
  `tags` VARCHAR(145) CHARACTER SET 'latin1' COLLATE 'latin1_general_ci' NULL DEFAULT NULL,
  `done_dt` DATE NULL DEFAULT NULL,
  `batch_id` INT(11) NULL DEFAULT NULL,
  PRIMARY KEY (`todo_id`),
  INDEX `fk_todo_todoGroup1_idx` (`group_id` ASC),
  INDEX `fk_todo_priority1_idx` (`priority_cd` ASC),
  INDEX `fk_todo_frequency1_idx` (`frequency_cd` ASC),
  INDEX `fk_todo_status1_idx` (`status_cd` ASC),
  INDEX `fk_todo_customer1_idx` (`customer_id` ASC),
  INDEX `fk_todo_todo_batch1_idx` (`batch_id` ASC),
  CONSTRAINT `fk_todo_customer1`
    FOREIGN KEY (`customer_id`)
    REFERENCES `db508430361`.`customer` (`customer_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_todo_frequency1`
    FOREIGN KEY (`frequency_cd`)
    REFERENCES `db508430361`.`todo_frequency` (`frequency_cd`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_todo_priority1`
    FOREIGN KEY (`priority_cd`)
    REFERENCES `db508430361`.`todo_priority` (`priority_cd`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_todo_status1`
    FOREIGN KEY (`status_cd`)
    REFERENCES `db508430361`.`todo_status` (`status_cd`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_todo_todoGroup1`
    FOREIGN KEY (`group_id`)
    REFERENCES `db508430361`.`todo_group` (`group_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_todo_todo_batch1`
    FOREIGN KEY (`batch_id`)
    REFERENCES `db508430361`.`todo_batch` (`batch_id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 642
DEFAULT CHARACTER SET = latin1
COLLATE = latin1_general_ci;


-- -----------------------------------------------------
-- Table `db508430361`.`account_type`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `db508430361`.`account_type` (
  `account_type_cd` TINYINT(4) NOT NULL,
  `description` VARCHAR(45) NULL,
  PRIMARY KEY (`account_type_cd`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `db508430361`.`account_period_status`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `db508430361`.`account_period_status` (
  `account_period_status_cd` TINYINT(4) NOT NULL,
  `description` VARCHAR(45) NULL,
  PRIMARY KEY (`account_period_status_cd`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `db508430361`.`account_period`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `db508430361`.`account_period` (
  `account_period_id` INT(11) NOT NULL AUTO_INCREMENT,
  `begin_dt` DATE NULL,
  `end_dt` DATE NULL,
  `account_type_cd` TINYINT(4) NOT NULL,
  `account_period_status_cd` TINYINT(4) NOT NULL,
  `customer_id` INT(11) NOT NULL,
  `event_id` INT(11) NOT NULL,
  PRIMARY KEY (`account_period_id`),
  INDEX `fk_account_period_account_type1_idx` (`account_type_cd` ASC),
  INDEX `fk_account_period_account_period_status1_idx` (`account_period_status_cd` ASC),
  INDEX `fk_account_period_customer1_idx` (`customer_id` ASC),
  INDEX `fk_account_period_event1_idx` (`event_id` ASC),
  CONSTRAINT `fk_account_period_account_type1`
    FOREIGN KEY (`account_type_cd`)
    REFERENCES `db508430361`.`account_type` (`account_type_cd`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_account_period_account_period_status1`
    FOREIGN KEY (`account_period_status_cd`)
    REFERENCES `db508430361`.`account_period_status` (`account_period_status_cd`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_account_period_customer1`
    FOREIGN KEY (`customer_id`)
    REFERENCES `db508430361`.`customer` (`customer_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_account_period_event1`
    FOREIGN KEY (`event_id`)
    REFERENCES `db508430361`.`event` (`event_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
