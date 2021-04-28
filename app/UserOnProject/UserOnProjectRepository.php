<?php declare(strict_types = 1);

namespace Pd\Monitoring\UserOnProject;

/**
 * @method UserOnProject|null getBy(array $conds)
 */
class UserOnProjectRepository extends \Nextras\Orm\Repository\Repository
{

	public static function getEntityClassNames(): array
	{
		return [
			UserOnProject::class,
		];
	}

}
