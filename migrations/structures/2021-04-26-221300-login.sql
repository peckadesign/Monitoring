ALTER TABLE `users`
ADD `password` char(255) COLLATE 'ascii_general_ci' NULL,
ADD `authtoken` char(20) COLLATE 'utf8_general_ci' NULL,
ADD `email` varchar(255) COLLATE 'ascii_general_ci' NULL;

ALTER TABLE `users`
ADD INDEX `authtoken` (`authtoken`);
