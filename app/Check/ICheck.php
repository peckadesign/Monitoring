<?php declare(strict_types = 1);

namespace Pd\Monitoring\Check;

interface ICheck
{

	public const TYPE_ALIVE = 0;
	public const TYPE_TERM = 1;
	public const TYPE_NUMBER_VALUE = 2;
	public const TYPE_DNS = 3;
	public const TYPE_CERTIFICATE = 4;
	public const TYPE_HTTP_STATUS_CODE = 5;
	public const TYPE_FEED = 6;
	public const TYPE_RABBIT_QUEUES = 7;
	public const TYPE_RABBIT_CONSUMERS = 8;
	public const TYPE_XPATH = 9;

	public const STATUS_OK = 0;
	public const STATUS_ALERT = 1;
	public const STATUS_ERROR = 2;


	public function getType() : int;


	public function getTitle() : string;

}
