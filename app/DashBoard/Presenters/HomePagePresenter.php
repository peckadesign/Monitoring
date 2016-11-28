<?php

namespace Pd\Monitoring\DashBoard\Presenters;

class HomePagePresenter extends BasePresenter
{

	use \Pd\Monitoring\DashBoard\Controls\Refresh\TFactory;

	/**
	 * @var \Pd\Monitoring\DashBoard\Controls\Project\IFactory
	 */
	private $projectControlFactory;

	/**
	 * @var \Pd\Monitoring\Project\ProjectsRepository
	 */
	private $projectsRepository;


	public function __construct(
		\Pd\Monitoring\DashBoard\Controls\Project\IFactory $projectControlFactory,
		\Pd\Monitoring\Project\ProjectsRepository $projectsRepository
	) {
		$this->projectControlFactory = $projectControlFactory;
		$this->projectsRepository = $projectsRepository;
	}


	public function renderDefault()
	{
		$this->template->projects = $this->projectsRepository->findAll();
	}


	protected function createComponentProject() : \Nette\Application\UI\Multiplier
	{
		$cb = function ($id) {
			$project = $this->projectsRepository->getById($id);
			$control = $this->projectControlFactory->create($project);

			return $control;
		};

		$control = new \Nette\Application\UI\Multiplier($cb);

		return $control;
	}
}
