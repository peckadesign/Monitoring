<?php declare(strict_types = 1);

namespace Pd\Monitoring\UserSlackNotifications;

/**
 * @method UserSlackNotifications getBy(array $conds)
 */
class UserSlackNotificationsRepository extends \Nextras\Orm\Repository\Repository
{

	public static function getEntityClassNames(): array
	{
		return [
			UserSlackNotifications::class,
		];
	}


	public function checkIfUserHasSlackNotifications(\Pd\Monitoring\User\User $user,\Pd\Monitoring\Project\Project $project): bool
	{
		return (bool) $this->getBy(["user" => $user, "project" => $project]);
	}

	public function deleteUserSlackNotifications(\Pd\Monitoring\User\User $user,\Pd\Monitoring\Project\Project $project)
	{
		$entity = $this->getBy(["user" => $user, "project" => $project]);
		$this->removeAndFlush($entity);
	}
}
