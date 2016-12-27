<?php

namespace Pd\Monitoring\Check\Commands\Publish;

abstract class PublishChecksCommand extends \Symfony\Component\Console\Command\Command
{

	use \Pd\Monitoring\Commands\TNamedCommand;

	/**
	 * @var \Kdyby\RabbitMq\IProducer
	 */
	private $producer;

	/**
	 * @var \Pd\Monitoring\Check\ChecksRepository
	 */
	private $checksRepository;


	public function __construct(
		\Kdyby\RabbitMq\IProducer $producer,
		\Pd\Monitoring\Check\ChecksRepository $checksRepository
	) {
		parent::__construct();

		$this->producer = $producer;
		$this->checksRepository = $checksRepository;
	}


	protected function configure()
	{
		parent::configure();

		$this->setName($this->generateName());
	}


	protected function execute(
		\Symfony\Component\Console\Input\InputInterface $input,
		\Symfony\Component\Console\Output\OutputInterface $output
	) {
		$conditions = $this->getConditions();

		$checks = $this->checksRepository->findAll($conditions);

		foreach ($checks as $check) {
			$this->producer->publish($check->id);
		}

		return 0;
	}


	abstract protected function getConditions(): array;

}
