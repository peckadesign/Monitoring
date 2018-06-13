<?php declare(strict_types = 1);

namespace Pd\Monitoring\DashBoard\Controls\Project;

class Control extends \Nette\Application\UI\Control
{

	/**
	 * @var \Pd\Monitoring\Project\Project
	 */
	private $project;

	/**
	 * @var \Pd\Monitoring\UsersFavoriteProject\UsersFavoriteProject
	 */
	private $favoriteProject;

	/**
	 * @var \Pd\Monitoring\UserSlackNotifications\UserSlackNotifications
	 */
	private $slackNotifications;

	/**
	 * @var \Nette\Security\User
	 */
	private $user;

	/**
	 * @var \Pd\Monitoring\Project\ProjectsRepository
	 */
	private $projectsRepository;

	/**
	 * @var \Pd\Monitoring\UsersFavoriteProject\UsersFavoriteProjectRepository
	 */
	private $usersFavoriteProjectsRepository;

	/**
	 * @var \Pd\Monitoring\UserSlackNotifications\UserSlackNotificationsRepository
	 */
	private $userSlackNotificationsRepository;

	/**
	 * @var \Pd\Monitoring\Check\ChecksRepository
	 */
	private $checksRepository;


	public function __construct(
		\Pd\Monitoring\Project\Project $project,
		\Pd\Monitoring\UsersFavoriteProject\UsersFavoriteProject $favoriteProject = NULL,
		\Pd\Monitoring\UserSlackNotifications\UserSlackNotifications $slackNotifications = NULL,
		\Nette\Security\User $user,
		\Pd\Monitoring\Project\ProjectsRepository $projectsRepository,
		\Pd\Monitoring\UsersFavoriteProject\UsersFavoriteProjectRepository $usersFavoriteProjectsRepository,
		\Pd\Monitoring\UserSlackNotifications\UserSlackNotificationsRepository $userSlackNotificationsRepository,
		\Pd\Monitoring\Check\ChecksRepository $checksRepository

	) {
		parent::__construct();
		$this->project = $project;
		$this->favoriteProject = $favoriteProject;
		$this->slackNotifications = $slackNotifications;
		$this->user = $user;
		$this->projectsRepository = $projectsRepository;
		$this->usersFavoriteProjectsRepository = $usersFavoriteProjectsRepository;
		$this->userSlackNotificationsRepository = $userSlackNotificationsRepository;
		$this->checksRepository = $checksRepository;
	}


	protected function createTemplate()
	{
		$template = parent::createTemplate();

		$template->addFilter('check', function (int $value) {
			switch ($value) {
				case \Pd\Monitoring\Check\ICheck::STATUS_OK:
					return 'success';
				case \Pd\Monitoring\Check\ICheck::STATUS_ALERT:
					return 'warning';
				case \Pd\Monitoring\Check\ICheck::STATUS_ERROR:
					return 'danger';
				default:
					return 'danger';
			}
		});

		return $template;
	}


	public function handleDeleteFavoriteProject(): void
	{
		if ($this->usersFavoriteProjectsRepository->checkIfUserHasFavoriteProject($this->user->identity, $this->project)) {
			$this->usersFavoriteProjectsRepository->deleteFavoriteProject($this->user->identity, $this->project);
			$this->presenter->flashMessage(\sprintf('Projekt "%s" byl odebrán z oblíbených', $this->project->name), \Pd\Monitoring\DashBoard\Presenters\BasePresenter::FLASH_MESSAGE_SUCCESS);
		} else {
			$this->presenter->flashMessage("Zadaná položka už byla smazána.", \Pd\Monitoring\DashBoard\Presenters\BasePresenter::FLASH_MESSAGE_ERROR);
		}
		$this->redirect("this");
	}


	public function handleSetFavoriteProject(): void
	{
		if ( ! $this->usersFavoriteProjectsRepository->checkIfUserHasFavoriteProject($this->user->identity, $this->project)) {
			$favoriteProject = new \Pd\Monitoring\UsersFavoriteProject\UsersFavoriteProject();
			$favoriteProject->user = $this->user->identity;
			$favoriteProject->project = $this->project;
			$this->usersFavoriteProjectsRepository->persistAndFlush($favoriteProject);
			$this->presenter->flashMessage(\sprintf('Projekt "%s" byl přidán do oblíbených', $favoriteProject->project->name), \Pd\Monitoring\DashBoard\Presenters\BasePresenter::FLASH_MESSAGE_SUCCESS);
		} else {
			$this->presenter->flashMessage("Zadaná položka se už v oblíbených nachází.", \Pd\Monitoring\DashBoard\Presenters\BasePresenter::FLASH_MESSAGE_ERROR);
		}
		$this->redirect("this");
	}


