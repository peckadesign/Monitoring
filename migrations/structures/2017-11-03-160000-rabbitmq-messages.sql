ALTER TABLE `checks`
ADD `maximum_message_count` varchar(255) NULL,
ADD `last_message_count` varchar(255) NULL AFTER `maximum_message_count`;
