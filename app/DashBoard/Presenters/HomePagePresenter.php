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

	/**
	 * @var \Pd\Monitoring\UsersFavoriteProject\UsersFavoriteProjectRepository
	 */
	private $usersFavoriteProjectsRepository;

	/**
	 * @var array|\Pd\Monitoring\UsersFavoriteProject\UsersFavoriteProject[]
	 */
	private $usersFavoriteProjects = [];

	/**
	 * @var array|\Pd\Monitoring\Project\Project[]
	 */
	private $projects = [];

	/**
	 * @var array|\Pd\Monitoring\Project\Project[]
	 */
	private $nonFavoriteProjects = [];


	public function __construct(
		\Pd\Monitoring\DashBoard\Controls\Project\IFactory $projectControlFactory,
		\Pd\Monitoring\Project\ProjectsRepository $projectsRepository,
		\Pd\Monitoring\UsersFavoriteProject\UsersFavoriteProjectRepository $usersFavoriteProjectsRepository
	) {
		$this->projectControlFactory = $projectControlFactory;
		$this->projectsRepository = $projectsRepository;
		$this->usersFavoriteProjectsRepository = $usersFavoriteProjectsRepository;
	}


	public function actionDefault()
	{
		$favoriteProjects = $this->usersFavoriteProjectsRepository->findBy(["user" => $this->getUser()->id])->orderBy("this->project->name");

		/** @var \Pd\Monitoring\UsersFavoriteProject\UsersFavoriteProject $favoriteProject */
		foreach ($favoriteProjects as $favoriteProject) {
			$this->usersFavoriteProjects[$favoriteProject->project->id] = $favoriteProject;
			$this->projects[$favoriteProject->project->id] = $favoriteProject->project;
		}

		$allProjects = $this->projectsRepository->findBy(["id!" => array_keys($this->usersFavoriteProjects)])->orderBy("name");
		/** @var \Pd\Monitoring\Project\Project $project */
		foreach ($allProjects as $project) {
			$this->projects[$project->id] = $project;
			$this->nonFavoriteProjects[$project->id] = $project;
		}
	}


	public function renderDefault()
	{
		$this->template->projects = $this->nonFavoriteProjects;
		$this->template->favoriteProjects = $this->usersFavoriteProjects;
	}


	protected function createComponentProject(): \Nette\Application\UI\Multiplier
	{
		$cb = function ($id) {
			$control = $this->projectControlFactory->create($this->projects[$id], $this->usersFavoriteProjects[$id]?? NULL);

			return $control;
		};

		$control = new \Nette\Application\UI\Multiplier($cb);

		return $control;
	}
}
