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


	public function getterStatus(): int
	{
		if ( ! $this->lastTimeout) {
			return ICheck::STATUS_ERROR;
		} else {
			if ($this->lastTimeout <= $this->timeout) {
				return ICheck::STATUS_OK;
			} else {
				return ICheck::STATUS_ALERT;
			}
		}
	}


	public function getTitle(): string
	{
		return 'Dostupnost URL';
	}
}
