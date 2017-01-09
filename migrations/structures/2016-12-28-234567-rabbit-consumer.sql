ALTER TABLE `checks` ADD `queues` VARCHAR(256)  NULL  DEFAULT NULL;
ALTER TABLE `checks` ADD `minimum_consumer_count` VARCHAR(256)  NULL  DEFAULT NULL  AFTER `queues`;
ALTER TABLE `checks` ADD `last_consumer_count` VARCHAR(256)  NULL  DEFAULT NULL  AFTER `minimum_consumer_count`;
ALTER TABLE `checks` ADD `login` VARCHAR(128)  NULL  DEFAULT NULL  AFTER `last_consumer_count`;
ALTER TABLE `checks` ADD `password` VARCHAR(128)  NULL  DEFAULT NULL  AFTER `login`;
