CREATE TABLE `user_on_project` (
`user` int(11) NOT NULL,
`project` int(11) NOT NULL,
`view` tinyint(1) NOT NULL,
`edit` tinyint(1) NOT NULL,
`admin` tinyint(1) NOT NULL,
PRIMARY KEY (`user`,`project`),
KEY `user_on_project_ibfk_2` (`project`),
CONSTRAINT `user_on_project_ibfk_1` FOREIGN KEY (`user`) REFERENCES `users` (`id`),
CONSTRAINT `user_on_project_ibfk_2` FOREIGN KEY (`project`) REFERENCES `projects` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_czech_ci;
