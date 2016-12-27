ALTER TABLE `checks` ADD `last_validdate` DATETIME  NULL;
ALTER TABLE `checks` ADD `days_before_warning` INT  NULL  DEFAULT NULL  AFTER `last_validdate`;
