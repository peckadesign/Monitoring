<?php declare(strict_types = 1);

namespace Pd\Monitoring\Monolog\DI;

class PresenterBridge
{

	/**
	 * @var \Monolog\Logger
	 */
	private $logger;

	/**
	 * @var array
	 */
	private $allowedTypes;


	public function __construct(
		array $allowedTypes,
		\Kdyby\Monolog\Logger $logger
	) {
		$this->allowedTypes = $allowedTypes;
		$this->logger = $logger;
	}


	public function onPresenter(\Nette\Application\Application $application, \Nette\Application\IPresenter $presenter): void
	{
		$success = FALSE;
		foreach ($this->allowedTypes as $allowedType) {
			$success = $presenter instanceof $allowedType;
		}

		if ( ! $success) {
			return;
		}

		$handler = new \Pd\Monitoring\Monolog\Handlers\FlashMessageHandler($presenter);
		$this->logger->pushHandler($handler);
	}

}
