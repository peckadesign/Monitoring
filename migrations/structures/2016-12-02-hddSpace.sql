/* 16:30:24 countrylife pdp5_countrylife2015_dev */ ALTER TABLE `checks` ADD `percent` INT  NULL  DEFAULT NULL  AFTER `paused`;
/* 16:31:10 countrylife pdp5_countrylife2015_dev */ ALTER TABLE `checks` ADD `total_space` DOUBLE  NULL  DEFAULT NULL  AFTER `percent`;
/* 16:31:22 countrylife pdp5_countrylife2015_dev */ ALTER TABLE `checks` ADD `free_space` DOUBLE  NULL  DEFAULT NULL  AFTER `totalSpace`;
