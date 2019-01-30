ALTER TABLE `checks`
ADD `xpath` varchar(255) COLLATE 'utf8_general_ci' NULL,
ADD `xpath_result` varchar(255) COLLATE 'utf8_general_ci' NULL,
ADD `xpath_last_result` varchar(255) COLLATE 'utf8_general_ci' NULL;
