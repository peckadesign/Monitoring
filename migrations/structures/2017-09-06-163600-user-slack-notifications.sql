ALTER TABLE `users`
ADD `slack_id` varchar(255) NOT NULL;

CREATE TABLE `user_slack_notifications` (
  `user` int(11) NOT NULL,
  `project` int(11) NOT NULL,
  PRIMARY KEY (`user`,`project`),
  KEY `project` (`project`),
  CONSTRAINT `user_slack_notifications_ibfk_1` FOREIGN KEY (`user`) REFERENCES `users` (`id`),
  CONSTRAINT `user_slack_notifications_ibfk_2` FOREIGN KEY (`project`) REFERENCES `projects` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_czech_ci;
