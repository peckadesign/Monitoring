<?php declare(strict_types = 1);

namespace Pd\Monitoring\Check\Commands\Publish;

use Pd\Monitoring\Commands\TNamedCommand;

class DnsCnameChecksCommand extends PublishChecksCommand
{
	use TNamedCommand;

	protected function getConditions(): array
	{
		return [
			'type' => \Pd\Monitoring\Check\ICheck::TYPE_DNS_CNAME,
		];
	}

}
