CREATE TABLE `slack_integration` (
`id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
`name` varchar(255) COLLATE 'utf8mb4_general_ci' NOT NULL,
`hookUrl` varchar(255) COLLATE 'ascii_general_ci' NOT NULL
);

CREATE TABLE `projects_x_slack_integration` (
`project` int(11) NOT NULL,
`slack_integration` int(11) NOT NULL,
FOREIGN KEY (`project`) REFERENCES `projects` (`id`) ON DELETE CASCADE,
FOREIGN KEY (`slack_integration`) REFERENCES `slack_integration` (`id`) ON DELETE RESTRICT
);

ALTER TABLE `projects_x_slack_integration`
ADD UNIQUE `project_slack_integration` (`project`, `slack_integration`),
DROP INDEX `project`;

ALTER TABLE `projects_x_slack_integration`
ADD PRIMARY KEY `project_slack_integration` (`project`, `slack_integration`),
DROP INDEX `project_slack_integration`;
