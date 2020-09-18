<?php declare(strict_types = 1);

namespace Pd\Monitoring\DI;

class Extension extends \Nette\DI\CompilerExtension
{

	public function beforeCompile()
	{
		$builder = $this->getContainerBuilder();
		$userStorageDefinitionName = $builder->getByType(\Nette\Security\IUserStorage::class) ?: 'nette.userStorage';
		$builder
			->getDefinition($userStorageDefinitionName)
			->setFactory(\Pd\Monitoring\User\UserStorage::class)
		;

		/** @var \Nette\DI\Definitions\ServiceDefinition $application */
		$application = $builder->getDefinition($builder->getByType(\Pd\Monitoring\Check\ChecksRepository::class));
		$application->addSetup('?->onFlush[] = ?', ['@self', [$builder->getDefinition($builder->getByType(\Pd\Monitoring\Elasticsearch\ChecksExporter::class)), 'export']]);

	}

}
