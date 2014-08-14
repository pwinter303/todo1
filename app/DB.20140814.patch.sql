SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

ALTER TABLE `todo`
ADD COLUMN `parent_todo_id` `parent_todo_id` BIGINT(20) NULL DEFAULT NULL ;
ADD INDEX `fk_todo_todo1_idx` (`parent_todo_id` ASC);

ALTER TABLE `todo`
ADD CONSTRAINT `fk_todo_todo1`
  FOREIGN KEY (`parent_todo_id`)
  REFERENCES `todo` (`todo_id`)
  ON DELETE CASCADE
  ON UPDATE NO ACTION;

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
