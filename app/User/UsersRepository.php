<?php declare(strict_types = 1);

namespace Pd\Monitoring\User;

/**
 * @method User|null getById(int $id)
 * @method User|null getBy(array $conds)
 */
class UsersRepository extends \Nextras\Orm\Repository\Repository
{

	public static function getEntityClassNames(): array
	{
		return [
			User::class,
		];
	}

}
