ALTER TABLE `users`
CHANGE `slack_id` `slack_id` varchar(255) COLLATE 'utf8_general_ci' NULL;

UPDATE `users` SET
`slack_id` = NULL
WHERE `slack_id` = '';
