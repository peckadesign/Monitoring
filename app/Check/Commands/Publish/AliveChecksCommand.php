<?php

namespace Pd\Monitoring\Check\Commands\Publish;


class AliveChecksCommand extends PublishChecksCommand
{

	protected function getConditions(): array
	{
		return [
			'type' => \Pd\Monitoring\Check\ICheck::TYPE_ALIVE,
		];
	}
}
