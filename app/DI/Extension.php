<?php

namespace Pd\Monitoring\DI;

use Pd;
use Nette;


class Extension extends Nette\DI\CompilerExtension
{

	public function beforeCompile()
	{
		$builder = $this->getContainerBuilder();
		$userStorageDefinitionName = $builder->getByType(Nette\Security\IUserStorage::class) ?: 'nette.userStorage';
		$builder
			->getDefinition($userStorageDefinitionName)
			->setFactory(Pd\Monitoring\User\UserStorage::class)
		;
	}

}
