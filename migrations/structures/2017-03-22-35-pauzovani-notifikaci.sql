ALTER TABLE `projects`
ADD `paused_to` varchar(255) NULL AFTER `maintenance`,
ADD `paused_from` varchar(255) NULL AFTER `paused_to`;

