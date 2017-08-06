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
	 * @var \Pd\Monitoring\User\User
	 */
	private $userRepository;


	public function __construct(
		\Pd\Monitoring\Project\Project $project,
		\Pd\Monitoring\UsersFavoriteProject\UsersFavoriteProject $favoriteProject = NULL,
		\Nette\Security\User $user,
		\Pd\Monitoring\Project\ProjectsRepository $projectsRepository,
		\Pd\Monitoring\UsersFavoriteProject\UsersFavoriteProjectRepository $usersFavoriteProjectsRepository,
		\Pd\Monitoring\User\UsersRepository $userRepository

	) {
		parent::__construct();
		$this->project = $project;
		$this->favoriteProject = $favoriteProject;
		$this->user = $user;
		$this->projectsRepository = $projectsRepository;
		$this->usersFavoriteProjectsRepository = $usersFavoriteProjectsRepository;
		$this->userRepository = $userRepository;
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


	public function handleDeleteFavoriteProject()
	{
		if ($this->usersFavoriteProjectsRepository->checkIfUserHasFavoriteProject($this->user->identity, $this->project)) {
			$this->usersFavoriteProjectsRepository->deleteFavoriteProject($this->user->identity, $this->project);
			$this->presenter->flashMessage(sprintf('Projekt "%s" byl odebrán z oblíbených', $this->project->name), \Pd\Monitoring\DashBoard\Presenters\BasePresenter::FLASH_MESSAGE_SUCCESS);
		} else {
			$this->presenter->flashMessage("Zadaná položka už byla smazána.", \Pd\Monitoring\DashBoard\Presenters\BasePresenter::FLASH_MESSAGE_ERROR);
		}
		$this->redirect("this");
	}


	public function handleSetFavoriteProject()
	{
		if ( ! $this->usersFavoriteProjectsRepository->checkIfUserHasFavoriteProject($this->user->identity, $this->project)) {
			$favoriteProject = new \Pd\Monitoring\UsersFavoriteProject\UsersFavoriteProject();
			$favoriteProject->user = $this->user->identity;
			$favoriteProject->project = $this->project;
			$this->usersFavoriteProjectsRepository->persistAndFlush($favoriteProject);
			$this->presenter->flashMessage(sprintf('Projekt "%s" byl přidán do oblíbených', $favoriteProject->project->name), \Pd\Monitoring\DashBoard\Presenters\BasePresenter::FLASH_MESSAGE_SUCCESS);
		} else {
			$this->presenter->flashMessage("Zadaná položka se už v oblíbených nachází.", \Pd\Monitoring\DashBoard\Presenters\BasePresenter::FLASH_MESSAGE_ERROR);
		}
		$this->redirect("this");
	}


	public function handleNotifications()
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


	public function render()
	{
		$this->template->project = $this->project;

		$checks = [
			\Pd\Monitoring\Check\ICheck::STATUS_OK => [],
			\Pd\Monitoring\Check\ICheck::STATUS_ALERT => [],
			\Pd\Monitoring\Check\ICheck::STATUS_ERROR => [],
		];
		foreach ($this->project->checks as $check) {
			if ($check->paused) {
				continue;
			}
			if ( ! isset($checks[$check->status])) {
				$checks[$check->status] = [];
			}
			$checks[$check->status][$check->id] = $check;
		}
		$total = count($this->project->checks);
		$percents = [];
		if ($total) {
			foreach ($checks as $status => $checksForStatus) {
				$percents[$status] = (count($checksForStatus) * 100) / $total;
			}
		}

		$this->template->checks = $checks;
		$this->template->percents = $percents;
		$this->template->favoriteProject = $this->favoriteProject;

		$this->template->setFile(__DIR__ . '/Control.latte');
		$this->template->render();
	}

}
