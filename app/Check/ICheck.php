<?php

namespace Pd\Monitoring\Check;

interface ICheck
{

	const TYPE_ALIVE = 0;
	const TYPE_TERM = 1;
	const TYPE_DNS = 3;
	const TYPE_PRODUCTS_TO_SELL = 7;

	const STATUS_OK = 0;
	const STATUS_ALERT = 1;
	const STATUS_ERROR = 2;


	public function getType() : int;


	public function getTitle() : string;

}
