<?php declare(strict_types = 1);

namespace Pd\Monitoring\DashBoard\Controls\AddEditCheck;

class Factory
{

	/**
	 * @var \Pd\Monitoring\Check\ChecksRepository
	 */
	private $checksRepository;

	/**
	 * @var \Pd\Monitoring\DashBoard\Forms\Factory
	 */
	private $formFactory;


	public function __construct(
		\Pd\Monitoring\Check\ChecksRepository $checksRepository,
		\Pd\Monitoring\DashBoard\Forms\Factory $formFactory
	) {
		$this->checksRepository = $checksRepository;
		$this->formFactory = $formFactory;
	}


	public function create(\Pd\Monitoring\Project\Project $project, int $type, \Pd\Monitoring\Check\Check $check = NULL): Control
	{
		switch ($type) {
			case \Pd\Monitoring\Check\ICheck::TYPE_ALIVE:
				$control = new Control($project, $check, new AliveCheckProcessor(), $this->formFactory, $this->checksRepository);
				break;

			case \Pd\Monitoring\Check\ICheck::TYPE_TERM:
				$control = new Control($project, $check, new TermCheckProcessor(), $this->formFactory, $this->checksRepository);
				break;

			case \Pd\Monitoring\Check\ICheck::TYPE_DNS:
				$control = new Control($project, $check, new DnsCheckProcessor(), $this->formFactory, $this->checksRepository);
				break;

			case \Pd\Monitoring\Check\ICheck::TYPE_CERTIFICATE:
				$control = new Control($project, $check, new CertificateCheckProcessor(), $this->formFactory, $this->checksRepository);
				break;

			case \Pd\Monitoring\Check\ICheck::TYPE_FEED:
				$control = new Control($project, $check, new FeedCheckProcessor(), $this->formFactory, $this->checksRepository);
				break;

			case \Pd\Monitoring\Check\ICheck::TYPE_RABBIT_QUEUES:
				$control = new Control($project, $check, new RabbitQueueCheckProcessor(), $this->formFactory, $this->checksRepository);
				break;

			case \Pd\Monitoring\Check\ICheck::TYPE_RABBIT_CONSUMERS:
				$control = new Control($project, $check, new RabbitConsumerCheckProcessor(), $this->formFactory, $this->checksRepository);
				break;

			default:
				throw new \InvalidArgumentException();
				break;
		}

		return $control;
	}
}
