CREATE TABLE `users_favorite_project` (
  `user` int(11) NOT NULL,
  `project` int(11) NOT NULL,
  PRIMARY KEY (`user`,`project`),
  KEY `users_favorite_project_ibfk_2` (`project`),
  CONSTRAINT `users_favorite_project_ibfk_1` FOREIGN KEY (`user`) REFERENCES `users` (`id`),
  CONSTRAINT `users_favorite_project_ibfk_2` FOREIGN KEY (`project`) REFERENCES `projects` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_czech_ci;
