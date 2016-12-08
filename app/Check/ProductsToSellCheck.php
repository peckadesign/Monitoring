<?php

namespace Pd\Monitoring\Check;

/**
 * @property string $url
 * @property int $count
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
			if ($this->lastCount >= $this->count) {
				return ICheck::STATUS_OK;
			} else {
				return ICheck::STATUS_ALERT;
			}
		}
	}


	public function getTitle(): string
	{
		return 'Počet prodejných produktů';
	}
}
