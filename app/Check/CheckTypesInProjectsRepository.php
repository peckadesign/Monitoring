<?php declare(strict_types = 1);

namespace Pd\Monitoring\Check;

class CheckTypesInProjectsRepository extends \Nextras\Orm\Repository\Repository
{

	public static function getEntityClassNames(): array
	{
		return [
			CheckTypesInProject::class,
		];
	}

}
