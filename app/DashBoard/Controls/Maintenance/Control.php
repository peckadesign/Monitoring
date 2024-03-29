<?php declare(strict_types = 1);

namespace Pd\Monitoring\DashBoard\Controls\Maintenance;

class Control extends \Nette\Application\UI\Control
{

	private \Pd\Monitoring\Project\Project $project;

	private \Pd\Monitoring\Project\ProjectsRepository $projectsRepository;

	private \Pd\Monitoring\Utils\IDateTimeProvider $dateTimeProvider;

	/**
	 * @var array|IOnToggle[]
	 */
	private array $onToggleHandlers = [];

	private \Pd\Monitoring\Slack\Notifier $notifier;

	private \Nette\Security\User $user;


	public function __construct(
		\Pd\Monitoring\Project\Project $project,
		\Pd\Monitoring\Project\ProjectsRepository $projectsRepository,
		\Pd\Monitoring\Utils\IDateTimeProvider $dateTimeProvider,
		\Pd\Monitoring\Slack\Notifier $notifier,
		\Nette\Security\User $user
	)
	{
		$this->project = $project;
		$this->projectsRepository = $projectsRepository;
		$this->dateTimeProvider = $dateTimeProvider;
		$this->notifier = $notifier;
		$this->user = $user;
	}


	protected function createTemplate(): \Nette\Application\UI\ITemplate
	{
		/** @var \Latte\Runtime\Template $template */
		$template = parent::createTemplate();

		$template->addFilter('dateTime', static function (\DateTimeImmutable $value)
		{
			return $value->format('j. n. Y H:i:s');
		});

		return $template;
	}


	public function render(): void
	{
		if ( ! $this->user->isAllowed($this->project, \Pd\Monitoring\User\AclFactory::PRIVILEGE_EDIT)) {
			return;
		}

		$this->template->project = $this->project;

		$this->template->setFile(__DIR__ . '/Control.latte');
		$this->template->render();
	}


	public function handleToggle(): void
	{
		if ( ! $this->user->isAllowed($this->project, \Pd\Monitoring\User\AclFactory::PRIVILEGE_EDIT)) {
			throw new \Nette\Application\ForbiddenRequestException();
		}

		if ($this->project->maintenance) {
			$this->project->maintenance = NULL;
			$action = 'vypnul';
		} else {
			$this->project->maintenance = $this->dateTimeProvider->getDateTime();
			$action = 'zapnul';
		}

		$this->projectsRepository->persistAndFlush($this->project);

		/** @var \Pd\Monitoring\User\User $identity */
		$identity = $this->user->getIdentity();
		$statusMessage = \sprintf(
			'%s %s údržbu projektu %s.',
			$identity->gitHubName,
			$action,
			$this->project->name
		);
		foreach ($this->project->getSlackIntegrations() as $slackIntegration) {
			$this->notifier->notify($slackIntegration->hookUrl, $slackIntegration->channel, $statusMessage, 'good', []);
		}

		foreach ($this->onToggleHandlers as $handler) {
			$handler->process($this);
		}

		$this->processRequest();
	}


	private function processRequest(): void
	{
		if ($this->getPresenter()->isAjax()) {
			$this->redrawControl();
		} else {
			$this->redirect('this');
		}
	}


	public function addOnToggle(IOnToggle $handler): void
	{
		$this->onToggleHandlers[] = $handler;
	}


	protected function createComponentMaintenanceForm(): \Nette\Application\UI\Form
	{
		$form = new \Nette\Application\UI\Form();

		$form->addCheckbox('maintenance', 'Údržba')->setDefaultValue($this->project->maintenance !== NULL);

		return $form;
	}

}
