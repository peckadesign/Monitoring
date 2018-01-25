ALTER TABLE `checks`
ADD `grade` varchar(3) COLLATE 'utf8_general_ci' NULL;

ALTER TABLE `checks`
ADD `last_grade` varchar(3) COLLATE 'utf8_general_ci' NULL;
