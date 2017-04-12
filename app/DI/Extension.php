<?php declare(strict_types=1);

namespace Pd\Monitoring\DI;

use Nette;
use Pd;


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


	public function afterCompile(Nette\PhpGenerator\ClassType $class)
	{
		parent::afterCompile($class);

		$container = $this->getContainerBuilder();

		/** @var Pd\Monitoring\Check\Listeners\NotifyWebSocketListener $notifyWebSocketListener */
		$notifyWebSocketListener = $container->getByType(Pd\Monitoring\Check\Listeners\NotifyWebSocketListener::class);

		/** @var \Nextras\Orm\Repository\Repository $projectsRepository */
		$projectsRepository = $container->getByType(Pd\Monitoring\Check\ChecksRepository::class);

		$projectsRepository->onFlush[] = function ($persisted, $removed) use ($notifyWebSocketListener) {
			foreach ($persisted as $entity) {
				$notifyWebSocketListener->onCheckChange($entity);
			}
		};
	}

}
