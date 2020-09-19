<?php declare(strict_types = 1);

namespace Pd\Monitoring\Check\Commands\Publish;

final class XpathChecksSiteMapCommand extends PublishChecksCommand
{

	use \Pd\Monitoring\Commands\TNamedCommand;


	protected function getConditions(): array
	{
		return [
			'type' => \Pd\Monitoring\Check\ICheck::TYPE_XPATH,
			'siteMap' => TRUE,
		];
	}

}
