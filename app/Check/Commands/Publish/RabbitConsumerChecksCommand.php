<?php

namespace Pd\Monitoring\Check\Commands\Publish;

use Pd\Monitoring\Commands\TNamedCommand;

class RabbitConsumerChecksCommand extends PublishChecksCommand
{
	use TNamedCommand;

	protected function getConditions(): array
	{
		return [
			'type' => \Pd\Monitoring\Check\ICheck::TYPE_RABBIT_CONSUMERS,
		];
	}

}
