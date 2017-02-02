<?php

namespace Pd\Monitoring\Check;

use Nextras;


class SlackNotifyLocksRepository extends Nextras\Orm\Repository\Repository
{

	public static function getEntityClassNames()
	{
		return [
			SlackNotifyLock::class,
		];
	}

}
