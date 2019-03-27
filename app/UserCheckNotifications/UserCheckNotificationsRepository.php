<?php declare(strict_types = 1);

namespace Pd\Monitoring\UserCheckNotifications;

/**
 * @method UserCheckNotifications getBy(array $conds)
 */
class UserCheckNotificationsRepository extends \Nextras\Orm\Repository\Repository
{

	public static function getEntityClassNames(): array
	{
		return [
			UserCheckNotifications::class,
		];
	}


	public function checkIfUserHasCheckNotifications(\Pd\Monitoring\User\User $user, \Pd\Monitoring\Check\Check $check): bool
	{
		return (bool) $this->getBy(["user" => $user, "check" => $check]);
	}


	public function deleteUserCheckNotifications(\Pd\Monitoring\User\User $user, \Pd\Monitoring\Check\Check $check): void
	{
		$entity = $this->getBy(["user" => $user, "check" => $check]);
		$this->removeAndFlush($entity);
	}

}
