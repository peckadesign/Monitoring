CREATE TABLE `user_check_notifications` (
`user` int(11) NOT NULL,
`check` int(11) NOT NULL,
PRIMARY KEY (`user`,`check`),
KEY `check` (`check`),
CONSTRAINT `user_check_notifications_ibfk_1` FOREIGN KEY (`user`) REFERENCES `users` (`id`),
CONSTRAINT `user_check_notifications_ibfk_2` FOREIGN KEY (`check`) REFERENCES `checks` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_czech_ci;

ALTER TABLE `user_slack_notifications`
RENAME TO `user_project_notifications`;
