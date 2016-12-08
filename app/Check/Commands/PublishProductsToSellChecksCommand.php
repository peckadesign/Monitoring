<?php

namespace Pd\Monitoring\Check\Commands;

use Pd\Monitoring\Commands\TNamedCommand;

class PublishProductsToSellChecksCommand extends PublishChecksCommand
{
	use TNamedCommand;

	protected function getConditions(): array
	{
		return [
			'type' => \Pd\Monitoring\Check\ICheck::TYPE_PRODUCTS_TO_SELL,
		];
	}
}
