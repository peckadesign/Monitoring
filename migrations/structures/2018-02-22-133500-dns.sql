ALTER TABLE `checks`
CHANGE `ip` `dns_value` varchar(255) COLLATE 'utf8_general_ci' NULL AFTER `name`,
CHANGE `last_ip` `last_dns_value` varchar(255) COLLATE 'utf8_general_ci' NULL AFTER `dns_value`;

ALTER TABLE `checks`
ADD `dns_type` varchar(10) NULL;

UPDATE `checks` SET
`dns_type` = 'A'
WHERE `dns_type` IS NULL;
