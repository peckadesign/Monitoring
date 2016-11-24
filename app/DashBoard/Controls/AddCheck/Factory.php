<?php

namespace Pd\Monitoring\DashBoard\Controls\AddCheck;

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


	public function create(\Pd\Monitoring\Project\Project $project, int $type) : CheckControl
	{
		switch ($type) {
			case \Pd\Monitoring\Check\ICheck::TYPE_ALIVE:
				$control = new AliveCheckControl($project, $type, $this->formFactory, $this->checksRepository);
				break;

			case \Pd\Monitoring\Check\ICheck::TYPE_TERM:
				$control = new TermCheckControl($project, $type, $this->formFactory, $this->checksRepository);
				break;

			default:
				throw new \InvalidArgumentException();
				break;
		}

		return $control;
	}
}
