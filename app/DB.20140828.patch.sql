ALTER TABLE `account_period` drop created_ts;
ALTER TABLE `account_period` drop updated_ts;

ALTER TABLE `customer` drop created_ts;
ALTER TABLE `customer` drop updated_ts;

ALTER TABLE `event` drop created_ts;
ALTER TABLE `event` drop updated_ts;

ALTER TABLE `payment` drop created_ts;
ALTER TABLE `payment` drop updated_ts;

ALTER TABLE `todo` drop created_ts;
ALTER TABLE `todo` drop updated_ts;

ALTER TABLE `todo_batch` drop created_ts;
ALTER TABLE `todo_batch` drop updated_ts;

ALTER TABLE `todo_group` drop created_ts;
ALTER TABLE `todo_group` drop updated_ts;

DROP TRIGGER IF EXISTS account_period_inserts;
DROP TRIGGER IF EXISTS customer_inserts;
DROP TRIGGER IF EXISTS event_inserts;
DROP TRIGGER IF EXISTS payment_inserts;
DROP TRIGGER IF EXISTS todo_inserts;
DROP TRIGGER IF EXISTS todo_batch_inserts;
DROP TRIGGER IF EXISTS todo_group_inserts;

ALTER TABLE `account_period`
ADD COLUMN `created_ts` timestamp NULL,
ADD COLUMN `updated_ts` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;
CREATE TRIGGER account_period_inserts BEFORE INSERT ON account_period FOR EACH ROW SET NEW.created_ts = CURRENT_TIMESTAMP;

ALTER TABLE `customer` 
ADD COLUMN `created_ts` timestamp NULL,
ADD COLUMN `updated_ts` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;
CREATE TRIGGER customer_inserts BEFORE INSERT ON customer FOR EACH ROW SET NEW.created_ts = CURRENT_TIMESTAMP;

ALTER TABLE `event` 
ADD COLUMN `created_ts` timestamp NULL,
ADD COLUMN `updated_ts` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;
CREATE TRIGGER event_inserts BEFORE INSERT ON event FOR EACH ROW SET NEW.created_ts = CURRENT_TIMESTAMP;

ALTER TABLE `payment` 
ADD COLUMN `created_ts` timestamp NULL,
ADD COLUMN `updated_ts` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;
CREATE TRIGGER payment_inserts BEFORE INSERT ON payment FOR EACH ROW SET NEW.created_ts = CURRENT_TIMESTAMP;

ALTER TABLE `todo` 
ADD COLUMN `created_ts` timestamp NULL,
ADD COLUMN `updated_ts` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;
CREATE TRIGGER todo_inserts BEFORE INSERT ON todo FOR EACH ROW SET NEW.created_ts = CURRENT_TIMESTAMP;


ALTER TABLE `todo_batch` 
ADD COLUMN `created_ts` timestamp NULL,
ADD COLUMN `updated_ts` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;
CREATE TRIGGER todo_batch_inserts BEFORE INSERT ON todo_batch FOR EACH ROW SET NEW.created_ts = CURRENT_TIMESTAMP;

ALTER TABLE `todo_group` 
ADD COLUMN `created_ts` timestamp NULL,
ADD COLUMN `updated_ts` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;
CREATE TRIGGER todo_group_inserts BEFORE INSERT ON todo_group FOR EACH ROW SET NEW.created_ts = CURRENT_TIMESTAMP;
