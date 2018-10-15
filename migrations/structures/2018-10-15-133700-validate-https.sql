ALTER TABLE `checks`
ADD `validate_https` tinyint(1) unsigned NULL;

UPDATE `checks` SET
`validate_https` = '1'
WHERE `type` IN (7,8);
