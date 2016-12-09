ALTER TABLE `checks` ADD `count` INT  NULL  DEFAULT NULL;
ALTER TABLE `checks` ADD `last_count` INT  NULL  DEFAULT NULL  AFTER `count`;
