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

}
