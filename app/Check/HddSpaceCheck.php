<?php

namespace Pd\Monitoring\Check;

/**
 * @property string $url
 * @property int $percent
 * @property float|NULL $totalSpace
 * @property float|NULL $freeSpace
 */
class HddSpaceCheck extends Check
{

	public function __construct()
	{
		parent::__construct();
		$this->type = ICheck::TYPE_HDD_SPACE;
	}


	public function getterStatus(): int
	{
		$percent = $this->getFreeSpacePercent();

		if ($percent === NULL) {
			return ICheck::STATUS_ERROR;
		} else {
			if ($this->percent <= $percent) {
				return ICheck::STATUS_OK;
			} else {
				return ICheck::STATUS_ALERT;
			}
		}
	}


	public function getTitle(): string
	{
		return 'Volné místo na disku';
	}


	/**
	 * @return float|null
	 */
	public function getFreeSpacePercent()
	{
		if ($this->freeSpace === NULL || $this->totalSpace === NULL) {
			return NULL;
		}

		return round($this->freeSpace / ($this->totalSpace / 100), 1);
	}
}
