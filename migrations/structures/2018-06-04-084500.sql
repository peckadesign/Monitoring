UPDATE `checks` SET
`last_check` = addtime(`last_check`, '2:00:00'),
`last_validdate` = addtime(`last_validdate`, '2:00:00'),
`term` = addtime(`term`, '2:00:00'),
`last_modified` = addtime(`last_modified`, '2:00:00');

UPDATE `projects` SET
`maintenance` = addtime(`maintenance`, '2:00:00');

UPDATE `slack_notify_locks` SET
`locked` = addtime(`locked`, '2:00:00');
