<?php declare(strict_types = 1);

namespace Pd\Monitoring\DashBoard\Controls\Project;

class Control extends \Nette\Application\UI\Control
{

	private \Pd\Monitoring\Project\Project $project;

	private ?\Pd\Monitoring\UsersFavoriteProject\UsersFavoriteProject $favoriteProject;

	private ?\Pd\Monitoring\UserProjectNotifications\UserProjectNotifications $slackNotifications;

	private \Nette\Security\User $user;

	private \Pd\Monitoring\Project\ProjectsRepository $projectsRepository;

	private \Pd\Monitoring\UsersFavoriteProject\UsersFavoriteProjectRepository $usersFavoriteProjectsRepository;

	private \Pd\Monitoring\UserProjectNotifications\UserProjectNotificationsRepository $userProjectNotificationsRepository;

	private \Pd\Monitoring\Check\ChecksRepository $checksRepository;

	private \Pd\Monitoring\UserOnProject\UserOnProjectRepository $userOnProjectRepository;


	public function __construct(
		\Pd\Monitoring\Project\Project $project,
		?\Pd\Monitoring\UsersFavoriteProject\UsersFavoriteProject $favoriteProject = NULL,
		?\Pd\Monitoring\UserProjectNotifications\UserProjectNotifications $slackNotifications = NULL,
		\Nette\Security\User $user,
		\Pd\Monitoring\Project\ProjectsRepository $projectsRepository,
		\Pd\Monitoring\UsersFavoriteProject\UsersFavoriteProjectRepository $usersFavoriteProjectsRepository,
		\Pd\Monitoring\UserProjectNotifications\UserProjectNotificationsRepository $userProjectNotificationsRepository,
		\Pd\Monitoring\Check\ChecksRepository $checksRepository,
		\Pd\Monitoring\UserOnProject\UserOnProjectRepository $userOnProjectRepository

	)
	{
		$this->project = $project;
		$this->favoriteProject = $favoriteProject;
		$this->slackNotifications = $slackNotifications;
		$this->user = $user;
		$this->projectsRepository = $projectsRepository;
		$this->usersFavoriteProjectsRepository = $usersFavoriteProjectsRepository;
		$this->userProjectNotificationsRepository = $userProjectNotificationsRepository;
		$this->checksRepository = $checksRepository;
		$this->userOnProjectRepository = $userOnProjectRepository;
	}


	protected function createTemplate(): \Nette\Application\UI\ITemplate
	{
		$template = parent::createTemplate();

		$template->addFilter('check', static function (int $value)
		{
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
		if ( ! $this->user->isAllowed($this->project, \Pd\Monitoring\User\AclFactory::PRIVILEGE_VIEW)) {
			throw new \Nette\Application\ForbiddenRequestException();
		}

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
		if ( ! $this->user->isAllowed($this->project, \Pd\Monitoring\User\AclFactory::PRIVILEGE_VIEW)) {
			throw new \Nette\Application\ForbiddenRequestException();
		}

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
		if ( ! $this->user->isAllowed($this->project, \Pd\Monitoring\User\AclFactory::PRIVILEGE_EDIT)) {
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


	public function handleSetUserProjectNotifications(): void
	{
		if ( ! $this->user->isAllowed($this->project, \Pd\Monitoring\User\AclFactory::PRIVILEGE_VIEW)) {
			throw new \Nette\Application\ForbiddenRequestException();
		}

		if ( ! $this->userProjectNotificationsRepository->checkIfUserHasSlackNotifications($this->user->identity, $this->project)) {
			$slackNotifications = new \Pd\Monitoring\UserProjectNotifications\UserProjectNotifications();
			$slackNotifications->user = $this->user->identity;
			$slackNotifications->project = $this->project;
			$this->userProjectNotificationsRepository->persistAndFlush($slackNotifications);
			$this->presenter->flashMessage(\sprintf('Notifikace k projektu "%s" budou odesílány do osobního kanálu', $slackNotifications->project->name), \Pd\Monitoring\DashBoard\Presenters\BasePresenter::FLASH_MESSAGE_SUCCESS);
		} else {
			$this->presenter->flashMessage("Tento odběr je již nastaven.", \Pd\Monitoring\DashBoard\Presenters\BasePresenter::FLASH_MESSAGE_ERROR);
		}
		$this->redirect("this");
	}


	public function handleDeleteUserProjectNotifications(): void
	{
		if ( ! $this->user->isAllowed($this->project, \Pd\Monitoring\User\AclFactory::PRIVILEGE_VIEW)) {
			throw new \Nette\Application\ForbiddenRequestException();
		}

		if ($this->userProjectNotificationsRepository->checkIfUserHasSlackNotifications($this->user->identity, $this->project)) {
			$this->userProjectNotificationsRepository->deleteUserProjectNotifications($this->user->identity, $this->project);
			$this->presenter->flashMessage(\sprintf('Notifikace k projektu "%s" byla odebrána', $this->project->name), \Pd\Monitoring\DashBoard\Presenters\BasePresenter::FLASH_MESSAGE_SUCCESS);
		} else {
			$this->presenter->flashMessage("Tato notifikace už byla odebrána", \Pd\Monitoring\DashBoard\Presenters\BasePresenter::FLASH_MESSAGE_ERROR);
		}
		$this->redirect("this");
	}


	public function render(): void
	{
		if ( ! $this->user->isAllowed($this->project, \Pd\Monitoring\User\AclFactory::PRIVILEGE_VIEW)) {
			return;
		}

		$this->template->project = $this->project;

		$groupedChecks = [
			\Pd\Monitoring\Check\ICheck::STATUS_OK => [],
			\Pd\Monitoring\Check\ICheck::STATUS_ALERT => [],
			\Pd\Monitoring\Check\ICheck::STATUS_ERROR => [],
		];

		$cb = static function (\Pd\Monitoring\UserOnProject\UserOnProject $userOnProject): int
		{
			return $userOnProject->project->id;
		};
		$allowedProjectsIds = \array_map($cb, \iterator_to_array($this->userOnProjectRepository->findBy(['user' => $this->user->getId()])->getIterator()));

		if (\count($this->project->subProjects)) {
			if ($this->user->getIdentity()->administrator) {
				$conditions = [
					'project' => $this->project->subProjects->getIterator()->fetchPairs('id', 'id'),
				];
			} else {
				$conditions = [
					'project' => \array_intersect($this->project->subProjects->getIterator()->fetchPairs('id', 'id'), $allowedProjectsIds),
				];
			}
			$checks = $this->checksRepository->findBy($conditions);
		} else {
			if ($this->user->getIdentity()->administrator) {
				$conditions = [
					'project' => $this->project->id,
				];
			} else {
				$conditions = [
					'project' => \array_intersect([$this->project->id], $allowedProjectsIds),
				];
			}
			$checks = $this->checksRepository->findBy($conditions);
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
				if ( ! \count($checksForStatus)) {
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
