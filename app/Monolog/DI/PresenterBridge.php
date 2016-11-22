<?php

namespace Pd\Monitoring\Monolog\DI;

use Pd;
use Kdyby;
use Monolog;
use Nette;


class PresenterBridge
{

	/**
	 * @var Monolog\Logger
	 */
	private $logger;

	/**
	 * @var Nette\Security\User
	 */
	private $user;

	/**
	 * @var array
	 */
	private $allowedTypes;


	public function __construct(
		array $allowedTypes,
		Kdyby\Monolog\Logger $logger,
		Nette\Security\User $user
	) {
		$this->allowedTypes = $allowedTypes;
		$this->logger = $logger;
		$this->user = $user;
	}


	public function onPresenter(Nette\Application\Application $application, Nette\Application\IPresenter $presenter)
	{
		$success = FALSE;
		foreach ($this->allowedTypes as $allowedType) {
			$success = $presenter instanceof $allowedType;
		}

		if ( ! $success) {
			return;
		}

		$handler = new Pd\Monitoring\Monolog\Handlers\FlashMessageHandler($presenter, $this->user);
		$this->logger->pushHandler($handler);
	}

}
