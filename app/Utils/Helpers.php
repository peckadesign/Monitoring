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

			if ($timeFrom[0] > $timeTo[0]) {
				$nowFrom = $now->sub(new \DateInterval('P1D'));
			} else {
				$nowFrom = $now;
			}
			$pausedFrom = $nowFrom->setTime((int) $timeFrom[0], (int) $timeFrom[1]);

			$pausedTo = $now->setTime((int) $timeTo[0], (int) $timeTo[1]);

			if ($now >= $pausedFrom && $now <= $pausedTo) {
				return TRUE;
			}
		}

		return FALSE;
	}

}
