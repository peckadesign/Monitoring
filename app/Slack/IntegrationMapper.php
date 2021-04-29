<?php declare(strict_types = 1);

namespace Pd\Monitoring\Slack;

class IntegrationMapper extends \Nextras\Orm\Mapper\Mapper
{

	public function getTableName(): string
	{
		return 'slack_integration';
	}

}
