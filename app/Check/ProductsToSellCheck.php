<?php

namespace Pd\Monitoring\Check;

/**
 * @property string $url
 * @property int|NULL $count
 * @property int|NULL $countDifference
 * @property int|NULL $previousCount
 * @property int|NULL $lastCount
 */
class ProductsToSellCheck extends Check
{

	public function __construct()
	{
		parent::__construct();
		$this->type = ICheck::TYPE_PRODUCTS_TO_SELL;
	}


	public function getterStatus(): int
	{
		if ( ! $this->lastCount) {
			return ICheck::STATUS_ERROR;
		} else {
			if ($this->count > 0 && $this->lastCount < $this->count) {
				return ICheck::STATUS_ALERT;
			}
			if ($this->countDifference > 0 && ( ! $this->previousCount || ($this->previousCount - $this->countDifference) > $this->lastCount)) {
				return ICheck::STATUS_ALERT;
			}

			return ICheck::STATUS_OK;
		}
	}


	public function getTitle(): string
	{
		return 'Počet prodejných produktů';
	}
}
