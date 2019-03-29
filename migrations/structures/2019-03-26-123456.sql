ALTER TABLE `checks`
ADD `site_map` tinyint(1) NULL;

UPDATE `checks` SET
`site_map` = 0
WHERE `type` = 0;

UPDATE `checks` SET
`site_map` = 0
WHERE `type` = 9;
