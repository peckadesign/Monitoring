<?php

namespace Pd\Monitoring\Check;

/**
 * @property string $url
 * @property int $timeout
 * @property int|NULL $lastTimeout
 */
class AliveCheck extends Check
{
	public function __construct()
	{
		parent::__construct();
		$this->type = ICheck::TYPE_ALIVE;
	}


	public function getTitle() : string
	{
		return 'Dostupnost URL';
	}
}
