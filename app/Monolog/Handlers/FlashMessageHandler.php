<?php declare(strict_types = 1);

namespace Pd\Monitoring\Monolog\Handlers;

final class FlashMessageHandler extends \Monolog\Handler\AbstractProcessingHandler
{

	private \Nette\Application\UI\Control $control;

	private \Monolog\Formatter\LineFormatter $formater;


	public function __construct(\Nette\Application\UI\Control $control)
	{
		parent::__construct();

		$this->control = $control;
		$this->formater = new \Monolog\Formatter\LineFormatter('%datetime%: %message%', 'j. n. Y H:i:s');
		$this->setFormatter($this->formater);
		$this->level = \Monolog\Logger::DEBUG;
	}


	protected function write(array $record): void
	{
		if ($record['level'] > \Monolog\Logger::NOTICE) {
			$level = \Pd\Monitoring\DashBoard\Presenters\BasePresenter::FLASH_MESSAGE_ERROR;
		} else {
			$level = \Pd\Monitoring\DashBoard\Presenters\BasePresenter::FLASH_MESSAGE_INFO;
		}

		$this->control->flashMessage($record['formatted'], $level);
	}

}
