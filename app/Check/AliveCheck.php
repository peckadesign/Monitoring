<?php

namespace Pd\Monitoring\Check;

/**
 * @property string $url
 * @property int|NULL $lastTimeout
 * @property int|NULL $beforeLastTimeout
 */
class AliveCheck extends Check
{

	const ALIVE_TIMEOUT = 5000;


	public function __construct()
	{
		parent::__construct();
		$this->type = ICheck::TYPE_ALIVE;
	}


	public function getterStatus(): int
	{
		if ( ! $this->lastTimeout && ! $this->beforeLastTimeout) {
			return ICheck::STATUS_ERROR;
		} elseif ( ! $this->lastTimeout || ! $this->beforeLastTimeout) {
			return ICheck::STATUS_ALERT;
		} else {
			if (($this->lastTimeout + $this->beforeLastTimeout) / 2 <= self::ALIVE_TIMEOUT) {
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
