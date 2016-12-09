ALTER TABLE `checks` ADD `count_difference` INT  NULL  DEFAULT NULL  AFTER `last_count`;
ALTER TABLE `checks` ADD `previous_count` INT  NULL  DEFAULT NULL  AFTER `count_difference`;
