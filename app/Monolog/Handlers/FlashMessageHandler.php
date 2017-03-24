<?php declare(strict_types = 1);

namespace Pd\Monitoring\Monolog\Handlers;

use Kdyby;
use Monolog;
use Nette;
use Pd;


class FlashMessageHandler extends Monolog\Handler\AbstractProcessingHandler
{

	/**
	 * @var Nette\Application\UI\Control
	 */
	private $control;

	/**
	 * @var Monolog\Formatter\LineFormatter
	 */
	private $formater;

	/**
	 * @var Nette\Security\User
	 */
	private $user;


	public function __construct(Nette\Application\UI\Control $control, Nette\Security\User $user)
	{
		$this->control = $control;
		$this->formater = new Monolog\Formatter\LineFormatter('%datetime%: %message%');
		$this->setFormatter($this->formater);
		$this->level = Monolog\Logger::DEBUG;
		$this->user = $user;
	}


	protected function write(array $record)
	{
		if ($record['level'] > Monolog\Logger::WARNING) {
			$level = Pd\Monitoring\DashBoard\Presenters\BasePresenter::FLASH_MESSAGE_WARNING;
		} else {
			$level = Pd\Monitoring\DashBoard\Presenters\BasePresenter::FLASH_MESSAGE_INFO;
		}

		$this->control->flashMessage($record['formatted'], $level);
	}
}
