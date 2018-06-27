<?php declare(strict_types = 1);

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
	 * @var \Pd\Monitoring\UserSlackNotifications\UserSlackNotificationsRepository
	 */
	private $userSlackNotificationsRepository;

	/**
	 * @var array|\Pd\Monitoring\UserSlackNotifications\UserSlackNotifications[]
	 */
	private $userSlackNotifications = [];

	/**
	 * @var array|\Pd\Monitoring\Project\Project[]
	 */
	private $projects = [];

	/**
	 * @var array|\Pd\Monitoring\Project\Project[]
	 */
	private $nonFavoriteProjects = [];

	/**
	 * @var array|\Pd\Monitoring\Project\Project[]
	 */
	private $referenceProjects = [];


	public function __construct(
		\Pd\Monitoring\DashBoard\Controls\Project\IFactory $projectControlFactory,
		\Pd\Monitoring\Project\ProjectsRepository $projectsRepository,
		\Pd\Monitoring\UsersFavoriteProject\UsersFavoriteProjectRepository $usersFavoriteProjectsRepository,
		\Pd\Monitoring\UserSlackNotifications\UserSlackNotificationsRepository $userSlackNotificationsRepository
	) {
		parent::__construct();
		$this->projectControlFactory = $projectControlFactory;
		$this->projectsRepository = $projectsRepository;
		$this->usersFavoriteProjectsRepository = $usersFavoriteProjectsRepository;
		$this->userSlackNotificationsRepository = $userSlackNotificationsRepository;
	}


	public function actionDefault(): void
	{
		$favoriteProjects = $this->usersFavoriteProjectsRepository->findBy(["user" => $this->getUser()->id])->orderBy("this->project->name");

		/** @var \Pd\Monitoring\UsersFavoriteProject\UsersFavoriteProject $favoriteProject */
		foreach ($favoriteProjects as $favoriteProject) {
			$this->usersFavoriteProjects[$favoriteProject->project->id] = $favoriteProject;
			$this->projects[$favoriteProject->project->id] = $favoriteProject->project;
		}

		$slackNotifications = $this->userSlackNotificationsRepository->findBy(["user" => $this->getUser()->id])->orderBy("this->project->name");

		/** @var \Pd\Monitoring\UserSlackNotifications\UserSlackNotifications $slackNotifications */
		foreach ($slackNotifications as $slackNotification) {
			$this->userSlackNotifications[$slackNotification->project->id] = $slackNotification;
		}

		$allProjects = $this->projectsRepository->findDashBoardProjects(\array_keys($this->usersFavoriteProjects));
		foreach ($allProjects as $project) {
			$this->projects[$project->id] = $project;
			$this->nonFavoriteProjects[$project->id] = $project;
		}

		$referenceProjects = $this->projectsRepository->findBy(['reference' => TRUE]);
		foreach ($referenceProjects as $project) {
			$this->projects[$project->id] = $project;
			$this->referenceProjects[$project->id] = $project;
		}
	}


	public function renderDefault(): void
	{
		$this->template->projects = $this->nonFavoriteProjects;
		$this->template->favoriteProjects = $this->usersFavoriteProjects;
		$this->template->referenceProjects = $this->referenceProjects;
	}


	protected function createComponentProject(): \Nette\Application\UI\Multiplier
	{
		$cb = function ($id) {
			$control = $this->projectControlFactory->create($this->projects[$id], $this->usersFavoriteProjects[$id]?? NULL, $this->userSlackNotifications[$id]?? NULL);

			return $control;
		};

		$control = new \Nette\Application\UI\Multiplier($cb);

		return $control;
	}
}
