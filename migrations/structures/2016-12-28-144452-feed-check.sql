ALTER TABLE `checks` ADD `size` FLOAT  NULL  DEFAULT NULL ;
ALTER TABLE `checks` ADD `last_size` INT  NULL  DEFAULT NULL  AFTER `size`;
ALTER TABLE `checks` ADD `maximum_age` INT  NULL  DEFAULT NULL  AFTER `last_size`;
ALTER TABLE `checks` ADD `last_modified` DATETIME  NULL  AFTER `maximum_age`;
