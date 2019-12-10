<?php declare(strict_types = 1);

namespace Pd\Monitoring\Utils;

final class Helpers
{

	private function __construct()
	{
		throw new \RuntimeException();
	}


	public static function dateTime($s): string
	{
		return $s instanceof \DateTimeInterface ? $s->format('j. n. Y H:i:s') : $s;
	}


	public static function plural(int $count, string $zero, string $one, string $two): string
	{
		if ($count === 1) {
			return $one;
		} elseif ($count >= 2 && $count <= 4) {
			return $two;
		} else {
			return $zero;
		}
	}


	public static function isInTimeInterval(\DateTimeImmutable $now, ?string $from, ?string $to): bool
	{
		if ($from && $to) {
			$timeFrom = \explode(':', $from);
			$timeFrom = \array_map('intval', $timeFrom);

			$timeTo = \explode(':', $to);
			$timeTo = \array_map('intval', $timeTo);

			$fromDateTime = $now->setTime($timeFrom[0], $timeFrom[1], 0, 0);
			$toDateTime = $now->setTime($timeTo[0], $timeTo[1], 0, 0);
			$endOfDay = $now->setTime(24, 00, 0, 0);
			$startOfDay = $now->setTime(0, 0, 0, 0);

			if ($fromDateTime > $toDateTime) {
				return
					($now >= $fromDateTime && $now <= $endOfDay)
					||
					($now >= $startOfDay && $now <= $toDateTime);
			}

			if ($fromDateTime < $toDateTime) {
				return $fromDateTime <= $now && $now <= $toDateTime;
			}

			return TRUE;
		}

		return FALSE;
	}

}
