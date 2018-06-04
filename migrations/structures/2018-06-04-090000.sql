ALTER TABLE `projects`
ADD `parent` int(11) NULL;

ALTER TABLE `projects`
ADD FOREIGN KEY (`parent`) REFERENCES `projects` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;
