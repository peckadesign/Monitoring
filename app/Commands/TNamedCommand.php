<?php declare(strict_types = 1);

namespace Pd\Monitoring\Commands;

use Nette;


trait TNamedCommand
{

	protected function generateName(): string
	{
		$className = __CLASS__;
		$name = substr($className, 0);
		$name = str_replace('Module', '', $name);
		$name = str_replace('Commands', '', $name);
		$name = str_replace('Command', '', $name);
		$name = str_replace('\\', ':', $name);
		$name = str_replace('::', ':', $name);
		$name = Nette\Utils\Strings::replace($name, '~([^:])([A-Z])~', '$1-$2');
		$name = strtolower($name);
		$name = ltrim($name, '-');

		return $name;
	}
}
