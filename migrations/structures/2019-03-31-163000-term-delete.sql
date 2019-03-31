DELETE FROM `checks`
WHERE `type` = '1';

ALTER TABLE `checks`
DROP `message`,
DROP `term`;
