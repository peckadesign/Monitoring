<?php declare(strict_types = 1);

namespace Pd\Monitoring\UsersFavoriteProject;

/**
 * @method UsersFavoriteProject getBy(array $conds)
 */
class UsersFavoriteProjectRepository extends \Nextras\Orm\Repository\Repository
{

	public static function getEntityClassNames(): array
	{
		return [
			UsersFavoriteProject::class,
		];
	}


	public function checkIfUserHasFavoriteProject(\Pd\Monitoring\User\User $user, \Pd\Monitoring\Project\Project $project): bool
	{
		return (bool) $this->getBy(["user" => $user, "project" => $project]);
	}


	public function deleteFavoriteProject(\Pd\Monitoring\User\User $user, \Pd\Monitoring\Project\Project $project)
	{
		$entity = $this->getBy(["user" => $user, "project" => $project]);
		$this->removeAndFlush($entity);
	}
}
