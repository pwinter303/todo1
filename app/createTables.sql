SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

DROP SCHEMA IF EXISTS `db508430361` ;
CREATE SCHEMA IF NOT EXISTS `db508430361` DEFAULT CHARACTER SET latin1 COLLATE latin1_general_ci ;
USE `db508430361` ;

-- -----------------------------------------------------
-- Table `db508430361`.`customer`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `db508430361`.`customer` ;

CREATE TABLE IF NOT EXISTS `db508430361`.`customer` (
  `customer_id` INT(11) NOT NULL AUTO_INCREMENT,
  `user_name` VARCHAR(145) CHARACTER SET 'latin1' COLLATE 'latin1_general_ci' NULL DEFAULT NULL,
  `password` VARCHAR(145) CHARACTER SET 'latin1' COLLATE 'latin1_general_ci' NULL DEFAULT NULL,
  PRIMARY KEY (`customer_id`),
  UNIQUE INDEX `user_name_UNIQUE` (`user_name` ASC))
ENGINE = InnoDB
AUTO_INCREMENT = 3
DEFAULT CHARACTER SET = latin1
COLLATE = latin1_general_ci;


-- -----------------------------------------------------
-- Table `db508430361`.`payment`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `db508430361`.`payment` ;

CREATE TABLE IF NOT EXISTS `db508430361`.`payment` (
  `pmt_id` INT(11) NOT NULL,
  `customer_Id` INT(11) NOT NULL AUTO_INCREMENT,
  `pmt_dt` DATETIME NULL DEFAULT NULL,
  `pmt_amt` DECIMAL(11,2) NULL DEFAULT NULL,
  PRIMARY KEY (`pmt_id`),
  INDEX `fk_payments_customer1_idx` (`customer_Id` ASC),
  CONSTRAINT `fk_payments_customer1`
    FOREIGN KEY (`customer_Id`)
    REFERENCES `db508430361`.`customer` (`customer_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = latin1
COLLATE = latin1_general_ci;


-- -----------------------------------------------------
-- Table `db508430361`.`tag`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `db508430361`.`tag` ;

CREATE TABLE IF NOT EXISTS `db508430361`.`tag` (
  `tag_id` INT(11) NOT NULL,
  `tag_name` VARCHAR(45) CHARACTER SET 'latin1' COLLATE 'latin1_general_ci' NULL DEFAULT NULL,
  `customer_id` INT(11) NOT NULL,
  PRIMARY KEY (`tag_id`),
  INDEX `fk_tags_customer1_idx` (`customer_id` ASC),
  CONSTRAINT `fk_tags_customer1`
    FOREIGN KEY (`customer_id`)
    REFERENCES `db508430361`.`customer` (`customer_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = latin1
COLLATE = latin1_general_ci;


-- -----------------------------------------------------
-- Table `db508430361`.`todo_frequency`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `db508430361`.`todo_frequency` ;

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
DROP TABLE IF EXISTS `db508430361`.`todo_priority` ;

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
DROP TABLE IF EXISTS `db508430361`.`todo_status` ;

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
DROP TABLE IF EXISTS `db508430361`.`todo_group` ;

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
DROP TABLE IF EXISTS `db508430361`.`todo_batch` ;

CREATE TABLE IF NOT EXISTS `db508430361`.`todo_batch` (
  `batch_id` INT(11) NOT NULL AUTO_INCREMENT,
  `customer_id` INT(11) NOT NULL,
  `file_name` VARCHAR(145) NULL,
  `upload_dt` DATETIME NULL,
  `count_uploaded` INT NULL,
  `count_error_no_group` INT NULL,
  `count_error_above_limit` INT NULL,
  INDEX `fk_todo_batch_customer1_idx` (`customer_id` ASC),
  PRIMARY KEY (`batch_id`),
  CONSTRAINT `fk_todo_batch_customer1`
    FOREIGN KEY (`customer_id`)
    REFERENCES `db508430361`.`customer` (`customer_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `db508430361`.`todo`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `db508430361`.`todo` ;

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
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 624;


-- -----------------------------------------------------
-- Table `db508430361`.`todo_tag_xref`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `db508430361`.`todo_tag_xref` ;

CREATE TABLE IF NOT EXISTS `db508430361`.`todo_tag_xref` (
  `tag_Id` INT(11) NOT NULL,
  PRIMARY KEY (`tag_Id`),
  INDEX `fk_tags_has_todo_tags1_idx` (`tag_Id` ASC),
  CONSTRAINT `fk_tags_has_todo_tags1`
    FOREIGN KEY (`tag_Id`)
    REFERENCES `db508430361`.`tag` (`tag_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = latin1
COLLATE = latin1_general_ci;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
