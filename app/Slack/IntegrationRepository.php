<?php declare(strict_types = 1);

namespace Pd\Monitoring\Slack;

/**
 * @method \Pd\Monitoring\Slack\Integration|null getById(int $id)
 */
class IntegrationRepository extends \Nextras\Orm\Repository\Repository
{

	public static function getEntityClassNames(): array
	{
		return [
			Integration::class,
		];
	}

}
