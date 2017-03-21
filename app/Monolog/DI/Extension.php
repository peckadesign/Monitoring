<?php

namespace Pd\Monitoring\Monolog\DI;

use Kdyby;
use Nette;


class Extension extends Nette\DI\CompilerExtension
{

	private $defaults = [
		'allowedTypes' => [],
	];


	public function beforeCompile()
	{
		$containerBuilder = $this->getContainerBuilder();

		$config = $this->validateConfig($this->defaults);

		$presenterBridge = $containerBuilder
			->addDefinition($this->prefix('presenterBridge'))
			->setClass(PresenterBridge::class, [$config['allowedTypes']])
		;

		$application = $containerBuilder->getDefinition($containerBuilder->getByType(Nette\Application\Application::class));
		$application->addSetup('?->onPresenter[] = ?', ['@self', [$presenterBridge, 'onPresenter']]);
	}


	public function setCompiler(Nette\DI\Compiler $compiler, $name)
	{
		$parent = parent::setCompiler($compiler, $name);

		$monologExtension = new Kdyby\Monolog\DI\MonologExtension();
		$compiler->addExtension('monolog', $monologExtension);

		$clockExtension = new Kdyby\Clock\DI\ClockExtension();
		$compiler->addExtension('clock', $clockExtension);

		return $parent;
	}

}
