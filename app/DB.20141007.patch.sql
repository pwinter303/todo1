SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

ALTER TABLE `db508430361`.`todo`
DROP FOREIGN KEY `fk_todo_todoGroup1`;

ALTER TABLE `db508430361`.`todo`
ADD CONSTRAINT `fk_todo_todoGroup1`
  FOREIGN KEY (`group_id`)
  REFERENCES `db508430361`.`todo_group` (`group_id`)
  ON DELETE CASCADE
  ON UPDATE NO ACTION;

CREATE TABLE IF NOT EXISTS `db508430361`.`demo_customer` (
  `customer_id` INT(11) NOT NULL,
  `last_used_ts` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `last_reset_ts` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX `fk_demo_customer_customer1_idx` (`customer_id` ASC),
  PRIMARY KEY (`customer_id`),
  CONSTRAINT `fk_demo_customer_customer1`
    FOREIGN KEY (`customer_id`)
    REFERENCES `db508430361`.`customer` (`customer_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = latin1
COLLATE = latin1_general_ci;


ALTER TABLE `db508430361`.`customer`
ADD COLUMN `referral_email` VARCHAR(145) NULL DEFAULT NULL AFTER `updated_ts`;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;




