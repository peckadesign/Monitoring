ALTER TABLE `checks`
CHANGE `dns_value` `dns_value` text COLLATE 'utf8_general_ci' NULL,
CHANGE `last_dns_value` `last_dns_value` text COLLATE 'utf8_general_ci' NULL AFTER `dns_value`;
