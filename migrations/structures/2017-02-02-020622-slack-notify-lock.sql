CREATE TABLE `slack_notify_locks` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `check` int(11) NOT NULL,
  `status` tinyint(3) NOT NULL,
  `locked` datetime NOT NULL,
  FOREIGN KEY (`check`) REFERENCES `checks` (`id`) ON DELETE CASCADE
) ENGINE='InnoDB' COLLATE 'utf8mb4_czech_ci';