	public function handleNotifications(): void
	{
		if ( ! $this->user->isAllowed('project', 'edit')) {
			throw new \Nette\Application\ForbiddenRequestException();
		}

		$this->project->notifications = ! $this->project->notifications;
		$this->projectsRepository->persistAndFlush($this->project);

		$this->getPresenter()->flashMessage('Nastavení notifikací bylo uloženo', \Pd\Monitoring\DashBoard\Presenters\BasePresenter::FLASH_MESSAGE_SUCCESS);

		if ($this->getPresenter()->isAjax()) {
			$this->redrawControl();
		} else {
			$this->redirect('this');
		}
	}


	public function handleSetUserSlackNotifications(): void
	{
		if ( ! $this->userSlackNotificationsRepository->checkIfUserHasSlackNotifications($this->user->identity, $this->project)) {
			$slackNotifications = new \Pd\Monitoring\UserSlackNotifications\UserSlackNotifications();
			$slackNotifications->user = $this->user->identity;
			$slackNotifications->project = $this->project;
			$this->userSlackNotificationsRepository->persistAndFlush($slackNotifications);
			$this->presenter->flashMessage(\sprintf('Notifikace k projektu "%s" budou odesílány do osobního kanálu', $slackNotifications->project->name), \Pd\Monitoring\DashBoard\Presenters\BasePresenter::FLASH_MESSAGE_SUCCESS);
		} else {
			$this->presenter->flashMessage("Tento odběr je již nastaven.", \Pd\Monitoring\DashBoard\Presenters\BasePresenter::FLASH_MESSAGE_ERROR);
		}
		$this->redirect("this");
	}


	public function handleDeleteUserSlackNotifications(): void
	{
		if ($this->userSlackNotificationsRepository->checkIfUserHasSlackNotifications($this->user->identity, $this->project)) {
			$this->userSlackNotificationsRepository->deleteUserSlackNotifications($this->user->identity, $this->project);
			$this->presenter->flashMessage(\sprintf('Notifikace k projektu "%s" byla odebrána', $this->project->name), \Pd\Monitoring\DashBoard\Presenters\BasePresenter::FLASH_MESSAGE_SUCCESS);
		} else {
			$this->presenter->flashMessage("Tato notifikace už byla odebrána", \Pd\Monitoring\DashBoard\Presenters\BasePresenter::FLASH_MESSAGE_ERROR);
		}
		$this->redirect("this");
	}


	public function render(): void
	{
		$this->template->project = $this->project;

		$groupedChecks = [
			\Pd\Monitoring\Check\ICheck::STATUS_OK => [],
			\Pd\Monitoring\Check\ICheck::STATUS_ALERT => [],
			\Pd\Monitoring\Check\ICheck::STATUS_ERROR => [],
		];

		if (\count($this->project->subProjects)) {
			$conditions = [
				'project' => $this->project->subProjects->getIterator()->fetchPairs('id', 'id'),
			];
			$checks = $this->checksRepository->findBy($conditions);
		} else {
			$checks = $this->project->checks;
		}

		foreach ($checks as $check) {
			if ($check->paused) {
				continue;
			}
			if ( ! isset($groupedChecks[$check->status])) {
				$groupedChecks[$check->status] = [];
			}
			$groupedChecks[$check->status][$check->id] = $check;
		}
		$total = \count($checks);
		$percents = [];
		if ($total) {
			foreach ($groupedChecks as $status => $checksForStatus) {
				if ( ! count($checksForStatus)) {
						continue;
				}
				$percents[$status] = (\count($checksForStatus) * 100) / $total;
			}
		}

		$this->template->checks = $groupedChecks;
		$this->template->percents = $percents;
		$this->template->favoriteProject = $this->favoriteProject;

		$this->template->slackNotifications = $this->slackNotifications;

		$this->template->setFile(__DIR__ . '/Control.latte');
		$this->template->render();
	}

}
