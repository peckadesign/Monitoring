ALTER TABLE `checks` ADD `last_code` INT  NULL  DEFAULT NULL;
ALTER TABLE `checks` ADD `code` INT  NULL  DEFAULT NULL  AFTER `last_code`;
