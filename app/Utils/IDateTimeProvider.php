<?php declare(strict_types = 1);

namespace Pd\Monitoring\Utils;

interface IDateTimeProvider
{

	public function getDateTime(): \DateTimeInterface;

}
