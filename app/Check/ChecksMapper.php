<?php declare(strict_types = 1);

namespace Pd\Monitoring\Check;

class ChecksMapper extends \Nextras\Orm\Mapper\Mapper
{

	protected function createStorageReflection()
	{
		$reflection = parent::createStorageReflection();
		$reflection->addMapping('value', 'number_value');

		return $reflection;
	}

}
