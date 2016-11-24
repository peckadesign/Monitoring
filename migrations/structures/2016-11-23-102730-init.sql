CREATE TABLE `checks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` int(11) NOT NULL,
  `project` int(11) NOT NULL,
  `url` varchar(255) NULL,
  `timeout` int(11) NULL,
  `lastTimeout` int(11) NULL,
  `status` tinyint(3),
  `last_check` datetime NULL,
  `message` varchar(255) NULL,
  `term` datetime NULL,
  `paused` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
