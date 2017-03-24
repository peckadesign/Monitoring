<?php declare(strict_types = 1);

namespace Pd\Monitoring\Project;

/**
 * @method Project getById(int $id)
 * @method Project getBy(array $conds)
 */
class ProjectsRepository extends \Nextras\Orm\Repository\Repository
{

	public static function getEntityClassNames()
	{
		return [
			Project::class,
		];
	}
}
