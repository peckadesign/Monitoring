CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `git_hub_id` int(11) NOT NULL,
  `git_hub_name` varchar(255) NOT NULL,
  `git_hub_token` varchar(255) NOT NULL,
  `system_user` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `projects` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
