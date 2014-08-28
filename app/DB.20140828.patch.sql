ALTER TABLE `account_period`
ADD COLUMN `created_ts` timestamp not null default 0,
ADD COLUMN `updated_ts` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER `created_ts`;

ALTER TABLE `customer` 
ADD COLUMN `created_ts` timestamp not null default 0,
ADD COLUMN `updated_ts` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER `created_ts`;

ALTER TABLE `event` 
ADD COLUMN `created_ts` timestamp not null default 0,
ADD COLUMN `updated_ts` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER `created_ts`;

ALTER TABLE `payment` 
ADD COLUMN `created_ts` timestamp not null default 0,
ADD COLUMN `updated_ts` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER `created_ts`;

ALTER TABLE `todo` 
ADD COLUMN `created_ts` timestamp not null default 0,
ADD COLUMN `updated_ts` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER `created_ts`;

ALTER TABLE `todo_batch` 
ADD COLUMN `created_ts` timestamp not null default 0,
ADD COLUMN `updated_ts` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER `created_ts`;

ALTER TABLE `todo_group` 
ADD COLUMN `created_ts` timestamp not null default 0,
ADD COLUMN `updated_ts` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER `created_ts`;
