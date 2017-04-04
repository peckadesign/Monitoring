<?php

namespace Pd\Monitoring\Check\Listeners;

class NotifyWebSocketListener implements \Pd\Monitoring\Check\IOnCheckChange
{

	/**
	 * @var \Kdyby\RabbitMq\IProducer
	 */
	private $producer;


	public function __construct(
		\Kdyby\RabbitMq\IProducer $producer,
		\Pd\Monitoring\Check\ChecksRepository $checksRepository
	) {
		$this->producer = $producer;
	}


	public function onCheckChange(\Pd\Monitoring\Check\Check $check): void
	{
		$this->producer->publish('', $check->id);
	}
}
