<?php declare(strict_types = 1);

namespace Pd\Monitoring\Utils;

final class CurrentDateTimeProvider implements IDateTimeProvider
{

	public function getDateTime(): \DateTimeInterface
	{
		return new \DateTimeImmutable();
	}

}
