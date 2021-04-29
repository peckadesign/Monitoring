<?php declare(strict_types = 1);

namespace Pd\Monitoring\Project;

/**
 * @method Project getById(int $id)
 * @method Project getBy(array $conds)
 * @method \Nextras\Orm\Collection\ICollection|Project[] findBy(array $conds)
 * @method \Nextras\Orm\Collection\ICollection|Project[] findDashBoardProjects(array $userFavoriteProjectsIds, ?array $onlyProjectsIds = NULL)
 * @method \Nextras\Orm\Collection\ICollection|Project[] findParentAbleProjects(?Project $project = NULL)
 */
class ProjectsRepository extends \Nextras\Orm\Repository\Repository
{

	public static function getEntityClassNames(): array
	{
		return [
			Project::class,
		];
	}

}
