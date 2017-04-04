<?php

namespace Pd\Monitoring\Check\Listeners;

class DispatchProjectChangeListener implements \Pd\Monitoring\Check\IOnCheckChange, \Pd\Monitoring\Project\IOnProjectChange
{

	/**
	 * @var \Kdyby\RabbitMq\IProducer
	 */
	private $producer;


	public function __construct(
		\Kdyby\RabbitMq\IProducer $producer
	) {
		$this->producer = $producer;
	}


	public function onCheckChange(\Pd\Monitoring\Check\Check $check): void
	{
		$this->onProjectChange($check->project);
	}


	public function onProjectChange(\Pd\Monitoring\Project\Project $project): void
	{
		$this->producer->publish('', $project->id);
	}
}
