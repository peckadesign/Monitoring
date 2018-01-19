<?php declare(strict_types = 1);

namespace Pd\Monitoring\Check;

/**
 * @property string $url
 * @property int|NULL $lastTimeout
 * @property int|NULL $beforeLastTimeout
 */
class AliveCheck extends Check
{

	public const ALIVE_TIMEOUT = 5000;


	public function __construct()
	{
		parent::__construct();
		$this->type = ICheck::TYPE_ALIVE;
	}


	public function getStatus(): int
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


	public function getterStatusMessage(): string
	{
		$message = '';
		if ($this->lastTimeout) {
			$message .= 'Aktuální odezva je ' . $this->lastTimeout . ' ms';
		} else {
			$message .= 'Aktuálně není žádná odezva';
		}
		$message .= '. ';
		if ($this->beforeLastTimeout) {
			$message .= 'Poslední odezva byla ' . $this->beforeLastTimeout . ' ms';
		} else {
			$message .= 'Předchozí odezvu se nepodařilo zjistit';
		}
		$message .= '.';

		return $message;
	}
}
